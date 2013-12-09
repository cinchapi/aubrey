<?php

namespace org\cinchapi\aubrey\internal;

require_once \dirname(__FILE__) . '/../require.php';

/**
 * Contains static utilities that facilitate interactions with the underlying
 * database.
 */
class Database {

    /**
     * Database handler singleton
     * @var PDO
     */
    private static $PERSISTENT_DB_HANDLER;

    /**
     * Search database handler singleton
     * @var PDO
     */
    private static $PERSISTENT_SEARCH_DB_HANDLER;

    /**
     * Convert any empty strings to NULL for mysql. If the string is not empty, then
     * it will be returned unmodified
     * @param string $string
     * @return string
     */
    public static function convertEmptyStringToNull($string) {
        if (!isset($string)) {
            $string = "NULL";
        }
        return $string;
    }

    /**
     * Delete rows from a table that match a criteria.
     * @param string $table
     * @param string $criteria deletion criteria using SQL syntax
     * @param array $connectionInfo (optional) information about the connection
     * <pre>
     * array(
     * 		'host' => string
     * 		'name' => string
     * 		'user' => string
     * 		'pass' => string
     * );
     * </pre>
     * @throws DatabaseException 
     * @return number the number of deleted rows
     */
    public static function delete($table, $criteria, $connectionInfo = null) {
        $sql = "DELETE FROM $table WHERE $criteria";
        $dbh = empty($connectionInfo) ? self::getPersistentHandlerForDb() : self::getHandler($connectionInfo['host'],
                        $connectionInfo['name'], $connectionInfo['user'],
                        $connectionInfo['pass']);
        $count = $dbh->exec($sql);
        $error = $dbh->errorInfo();
        $code = $error[0];
        $message = $error[2];
        if ($code != "00000") {
            throw new DatabaseException("SQL Error $code: Attempted to execute $sql and the following error occured: $message");
        }
        return $count;
    }

    /**
     * Check to see if a database contains a certain table
     * @param string $tableName
     * @param array $connectionInfo (optional) information about the connection
     * <pre>
     * array(
     * 		'host' => string
     * 		'name' => string
     * 		'user' => string
     * 		'pass' => string
     * );
     * </pre>
     * By default, the values listed in prefs.php are used	 
     * @return boolean
     */
    public static function existsTable($tableName, $connectionInfo = null) {
        $dbh = empty($connectionInfo) ? self::getPersistentHandlerForDb() : self::getHandler($connectionInfo['host'],
                        $connectionInfo['name'], $connectionInfo['user'],
                        $connectionInfo['pass']);
        $sql = "SELECT COUNT(*) as count FROM $tableName";
        $dbh->query($sql);
        $error = $dbh->errorInfo();
        $code = $error[0];
        return $code == "00000";
    }

    /**
     * Get the default connection information 
     * @return array
     */
    private static function getDefaultConnectionInfo() {
        $connectionInfo = array('host' => DB_HOST, 'name' => DB_NAME, 'user' => DB_USER,
            'pass' => DB_PASS);
        return $connectionInfo;
    }

    /**
     * Get the database handler
     * @param string $dbhost
     * @param string $dbname
     * @param string $dbuser
     * @param string $dbpass
     * @throws DatabaseException
     * @return PDO
     */
    public static function getHandler($dbhost = DB_HOST, $dbname = DB_NAME,
            $dbuser = DB_USER, $dbpass = DB_PASS) {
        try {
            $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser,
                    $dbpass, array(PDO::ATTR_PERSISTENT => true));
            mysql_pconnect($dbhost, $dbuser, $dbpass); //this call is necessary to make mysql_real_escape_string() work
            return $dbh;
        }
        catch (Exception $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * Return a persistent handler to the database
     * @return PDO
     * @since 1.1.1
     */
    public static function getPersistentHandlerForDb() {
        self::$PERSISTENT_DB_HANDLER = empty(self::$PERSISTENT_DB_HANDLER) ? self::getHandler(DB_HOST,
                        DB_NAME, DB_USER, DB_PASS) : self::$PERSISTENT_DB_HANDLER;
        return self::$PERSISTENT_DB_HANDLER;
    }

    /**
     * Return a persistent handler to the search database
     * @return PDO
     * @since 1.1.1
     */
    public static function getPersistentHandlerForSearchDb() {
        self::$PERSISTENT_SEARCH_DB_HANDLER = empty(self::$PERSISTENT_SEARCH_DB_HANDLER)
                    ? self::getHandler(SEARCH_DB_HOST, SEARCH_DB_NAME,
                        SEARCH_DB_USER, SEARCH_DB_PASS) : self::$PERSISTENT_SEARCH_DB_HANDLER;
        return self::$PERSISTENT_SEARCH_DB_HANDLER;
    }

    /**
     * Insert one or more rows into a table using a prepared statement for optimal performance.
     * @param string $table
     * @param mixed $columns single column, comma separated list of columns or array of columns [i.e. array(column1, column2, column3)]
     * @param array $rows 2d array of rows to insert. Each index of the array should contain another array that describes the row
     * <pre>
     * array(
     * 		array(value1, value2, value3), 
     * 		array(value1,value2,value3
     * );
     * </pre>
     * @param array $connectionInfo (optional) information about the connection
     * <pre>
     * array(
     * 		'host' => string
     * 		'name' => string
     * 		'user' => string
     * 		'pass' => string
     * );
     * </pre>
     * By default, the values listd in prefs.php are used
     * @throws DatabaseException
     * @return number
     */
    public static function insert($table, $columns, $rows, $connectionInfo = null) {
        if (!is_array($columns)) {
            $columns = str_replace(", ", ",", $columns);
            $columns = array_filter(explode(",", $columns));
        }
        $quesMarks = array();
        foreach ($columns as $column) {
            $quesMarks[] = "?";
        }
        $quesMarks = implode(", ", $quesMarks);
        $columns = implode(",", $columns);
        $sql = "INSERT INTO $table ($columns) VALUES ($quesMarks)";
        $dbh = empty($connectionInfo) ? self::getPersistentHandlerForDb() : self::getHandler($connectionInfo['host'],
                        $connectionInfo['name'], $connectionInfo['user'],
                        $connectionInfo['pass']);
        $stmt = $dbh->prepare($sql);
        $count = 0;
        foreach ($rows as $row) {
            $row = array_map("self::convertEmptyStringToNull", $row);
            $stmt->execute($row);
            $error = $stmt->errorInfo();
            $code = $error[0];
            $message = $error[2];
            if ($code != "00000") {
                $row = implode(",", $row);
                throw new DatabaseException("SQL Error $code: Attempted to insert ($row) into ($columns) of $table using: $sql and the following error occured: $message",
                $code);
            }
            $count++;
        }
        return $count;
    }

    /**
     * Select rows from a table that match a criteria.
     * @param string $columns comma separated list of columns to select
     * @param string $table
     * @param string $criteria selection criteria using SQL syntax
     * @param boolean $fetchFirstRowOnly (optional) flag to indicate that only the first row from a result set should be fetched
     * @param array $connectionInfo (optional) information about the connection
     * <pre>
     * array(
     * 		'host' => string
     * 		'name' => string
     * 		'user' => string
     * 		'pass' => string
     * );
     * </pre>
     * @throws DatabaseException
     * @return array array of selected rows
     */
    public static function select($columns, $table, $criteria,
            $fetchFirstRowOnly = false, $connectionInfo = null) {
        if (empty($criteria)) {
            $criteria = " 1=1";
        }
        $sql = "SELECT $columns FROM $table WHERE $criteria";
        $dbh = empty($connctionInfo) ? self::getPersistentHandlerForDb() : self::getHandler($connectionInfo['host'],
                        $connectionInfo['name'], $connectionInfo['user'],
                        $connectionInfo['pass']);
        $result = $dbh->query($sql);
        $error = $dbh->errorInfo();
        $code = $error[0];
        $message = $error[2];
        if ($code != "00000") {
            throw new DatabaseException("SQL Error $code: Attempted to select $columns from $table  using: $sql and the following error occured: $message");
        }
        $rows = $fetchFirstRowOnly ? $result->fetch(PDO::FETCH_ASSOC) : $result->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * Update rows in table that match a criteria
     * @param string $table
     * @param array $data the updated data. Should be an assoc array mapping column names to the new  values
     * @param string $criteria update criteria using SQL syntax
     * @param array $connectionInfo (optional) information about the connection
     * <pre>
     * array(
     * 		'host' => string
     * 		'name' => string
     * 		'user' => string
     * 		'pass' => string
     * );
     * </pre>
     * @throws DatabaseException
     * @return number the number of affected rows
     */
    public static function update($table, $data, $criteria, $connectionInfo = null) {
        if (empty($criteria)) {
            $criteria = "1=1";
        }
        $sql = "UPDATE $table SET";
        $first = true;
        foreach ($data as $column => $value) {
            if (!$first) {
                $sql.=", ";
            }
            $value = self::convertEmptyStringToNull($value);
            $sql.= " $column = '$value'";
            $first = false;
        }
        $sql.=" WHERE $criteria";
        $dbh = empty($connectionInfo) ? self::getPersistentHandlerForDb() : self::getHandler($connectionInfo['host'],
                        $connectionInfo['name'], $connectionInfo['user'],
                        $connectionInfo['pass']);
        $count = $dbh->exec($sql);
        $error = $dbh->errorInfo();
        $code = $error[0];
        $message = $error[2];
        if ($code != "00000") {
            throw new DatabaseException("SQL Error $code: Attempted to update $table using: $sql and the following error occured: $message");
        }
        return $count;
    }

}

?>
