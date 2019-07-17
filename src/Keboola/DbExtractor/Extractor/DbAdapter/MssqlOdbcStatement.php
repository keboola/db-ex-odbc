<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor\DbAdapter;

class MssqlOdbcStatement implements PdoStatementInterface
{
    /** @var resource */
    private $stmt;

    public function __construct(
        $odbcResult
    ) {
        if (!is_resource($odbcResult)) {
            throw new \Exception('Expected odbc resource, got ' . print_r($odbcResult, true));
        }
        $this->stmt = $odbcResult;
    }

    public function __destruct()
    {
        odbc_free_result($this->stmt);
    }

    public function execute($bind = null)
    {
        if ($bind === null) {
            $bind = [];
        }
        $parametersArray = $this->repairBinding($bind);
        return odbc_execute($this->stmt, $parametersArray);
    }

    public function fetch($fetchStyle = null, $cursor_orientation = PdoInterface::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        if ($fetchStyle == PdoInterface::FETCH_ASSOC) {
            return odbc_fetch_array($this->stmt);
        } else {
            throw new \Exception(sprintf('Invalid fetchStyle "%s"', $fetchStyle));
        }
    }

    public function bindParam(
        $parameter,
        &$variable,
        $data_type = PdoInterface::PARAM_STR,
        $length = null,
        $driver_options = null
    ) {
        throw new \Exception('Not implemented yet');
    }

    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
        throw new \Exception('Not implemented yet');
    }

    public function bindValue($parameter, $value, $data_type = PdoInterface::PARAM_STR)
    {
        throw new \Exception('Not implemented yet');
    }

    public function rowCount()
    {
        throw new \Exception('Not implemented yet');
    }

    public function fetchColumn($column_number = 0)
    {
        throw new \Exception('Not implemented yet');
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, array $ctor_args = [])
    {
        throw new \Exception('Not implemented yet');
    }

    public function fetchObject($class_name = "stdClass", array $ctor_args = [])
    {
        throw new \Exception('Not implemented yet');
    }

    public function errorCode()
    {
        throw new \Exception('Not implemented yet');
    }

    public function errorInfo()
    {
        throw new \Exception('Not implemented yet');
    }

    public function setAttribute($attribute, $value)
    {
        throw new \Exception('Not implemented yet');
    }

    public function getAttribute($attribute)
    {
        throw new \Exception('Not implemented yet');
    }

    public function columnCount()
    {
        throw new \Exception('Not implemented yet');
    }

    public function getColumnMeta($column)
    {
        throw new \Exception('Not implemented yet');
    }

    public function setFetchMode($mode, $classNameObject = null, array $ctorarfg = [])
    {
        throw new \Exception('Not implemented yet');
    }

    public function nextRowset()
    {
        throw new \Exception('Not implemented yet');
    }

    public function closeCursor()
    {
        throw new \Exception('Not implemented yet');
    }

    public function debugDumpParams()
    {
        throw new \Exception('Not implemented yet');
    }

    /**
     * Avoid odbc file open http://php.net/manual/en/function.odbc-execute.php
     *
     * @param array $bind
     * @return array
     */
    private function repairBinding(array $bind): array
    {
        return array_map(function ($value) {
            if (preg_match("/^'.*'$/", $value)) {
                return " {$value} ";
            } else {
                return $value;
            }
        }, $bind);
    }
}
