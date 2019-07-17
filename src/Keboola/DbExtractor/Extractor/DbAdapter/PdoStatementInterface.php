<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor\DbAdapter;

use Traversable;

interface PdoStatementInterface /*extends Traversable*/
{
    public function execute(
        $input_parameters = null
    );

    public function fetch($fetch_style = null, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0);

    public function bindParam(
        $parameter,
        &$variable,
        $data_type = \PDO::PARAM_STR,
        $length = null,
        $driver_options = null
    );

    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null);

    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR);

    public function rowCount();

    public function fetchColumn($column_number = 0);

    public function fetchAll($fetch_style = null, $fetch_argument = null, array $ctor_args = []);

    public function fetchObject($class_name = "stdClass", array $ctor_args = []);

    public function errorCode();

    public function errorInfo();

    public function setAttribute($attribute, $value);

    public function getAttribute($attribute);

    public function columnCount();

    public function getColumnMeta($column);

    public function setFetchMode($mode, $classNameObject = null, array $ctorarfg = []);

    public function nextRowset();

    public function closeCursor();

    public function debugDumpParams();
}
