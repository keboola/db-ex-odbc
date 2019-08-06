<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor\DbAdapter;

use phpDocumentor\Reflection\Types\Mixed_;
use Traversable;

interface PdoStatementInterface /*extends Traversable*/
{
    public function execute(
        $input_parameters = null
    ): bool;

    public function fetch($fetch_style = null, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0);

    public function bindParam(
        $parameter,
        &$variable,
        $data_type = \PDO::PARAM_STR,
        $length = null,
        $driver_options = null
    ): bool;

    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null): bool;

    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR): bool;

    public function rowCount(): int;

    public function fetchColumn($column_number = 0);

    public function fetchAll($fetch_style = null, $fetch_argument = null, array $ctor_args = []): array;

    public function fetchObject($class_name = "stdClass", array $ctor_args = []);

    public function errorCode(): string;

    public function errorInfo(): array;

    public function setAttribute($attribute, $value): bool;

    public function getAttribute($attribute);

    public function columnCount(): int;

    public function getColumnMeta($column): array;

    public function setFetchMode($mode, $classNameObject = null, array $ctorarfg = []): bool;

    public function nextRowset(): bool;

    public function closeCursor(): bool;

    public function debugDumpParams(): void;
}
