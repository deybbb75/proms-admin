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
 * - Changed DB::$affectedRows() to $db->affectedRows property for better usability.
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
require_once 'DBException.php';

class DB
{
    private $pdo;
    private static $instances = [];     // Support multiple named instances
    private $showErrors       = false;  // Set to true for development, false for production

    /**
     * INTERNAL: The number of affected rows from the last query.
     */
    public $affectedRows;

    /**
     * INTERNAL: The start time, in miliseconds.
     */
    private $mtStart;

    /**
     * INTERNAL: The number of executed queries.
     */
    private $nbQueries;

    /**
     * INTERNAL: The last result resource of a query().
     */
    private $lastResult;

    /**
     * INTERNAL: The instance alias/name
     */
    private $alias;

    private function __construct($config, $alias = 'default')
    {
        $this->alias      = $alias;
        $this->mtStart    = $this->getMicroTime();
        $this->nbQueries  = 0;
        $this->lastResult = NULL;

        // Set error display mode from configuration (default: false for production)
        $this->showErrors = isset($config['showErrors']) ? (bool) $config['showErrors'] : false;

        if (!isset($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbDatabase'])) {
            throw new DBException('Database configuration is incomplete.');
        }

        try {
            $dsn       = "mysql:host={$config['dbHost']};dbname={$config['dbDatabase']};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $config['dbUser'], $config['dbPass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->showErrors
                ? 'Database connection error: ' . $e->getMessage()
                : 'Database connection error. Please check your configuration.';
            throw new DBException($errorMessage);
        }
    }

    public static function getInstance($alias = 'default', $config = null)
    {
        if (!isset(self::$instances[$alias])) {
            if ($config === null) {
                // Set fallback to global config if none provided
                if (!isset($GLOBALS['INF_CONFIG'])) {
                    throw new DBException('No database configuration found.');
                }
                $config = $GLOBALS['INF_CONFIG'];
            }
            self::$instances[$alias] = new DB($config, $alias);
        }
        return self::$instances[$alias];
    }

    public static function getInstanceWithDB($alias, $dbname)
    {
        if (!isset($GLOBALS['INF_CONFIG'])) {
            throw new DBException('No database configuration found.');
        }
        $config               = $GLOBALS['INF_CONFIG'];
        $config['dbDatabase'] = $dbname;  // Override database name
        return self::getInstance($alias, $config);
    }

    private function handleError(PDOException $e)
    {
        $logFile = __DIR__ . '/db_errors.log';
        $message = '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL
            . 'Trace: ' . $e->getTraceAsString() . PHP_EOL . PHP_EOL;

        // Always log errors to file
        error_log($message, 3, $logFile);

        // Only display detailed errors if showErrors is enabled (development mode)
        if ($this->showErrors) {
            // Development mode: Show detailed error information
            error_log('DB Error: ' . $e->getMessage());
            error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        }
    }

    /**
     * Create a user-friendly error message based on showErrors setting.
     * @param string $devMessage Detailed error message for development.
     * @param string $prodMessage Generic error message for production.
     * @return string The appropriate error message.
     */
    private function createErrorMessage($devMessage, $prodMessage = 'A database error occurred. Please try again.')
    {
        return $this->showErrors ? $devMessage : $prodMessage;
    }

    /**
     * Enable error display for development environments.
     * @return void
     */
    public function enableErrorDisplay()
    {
        $this->showErrors = true;
    }

    /**
     * Disable error display for production environments.
     * @return void
     */
    public function disableErrorDisplay()
    {
        $this->showErrors = false;
    }

    /**
     * Check if error display is enabled.
     * @return bool True if errors are displayed, false otherwise.
     */
    public function isErrorDisplayEnabled()
    {
        return $this->showErrors;
    }

    /**
     * Set error display mode.
     * @param bool $enabled True to enable error display, false to disable.
     * @return void
     */
    public function setErrorDisplay($enabled)
    {
        $this->showErrors = (bool) $enabled;
    }

    /**
     * Sanitize input data by trimming whitespace from strings.
     */
    public function sanitizeInput(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_resource($value))
                continue;
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }
        return $data;
    }

    /**
     * Get the PDO parameter type based on the value's type.
     * @param mixed $value The value to determine the parameter type for.
     * @return int The PDO parameter type constant.
     */
    private static function getPDOParamType($value)
    {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            case is_resource($value):
                return PDO::PARAM_LOB;
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * Bind all values in the given associative array to the prepared statement.
     * @param PDOStatement $stmt The prepared statement to bind values to.
     * @param array $params The associative array of parameters to bind.
     */
    private function bindAllValues(PDOStatement $stmt, array $params)
    {
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, self::getPDOParamType($value));
        }
    }

    /**
     * Get the current microtime in seconds.
     * @return float The current microtime.
     */
    private function getMicroTime()
    {
        list($msec, $sec) = explode(' ', microtime());
        return floor($sec / 1000) + $msec;
    }

    /**
     * Get the execution time of the database operations in seconds.
     * @return float The execution time in seconds.
     */
    public function getExecTime()
    {
        return round(($this->getMicroTime() - $this->mtStart) * 1000) / 1000;
    }

    /**
     * Get the number of queries executed since the last reset.
     * @return int The number of executed queries.
     */
    public function getQueriesCount()
    {
        return $this->nbQueries;
    }

    /**
     * Go back to the first element of the result line.
     * @param $result The resssource returned by a query() function.
     */
    public function resetFetch()
    {
        if ($this->lastResult instanceof PDOStatement) {
            $this->lastResult->execute();  // Reset the cursor to the beginning
        } else {
            $this->handleError(new PDOException('No valid result set to reset.'));
            throw new DBException('No valid result set to reset.');
        }
    }

    /**
     * Get the last inserted ID from the database.
     * @return string The last inserted ID.
     * @throws DBException If there is no database connection available.
     */
    public function lastInsertedId()
    {
        if ($this->pdo) {
            return $this->pdo->lastInsertId();
        } else {
            $this->handleError(new PDOException('No database connection available.'));
            throw new DBException('No database connection available.');
        }
    }

    /**
     * Close the database connection.
     * This method is called automatically when the script ends, but can be called manually if needed.
     */
    public function close($alias = 'default')
    {
        $this->pdo = null;                      // Close the connection
        unset(self::$instances[$this->alias]);  // Remove instance from the static array
    }

    /**
     * Query the database with a prepared statement.
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return PDOStatement The prepared statement object, to use with fetchNextObject().
     * @throws DBException If the query fails.
     */
    public function query($sql, array $params = array())
    {
        try {
            $this->nbQueries++;
            $params = $this->sanitizeInput($params);
            $sql    = $this->validateSQL($sql);
            $sql    = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt   = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            $this->affectedRows = $stmt->rowCount();
            $this->lastResult   = $stmt;
            return $stmt;
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Get all data from the database.
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array The result set as an associative array.
     * @throws DBException If the query fails.
     */
    public function select($sql, array $params = array())
    {
        try {
            $this->nbQueries++;
            $params = $this->sanitizeInput($params);
            $sql    = $this->validateSQL($sql);
            $sql    = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt   = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            $this->affectedRows = $stmt->rowCount();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Run a SQL command that does not return data (e.g., INSERT, UPDATE, DELETE).
     * @param string $sql The SQL command to execute.
     * @param array $params The parameters to bind to the command.
     * @return bool True on success, false on failure.
     * @throws DBException If the command fails.
     */
    public function run($sql, array $params = array())
    {
        try {
            $this->nbQueries++;
            $params = $this->sanitizeInput($params);
            $sql    = $this->validateSQL($sql);
            $sql    = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt   = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            $this->affectedRows = $stmt->rowCount();
            return true;
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Fetch the next row as an object from the given statement or the last result.
     * Useful for iterating results in a while loop.
     * @param PDOStatement|null $stmt The statement to fetch from, or null to use last result.
     * @return object|false The next row as an object, or false if no more rows.
     */
    public function fetchNextObject($stmt = NULL)
    {
        if ($stmt == NULL) {
            $stmt = $this->lastResult;
        }
        if ($stmt instanceof PDOStatement) {
            try {
                return $stmt->fetchObject();
            } catch (PDOException $e) {
                $this->handleError($e);
                return false;
            }
        }
        return false;
    }

    /**
     * Get the number of rows returned by the last query.
     * Note: For SELECT statements, this may not work reliably with MySQL.
     * Use COUNT(*) queries for accurate row counts with SELECT statements.
     * @param PDOStatement|null $stmt The statement to check, or null to use last result.
     * @return int The number of rows returned.
     */
    public function numRows($stmt = NULL)
    {
        if ($stmt == NULL) {
            $stmt = $this->lastResult;
        }
        if ($stmt instanceof PDOStatement) {
            try {
                return $stmt->rowCount();
            } catch (PDOException $e) {
                $this->handleError($e);
                return 0;
            }
        }
        return 0;
    }

    /**
     * Get data in JSON format.
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @throws DBException If the query fails.
     * @return string The JSON-encoded result set.
     */
    public function queryGetJSON($sql, $params = array())
    {
        try {
            $this->nbQueries++;
            $params = $this->sanitizeInput($params);
            $sql    = rtrim($sql, ';');  // Ensure no trailing semicolon
            $this->validateSQL($sql);
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            $data = array();
            while ($row = $this->fetchNextObject($stmt)) {
                $data[] = $row;
            }
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Fetch a unique object from the database.
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return object|null The fetched object, or null if not found.
     */
    public function queryUniqueObject($sql, array $params = array())
    {
        try {
            $params = $this->sanitizeInput($params);
            $sql    = $this->validateSQL($sql);
            $sql    = rtrim($sql, ';');  // Ensure no trailing semicolon
            if (strpos($sql, 'LIMIT') === false) {
                $sql .= ' LIMIT 1';      // Ensure we only fetch one result
            }
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            $result = $this->fetchNextObject($stmt);
            return $result ?: null;      // Return null if no result found
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Fetch a unique value from the database.
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return string|null The fetched value, or null if not found.
     */
    public function queryUniqueValue($sql, array $params = array())
    {
        try {
            $params = $this->sanitizeInput($params);
            $sql    = $this->validateSQL($sql);
            $sql    = rtrim($sql, ';');                          // Ensure no trailing semicolon
            if (strpos($sql, 'LIMIT') === false) {
                $sql .= ' LIMIT 1';                              // Ensure we only fetch one result
            }
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            $result = $stmt->fetchColumn();
            return $result !== false ? (string) $result : null;  // Return null if no result found
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Count the number of rows in a table with optional WHERE conditions.
     * @param string $table The name of the table to count rows from.
     * @param string $where Optional WHERE clause conditions.
     * @param array $params Optional associative array of parameters for the WHERE clause.
     * @return int The number of rows matching the conditions.
     * @throws DBException If the query fails.
     */
    public function countOf($table, $where = '', array $params = array())
    {
        $table  = $this->validateTableName($table);
        $params = $this->sanitizeInput($params);
        $sql    = "SELECT COUNT(*) FROM `$table`";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        try {
            $this->nbQueries++;
            $sql  = $this->validateSQL($sql);
            $sql  = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Get the sum of a column in a table with optional WHERE conditions.
     * @param string $table The name of the table to get the sum from.
     * @param string $column The name of the column to sum.
     * @param string $where Optional WHERE clause conditions.
     * @param array $params Optional associative array of parameters for the WHERE clause.
     * @return float The sum of the specified column.
     * @throws DBException If the query fails.
     */
    public function sumOf($column, $table, $where = '', array $params = array())
    {
        $column = $this->validateColumnName($column);
        $table  = $this->validateTableName($table);
        $params = $this->sanitizeInput($params);
        $sql    = "SELECT SUM(`$column`) FROM `$table`";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        try {
            $this->nbQueries++;
            $sql  = $this->validateSQL($sql);
            $sql  = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Get the sum of a column in a table with optional WHERE conditions.
     * @param string $table The name of the table to get the sum from.
     * @param string $column The name of the column to sum.
     * @param string $where Optional WHERE clause conditions.
     * @param array $params Optional associative array of parameters for the WHERE clause.
     * @return float The sum of the specified column.
     * @throws DBException If the query fails.
     */
    public function maxOf($column, $table, $where = '', array $params = array())
    {
        $column = $this->validateColumnName($column);
        $table  = $this->validateTableName($table);
        $params = $this->sanitizeInput($params);
        $sql    = "SELECT MAX(`$column`) FROM `$table`";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        try {
            $this->nbQueries++;
            $sql  = $this->validateSQL($sql);
            $sql  = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Get the minimum value of a column in a table with optional WHERE conditions.
     * @param string $table The name of the table to get the min value from.
     * @param string $column The name of the column to get the min value of.
     * @param string $where Optional WHERE clause conditions.
     * @param array $params Optional associative array of parameters for the WHERE clause.
     * @return float The minimum value of the specified column.
     * @throws DBException If the query fails.
     */
    public function minOf($column, $table, $where = '', array $params = array())
    {
        $column = $this->validateColumnName($column);
        $table  = $this->validateTableName($table);
        $params = $this->sanitizeInput($params);
        $sql    = "SELECT MIN(`$column`) FROM `$table`";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        try {
            $this->nbQueries++;
            $sql  = $this->validateSQL($sql);
            $sql  = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Insert data into a table.
     * @param array $data An associative array of column names and values to insert.
     * @param string $table The name of the table to insert into.
     * @return bool True on success, false on failure.
     * @throws DBException If the insert fails.
     */
    public function executeInsert(array $data, $table)
    {
        $table = $this->validateTableName($table);
        $this->validateColumnNames(array_keys($data));
        $data = $this->sanitizeInput($data);

        $columns      = implode('`, `', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql          = "INSERT INTO `$table` (`$columns`) VALUES ($placeholders)";

        try {
            $this->nbQueries++;
            $sql  = $this->validateSQL($sql);
            $sql  = rtrim($sql, ';');
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $data);  // Bind all values manually
            $stmt->execute();
            $this->affectedRows = $stmt->rowCount();
            return true;
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Update data in a table.
     * @param array $data An associative array of column names and values to update.
     * @param string $table The name of the table to update.
     * @param string $where The WHERE clause conditions for the update.
     * @param array $params Optional associative array of parameters for the WHERE clause.
     * @return bool True on success, false on failure.
     * @throws DBException If the update fails.
     */
    public function executeUpdate(array $data, $table, $where, array $params = array())
    {
        $table = $this->validateTableName($table);
        $this->validateColumnNames(array_keys($data));
        $this->validateColumnNames(array_keys($params));

        $data   = $this->sanitizeInput($data);
        $params = $this->sanitizeInput($params);

        $setParts = array();
        foreach ($data as $key => $value) {
            $setParts[]         = "`$key` = :set_$key";
            $params["set_$key"] = $value;
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE `$table` SET $setClause WHERE $where";

        try {
            $this->nbQueries++;
            $sql  = $this->validateSQL($sql);
            $sql  = rtrim($sql, ';');
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);  // Bind all values manually
            $stmt->execute();
            $this->affectedRows = $stmt->rowCount();
            return true;
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Delete data from a table.
     * @param string $table The name of the table to delete from.
     * @param string $where The WHERE clause conditions for the delete.
     * @param array $params Optional associative array of parameters for the WHERE clause.
     * @return bool True on success, false on failure.
     * @throws DBException If the delete fails.
     */
    public function executeDelete($table, $where, array $params = array())
    {
        $table  = $this->validateTableName($table);
        $params = $this->sanitizeInput($params);

        $sql = "DELETE FROM `$table` WHERE $where";

        try {
            $this->nbQueries++;
            $sql  = $this->validateSQL($sql);
            $sql  = rtrim($sql, ';');  // Ensure no trailing semicolon
            $stmt = $this->pdo->prepare($sql);
            $this->bindAllValues($stmt, $params);
            $stmt->execute();
            $this->affectedRows = $stmt->rowCount();
            return true;
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Database query error: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Check for duplicate entries in the database.
     * @param string $sql The SQL query to check for duplicates.
     * @param array $params The parameters to bind to the query.
     * @param bool $m Whether to use a specific method for checking duplicates.
     * @return bool True if duplicates are found, false otherwise.
     * @throws DBException If the query fails.
     */
    public function hasDuplicate($sql, array $params = array(), $m = true)
    {
        try {
            $data = $this->select($sql, $params);
            if (empty($data)) {
                return false;
            }
            return $this->getDuplicate($data, $params, $m);
        } catch (Exception $e) {
            $errorMessage = $this->createErrorMessage($e->getMessage());
            throw new DBException($errorMessage);
        }
    }

    /**
     * Validate table name to prevent SQL injection.
     * @param string $table The table name to validate.
     * @return string The validated table name.
     * @throws DBException If the table name is invalid.
     */
    private function validateTableName($table)
    {
        // Allow only alphanumeric characters and underscores
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            throw new DBException('Invalid table name: ' . $table);
        }
        return $table;
    }

    /**
     * Validate column name to prevent SQL injection.
     * @param string $column The column name to validate.
     * @return string The validated column name.
     * @throws DBException If the column name is invalid.
     */
    private function validateColumnName($column)
    {
        // Allow only alphanumeric characters and underscores
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            throw new DBException('Invalid column name: ' . $column);
        }
        return $column;
    }

    /**
     * Validate an array of column names to prevent SQL injection.
     * @param array $columns The column names to validate.
     * @return array The validated column names.
     * @throws DBException If any column name is invalid.
     */
    private function validateColumnNames(array $columns)
    {
        foreach ($columns as $column) {
            $this->validateColumnName($column);
        }
        return $columns;
    }

    /**
     * Validate SQL query for basic security.
     * @param string $sql The SQL query to validate.
     * @throws DBException If the SQL contains potentially dangerous content.
     */
    private function validateSQL($sql)
    {
        // Remove whitespace and convert to lowercase for validation
        $cleanSql = preg_replace('/\s+/', ' ', strtolower(trim($sql)));

        // Check for multiple statements (semicolon followed by non-whitespace)
        if (preg_match('/;\s*\w/', $cleanSql)) {
            throw new DBException('Invalid SQL query: Multiple statements not allowed');
        }

        // Check for dangerous SQL keywords after semicolon
        $dangerousPatterns = array(
            '/;\s*(drop|alter|create|truncate|delete|insert|update|replace|grant|revoke)/i',
            '/;\s*--/',
            '/;\s*\/\*/',
            '/union\s+select/i',
            "/'\s*;\s*\w+/i",
            '/\"\s*;\s*\w+/i'
        );

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $sql)) {
                throw new DBException('Invalid SQL query: Potentially dangerous content detected');
            }
        }

        return $sql;
    }

    /**
     * Begin a database transaction.
     * @return bool True on success, false on failure.
     * @throws DBException If the transaction cannot be started.
     */
    public function beginTransaction()
    {
        try {
            if ($this->pdo->inTransaction()) {
                throw new DBException('Transaction already in progress.');
            }
            return $this->pdo->beginTransaction();
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Failed to begin transaction: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Commit the current transaction.
     * @return bool True on success, false on failure.
     * @throws DBException If the transaction cannot be committed.
     */
    public function commit()
    {
        try {
            if (!$this->pdo->inTransaction()) {
                throw new DBException('No transaction in progress.');
            }
            return $this->pdo->commit();
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Failed to commit transaction: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Roll back the current transaction.
     * @return bool True on success, false on failure.
     * @throws DBException If the transaction cannot be rolled back.
     */
    public function rollback()
    {
        try {
            if (!$this->pdo->inTransaction()) {
                throw new DBException('No transaction in progress.');
            }
            return $this->pdo->rollback();
        } catch (PDOException $e) {
            $this->handleError($e);
            $errorMessage = $this->createErrorMessage(
                'Failed to rollback transaction: ' . $e->getMessage()
            );
            throw new DBException($errorMessage);
        }
    }

    /**
     * Check if a transaction is currently active.
     * @return bool True if a transaction is active, false otherwise.
     */
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Execute a function within a database transaction.
     * If the function throws an exception, the transaction is rolled back.
     * If the function completes successfully, the transaction is committed.
     *
     * @param callable $callback The function to execute within the transaction.
     * @return mixed The return value of the callback function.
     * @throws DBException If the transaction fails or the callback throws an exception.
     */
    public function transaction($callback)
    {
        if (!is_callable($callback)) {
            throw new DBException('Transaction callback must be callable.');
        }

        $this->beginTransaction();

        try {
            $result = call_user_func($callback, $this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    // ========================== HELPER FUNCTIONS ========================== //

    /**
     * Function to check for duplicate values in a database row against a POST array.
     * @param array $dbRows An array of database rows, where each row is an associative array.
     * @param array $post An array of values from a POST request.
     * @return string A message indicating whether duplicates were found and which values were duplicated.
     */
    private function getDuplicate($dbRows, $post, $m = true)
    {
        $duplicates = array();

        foreach ($dbRows as $record) {
            foreach ($post as $postValue) {
                foreach ($record as $dbValue) {
                    if (strcasecmp($dbValue, $postValue) === 0) {
                        $duplicates[] = $postValue;
                    }
                }
            }
        }

        $duplicates = array_unique($duplicates);

        // Format each value with bold
        $formatted = array();
        foreach ($duplicates as $val) {
            $formatted[] = "'<strong>" . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "</strong>'";
        }

        $count = count($formatted);

        if ($count === 1) {
            $text = $formatted[0];
        } elseif ($count === 2) {
            $text = $formatted[0] . ' and ' . $formatted[1];
        } else {
            $last = array_pop($formatted);
            $text = implode(', ', $formatted) . ' and ' . $last;
        }

        if ($m) {
            return "Found duplication for $text.";
        } else {
            return $duplicates;
        }
    }
}