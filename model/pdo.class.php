<?php
/**
 * Database connection and query execution class.
 * This class provides methods for interacting with a MySQL database using PDO.
 * It includes features for error handling, prepared statements, and transaction management.
 *
 * This class is designed to be secure and efficient, preventing SQL injection. Supports multiple named instances for different database connections.
 *
 * @package Database
 * @version 1.3
 * @author Clarence M. Sarmiento
 * @license MIT License
 *
 * NEW UPDATES:
 * Version 1.3
 * - Added support for named instances to allow multiple database connections.
 *
 * Version 1.2
 * - Added hasDuplicate() method to check for duplicate entries.
 * - Proper parameter binding to prevent SQL injection.
 * - Improved error handling and logging.
 *
 * Version 1.1
 * - Improved error handling with custom exceptions.
 *
 * Version 1.0 (July 14, 2025)
 * - Initial release.
 *
 * FUNCTIONS:
 * * -> CRUD operations: executeInsert(), executeUpdate(), executeDelete()
 * - executeInsert($data, $table): Insert data into a table.
 * - executeUpdate($data, $table, $where, $params = array()): Update data in a table.
 * - executeDelete($table, $where, $params = array()): Delete data from a table.
 *
 * * -> Data retrieval: select(), queryUniqueObject(), queryUniqueValue(), countOf(), sumOf(), maxOf(), minOf()
 * - select($sql, $params = array()): Execute a SELECT query and return all results.
 * - queryUniqueObject($sql, $params = array()): Fetch a unique object from the database.
 * - queryUniqueValue($sql, $params = array()): Fetch a unique value from the database.
 * - countOf($table, $where = '', $params = array()): Count the number of rows in a table with optional conditions.
 * - sumOf($column, $table, $where = '', $params = array()): Get the sum of a column in a table with optional conditions.
 * - maxOf($column, $table, $where = '', $params = array()): Get the maximum value of a column in a table with optional conditions.
 * - minOf($column, $table, $where = '', $params = array()): Get the minimum value of a column in a table with optional conditions.
 *
 * * -> Query execution: query(), run(), queryGetJSON()
 * - query($sql, $params = array()): Execute a SQL query with optional parameters.
 * - run($sql, $params = array()): Execute a SQL command that does not return data.
 * - queryGetJSON($sql, $params = array()): Execute a query and return results in JSON format.
 *
 * * -> Result handling: fetchNextObject(), numRows(), lastInsertedId(), resetFetch()
 * - fetchNextObject($stmt = NULL): Fetch the next row as an object from the result set.
 * - numRows($stmt = NULL): Get the number of rows returned by the last query.
 * - lastInsertedId(): Get the last inserted ID from the database.
 * - resetFetch($stmt = NULL): Reset the fetch position for the result set.
 *
 * * -> Instance management:
 * - getInstance($alias = 'default', $config = null): Get a singleton instance of the DB class.
 * - getInstanceWithDB($alias, $dbname): Get a singleton instance with a specific database.
 *
 * * -> Error handling and configuration:
 * - enableErrorDisplay(): Enable error display for development.
 * - disableErrorDisplay(): Disable error display for production.
 * - isErrorDisplayEnabled(): Check if error display is enabled.
 * - setErrorDisplay($enabled): Set error display mode.
 *
 * * -> Utility functions:
 * - hasDuplicate($sql, $params = array(), $m = true): Check for duplicate entries in the database.
 */
declare(strict_types=1);

require_once 'DBException.php';

class DB
{
    private ?PDO $pdo = null;
    private static array $instances = [];
    private bool $showErrors = false;

    public int $affectedRows = 0;

    private float $mtStart;
    private int $nbQueries = 0;
    private ?PDOStatement $lastResult = null;

    private string $alias;

    /**
     * Constructor
     */
    private function __construct(array $config, string $alias = 'default')
    {
        $this->alias   = $alias;
        $this->mtStart = microtime(true);

        $this->showErrors = $config['showErrors'] ?? false;

        if (!isset($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbDatabase'])) {
            throw new DBException("Database configuration is incomplete.");
        }

        try {
            $dsn = "mysql:host={$config['dbHost']};dbname={$config['dbDatabase']};charset=utf8mb4";

            $this->pdo = new PDO(
                $dsn,
                $config['dbUser'],
                $config['dbPass'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false
                ]
            );
        } catch (PDOException $e) {
            $this->handleError($e);
            throw new DBException(
                $this->showErrors
                    ? "Connection failed: " . $e->getMessage()
                    : "Database connection error."
            );
        }
    }

    /**
     * Singleton instance
     */
    public static function getInstance(string $alias = 'default', ?array $config = null): DB
    {
        if (!isset(self::$instances[$alias])) {

            if (!$config) {
                if (!isset($GLOBALS['INF_CONFIG'])) {
                    throw new DBException("No database configuration found.");
                }
                $config = $GLOBALS['INF_CONFIG'];
            }

            self::$instances[$alias] = new DB($config, $alias);
        }
        return self::$instances[$alias];
    }

    /**
     * Get instance with overridden database name
     */
    public static function getInstanceWithDB(string $alias, string $dbname): DB
    {
        if (!isset($GLOBALS['INF_CONFIG'])) {
            throw new DBException("No global database config found.");
        }

        $config = $GLOBALS['INF_CONFIG'];
        $config['dbDatabase'] = $dbname;

        return self::getInstance($alias, $config);
    }

    /**
     * Error handling + log
     */
    private function handleError(Throwable $e): void
    {
        $logFile = __DIR__ . "/db_errors.log";

        $message = "[" . date("Y-m-d H:i:s") . "] "
                 . $e->getMessage() . PHP_EOL
                 . $e->getTraceAsString() . PHP_EOL . PHP_EOL;

        error_log($message, 3, $logFile);

        if ($this->showErrors) {
            error_log("DB ERROR: " . $e->getMessage());
        }
    }

    private function createErrorMessage(string $dev, string $prod = "A database error occurred."): string
    {
        return $this->showErrors ? $dev : $prod;
    }

    public function enableErrorDisplay(): void { $this->showErrors = true; }
    public function disableErrorDisplay(): void { $this->showErrors = false; }
    public function isErrorDisplayEnabled(): bool { return $this->showErrors; }

    /**
     * Validate SQL — PHP 8.4 safe
     */
    private function validateSQL(string $sql): string
    {
        if (stripos($sql, "DROP ") !== false ||
            stripos($sql, "TRUNCATE ") !== false ||
            stripos($sql, "--") !== false
        ) {
            throw new DBException("Dangerous SQL detected.");
        }
        return $sql;
    }

    private function validateTableName(string $table): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new DBException("Invalid table name.");
        }
        return $table;
    }

    private function validateColumnName(string $col): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
            throw new DBException("Invalid column name.");
        }
        return $col;
    }

    /**
     * Bind values with correct types (PHP 8 strict)
     */
    private function bindAllValues(PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            $type = match (true) {
                is_int($value)  => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                $value === null => PDO::PARAM_NULL,
                default         => PDO::PARAM_STR,
            };

            $stmt->bindValue(":" . $key, $value, $type);
        }
    }

    /**
     * Core query method
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $this->nbQueries++;

            $sql = $this->validateSQL(rtrim($sql, ";"));

            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();

            $this->lastResult = $stmt;
            $this->affectedRows = $stmt->rowCount();

            return $stmt;
        } catch (PDOException $e) {
            $this->handleError($e);
            throw new DBException(
                $this->createErrorMessage("Query failed: " . $e->getMessage())
            );
        }
    }

    public function select(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function run(string $sql, array $params = []): bool
    {
        $this->query($sql, $params);
        return true;
    }

    public function fetchNextObject(?PDOStatement $stmt = null): object|false
    {
        $stmt = $stmt ?? $this->lastResult;
        return $stmt?->fetchObject() ?: false;
    }

    public function numRows(?PDOStatement $stmt = null): int
    {
        return ($stmt ?? $this->lastResult)?->rowCount() ?? 0;
    }

    public function lastInsertedId(): string
    {
        return $this->pdo?->lastInsertId() ?? "0";
    }

    /**
     * JSON output
     */
    public function queryGetJSON(string $sql, array $params = []): string
    {
        $stmt = $this->query($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        return json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * UNIQUE VALUE
     */
    public function queryUniqueValue(string $sql, array $params = []): ?string
    {
        $sql .= " LIMIT 1";
        $stmt = $this->query($sql, $params);
        $val = $stmt->fetchColumn();
        return $val !== false ? strval($val) : null;
    }

    public function queryUniqueObject(string $sql, array $params = []): ?object
    {
        $sql .= " LIMIT 1";
        $stmt = $this->query($sql, $params);
        $obj = $stmt->fetchObject();
        return $obj ?: null;
    }

    /**
     * AGGREGATES: count, sum, max, min
     */
    public function countOf(string $table, string $where = "", array $params = []): int
    {
        $table = $this->validateTableName($table);
        $sql = "SELECT COUNT(*) FROM `$table`" . ($where ? " WHERE $where" : "");
        return (int) $this->queryUniqueValue($sql, $params);
    }

    public function sumOf(string $column, string $table, string $where = "", array $params = []): float
    {
        $column = $this->validateColumnName($column);
        $table  = $this->validateTableName($table);
        $sql = "SELECT SUM(`$column`) FROM `$table`" . ($where ? " WHERE $where" : "");
        return (float) $this->queryUniqueValue($sql, $params);
    }

    public function maxOf(string $column, string $table, string $where = "", array $params = []): float
    {
        $column = $this->validateColumnName($column);
        $table  = $this->validateTableName($table);
        $sql = "SELECT MAX(`$column`) FROM `$table`" . ($where ? " WHERE $where" : "");
        return (float) $this->queryUniqueValue($sql, $params);
    }

    public function minOf(string $column, string $table, string $where = "", array $params = []): float
    {
        $column = $this->validateColumnName($column);
        $table  = $this->validateTableName($table);
        $sql = "SELECT MIN(`$column`) FROM `$table`" . ($where ? " WHERE $where" : "");
        return (float) $this->queryUniqueValue($sql, $params);
    }

    /**
     * INSERT using array
     */
    public function executeInsert(array $data, string $table): bool
    {
        $table = $this->validateTableName($table);

        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ":$c", $columns);

        $sql = "INSERT INTO `$table` (`"
             . implode("`,`", $columns)
             . "`) VALUES ("
             . implode(",", $placeholders)
             . ")";

        return $this->run($sql, $data);
    }

    /**
     * UPDATE
     */
    public function executeUpdate(array $data, string $table, string $where, array $params = []): bool
    {
        $table = $this->validateTableName($table);

        $set = [];
        foreach ($data as $col => $val) {
            $this->validateColumnName($col);
            $set[] = "`$col` = :upd_$col";
        }

        $sql = "UPDATE `$table` SET " . implode(",", $set) . " WHERE $where";

        // rename parameters so they don’t conflict
        $merged = [];
        foreach ($data as $col => $val) {
            $merged["upd_$col"] = $val;
        }
        $merged += $params;

        return $this->run($sql, $merged);
    }

    /**
     * DELETE
     */
    public function executeDelete(string $table, string $where, array $params = []): bool
    {
        $table = $this->validateTableName($table);
        $sql = "DELETE FROM `$table` WHERE $where";
        return $this->run($sql, $params);
    }

    /**
     * DUPLICATE CHECK
     */
    public function hasDuplicate(string $sql, array $params = []): bool
    {
        $count = $this->queryUniqueValue($sql, $params);
        return !empty($count) && intval($count) > 0;
    }

    /**
     * Close connection
     */
    public function close(): void
    {
        $this->pdo = null;
        unset(self::$instances[$this->alias]);
    }
}
?>
