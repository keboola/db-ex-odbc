<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Extractor\DbAdapter;

interface PdoInterface
{
    const ATTR_AUTOCOMMIT = 0;
    const ATTR_CASE = 8;
    const ATTR_CLIENT_VERSION = 5;
    const ATTR_CONNECTION_STATUS = 7;
    const ATTR_CURSOR = 10;
    const ATTR_CURSOR_NAME = 9;
    const ATTR_DEFAULT_FETCH_MODE = 19;
    const ATTR_DEFAULT_STR_PARAM = 21;
    const ATTR_DRIVER_NAME = 16;
    const ATTR_EMULATE_PREPARES = 20;
    const ATTR_ERRMODE = 3;
    const ATTR_FETCH_CATALOG_NAMES = 15;
    const ATTR_FETCH_TABLE_NAMES = 14;
    const ATTR_MAX_COLUMN_LEN = 18;
    const ATTR_ORACLE_NULLS = 11;
    const ATTR_PERSISTENT = 12;
    const ATTR_PREFETCH = 1;
    const ATTR_SERVER_INFO = 6;
    const ATTR_SERVER_VERSION = 4;
    const ATTR_STATEMENT_CLASS = 13;
    const ATTR_STRINGIFY_FETCHES = 17;
    const ATTR_TIMEOUT = 2;
    const CASE_LOWER = 2;
    const CASE_NATURAL = 0;
    const CASE_UPPER = 1;
    const CURSOR_FWDONLY = 0;
    const CURSOR_SCROLL = 1;
    const ERRMODE_EXCEPTION = 2;
    const ERRMODE_SILENT = 0;
    const ERRMODE_WARNING = 1;
    const ERR_NONE = 00000;
    const FETCH_ASSOC = 2;
    const FETCH_BOTH = 4;
    const FETCH_BOUND = 6;
    const FETCH_CLASS = 8;
    const FETCH_CLASSTYPE = 262144;
    const FETCH_COLUMN = 7;
    const FETCH_FUNC = 10;
    const FETCH_GROUP = 65536;
    const FETCH_INTO = 9;
    const FETCH_KEY_PAIR = 12;
    const FETCH_LAZY = 1;
    const FETCH_NAMED = 11;
    const FETCH_NUM = 3;
    const FETCH_OBJ = 5;
    const FETCH_ORI_ABS = 4;
    const FETCH_ORI_FIRST = 2;
    const FETCH_ORI_LAST = 3;
    const FETCH_ORI_NEXT = 0;
    const FETCH_ORI_PRIOR = 1;
    const FETCH_ORI_REL = 5;
    const FETCH_PROPS_LATE = 1048576;
    const FETCH_SERIALIZE = 524288;
    const FETCH_UNIQUE = 196608;
    const MYSQL_ATTR_COMPRESS = 1006;
    const MYSQL_ATTR_DIRECT_QUERY = 1007;
    const MYSQL_ATTR_FOUND_ROWS = 1008;
    const MYSQL_ATTR_IGNORE_SPACE = 1009;
    const MYSQL_ATTR_INIT_COMMAND = 1002;
    const MYSQL_ATTR_LOCAL_INFILE = 1001;
    const MYSQL_ATTR_MAX_BUFFER_SIZE = 1005;
    const MYSQL_ATTR_MULTI_STATEMENTS = 1015;
    const MYSQL_ATTR_READ_DEFAULT_FILE = 1003;
    const MYSQL_ATTR_READ_DEFAULT_GROUP = 1004;
    const MYSQL_ATTR_SSL_CA = 1012;
    const MYSQL_ATTR_SSL_CAPATH = 1013;
    const MYSQL_ATTR_SSL_CERT = 1011;
    const MYSQL_ATTR_SSL_CIPHER = 1014;
    const MYSQL_ATTR_SSL_KEY = 1010;
    const MYSQL_ATTR_SSL_VERIFY_SERVER_CERT = 1016;
    const MYSQL_ATTR_USE_BUFFERED_QUERY = 1000;
    const NULL_EMPTY_STRING = 1;
    const NULL_NATURAL = 0;
    const NULL_TO_STRING = 2;
    const PARAM_BOOL = 5;
    const PARAM_EVT_ALLOC = 0;
    const PARAM_EVT_EXEC_POST = 3;
    const PARAM_EVT_EXEC_PRE = 2;
    const PARAM_EVT_FETCH_POST = 5;
    const PARAM_EVT_FETCH_PRE = 4;
    const PARAM_EVT_FREE = 1;
    const PARAM_EVT_NORMALIZE = 6;
    const PARAM_INPUT_OUTPUT = 2147483648;
    const PARAM_INT = 1;
    const PARAM_LOB = 3;
    const PARAM_NULL = 0;
    const PARAM_STMT = 4;
    const PARAM_STR = 2;
    const PARAM_STR_CHAR = 536870912;
    const PARAM_STR_NATL = 1073741824;
    const PGSQL_ASSOC = 1;
    const PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT = 1000;
    const PGSQL_BAD_RESPONSE = 5;
    const PGSQL_BOTH = 3;
    const PGSQL_COMMAND_OK = 1;
    const PGSQL_CONNECTION_AUTH_OK = 5;
    const PGSQL_CONNECTION_AWAITING_RESPONSE = 4;
    const PGSQL_CONNECTION_BAD = 1;
    const PGSQL_CONNECTION_MADE = 3;
    const PGSQL_CONNECTION_OK = 0;
    const PGSQL_CONNECTION_SETENV = 6;
    const PGSQL_CONNECTION_SSL_STARTUP = 7;
    const PGSQL_CONNECTION_STARTED = 2;
    const PGSQL_CONNECT_ASYNC = 4;
    const PGSQL_CONNECT_FORCE_NEW = 2;
    const PGSQL_CONV_FORCE_NULL = 4;
    const PGSQL_CONV_IGNORE_DEFAULT = 2;
    const PGSQL_CONV_IGNORE_NOT_NULL = 8;
    const PGSQL_COPY_IN = 4;
    const PGSQL_COPY_OUT = 3;
    const PGSQL_DIAG_CONTEXT = 87;
    const PGSQL_DIAG_INTERNAL_POSITION = 112;
    const PGSQL_DIAG_INTERNAL_QUERY = 113;
    const PGSQL_DIAG_MESSAGE_DETAIL = 68;
    const PGSQL_DIAG_MESSAGE_HINT = 72;
    const PGSQL_DIAG_MESSAGE_PRIMARY = 77;
    const PGSQL_DIAG_SEVERITY = 83;
    const PGSQL_DIAG_SOURCE_FILE = 70;
    const PGSQL_DIAG_SOURCE_FUNCTION = 82;
    const PGSQL_DIAG_SOURCE_LINE = 76;
    const PGSQL_DIAG_SQLSTATE = 67;
    const PGSQL_DIAG_STATEMENT_POSITION = 80;
    const PGSQL_DML_ASYNC = 1024;
    const PGSQL_DML_ESCAPE = 4096;
    const PGSQL_DML_EXEC = 512;
    const PGSQL_DML_NO_CONV = 256;
    const PGSQL_DML_STRING = 2048;
    const PGSQL_EMPTY_QUERY = 0;
    const PGSQL_ERRORS_DEFAULT = 1;
    const PGSQL_ERRORS_TERSE = 0;
    const PGSQL_ERRORS_VERBOSE = 2;
    const PGSQL_FATAL_ERROR = 7;
    const PGSQL_NONFATAL_ERROR = 6;
    const PGSQL_NOTICE_ALL = 2;
    const PGSQL_NOTICE_CLEAR = 3;
    const PGSQL_NOTICE_LAST = 1;
    const PGSQL_NUM = 2;
    const PGSQL_POLLING_ACTIVE = 4;
    const PGSQL_POLLING_FAILED = 0;
    const PGSQL_POLLING_OK = 3;
    const PGSQL_POLLING_READING = 1;
    const PGSQL_POLLING_WRITING = 2;
    const PGSQL_SEEK_CUR = 1;
    const PGSQL_SEEK_END = 2;
    const PGSQL_SEEK_SET = 0;
    const PGSQL_STATUS_LONG = 1;
    const PGSQL_STATUS_STRING = 2;
    const PGSQL_TRANSACTION_ACTIVE = 1;
    const PGSQL_TRANSACTION_IDLE = 0;
    const PGSQL_TRANSACTION_INERROR = 3;
    const PGSQL_TRANSACTION_INTRANS = 2;
    const PGSQL_TRANSACTION_UNKNOWN = 4;
    const PGSQL_TUPLES_OK = 2;
    const SQLITE_ATTR_OPEN_FLAGS = 1000;
    const SQLITE_DETERMINISTIC = 2048;
    const SQLITE_OPEN_CREATE = 4;
    const SQLITE_OPEN_READONLY = 1;
    const SQLITE_OPEN_READWRITE = 2;
    const SQLSRV_ATTR_CLIENT_BUFFER_MAX_KB_SIZE = 1004;
    const SQLSRV_ATTR_CURSOR_SCROLL_TYPE = 1003;
    const SQLSRV_ATTR_DIRECT_QUERY = 1002;
    const SQLSRV_ATTR_ENCODING = 1000;
    const SQLSRV_ATTR_FETCHES_NUMERIC_TYPE = 1005;
    const SQLSRV_ATTR_QUERY_TIMEOUT = 1001;
    const SQLSRV_CURSOR_BUFFERED = 42;
    const SQLSRV_CURSOR_DYNAMIC = 2;
    const SQLSRV_CURSOR_KEYSET = 1;
    const SQLSRV_CURSOR_STATIC = 3;
    const SQLSRV_ENCODING_BINARY = 2;
    const SQLSRV_ENCODING_DEFAULT = 1;
    const SQLSRV_ENCODING_SYSTEM = 3;
    const SQLSRV_ENCODING_UTF8 = 65001;
    const SQLSRV_PARAM_OUT_DEFAULT_SIZE = -1;
    const SQLSRV_TXN_READ_COMMITTED = "READ_COMMITTED";
    const SQLSRV_TXN_READ_UNCOMMITTED = "READ_UNCOMMITTED";
    const SQLSRV_TXN_REPEATABLE_READ = "REPEATABLE_READ";
    const SQLSRV_TXN_SERIALIZABLE = "SERIALIZABLE";
    const SQLSRV_TXN_SNAPSHOT = "SNAPSHOT";

    public function __construct($dsn, $username = null, $passwd = null, $options = null);

    public function prepare($statement, array $driver_options = []);

    public function beginTransaction();

    public function commit();

    public function rollBack();

    public function inTransaction();

    public function setAttribute($attribute, $value);

    public function exec($statement);

    public function query($statement, $mode = 19, $arg3 = null, array $ctorargs = []);

    public function lastInsertId($name = null);

    public function errorCode();

    public function errorInfo();

    public function getAttribute($attribute);

    public function quote($string, $parameter_type = 2);
}

