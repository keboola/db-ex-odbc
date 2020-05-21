<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor;

use Keboola\DbExtractor\Exception\DeadConnectionException;
use Symfony\Component\Process\Process;
use Keboola\Csv\CsvFile;
use Keboola\DbExtractor\Exception\ApplicationException;
use Keboola\DbExtractor\Exception\UserException;
use Keboola\DbExtractor\Extractor\DbAdapter\MssqlOdbcStatement;
use Keboola\DbExtractor\Extractor\DbAdapter\PdoInterface;
use Keboola\DbExtractor\RetryProxy;

class ODBC extends Extractor
{
    public const INCREMENT_TYPE_NUMERIC = 'numeric';
    public const INCREMENT_TYPE_DATETIME = 'datetime';
    public const INCREMENT_TYPE_BINARY = 'binary';
    public const INCREMENT_TYPE_QUOTABLE = 'quotable';

    /** @var MetadataProvider */
    private $metadataProvider;

    /** @var DbAdapter\MssqlAdapter */
    protected $db;

    public function __construct(array $parameters, array $state = [], $logger = null)
    {
        parent::__construct($parameters, $state, $logger);

        $this->metadataProvider = new MetadataProvider($this->db);
    }

    public function createConnection(array $params): DbAdapter\MssqlAdapter
    {
        // check params
        if (isset($params['#password'])) {
            $params['password'] = $params['#password'];
        }

        foreach (['host', 'database', 'user', 'password'] as $r) {
            if (!array_key_exists($r, $params)) {
                throw new UserException(sprintf("Parameter %s is missing.", $r));
            }
        }

        // construct DSN connection string
        $host = $params['host'];
        $host .= (isset($params['port']) && $params['port'] !== '1433') ? ',' . $params['port'] : '';
        $host .= empty($params['instance']) ? '' : '\\\\' . $params['instance'];
        $options[] = 'Server=' . $host;
        $options[] = 'Database=' . $params['database'];
        $options[] = 'RTK=' . getenv('RTK_LICENSE');
        $dsn = sprintf("DRIVER={CData ODBC Driver for SQL Server};%s", implode(';', $options));
        $this->logger->info("Connecting to DSN '" . $dsn . "'");

        // ms sql doesn't support options
        $pdo = new DbAdapter\MssqlAdapter($dsn, $params['user'], $params['password']);
        return $pdo;
    }

    public function getConnection(): DbAdapter\MssqlAdapter
    {
        return $this->db;
    }

    public function testConnection(): void
    {
        $this->db->testConnection();
    }

    private function stripNullBytesInEmptyFields(string $fileName): void
    {
        // this will replace null byte column values in the file
        // this is here because BCP will output null bytes for empty strings
        // this can occur in advanced queries where the column isn't sanitized
        $nullAtStart = 's/^\x00,/,/g';
        $nullAtEnd = 's/,\x00$/,/g';
        $nullInTheMiddle = 's/,\x00,/,,/g';
        $sedCommand = sprintf('sed -e \'%s;%s;%s\' -i %s', $nullAtStart, $nullInTheMiddle, $nullAtEnd, $fileName);

        $process = new Process($sedCommand);
        $process->setTimeout(300);
        $process->run();
        if ($process->getExitCode() !== 0 || !empty($process->getErrorOutput())) {
            throw new ApplicationException(
                sprintf("Error Stripping Nulls: %s", $process->getErrorOutput())
            );
        }
    }

    public function export(array $table): array
    {
        $export = parent::export($table);
        if (!array_key_exists('table', $table) && array_key_exists('query', $table)) {
            $manifestFile = $this->getOutputFilename($table['outputTable']) . '.manifest';
            if (file_exists($manifestFile)) {
                $manifest = json_decode(file_get_contents($manifestFile), true);
                $manifest['columns'] = $this->getAdvancedQueryColumns($table['query']);
                file_put_contents($manifestFile, json_encode($manifest));
                $this->stripNullBytesInEmptyFields($this->getOutputFilename($table['outputTable']));
            }
        }
        return $export;
    }

    /**
     * @param string $query
     * @return array|bool
     * @throws UserException
     */
    public function getAdvancedQueryColumns(string $query)
    {
        // This will only work if the server is >= sql server 2012
        $sql = sprintf(
            "SELECT name, system_type_name FROM sys.dm_exec_describe_first_result_set('%s', null, 1);",
            rtrim(trim(str_replace("'", "''", $query)), ';')
        );
        try {
            $stmt = $this->db->query($sql);
            $result = $stmt->fetchAll(PdoInterface::FETCH_ASSOC);
            if (is_array($result) && !empty($result)) {
                return array_map(
                    function ($row) {
                        return $row['name'];
                    },
                    $result
                );
            }
            return false;
        } catch (\Exception $e) {
            throw new UserException(
                sprintf('DB query "%s" failed: %s', $sql, $e->getMessage()),
                0,
                $e
            );
        }
    }

    public function getTables(?array $tables = null): array
    {
        $proxy = new RetryProxy($this->logger);
        return $proxy->call(function () use ($tables): array {
            try {
                return $this->metadataProvider->getTables($tables);
            } catch (\Throwable $exception) {
                try {
                    $this->isAlive();
                } catch (DeadConnectionException $deadConnectionException) {
                    $this->db = $this->createConnection($this->getDbParameters());
                    $this->metadataProvider = new MetadataProvider($this->db);
                }
                throw $exception;
            }
        });
    }

    public function columnToBcpSql(array $column): string
    {
        $datatype = new MssqlDataType(
            $column['type'],
            array_intersect_key($column, array_flip(MssqlDataType::DATATYPE_KEYS))
        );
        $colstr = $escapedColumnName = $this->db->quoteIdentifier($column['name']);
        if ($datatype->getType() === 'timestamp') {
            $colstr = sprintf('CONVERT(NVARCHAR(MAX), CONVERT(BINARY(8), %s), 1)', $colstr);
        } else if ($datatype->getBasetype() === 'STRING') {
            if ($datatype->getType() === 'text'
                || $datatype->getType() === 'ntext'
                || $datatype->getType() === 'xml'
            ) {
                $colstr = sprintf('CAST(%s as nvarchar(max))', $colstr);
            }
            $colstr = sprintf("REPLACE(%s, char(34), char(34) + char(34))", $colstr);
            if ($datatype->isNullable()) {
                $colstr = sprintf("COALESCE(%s,'')", $colstr);
            }
            $colstr = sprintf("char(34) + %s + char(34)", $colstr);
        } else if ($datatype->getBasetype() === 'TIMESTAMP'
            && strtoupper($datatype->getType()) !== 'SMALLDATETIME'
        ) {
            $colstr = sprintf('CONVERT(DATETIME2(0),%s)', $colstr);
        }
        if ($colstr !== $escapedColumnName) {
            return $colstr . ' AS ' . $escapedColumnName;
        }
        return $colstr;
    }

    public function simpleQuery(array $table, array $columns = array()): string
    {
        $queryStart = "SELECT";
        if (isset($this->incrementalFetching['limit'])) {
            $queryStart .= sprintf(
                " TOP %d",
                $this->incrementalFetching['limit']
            );
        }

        if (count($columns) > 0) {
            $query = sprintf(
                "%s %s FROM %s.%s",
                $queryStart,
                implode(
                    ', ',
                    array_map(
                        function ($column): string {
                            return $this->db->quoteIdentifier($column);
                        },
                        $columns
                    )
                ),
                $this->db->quoteIdentifier($table['schema']),
                $this->db->quoteIdentifier($table['tableName'])
            );
        } else {
            $query = sprintf(
                "%s * FROM %s.%s",
                $queryStart,
                $this->db->quoteIdentifier($table['schema']),
                $this->db->quoteIdentifier($table['tableName'])
            );
        }

        if (isset($table['nolock']) && $table['nolock']) {
            $query .= " WITH(NOLOCK)";
        }
        $incrementalAddon = $this->getIncrementalQueryAddon();
        if ($incrementalAddon) {
            $query .= $incrementalAddon;
        }
        return $query;
    }

    public function columnToPdoSql(array $column): string
    {
        $datatype = new MssqlDataType(
            $column['type'],
            array_intersect_key($column, array_flip(MssqlDataType::DATATYPE_KEYS))
        );
        $colstr = $escapedColumnName = $this->db->quoteIdentifier($column['name']);
        if ($datatype->getType() === 'timestamp') {
            $colstr = sprintf('CONVERT(NVARCHAR(MAX), CONVERT(BINARY(8), %s), 1)', $colstr);
        } else {
            if ($datatype->getType() === 'text'
                || $datatype->getType() === 'ntext'
                || $datatype->getType() === 'xml'
            ) {
                $colstr = sprintf('CAST(%s as nvarchar(max))', $colstr);
            }
        }
        if ($colstr !== $escapedColumnName) {
            return $colstr . ' AS ' . $escapedColumnName;
        }
        return $colstr;
    }

    public function getSimplePdoQuery(array $table, ?array $columns = []): string
    {
        $queryStart = "SELECT";
        if (isset($this->incrementalFetching['limit'])) {
            $queryStart .= sprintf(
                " TOP %d",
                $this->incrementalFetching['limit']
            );
        }

        if ($columns && count($columns) > 0) {
            $query = sprintf(
                "%s %s FROM %s.%s",
                $queryStart,
                implode(
                    ', ',
                    array_map(
                        function (array $column): string {
                            return $this->columnToPdoSql($column);
                        },
                        $columns
                    )
                ),
                $this->db->quoteIdentifier($table['schema']),
                $this->db->quoteIdentifier($table['tableName'])
            );
        } else {
            $query = sprintf(
                "%s * FROM %s.%s",
                $queryStart,
                $this->db->quoteIdentifier($table['schema']),
                $this->db->quoteIdentifier($table['tableName'])
            );
        }
        if ($table['nolock']) {
            $query .= " WITH(NOLOCK)";
        }
        $incrementalAddon = $this->getIncrementalQueryAddon();
        if ($incrementalAddon) {
            $query .= $incrementalAddon;
        }
        return $query;
    }

    public static function getColumnMetadata(array $column): array
    {
        $datatype = new MssqlDataType(
            $column['type'],
            array_intersect_key($column, array_flip(self::DATATYPE_KEYS))
        );
        $columnMetadata = $datatype->toMetadata();
        $nonDatatypeKeys = array_diff_key($column, array_flip(self::DATATYPE_KEYS));
        foreach ($nonDatatypeKeys as $key => $value) {
            if ($key === 'name') {
                $columnMetadata[] = [
                    'key' => "KBC.sourceName",
                    'value' => $value,
                ];
            } else {
                $columnMetadata[] = [
                    'key' => "KBC." . $key,
                    'value' => $value,
                ];
            }
        }
        return $columnMetadata;
    }

    public function validateIncrementalFetching(array $table, string $columnName, ?int $limit = null): void
    {
        $query = sprintf(
            "SELECT is_identity, TYPE_NAME(system_type_id) AS data_type 
            FROM sys.columns 
            WHERE object_id = OBJECT_ID('[%s].[%s]') AND sys.columns.name = '%s'",
            $table['schema'],
            $table['tableName'],
            $columnName
        );

        $res = $this->db->query($query);
        $columns = $res->fetchAll();

        if (count($columns) === 0) {
            throw new UserException(
                sprintf(
                    'Column [%s] specified for incremental fetching was not found in the table',
                    $columnName
                )
            );
        }

        $this->incrementalFetching['column'] = $columnName;
        if (in_array($columns[0]['data_type'], MssqlDataType::getNumericTypes())) {
            $this->incrementalFetching['type'] = self::INCREMENT_TYPE_NUMERIC;
        } else if ($columns[0]['data_type'] === 'timestamp') {
            $this->incrementalFetching['type'] = self::INCREMENT_TYPE_BINARY;
        } else if ($columns[0]['data_type'] === 'smalldatetime') {
            $this->incrementalFetching['type'] = self::INCREMENT_TYPE_QUOTABLE;
        } else if (in_array($columns[0]['data_type'], MssqlDataType::TIMESTAMP_TYPES)) {
            $this->incrementalFetching['type'] = self::INCREMENT_TYPE_DATETIME;
        } else {
            throw new UserException(
                sprintf(
                    'Column [%s] specified for incremental fetching is not numeric or datetime',
                    $columnName
                )
            );
        }
        if ($limit) {
            $this->incrementalFetching['limit'] = $limit;
        }
    }

    private function getIncrementalQueryAddon(): ?string
    {
        $incrementalAddon = null;
        if ($this->incrementalFetching) {
            if (isset($this->state['lastFetchedRow'])) {
                $lastFetchedRow = $this->state['lastFetchedRow'];
                if ($this->incrementalFetching['type'] == self::INCREMENT_TYPE_BINARY) {
                    $lastFetchedRow = '0x' . bin2hex($lastFetchedRow);
                }
                $incrementalAddon = sprintf(
                    " WHERE %s >= %s",
                    $this->db->quoteIdentifier($this->incrementalFetching['column']),
                    $this->shouldQuoteComparison($this->incrementalFetching['type'])
                        ? $this->db->quote($lastFetchedRow)
                        : $lastFetchedRow
                );
            }
            $incrementalAddon .= sprintf(" ORDER BY %s", $this->db->quoteIdentifier($this->incrementalFetching['column']));
        }
        return $incrementalAddon;
    }

    private function shouldQuoteComparison(string $type): bool
    {
        if ($type === self::INCREMENT_TYPE_NUMERIC || $type === self::INCREMENT_TYPE_BINARY) {
            return false;
        }
        return true;
    }

    /**
     * @param PDOStatement $stmt
     * @param CsvFile $csv
     * @param boolean $includeHeader
     * @return array ['rows', 'lastFetchedRow']
     */
    protected function writeToCsvLocal(MssqlOdbcStatement $stmt, CsvFile $csv, bool $includeHeader = true): array
    {
        $output = [];

        $resultRow = $stmt->fetch(PdoInterface::FETCH_ASSOC);

        if (is_array($resultRow) && !empty($resultRow)) {
            // write header and first line
            if ($includeHeader) {
                $csv->writeRow(array_keys($resultRow));
            }
            $csv->writeRow($resultRow);

            // write the rest
            $numRows = 1;
            $lastRow = $resultRow;

            while ($resultRow = $stmt->fetch(PdoInterface::FETCH_ASSOC)) {
                $csv->writeRow($resultRow);
                $lastRow = $resultRow;
                $numRows++;
            }
            $stmt->closeCursor();

            if (isset($this->incrementalFetching['column'])) {
                if (!array_key_exists($this->incrementalFetching['column'], $lastRow)) {
                    throw new UserException(
                        sprintf(
                            "The specified incremental fetching column %s not found in the table",
                            $this->incrementalFetching['column']
                        )
                    );
                }
                $output['lastFetchedRow'] = $lastRow[$this->incrementalFetching['column']];
            }
            $output['rows'] = $numRows;
            return $output;
        }
        // no rows found.  If incremental fetching is turned on, we need to preserve the last state
        if ($this->incrementalFetching['column'] && isset($this->state['lastFetchedRow'])) {
            $output = $this->state;
        }
        $output['rows'] = 0;
        return $output;
    }
}
