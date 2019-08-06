<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor\DbAdapter;

use phpDocumentor\Reflection\Types\Mixed_;

interface PdoInterface
{
    public const ATTR_AUTOCOMMIT = 0;
    public const ATTR_CASE = 8;
    public const ATTR_CLIENT_VERSION = 5;
    public const ATTR_CONNECTION_STATUS = 7;
    public const ATTR_CURSOR = 10;
    public const ATTR_CURSOR_NAME = 9;
    public const ATTR_DEFAULT_FETCH_MODE = 19;
    public const ATTR_DEFAULT_STR_PARAM = 21;
    public const ATTR_DRIVER_NAME = 16;
    public const ATTR_EMULATE_PREPARES = 20;
    public const ATTR_ERRMODE = 3;
    public const ATTR_FETCH_CATALOG_NAMES = 15;
    public const ATTR_FETCH_TABLE_NAMES = 14;
    public const ATTR_MAX_COLUMN_LEN = 18;
    public const ATTR_ORACLE_NULLS = 11;
    public const ATTR_PERSISTENT = 12;
    public const ATTR_PREFETCH = 1;
    public const ATTR_SERVER_INFO = 6;
    public const ATTR_SERVER_VERSION = 4;
    public const ATTR_STATEMENT_CLASS = 13;
    public const ATTR_STRINGIFY_FETCHES = 17;
    public const ATTR_TIMEOUT = 2;
    public const CASE_LOWER = 2;
    public const CASE_NATURAL = 0;
    public const CASE_UPPER = 1;
    public const CURSOR_FWDONLY = 0;
    public const CURSOR_SCROLL = 1;
    public const ERRMODE_EXCEPTION = 2;
    public const ERRMODE_SILENT = 0;
    public const ERRMODE_WARNING = 1;
    public const ERR_NONE = 00000;
    public const FETCH_ASSOC = 2;
    public const FETCH_BOTH = 4;
    public const FETCH_BOUND = 6;
    public const FETCH_CLASS = 8;
    public const FETCH_CLASSTYPE = 262144;
    public const FETCH_COLUMN = 7;
    public const FETCH_FUNC = 10;
    public const FETCH_GROUP = 65536;
    public const FETCH_INTO = 9;
    public const FETCH_KEY_PAIR = 12;
    public const FETCH_LAZY = 1;
    public const FETCH_NAMED = 11;
    public const FETCH_NUM = 3;
    public const FETCH_OBJ = 5;
    public const FETCH_ORI_ABS = 4;
    public const FETCH_ORI_FIRST = 2;
    public const FETCH_ORI_LAST = 3;
    public const FETCH_ORI_NEXT = 0;
    public const FETCH_ORI_PRIOR = 1;
    public const FETCH_ORI_REL = 5;
    public const FETCH_PROPS_LATE = 1048576;
    public const FETCH_SERIALIZE = 524288;
    public const FETCH_UNIQUE = 196608;
    public const MYSQL_ATTR_COMPRESS = 1006;
    public const MYSQL_ATTR_DIRECT_QUERY = 1007;
    public const MYSQL_ATTR_FOUND_ROWS = 1008;
    public const MYSQL_ATTR_IGNORE_SPACE = 1009;
    public const MYSQL_ATTR_INIT_COMMAND = 1002;
    public const MYSQL_ATTR_LOCAL_INFILE = 1001;
    public const MYSQL_ATTR_MAX_BUFFER_SIZE = 1005;
    public const MYSQL_ATTR_MULTI_STATEMENTS = 1015;
    public const MYSQL_ATTR_READ_DEFAULT_FILE = 1003;
    public const MYSQL_ATTR_READ_DEFAULT_GROUP = 1004;
    public const MYSQL_ATTR_SSL_CA = 1012;
    public const MYSQL_ATTR_SSL_CAPATH = 1013;
    public const MYSQL_ATTR_SSL_CERT = 1011;
    public const MYSQL_ATTR_SSL_CIPHER = 1014;
    public const MYSQL_ATTR_SSL_KEY = 1010;
    public const MYSQL_ATTR_SSL_VERIFY_SERVER_CERT = 1016;
    public const MYSQL_ATTR_USE_BUFFERED_QUERY = 1000;
    public const NULL_EMPTY_STRING = 1;
    public const NULL_NATURAL = 0;
    public const NULL_TO_STRING = 2;
    public const PARAM_BOOL = 5;
    public const PARAM_EVT_ALLOC = 0;
    public const PARAM_EVT_EXEC_POST = 3;
    public const PARAM_EVT_EXEC_PRE = 2;
    public const PARAM_EVT_FETCH_POST = 5;
    public const PARAM_EVT_FETCH_PRE = 4;
    public const PARAM_EVT_FREE = 1;
    public const PARAM_EVT_NORMALIZE = 6;
    public const PARAM_INPUT_OUTPUT = 2147483648;
    public const PARAM_INT = 1;
    public const PARAM_LOB = 3;
    public const PARAM_NULL = 0;
    public const PARAM_STMT = 4;
    public const PARAM_STR = 2;
    public const PARAM_STR_CHAR = 536870912;
    public const PARAM_STR_NATL = 1073741824;
    public const PGSQL_ASSOC = 1;
    public const PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT = 1000;
    public const PGSQL_BAD_RESPONSE = 5;
    public const PGSQL_BOTH = 3;
    public const PGSQL_COMMAND_OK = 1;
    public const PGSQL_CONNECTION_AUTH_OK = 5;
    public const PGSQL_CONNECTION_AWAITING_RESPONSE = 4;
    public const PGSQL_CONNECTION_BAD = 1;
    public const PGSQL_CONNECTION_MADE = 3;
    public const PGSQL_CONNECTION_OK = 0;
    public const PGSQL_CONNECTION_SETENV = 6;
    public const PGSQL_CONNECTION_SSL_STARTUP = 7;
    public const PGSQL_CONNECTION_STARTED = 2;
    public const PGSQL_CONNECT_ASYNC = 4;
    public const PGSQL_CONNECT_FORCE_NEW = 2;
    public const PGSQL_CONV_FORCE_NULL = 4;
    public const PGSQL_CONV_IGNORE_DEFAULT = 2;
    public const PGSQL_CONV_IGNORE_NOT_NULL = 8;
    public const PGSQL_COPY_IN = 4;
    public const PGSQL_COPY_OUT = 3;
    public const PGSQL_DIAG_CONTEXT = 87;
    public const PGSQL_DIAG_INTERNAL_POSITION = 112;
    public const PGSQL_DIAG_INTERNAL_QUERY = 113;
    public const PGSQL_DIAG_MESSAGE_DETAIL = 68;
    public const PGSQL_DIAG_MESSAGE_HINT = 72;
    public const PGSQL_DIAG_MESSAGE_PRIMARY = 77;
    public const PGSQL_DIAG_SEVERITY = 83;
    public const PGSQL_DIAG_SOURCE_FILE = 70;
    public const PGSQL_DIAG_SOURCE_FUNCTION = 82;
    public const PGSQL_DIAG_SOURCE_LINE = 76;
    public const PGSQL_DIAG_SQLSTATE = 67;
    public const PGSQL_DIAG_STATEMENT_POSITION = 80;
    public const PGSQL_DML_ASYNC = 1024;
    public const PGSQL_DML_ESCAPE = 4096;
    public const PGSQL_DML_EXEC = 512;
    public const PGSQL_DML_NO_CONV = 256;
    public const PGSQL_DML_STRING = 2048;
    public const PGSQL_EMPTY_QUERY = 0;
    public const PGSQL_ERRORS_DEFAULT = 1;
    public const PGSQL_ERRORS_TERSE = 0;
    public const PGSQL_ERRORS_VERBOSE = 2;
    public const PGSQL_FATAL_ERROR = 7;
    public const PGSQL_NONFATAL_ERROR = 6;
    public const PGSQL_NOTICE_ALL = 2;
    public const PGSQL_NOTICE_CLEAR = 3;
    public const PGSQL_NOTICE_LAST = 1;
    public const PGSQL_NUM = 2;
    public const PGSQL_POLLING_ACTIVE = 4;
    public const PGSQL_POLLING_FAILED = 0;
    public const PGSQL_POLLING_OK = 3;
    public const PGSQL_POLLING_READING = 1;
    public const PGSQL_POLLING_WRITING = 2;
    public const PGSQL_SEEK_CUR = 1;
    public const PGSQL_SEEK_END = 2;
    public const PGSQL_SEEK_SET = 0;
    public const PGSQL_STATUS_LONG = 1;
    public const PGSQL_STATUS_STRING = 2;
    public const PGSQL_TRANSACTION_ACTIVE = 1;
    public const PGSQL_TRANSACTION_IDLE = 0;
    public const PGSQL_TRANSACTION_INERROR = 3;
    public const PGSQL_TRANSACTION_INTRANS = 2;
    public const PGSQL_TRANSACTION_UNKNOWN = 4;
    public const PGSQL_TUPLES_OK = 2;
    public const SQLITE_ATTR_OPEN_FLAGS = 1000;
    public const SQLITE_DETERMINISTIC = 2048;
    public const SQLITE_OPEN_CREATE = 4;
    public const SQLITE_OPEN_READONLY = 1;
    public const SQLITE_OPEN_READWRITE = 2;
    public const SQLSRV_ATTR_CLIENT_BUFFER_MAX_KB_SIZE = 1004;
    public const SQLSRV_ATTR_CURSOR_SCROLL_TYPE = 1003;
    public const SQLSRV_ATTR_DIRECT_QUERY = 1002;
    public const SQLSRV_ATTR_ENCODING = 1000;
    public const SQLSRV_ATTR_FETCHES_NUMERIC_TYPE = 1005;
    public const SQLSRV_ATTR_QUERY_TIMEOUT = 1001;
    public const SQLSRV_CURSOR_BUFFERED = 42;
    public const SQLSRV_CURSOR_DYNAMIC = 2;
    public const SQLSRV_CURSOR_KEYSET = 1;
    public const SQLSRV_CURSOR_STATIC = 3;
    public const SQLSRV_ENCODING_BINARY = 2;
    public const SQLSRV_ENCODING_DEFAULT = 1;
    public const SQLSRV_ENCODING_SYSTEM = 3;
    public const SQLSRV_ENCODING_UTF8 = 65001;
    public const SQLSRV_PARAM_OUT_DEFAULT_SIZE = -1;
    public const SQLSRV_TXN_READ_COMMITTED = "READ_COMMITTED";
    public const SQLSRV_TXN_READ_UNCOMMITTED = "READ_UNCOMMITTED";
    public const SQLSRV_TXN_REPEATABLE_READ = "REPEATABLE_READ";
    public const SQLSRV_TXN_SERIALIZABLE = "SERIALIZABLE";
    public const SQLSRV_TXN_SNAPSHOT = "SNAPSHOT";

    public function __construct($dsn, $username = null, $passwd = null, $options = null);

    public function prepare($statement, array $driver_options = []): PdoStatementInterface;

    public function beginTransaction(): bool;

    public function commit(): bool;

    public function rollBack(): bool;

    public function inTransaction(): bool;

    public function setAttribute($attribute, $value): bool;

    public function exec($statement): void;

    public function query($statement, $mode = 19, $arg3 = null, array $ctorargs = []): PdoStatementInterface;

    public function lastInsertId($name = null): string;

    public function errorCode(): string;

    public function errorInfo(): array;

    public function getAttribute($attribute);

    public function quote($string, $parameter_type = 2): string;
}
