<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor\DbAdapter;

use Keboola\DbExtractor\Exception\ApplicationException;
use Keboola\DbExtractor\Exception\UserException;

class MssqlAdapter implements PdoInterface
{
    /** @var resource */
    private $connection;

    public function __construct(
        $dsn,
        $username = null,
        $passwd = null,
        $options = null
    ) {
        $this->connection = odbc_connect($dsn, $username, $passwd);
    }

    public function testConnection(): void
    {
        $this->query('SELECT GETDATE() AS CurrentDateTime');
    }

    public function query($statement, $bind = [], $mode = 19, $arg3 = null, array $ctorargs = []): PdoStatementInterface
    {
        try {
            return $this->prepare($statement);
        } catch (\Throwable $e) {
            throw new ApplicationException($e->getMessage(), 0, $e);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->connection)) {
            odbc_close($this->connection);
        }
    }

    public function quoteIdentifier(string $obj): string
    {
        return "[{$obj}]";
    }

    public function fetchServerVersion(): string
    {
        // get the MSSQL Server version (note, 2008 is version 10.*
        $res = $this->query("SELECT SERVERPROPERTY('ProductVersion') AS version;");

        $res->execute();
        $versionString = $res->fetch(PdoInterface::FETCH_ASSOC);
        if (!isset($versionString['version'])) {
            throw new UserException("Unable to get SQL Server Version Information");
        }
        return $versionString['version'];
    }

    public function prepare($statement, array $driver_options = []): PdoStatementInterface
    {
        return new MssqlOdbcStatement(odbc_prepare($this->connection, $statement));
    }

    public function beginTransaction(): bool
    {
        return odbc_autocommit($this->connection, 0);
    }

    public function commit(): bool
    {
        return odbc_commit($this->connection);
    }

    public function rollBack(): bool
    {
        return odbc_rollback($this->connection);
    }

    public function inTransaction(): bool
    {
        throw new \Exception('Not implemented yet');
        return false;
    }

    public function setAttribute($attribute, $value): bool
    {
        throw new \Exception('Not implemented yet');
        return false;
    }

    public function exec($statement): void
    {
        $stmt = $this->query($statement);
//        var_dump($statement);
        $stmt->execute();
    }

    public function lastInsertId($name = null): string
    {
        throw new \Exception('Not implemented yet');
        return '';
    }

    public function errorCode(): string
    {
        throw new \Exception('Not implemented yet');
        return '';
    }

    public function errorInfo(): array
    {
        throw new \Exception('Not implemented yet');
        return [];
    }

    public function getAttribute($attribute)
    {
        throw new \Exception('Not implemented yet');
    }

    public function quote($string, $parameterType = PdoInterface::PARAM_STR): string
    {
        if ($parameterType === PdoInterface::PARAM_STR) {
            return sprintf("'%s'", str_replace("'", "''", $string));
        }
        throw new \Exception(sprintf('Cannot quote "%s" parameter type', $parameterType));
    }
}
