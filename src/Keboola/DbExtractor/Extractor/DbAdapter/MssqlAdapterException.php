<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor\DbAdapter;

use Exception;
use PDOException;
use Throwable;

class MssqlAdapterException extends PDOException
{
    public static function fromQueryAndPreviousException(string $query, Throwable $e)
    {
        return new self(sprintf('"%s" for "%s"', $e->getMessage(), $query), 0, $e);
    }

    public static function fromMessage(string $message)
    {
        return new self($message);
    }

    private function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
