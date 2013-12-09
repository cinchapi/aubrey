<?php

namespace org\cinchapi\aubrey;

require_once \dirname(__FILE__) . '/require.php';

/**
 * The interface to the Aubrey data store. Aubrey is a database wrapper based on
 * the entity-attribute-value with classes and relationships (EAV/CR) model. Aubrey
 * is backed by MySQL. Aubrey is designed to enable rapid development of applications
 * that have sparse and heterogenous data, large numbers of classes and classes instances,
 * and/or have dynamic ontologies.
 */
class Aubrey {
    /**
     * Identifier used in the load*() functions to load all the object's properties
     */

    const LOAD_ALL_OBJECT_PROPERTIES = "*";

    /**
     * Value that indicates object resolution should combine similar properties
     */
    const OBJECT_COPY_COMBINE = 1;

    /**
     * Value that indicates object resolution should overwrite similar properties
     */
    const OBJECT_COPY_OVERWRITE = 2;

    /**
     * Prefix for all object cache keys
     */
    private static $_CACHE_KEY_PREFIX = "koncourse_object_";

    /**
     * Prefix for all accepted value type cache keys
     */
    private static $_CACHE_KEY_PREFIX_ACCEPTED_VALUE_TYPE = "avt-for-";

    /**
     * Name of table that holds all the generated ids
     */
    private static $_SCHEMA_IDS_TABLE = "koncourse_ids";

    /**
     * Name of the column that contains the generated ids on the $_SCHEMA_IDS_TABLE
     */
    private static $_SCHEMA_IDS_TABLE_ID_COLUMN = "id";

    /**
     * Name of table that holds all the Koncourse_Object data
     */
    private static $_SCHEMA_DATA_TABLE = "koncourse_data";

    /**
     * Name of column that contains the object reference on the $_SCHEMA_DATA_TABLE
     */
    private static $_SCHEMA_DATA_TABLE_OBJECT_COLUMN = "object";

    /**
     * Name of column that contains properties on the $_SCHEMA_DATA_TABLE
     */
    private static $_SCHEMA_DATA_TABLE_PROPERTY_COLUMN = "property";

    /**
     * Name of column that contains property values on the $_SCHEMA_DATA_TABLE
     */
    private static $_SCHEMA_DATA_TABLE_VALUE_COLUMN = "value";

    /**
     * Name of column that contains property value types on the $_SCHEMA_DATA_TABLE
     */
    private static $_SCHEMA_DATA_TABLE_VALUE_TYPE_COLUMN = "value_type";

    /**
     * Name of column that contains property value hashes on the $_SCHEMA_DATA_TABLE
     */
    private static $_SCHEMA_DATA_TABLE_VALUE_HASH_COLUMN = "value_hash";

    /**
     * Name of column that contains the added timestamp on the $_SCHEMA_DATA_TABLE
     */
    private static $_SCHEMA_DATA_TABLE_ADDED_COLUMN = "added";

    /**
     * Name of table that holds all the Koncourse_Object data revisions
     */
    private static $_SCHEMA_DATA_REVISIONS_TABLE = "koncourse_data_revisions";

    /**
     * Name of column that contains the object reference on the $_SCHEMA_DATA_REVISIONS_TABLE
     */
    private static $_SCHEMA_DATA_REVISIONS_TABLE_OBJECT_COLUMN = "object";

    /**
     * Name of column that contains the revised property on the $_SCHEMA_DATA_REVISIONS_TABLE
     */
    private static $_SCHEMA_DATA_REVISIONS_TABLE_PROPERTY_COLUMN = "property";

    /**
     * Name of column that contains the revision type on the $_SCHEMA_DATA_REVISIONS_TABLE
     */
    private static $_SCHEMA_DATA_REVISIONS_TABLE_REVISION_TYPE_COLUMN = "revision_type";

    /**
     * Name of column that contains the revision timestamp on the $_SCHEMA_DATA_REVISIONS_TABLE
     */
    private static $_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN = "time";

    /**
     * Name of column that contains the revised value on the $_SCHEMA_DATA_REVISIONS_TABLE
     */
    private static $_SCHEMA_DATA_REVISIONS_TABLE_VALUE_COLUMN = "value";

    /**
     * Name of the index for an index table's index index
     */
    private static $_SCHEMA_INDEX_TABLE_INDEX_INDEX_NAME = "index";

    /**
     * Name of the column that contains an index table's indexed objects
     */
    private static $_SCHEMA_INDEX_TABLE_OBJECT_COLUMN = "object";

    /**
     * Name of the column that contains an index table's indexed properties
     */
    private static $_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN = "property";

    /**
     * Name of the index for an index table's property index
     */
    private static $_SCHEMA_INDEX_TABLE_PROPERTY_INDEX_NAME = "property";

    /**
     * Name of the column that contains an index table's index strings
     */
    private static $_SCHEMA_INDEX_TABLE_STRING_COLUMN = "hash";

    /**
     * Name of the index for an index table's string index
     */
    private static $_SCHEMA_INDEX_TABLE_STRING_INDEX_NAME = "hash";

    /**
     * Name of table that holds all the Koncourse_Objects
     */
    private static $_SCHEMA_OBJECTS_TABLE = "koncourse_objects";

    /**
     * Name of column that contains the identifier on the $_SCHEMA_OBJECTS_TABLE
     */
    private static $_SCHEMA_OBJECTS_TABLE_ID_COLUMN = "id";

    /**
     * Name of column that contains the classifier on the $_SCHEMA_OBJECTS_TABLE
     */
    private static $_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN = "class";

    /**
     * Name of column that contains the created timestamp on the $_SCHEMA_OBJECTS_TABLE
     */
    private static $_SCHEMA_OBJECTS_TABLE_CREATED_COLUMN = "created";

    /**
     * Name of column that contains the last_cached timestamp on the $_SCHEMA_OBJECTS_TABLE
     */
    private static $_SCHEMA_OBJECTS_TABLE_LAST_CACHED_COLUMN = "last_cached";

    /**
     * Name of column that contains the last_updated timestamp on the $_SCHEMA_OBJECTS_TABLE
     */
    private static $_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN = "last_revised";

    /**
     * Name of column that contains the track_revisions instruction on the $_SCHEMA_OBJECTS_TABLE
     */
    private static $_SCHEMA_OBJECTS_TABLE_TRACK_REVISIONS_COLUMN = "track_revisions";

    /**
     * Operators that may be used in a selection criteria
     * a != b
     * a >= b
     * a <= b
     * a = b
     * a > b
     * a < b
     * a LIKE b
     * a RANGE b c
     */
    private static $_SELECTION_CRITERIA_OPERATORS = array('!=', '>=', '<=', '=',
        '>', '<', 'LIKE', 'RANGE');

    /**
     * Array mapping human readable intrinsic property identifiers to their corresponding system values
     */
    public static $INTRINSIC_PROPERTIES =
            array('id'    => "id", 'class' => "class");

    /**
     * Command to generate a random unique id
     */
    private static $RANDOM_UNIQUE_ID_GENERATION_COMMAND = "od -An -N4 -i /dev/urandom";

    /**
     * Value that indicates an additive revision
     */
    private static $REVISION_TYPE_ADDITION = "+";

    /**
     * Value that indicates an minus revision
     */
    private static $REVISION_TYPE_MINUS = "-";

    /**
     * Value that indicates an object should NOT have its revisions tracked
     */
    private static $TRACK_REVISIONS_FALSE = -1;

    /**
     * Value that indicates an object should have its revisions tracked
     */
    private static $TRACK_REVISIONS_TRUE = 1;

    /**
     * Array mapping human readable type identifiers to their corresponding system values
     */
    public static $VALUE_TYPES =
            array('integer'  => "INTEGER", "float"    => "FLOAT", "string"   => "STRING",
        "datetime" => "DATETIME", "boolean"  => "BOOLEAN");

    /**
     * Append a new value to a property on an object.
     * <strong>NOTE:</strong> This function is useful for adding/modifying properties that are meant to be multi valued<br />
     * <strong>NOTE:</strong> The db schema constrains objects such that duplicate values CANNOT be added on an object on the same property.
     * @param int $object
     * @param string $property
     * @param mixed $value
     * @param boolean $index (optional) flag to indicate that the property should be indexed after appending the value
     * @return boolean
     * @since 1.1.0
     */
    public static function addPropertyValue($object, $property, $value,
            $index = false) {
        $property = self::formatPropertyName($property);
        if (!in_array($property, self::$INTRINSIC_PROPERTIES) && !empty($property)) {
            if (self::isConsistentValueTypeForPropertyOnObject($value,
                            $property, $object)) {
                try {
                    if (self::insertPropertyValue($object, $property, $value) == 1) {
                        if ($index) self::indexProperty($object, $property);
                        return self::shouldTrackRevisions($object) ? self::trackRevision($object,
                                        $property, $value,
                                        self::$REVISION_TYPE_ADDITION) : true;
                    }
                    else {
                        return false;
                    }
                }
                catch (internal\DatabaseException $e) {
                    return false;
                }
            }
            else {
                throw new \InvalidArgumentException($value,
                "'$value' does not have the appropriate value type for $property");
            }
        }
        else {
            throw new \InvalidArgumentException($property,
            "$property is not a valid property name");
        }
    }

    /**
     * Begin a transaction. Changes issued within a transaction remain pending and can be rolled back until they are committed . 
     * Call Koncourse::commitTransaction() to finalize the changes.
     * @return boolean
     * @since 1.1.1
     */
    public static function beginTransaction() {
        return internal\Database::getPersistentHandlerForDb()->beginTransaction();
    }

    /**
     * Breakdown a valid selection criteria using the Shunting-yard algorithin
     * @see http://en.wikipedia.org/wiki/Shunting-yard_algorithm
     * @param string $criteria
     * @return array $criteria in reverse polish notation {@link http://en.wikipedia.org/wiki/Reverse_Polish_notation}
     * @since 1.1.0
     */
    private static function breakdownSelectionCriteria($criteria) {
        $rangeSeparator = "*\*";
        $spaceSeperator = "++\++";
        $_criteria = $criteria;
        $stack = array();
        $queue = array();
        $criteria = str_replace("(", " ( ", $criteria);
        $criteria = str_replace(")", " ) ", $criteria);
        $criteria = preg_replace("/(range) ([0-9]+) ([0-9]+)/i",
                "$1$2$rangeSeparator$3", $criteria);
        $criteria = preg_replace("/'([a-zA-Z0-9]+)([\s])+([a-zA-Z0-9]+)'[\s]*/i",
                "$1$spaceSeperator$3", $criteria);
        println($criteria);
        foreach (self::$_SELECTION_CRITERIA_OPERATORS as $operator) {
            $criteria = str_ireplace(" $operator", $operator, $criteria);
            $criteria = str_ireplace("$operator ", $operator, $criteria);
        }
        $tokens = array_filter(explode(" ", $criteria));
        foreach ($tokens as $token) {
            $token = str_replace($rangeSeparator, " ", $token);
            $token = str_replace($spaceSeperator, " ", $token);
            if (strcasecmp($token, "and") == 0 || strcasecmp($token, "or") == 0) {
                while (!empty($stack)) {
                    $topOfStack = $topOfStack = $stack[count($stack) - 1];
                    if (strcasecmp($token, "or") == 0 && (strcasecmp($topOfStack,
                                    "or") == 0 || strcasecmp($topOfStack, "and")
                            == 0)) {
                        $queue[] = array_pop($stack);
                    }
                    else {
                        break;
                    }
                }
                array_push($stack, $token);
            }
            else if (strcasecmp($token, "(") == 0) {
                array_push($stack, $token);
            }
            else if (strcasecmp($token, ")") == 0) {
                $foundLeftParen = false;
                while (!empty($stack)) {
                    $topOfStack = $stack[count($stack) - 1];
                    if (strcasecmp($topOfStack, "(") == 0) {
                        $foundLeftParen = true;
                        break;
                    }
                    else {
                        $queue[] = array_pop($stack);
                    }
                }
                if (!$foundLeftParen) {
                    throw new \Exception("Syntax error in criteria $_criteria. Mismatched parenthesis");
                }
                array_pop($stack);
            }
            else {
                $queue[] = $token;
            }
        }
        while (!empty($stack)) {
            $topOfStack = $stack[count($stack) - 1];
            if (strcasecmp($topOfStack, ")") == 0 || strcasecmp($topOfStack, "(")
                    == 0) {
                throw new \Exception("Syntax error in criteria $_criteria. Mismatched parenthesis");
            }
            $queue[] = array_pop($stack);
        }
        return $queue;
    }

    /**
     * Commit a transaction and finalize any changes made therein.
     * @return boolean
     * @since 1.1.1
     */
    public static function commitTransaction() {
        return internal\Database::getPersistentHandlerForDb()->commit();
    }

    /**
     * Copy the $source object to the $target object. $source and $target must be of the same class. 
     * For any similar properties shared between the two, $source and $target should have the same value
     * type(s) for similar properties, or the operation will fail. The copy operation is wrapped in a transaction, so a failed copy will
     * return data back to the original state
     * @param int $source source object
     * @param int $target target object
     * @param boolean $deleteSource (optional) flag to indicate that the $source object should be deleted after it is copied
     * @param int $mode (optional) Koncourse::$OBJECT_COPY_COMBINE (default) || Koncourse::$OBJECT_COPY_OVERWRITE. In combine mode,
     * similar properties between $source and $target are combined. In overwrite mode, the value(s) of the property on $source overwrites
     * a similar property on $target
     * @return boolean
     */
    public static function copy($source, $target, $deleteSource = false,
            $mode = self::OBJECT_COPY_COMBINE) {
        if (self::load($source, "class") != self::load($target, "class")) {
            return false;
        }
        self::beginTransaction();
        try {
            $sourceData = self::load($source);
            foreach ($sourceData as $property => $value) {
                if ($property == self::formatPropertyName("class")) {
                    continue;
                }
                if ($mode == self::OBJECT_COPY_OVERWRITE) {
                    self::removePropertyValue($target, $property);
                }
                $value = !is_array($value) ? array($value) : $value;
                foreach ($value as $val) {
                    self::addPropertyValue($target, $property, $val);
                }
            }
            if ($deleteSource) {
                self::delete($source);
            }
            self::commitTransaction();
            return true;
        }
        catch (\Exception $e) {
            self::rollbackTransaction();
        }
    }

    /**
     * Create a new Koncourse_Object of the specified $class
     * @param string $class
     * @param boolean $trackRevisions (optional) flag to indicate that the object should have its revisions tracked
     * @return int the id of the object
     * @since 1.1.0
     */
    public static function create($class, $trackRevisions = false) {
        $class = self::formatClassName($class);
        if (!in_array($class, array_values(self::$VALUE_TYPES)) && !empty($class)) {
            $object = self::generateRandomUniqueId();
            $created = util\Time::now();
            $trackRevisions = $trackRevisions ? self::$TRACK_REVISIONS_TRUE : self::$TRACK_REVISIONS_FALSE;
            $count = internal\Database::insert(self::$_SCHEMA_OBJECTS_TABLE,
                            "" . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . ","
                            . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN . "," . self::$_SCHEMA_OBJECTS_TABLE_CREATED_COLUMN . "" . ","
                            . self::$_SCHEMA_OBJECTS_TABLE_LAST_CACHED_COLUMN . "" . "," . self::$_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN . ","
                            . self::$_SCHEMA_OBJECTS_TABLE_TRACK_REVISIONS_COLUMN . "",
                            array(array($object, $class, $created, 0, $created, $trackRevisions)));
            assert($count == 1);
            return $object;
        }
        else {
            throw new \InvalidArgumentException($class,
            "$class is not a valid class name");
        }
    }

    /**
     * Create an index table for a class
     * @param string $class
     * @return boolean
     * @since 1.1.0
     */
    private static function createIndexTable($class) {
        $table = self::generateIndexTableName($class);
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (`" . self::$_SCHEMA_INDEX_TABLE_STRING_COLUMN . "` VARCHAR (32) NOT NULL,
				`" . self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN . "` INT (20) NOT NULL, `" . self::$_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN .
                "` VARCHAR (100) NOT NULL, UNIQUE KEY `" . self::$_SCHEMA_INDEX_TABLE_INDEX_INDEX_NAME . "` (`" . self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN .
                "`,`" . self::$_SCHEMA_INDEX_TABLE_STRING_COLUMN . "`,`" . self::$_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN . "`), KEY `" .
                self::$_SCHEMA_INDEX_TABLE_STRING_INDEX_NAME . "` (`" . self::$_SCHEMA_INDEX_TABLE_STRING_COLUMN . "`), KEY `" .
                self::$_SCHEMA_INDEX_TABLE_PROPERTY_INDEX_NAME . "` (`" . self::$_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN . "`)) ENGINE = INNODB;";
        $db = internal\Database::getPersistentHandlerForSearchDb();
        return $db->exec($sql);
    }

    /**
     * Delete an object
     * @param int $object
     * @param string $deleteOnlyIfObjectIsA (optional) specify a class that the object must belong to in order to be deleted. This is useful
     * for child classes that override this method and want to make sure that the id that is passed in actually belongs to an object
     * of the class
     * @return boolean
     * @since 1.1.0
     */
    public static function delete($object, $deleteOnlyIfObjectIsA = null) {
        if (!is_null($deleteOnlyIfObjectIsA) && !self::exists($object,
                        $deleteOnlyIfObjectIsA)) {
            throw new \Exception("Cannot delete object with $object because it does not belong to the $deleteOnlyIfObjectIsA class");
        }
        else {
            $class = self::load($object, "class");
            $count = internal\Database::delete(self::$_SCHEMA_OBJECTS_TABLE,
                            "" . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object");
            assert($count < 2);
            if ($count == 1) {
                $cacheKey = self::$_CACHE_KEY_PREFIX . $object;
                internal\Cache::remove($cacheKey);
                self::deleteIndicesHelper($object, $class);
                return self::releaseRandomUniqueId($object);
                //TODO figure out if I want to track minus revisions when an object is deleted
            }
            else {
                return false;
            }
        }
    }

    /**
     * Delete all the objects of a certain class
     * @param string $class
     * @return int the number of deleted objects
     * @since 1.1.0
     */
    public static function deleteAllObjectsOfClass($class) {
        $objects = self::getAllObjectsOfClass($class);
        $count = 0;
        foreach ($objects as $object) {
            $count = self::delete($object) ? $count + 1 : $count;
        }
        return $count;
    }

    /**
     * Delete an object's indices 
     * @param int $object
     * @return int the number of deleted indices
     * @since 1.1.0
     */
    public static function deleteIndices($object) {
        $class = self::load($object, "class");
        return self::deleteIndicesHelper($object, $class);
    }

    /**
     * Helper function to delete an object's indices
     * @param int $object
     * @param string $class the object's class
     * @return int the number of deleted indices
     * @since 1.1.0
     */
    private static function deleteIndicesHelper($object, $class) {
        $table = self::generateIndexTableName($class);
        $db = internal\Database::getPersistentHandlerForSearchDb();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT); //error silently to prevent a situation where an exception is thrown because $table does not exist when an object of the class has never been indexed
        $sql = "DELETE FROM $table WHERE " . self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN . " = $object";
        return $db->exec($sql);
    }

    /**
     * Delete an object's indices for a property and optionally for a specific value on the property
     * @param int $object
     * @param string $property
     * @param mixed $value (optional) If not specified, then all of the indices for the property will be deleted
     * @return mixed int, the number of deleted indices if deleting indices for entire property or array, array of remaining indices if
     * deleting a specific value's indices
     * @since 1.1.0
     */
    public static function deletePropertyIndices($object, $property,
            $value = null) {
        $class = self::load($object, "class");
        $table = self::generateIndexTableName($class);
        $db = internal\Database::getPersistentHandlerForSearchDb();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $count = $sql = "DELETE FROM $table WHERE " . self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN . " = $object AND " . self::$_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN . " = '$property'";
        return empty($value) ? self::indexProperty($object, $property) : $count;
    }

    /**
     * Turn off revision tracking for an object
     * @param int $object
     * @return boolean
     * @since 1.1.0
     */
    public static function disableRevisionTracking($object) {
        if (self::exists($object)) {
            $count = internal\Database::update(self::$_SCHEMA_OBJECTS_TABLE,
                            array(self::$_SCHEMA_OBJECTS_TABLE_TRACK_REVISIONS_COLUMN => self::$TRACK_REVISIONS_FALSE),
                            "" . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object");
            return assert($count == 1);
        }
        else {
            return false;
        }
    }

    /**
     * Turn on revision tracking for an object
     * @param int $object
     * @return boolean
     * @since 1.1.0
     */
    public static function enableRevisionTracking($object) {
        if (self::exists($object)) {
            $count = internal\Database::update(self::$_SCHEMA_OBJECTS_TABLE,
                            array(self::$_SCHEMA_OBJECTS_TABLE_TRACK_REVISIONS_COLUMN => self::$TRACK_REVISIONS_TRUE),
                            "" . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object");
            return assert($count == 1);
        }
        else {
            return false;
        }
    }

    /**
     * Check to see if there exists an object with an id
     * @param int $object
     * @param string $class (optional) only check if there exists and object of the specified $class with $object
     * @return boolean
     * @since 1.1.0
     */
    public static function exists($object, $class = null) {
        if (empty($object)) return false;
        if (!is_null($class)) {
            $class = self::formatClassName($class);
            $rows = internal\Database::select(self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN,
                            self::$_SCHEMA_OBJECTS_TABLE,
                            "" .
                            self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object AND " . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN . " = '$class'");
        }
        else {
            $rows = internal\Database::select(self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN,
                            self::$_SCHEMA_OBJECTS_TABLE,
                            "" .
                            self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object");
        }
        return !empty($rows);
    }

    /**
     * Export data from Koncourse
     * @param string $class the class the export
     * @param string $directory absolute path to the export directory
     * @param string $format the export format (xls || html)
     * @param string $filename (optional) name of the exported file
     * @param string $matchingCriteria (optional) criteria to use when selecting objects of the class to export. By default all the objects of the class are exported
     * @param string $propertiesToExport (optional) comma separated list of properties to export. By default all the properties of each object are exported
     * @param string $dilimeterForMultiValuedProperties (optional) delimeter to use when separating the values for a multi-valued property
     * @return void
     * @since 1.1.0
     */
    public static function export($class, $directory, $format, $filename = null,
            $matchingCriteria = null,
            $propertiesToExport = self::LOAD_ALL_OBJECT_PROPERTIES,
            $dilimeterForMultiValuedProperties = "|") {
        $objects = empty($matchingCriteria) ? self::getAllObjectsOfClass($class)
                    : self::getObjectsOfClassThatMeetCriteria($class,
                        $matchingCriteria);
        $matrix = array();
        $headers = array();
        $headers[] = "id";
        $rowCount = 1;
        foreach ($objects as $object) {
            $matrix['id'][$rowCount] = $object;
            $data = self::load($object, $propertiesToExport);
            unset($data['class']);
            foreach ($data as $property => $value) {
                $value = is_array($value) ? implode($dilimeterForMultiValuedProperties,
                                $value) : $value;
                $matrix[$property][$rowCount] = $value;
            }
            $rowCount++;
        }
        $directory = !util\Strings::endsWith("/", $directory) ? $directory . '/'
                    : $directory;
        $filename = empty($filename) ? md5($class . microtime(true)) : $filename;
        $file = $directory . $filename;
        switch ($format) {
            case 'csv':
                return self::exportToExcel($format, $matrix, $file . ".csv");
                break;
            case 'html':
                return self::exportToExcel($format, $matrix, $file . ".html");
                break;
            case 'xls':
                return self::exportToExcel($format, $matrix, $file . ".xls");
                break;
            default:
                throw new \InvalidArgumentException($format,
                "$format is not a valid Koncourse export format.");
                break;
        }
    }

    /**
     * Export to an excel file
     * @param string $format export format (csv || xls || html)
     * @param array $data 2d array mapping column headers to arrays mapping row numbers to values
     * @param string $file export file name
     * @return boolean
     * @since 1.1.0
     */
    private static function exportToExcel($format, $data, $file) {
        require_once dirname(__FILE__) . "/../lib/PHPExcel/PHPExcel.php";
        require_once dirname(__FILE__) . "/../lib/PHPExcel/PHPExcel/Writer/Excel5.php";
        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle("Koncourse");
        $columnCount = 0;
        foreach ($data as $header => $rows) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow($columnCount,
                    1, $header);
            foreach ($rows as $rowCount => $value) {
                $excel->getActiveSheet()->setCellValueByColumnAndRow($columnCount,
                        $rowCount + 1, $value);
            }
            $columnCount++;
        }
        switch ($format) {
            case 'csv':
                $writer = new \PHPExcel_Writer_CSV($excel);
                break;
            case 'html':
                $writer = new \PHPExcel_Writer_HTML($excel);
                break;
            case 'xls':
                $writer = new \PHPExcel_Writer_Excel5($excel);
                break;
            default:
                throw new \InvalidArgumentException($format,
                "$format is not a valid Excel export format.");
                break;
        }
        $writer->save($file);
        return true;
    }

    /**
     * Check to see if there exists a resource with an id
     * @return boolean
     * @since 1.1.0
     */
    private static function existsResourceWithId($object) {
        $rows = internal\Database::select("*",
                        "" . self::$_SCHEMA_IDS_TABLE . "",
                        "" . self::$_SCHEMA_IDS_TABLE_ID_COLUMN . " = $object");
        return !empty($rows);
    }

    /**
     * Properly format a class name.
     * @param string $class
     * @return string
     * @since 1.1.0
     */
    private static function formatClassName($class) {
        $invalidChars = array(" ", "/", "-");
        $class = strtoupper(trim($class));
        foreach ($invalidChars as $char) {
            $property = str_replace($char, "_", $class);
        }
        return $class;
    }

    /**
     * Properly format a property name.
     * @param string $property
     * @return string
     * @since 1.1.0
     */
    private static function formatPropertyName($property) {
        $invalidChars = array(" ", "/", "-");
        $property = strtolower(trim($property));
        foreach ($invalidChars as $char) {
            $property = str_replace($char, "_", $property);
        }
        return $property;
    }

    /**
     * Generate the index table name for a class
     * @param string $class
     * @return string 
     * @since 1.1.0
     */
    private static function generateIndexTableName($class) {
        return strtolower($class) . "_index";
    }

    /**
     * Generate a random unique id.
     * @param boolean $useSimpleMethod (optional) flag to generate ids based on the current unix millisecond as opposed to using /dev/urandom.
     * This method is faster, but less secure since the generated ids are predictable and sequential
     * @param boolean $doNotRecord (optional) flag to indicate that the generated id should not be recorded and set aside so it is isn't generated again
     * @return int
     * @since 1.1.0
     */
    private static function generateRandomUniqueId($useSimpleMethod = false,
            $doNotRecord = false) {
        $object = $useSimpleMethod ? microtime() : abs(preg_replace('/\s+/',
                                ' ',
                                trim(shell_exec(self::$RANDOM_UNIQUE_ID_GENERATION_COMMAND))));
        if (self::existsResourceWithId($object)) {
            \Logger::getLogger("main")->info("Generated an existing id");
            return self::generateRandomUniqueId($useSimpleMethod, $doNotRecord);
        }
        if (!$doNotRecord) {
            internal\Database::insert(self::$_SCHEMA_IDS_TABLE,
                    "" . self::$_SCHEMA_IDS_TABLE_ID_COLUMN . "",
                    array(array($object)));
        }
        return $object;
    }

    /**
     * Get all the objects in Koncourse
     * @return array int[] 
     * @since 1.1.0
     */
    private static function getAllObjects() {
        $rows = internal\Database::getPersistentHandlerForDb()->query("SELECT " . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " FROM " .
                        self::$_SCHEMA_OBJECTS_TABLE . " ORDER BY created ASC")->fetchAll(PDO::FETCH_COLUMN,
                0);
        return $rows;
    }

    /**
     * Get all the classes in Koncourse
     * @return array String[]
     * @since 1.1.0
     */
    public static function getAllClasses() {
        $rows = internal\Database::getPersistentHandlerForDb()->query("SELECT " . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN . " FROM " .
                        self::$_SCHEMA_OBJECTS_TABLE . " GROUP BY " . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN)->fetchAll(PDO::FETCH_COLUMN,
                0);
        return $rows;
    }

    /**
     * Get all the objects that belong to a class in the order that they were created.
     * @param string $class
     * @return array int[] get* functions only return object ids and DO NOT return object data. To retrieve object data, see the load* functions
     * @since 1.1.0
     */
    public static function getAllObjectsOfClass($class) {
        $class = self::formatClassName($class);
        $rows = internal\Database::getPersistentHandlerForDb()->query("SELECT " . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " FROM " .
                        self::$_SCHEMA_OBJECTS_TABLE . " WHERE " . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN . " = '$class' ORDER BY created ASC")->fetchAll(PDO::FETCH_COLUMN,
                0);
        return $rows;
    }

    /**
     * Get the indices for an object's property
     * @param int $object
     * @param string $property
     * @return array 
     * @since 1.1.0
     */
    private static function getIndices($object, $property) {
        $class = self::load($object, "class");
        $table = self::generateIndexTableName($class);
        $sql = "SELECT " . self::$_SCHEMA_INDEX_TABLE_STRING_COLUMN . " FROM $table WHERE " . self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN .
                " = $object AND " . self::$_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN . " = '$property'";
        $db = internal\Database::getPersistentHandlerForSearchDb();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $indices = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE,
                0);
        return array_values($indices);
    }

    /**
     * Get the objects of a class that meet a criteria
     * @param string $class
     * @param string $criteria a string that uses SQL syntax to describe the relationship between properties and values
     * using the 6 boolean operators (==, !=, <=, >=, >, <) or the MySQL LIKE operator, or the RANGE operator (RANGE is inclusive) 
     * <strong>Example</strong>
     * <code>
     * $criteria = "prop1 = val1 AND prop2 != val1 AND prop3 > 10 AND prop4 < 10 AND prop5 >= val5 AND prop6 <= val6 AND prop7 RANGE val7a val7b";
     * </code>
     * @return array int[] get* functions only return object ids and DO NOT return object data. To retrieve object data, see the load* functions
     * @since 1.1.0
     */
    public static function getObjectsOfClassThatMeetCriteria($class, $criteria) {
        $class = self::formatClassName($class);
        $criteria = self::breakdownSelectionCriteria($criteria);
        $results = array();
        foreach ($criteria as $clause) {
            $maxIndex = count($results) - 1;
            if (strcasecmp($clause, "and") == 0) {
                $postOperationResults = array_intersect($results[$maxIndex],
                        $results[$maxIndex - 1]);
                $results[$maxIndex - 1] = $postOperationResults;
                unset($results[$maxIndex]);
                $results = array_values($results);
            }
            else if (strcasecmp($clause, "or") == 0) {
                $postOperationResults = array_merge($results[$maxIndex],
                        $results[$maxIndex - 1]);
                $results[$maxIndex - 1] = $postOperationResults;
                unset($results[$maxIndex]);
                $results = array_values($results);
            }
            else {
                $results[] = self::getObjectsOfClassThatMeetCriteriaHelper($class,
                                $clause);
            }
        }
        return $results[0];
    }

    /**
     * Helper function to get objects of class that meet criteria using the Shunting Yard Algorithm (http://en.wikipedia.org/wiki/Shunting-yard_algorithm)
     * @param string $class
     * @param string $criterion single selection criterion
     * @return array 
     * @since 1.1.0
     */
    private static function getObjectsOfClassThatMeetCriteriaHelper($class,
            $criterion) {
        $parts = array();
        $_criterion = array();
        foreach (self::$_SELECTION_CRITERIA_OPERATORS as $delimter) {
            if (count($parts) == 2) {
                break;
            }
            else {
                $parts = explode($delimter, $criterion);
                $operator = $delimter;
            }
        }
        $objects = array();
        $property = self::formatPropertyName($parts[0]);
        $value = $parts[1];
        $sql = "SELECT " . self::$_SCHEMA_DATA_TABLE_OBJECT_COLUMN . " FROM " . self::$_SCHEMA_DATA_TABLE . " JOIN " .
                self::$_SCHEMA_OBJECTS_TABLE . " ON " . self::$_SCHEMA_DATA_TABLE_OBJECT_COLUMN . " = " .
                self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " WHERE";
        if ($operator == "=" || $operator == "!=") {
            $hash = md5($value);
            $sql.=" " . self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . " = '$property' AND " . self::$_SCHEMA_DATA_TABLE_VALUE_HASH_COLUMN .
                    " $operator '$hash' AND " . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN . " = '$class'";
        }
        else if ($operator == "RANGE") {
            $value = explode(" ", $value);
            $valueMin = $value[0];
            $valueMax = $value[1];
            $sql.=" " . self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . " = '$property' AND " . self::$_SCHEMA_DATA_TABLE_VALUE_COLUMN .
                    " >= $valueMin AND " . self::$_SCHEMA_DATA_TABLE_VALUE_COLUMN . " <= $valueMax AND " . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN . " = '$class'";
        }
        else {
            if (self::getValueType($value) == self::$VALUE_TYPES['string']) {
                $value = "'$value'";
            }
            $sql.=" " . self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . " = '$property' AND " . self::$_SCHEMA_DATA_TABLE_VALUE_COLUMN .
                    " $operator $value AND " . self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN . " = '$class'";
        }
        $objects = internal\Database::getPersistentHandlerForDb()->query($sql)->fetchAll(PDO::FETCH_COLUMN,
                0);
        return array_values($objects);
    }

    /**
     * Get stats about Koncourse
     * @return array
     * @since 1.1.0
     */
    public static function getStats() {
        return array();
    }

    /**
     * Get the number of objects in Koncourse
     * @return int
     * @since 1.1.0
     */
    private static function getStatsNumOfObjects() {
        $objects = self::getAllObjects();
        return count($objects);
    }

    /**
     * Get an object's time of creation
     * @param int $object
     * @return float
     */
    public static function getTimeOfCreation($object) {
        $row = internal\Database::select(self::$_SCHEMA_OBJECTS_TABLE_CREATED_COLUMN,
                        self::$_SCHEMA_OBJECTS_TABLE,
                        self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object",
                        true);
        return $row[self::$_SCHEMA_OBJECTS_TABLE_CREATED_COLUMN];
    }

    /**
     * Get an object's time of last caching
     * @param int $object
     * @return float
     */
    public static function getTimeOfLastCache($object) {
        $row = internal\Database::select(self::$_SCHEMA_OBJECTS_TABLE_LAST_CACHED_COLUMN,
                        self::$_SCHEMA_OBJECTS_TABLE,
                        self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object",
                        true);
        return $row[self::$_SCHEMA_OBJECTS_TABLE_LAST_CACHED_COLUMN];
    }

    /**
     * Get an object's time of last revision
     * @param int $object
     * @return float
     */
    public static function getTimeOfLastRevision($object) {
        $row = internal\Database::select(self::$_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN,
                        self::$_SCHEMA_OBJECTS_TABLE,
                        self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object",
                        true);
        return $row[self::$_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN];
    }

    /**
     * Get the value type of a quantity
     * @param mixed $value
     * @throws Koncourse_Std_Err_Exception
     * @return string {KONCOURSE_OBJECT_CLASS_NAME} || DATETIME || INTEGER || BOOLEAN || FLOAT || STRING
     * @since 1.1.0
     */
    public static function getValueType($value) {
        $type = gettype($value);
        switch ($type) {
            case 'integer':
                if (self::exists($value)) {
                    $class = self::load($value, "class", false);
                    return $class;
                }
                else {
                    return self::$VALUE_TYPES['integer'];
                }
                break;
            case 'boolean':
                return self::$VALUE_TYPES['boolean'];
                break;
            case 'double':
                return self::$VALUE_TYPES['float'];
                break;
            case 'string':
                if (is_numeric($value) && is_float_value($value)) { //check for no decimal points
                    return self::getValueType((float) $value);
                }
                else if (is_numeric($value) && !is_float_value($value)) {//check for decimal points
                    return self::getValueType((int) $value);
                }
                else if (util\Time::isDateTimeString($value)) {
                    return self::$VALUE_TYPES['datetime'];
                }
                else if (strtolower($value) == "false" || strtolower($value) == "true") {
                    return self::$VALUE_TYPES['boolean'];
                }
                else {
                    return self::$VALUE_TYPES["string"];
                }
                break;
            case 'array':
                throw new \Exception("$value is an ARRAY, which is not a valid Koncourse_Object value type");
                break;
            case 'object':
                throw new \Exception("$value is an OBJECT, which is not a valid Koncourse_Object value type");
                break;
            case 'resource':
                throw new \Exception("$value is a RESOURCE, which is not a valid Koncourse_Object value type");
                break;
            case 'NULL':
                throw new \Exception("The value of ($value) is NULL, which is not a valid Koncourse_Object value type");
                break;
            default:
                throw new \Exception("Cannot determine value type of of $value");
                break;
        }
    }

    /**
     * Check to see if the version of the object that is possibly stored in the cache is stale
     * @param int $object
     * @return boolean
     * @since 1.1.0
     */
    private static function hasStaleCache($object) {
        $row = internal\Database::select("" . self::$_SCHEMA_OBJECTS_TABLE_LAST_CACHED_COLUMN . ", " .
                        self::$_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN . "",
                        self::$_SCHEMA_OBJECTS_TABLE,
                        "" .
                        self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object",
                        true);
        if ($row) {
            return $row[self::$_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN] > $row[self::$_SCHEMA_OBJECTS_TABLE_LAST_CACHED_COLUMN];
        }
        else {
            throw new \InvalidArgumentException($object,
            "$object is not a valid Koncourse_Object");
        }
    }

    /**
     * Import data into Koncourse from a file
     * @param string $file
     * @param string $class the class to import the objects as
     * @param string $callable (optional) a callable function to use for setting/adding properties on the created object.
     * The callable function must take 3 parameters: $object (int), $property (string), $value (mixed). Defaults to Koncourse_Object::setPropertyValue()
     * @param boolean $trackRevisions (optional) default is false 
     * @throws Koncourse_Std_Err_Exception
     * @return array array of created object ids
     * @since 1.1.0
     */
    public static function import($file, $class,
            $callable = "Aubrey::setPropertyValue", $trackRevisions = false) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($extension == "csv") {
            return self::importCsvFile($file, $class, $callable, $trackRevisions);
        }
        else if ($extension == "xls") {
            return self::importXlsFile($file, $class, $callable, $trackRevisions);
        }
        else {
            throw new \Exception("Cannot import file with extension $extension");
        }
    }

    /**
     * Import data from a file into Koncourse with the option to sync it with existing data. 
     * Each row of the file will be processed individually in either <strong>sync mode</strong> or <strong>create mode</strong>. If $columnThatContainsResolvablePropertyForExistingObjects is specified and the row contains a value for that column, then the row will be processed in <em>sync mode</em>. Otherwise, the row will be processed in <em>create mode</em>. 
     * For a row being processed in n sync mode, it is possible for multiple objects to be resolved for the sync if the value of $columnThatContainsResolvablePropertyForExistingObjects belongs to multiple objects of the specified $class. In this instance, all of the resolved objects will be synced with the data from the row. For a row being processed in create mode, only one object will be created.
     * @param string $file full path to the file. The file must contain named column headers. Valid file types are csv and xls
     * @param string $class the class to import new object as in create mode || the class to select existing objects from in sync mode
     * @param string $columnThatContainsResolvablePropertyForExistingObjects (optional) the name of the column in the $file that contains the property value to 
     * use for resolving existing objects that should be synced. Specifying this parameter will attempt to process the row in sync mode. Actual processing in sync mode will
     * only occur iff there is a value for this column in the row and there is at least one existing object of $class that has that value for a property named $columnThatContainsResolvablePropertyForExistingObjects
     * @param string $callable (optional) a callable function to use for setting/adding properties on the created object.
     * The callable function must take 3 parameters: $object (int), $property (string), $value (mixed). Defaults to Koncourse_Object::setPropertyValue()
     * @param boolean $trackRevisionsForCreatedObjects (optional) default is false 
     * @param string $logFile (optional) specify a file in which to dump logs from the import
     * @return array array of mapping created or synced object ids to a description of revisions
     * @since 1.1.0
     */
    public static function importSync($file, $class,
            $columnThatContainsResolvablePropertyForExistingObjects = null,
            $callable = "Aubrey::setPropertyValue",
            $trackRevisionsForCreatedObjects = false, $logFile = null) {
        $data = array(); //extract the data from the file
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($extension == "csv") {
            $data = util\Arrays::fromCsvFile($file);
        }
        else {
            throw new \Exception("$file is not a valid file for import and sync");
        }
        return self::importSyncHelper($data, $class,
                        $columnThatContainsResolvablePropertyForExistingObjects,
                        $callable, $trackRevisionsForCreatedObjects, $logFile,
                        $file);
    }

    /**
     * Helper function for importing and syncing data from an external source
     * @param array $data 2d array of arrays where each sub array (row) maps a property name to a property value
     * @param string $class the class to import new object as in create mode || the class to select existing objects from in sync mode
     * @param string $columnThatContainsResolvablePropertyForExistingObjects (optional) the name of the column in the $file that contains the property value to 
     * use for resolving existing objects that should be synced. Specifying this parameter will attempt to process the row in sync mode. Actual processing in sync mode will
     * only occur iff there is a value for this column in the row and there is at least one existing object of $class that has that value for a property named $columnThatContainsResolvablePropertyForExistingObjects
     * @param string $callable (optional) a callable function to use for setting/adding properties on the created object.
     * The callable function must take 3 parameters: $object (int), $property (string), $value (mixed). Defaults to Koncourse_Object::setPropertyValue()
     * @param boolean $trackRevisionsForCreatedObjects (optional) default is false 
     * @param string $logFile (optional) specify a file in which to dump logs from the import
     * @param string $externalSource the name of the external data source
     * @return array array of mapping created or synced object ids to a description of revisions
     * @since 1.1.0
     */
    private static function importSyncHelper($data, $class,
            $columnThatContainsResolvablePropertyForExistingObjects = null,
            $callable = "Aubrey::setPropertyValue",
            $trackRevisionsForCreatedObjects = false, $logFile = null,
            $externalSource = null) {
        set_time_limit(0); //prevent script execution timeout
        $start = util\Time::now();
        $externalSource = empty($externalSource) ? "the external data source" : $externalSource;
        $logFile = empty($logFile) ? "importsync" . time() . "-" . md5($externalSource)
                    : $logFile;
        $errorCount = 0;
        $rowCount = 1;
        $affectedObjects = array();
        foreach ($data as $row) { //go through each row of the array
            $objects = array();
            if (!empty($columnThatContainsResolvablePropertyForExistingObjects) && !empty($row[$columnThatContainsResolvablePropertyForExistingObjects])) { //process the current row in sync mode
                $mode = 1;
                $resolvablePropertyValue = $row[$columnThatContainsResolvablePropertyForExistingObjects];
                $objects = self::getObjectsOfClassThatMeetCriteria($class,
                                "$columnThatContainsResolvablePropertyForExistingObjects = $resolvablePropertyValue");
            }
            else { //process the current row in create mode
                $mode = 2;
                $objects[] = self::create($class,
                                $trackRevisionsForCreatedObjects);
                $createMode = true; //process the current row in create mode
            }
            foreach ($objects as $object) { //go through each of the resolved/created objects and apply $callable using the data in the $row
                $columnCount = 1;
                foreach ($row as $key => $value) { //go through each column of the row and apply $callable with the data in the cell
                    try {
                        call_user_func_array($callable,
                                array($object, $key, $value));
                        $affectedObjects[$object] = $object;
                        \Logger::getLogger("main")->info("Successfully imported column $columnCount in row $rowCount of $externalSource and applied it to object $object with $callable");
                    }
                    catch (Exception $e) {
                        $errorCount++;
                        \Logger::getLogger("main")->error("Attempted to import column $columnCount in row $rowCount of $externalSource, but there was an Exception: " . $e->getMessage());
                        ;
                    }
                    $columnCount++;
                }
            }
            $rowCount++;
        }
        $elapsed = util\Time::getElapsedTimeString($start);
        \Logger::getLogger("main")->info("Finished the importsync of $externalSource in $elapsed. Affected " . count($affectedObjects) . " objects and encountered $errorCount errors.");
        return array_keys($affectedObjects);
    }

    /**
     * Import data from a file with inverted indices (i.e. the property keys are listed vertically in one column and the property values are listed vertically in another column) with the option to sync it with existing data
     * Each row of the file will be transformed to normal form. In particular, the value of $columnThatContainsKeys will become a column header for the row and the value of $columnThatContainsValues will become the value
     * for the column in that row. You may also specify $keyPrefix and $keySuffix to normalize the column header. Once the dats is normalized, it will be importsynced similarly to Koncourse::importSync
     * @param string $file
     * @param string $class
     * @param string $columnThatContainsValues
     * @param string $columnThatContainsKeys
     * @param string $keyPrefix (optional) prefix for all column headers
     * @param string $keySuffix (optional) sufix for all column headers
     * @param string $columnThatContainsResolvablePropertyForExistingObjects (optional)
     * @param string $callable (optional)
     * @param boolean $trackRevisionsForCreatedObjects (optional)
     * @param string $logFile (optional)
     * @throws Koncourse_Std_Err_Exception
     * @return array
     * @see Koncourse::importSync
     */
    public static function importSyncInverted($file, $class,
            $columnThatContainsValues, $columnThatContainsKeys,
            $keyPrefix = null, $keySuffix = null,
            $columnThatContainsResolvablePropertyForExistingObjects = null,
            $callable = "Aubrey::setPropertyValue",
            $trackRevisionsForCreatedObjects = false, $logFile = null) {
        $_data = array(); //extract the data from the file
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($extension == "csv") {
            $_data = util\Arrays::fromCsvFile($file);
        }
        else {
            throw new \Exception("$file is not a valid file for import and sync");
        }
        //transform $_data to normal form
        $data = array();
        foreach ($_data as $_row) {
            $row = array();
            if (!empty($_row[$columnThatContainsResolvablePropertyForExistingObjects])) {
                $row[$columnThatContainsResolvablePropertyForExistingObjects] = $_row[$columnThatContainsResolvablePropertyForExistingObjects];
            }
            if (!empty($_row[$columnThatContainsKeys]) && !empty($_row[$columnThatContainsValues])) {
                $row[self::formatPropertyName($keyPrefix . $_row[$columnThatContainsKeys] . $keySuffix)]
                        = $_row[$columnThatContainsValues];
            }
            if (!empty($row)) {
                $data[] = $row;
            }
        }
        return self::importSyncHelper($data, $class,
                        $columnThatContainsResolvablePropertyForExistingObjects,
                        $callable, $trackRevisionsForCreatedObjects, $logFile,
                        $file);
    }

    /**
     * Import a csv file. The file must be well-formed, with the first row containing property names.
     * @param string $file
     * @param string $class
     * @param string $callable (optional) a callable function to use for setting/adding properties on the created object. 
     * The callable function must take 3 parameters: $object (int), $property (string), $value (mixed). Defaults to Koncourse::setPropertyValue()
     * @param boolean $trackRevisions (optional) default is false
     * @param string $delimeter (optional) default is comma
     * @param string $enclosure (optional) default is double quote
     * @param string $escape (optional) default is backslash
     * @param string $logFile (optional) specify a logfile for the import
     * @return array array of created object ids
     * @since 1.1.0
     */
    private static function importCsvFile($file, $class,
            $callable = "Aubrey::setPropertyValue", $trackRevisions = false,
            $delimeter = ",", $enclosure = "\"", $escape = "\\", $logFile = null) {
        set_time_limit(0);
        $handle = fopen($file, "r");
        if ($handle) {
            $logFile = empty($logFile) ? "csvimport" . time() . "-" . md5($file)
                        : $logFile;
            \Logger::getLogger("main")->info("Import of $file started at " . util\Time::now());
            $start = util\Time::now();
            $headers = array();
            $count = 0;
            $errors = 0;
            $objects = array();
            while ($row = fgetcsv($handle, 0, $delimeter, $enclosure, $escape)) {
                if (empty($headers)) $headers = $row;
                else {
                    $object = self::create($class, $trackRevisions);
                    foreach ($row as $index => $value) {
                        $property = $headers[$index];
                        if (empty($value)) {
                            continue;
                        }
                        try {
                            call_user_func_array($callable,
                                    array($object, $property, $value));
                        }
                        catch (Exception $e) {
                            self::delete($object);
                            $errors++;
                            \Logger::getLogger("main")->error("Attempted to import row $count of $file, but there was an Exception: " . $e->getMessage());
                            break;
                        }
                    }
                    $objects[] = $object;
                    $count++;
                    if (empty($_SERVER['HTTP_USER_AGENT'])) {
                        echo ".";
                    }
                }
            }
            $elapsed = util\Time::getElapsedTimeString($start);
            \Logger::getLogger("main")->info("Finished importing $file in $elapsed. Created " . count($objects) . " objects and encountered $errors errors.");
            if (empty($_SERVER['HTTP_USER_AGENT'])) {
                echo "\n";
            }
            return $objects;
        }
        else {
            throw new \Exception("Could not open $file");
        }
    }

    /**
     * Import a file with the classic excel format (xls)
     * @param string $file
     * @param string $class
     * @param string $callable (optional) a callable function to use for setting/adding properties on the created object.
     * The callable function must take 3 parameters: $object (int), $property (string), $value (mixed). Defaults to Koncourse::setPropertyValue()
     * @param boolean $trackRevisions (optional)
     * @throws Koncourse_Std_Err_Exception
     * @return array array of created object ids
     * @since 1.1.0
     */
    private static function importXlsFile($file, $class,
            $callable = "Aubrey::setPropertyValue", $trackRevisions = false,
            $logFile = null) {
        require_once dirname(__FILE__) . "/../lib/PHP_Excel_Reader/excel_reader2.php";
        $logFile = empty($logFile) ? "xlsimport" . time() . "-" . md5($file) : $logFile;
        \Logger::getLogger("main")->info("Import of $file started at " . util\Time::now());
        $start = util\Time::now();
        set_time_limit(0);
        $data = new Spreadsheet_Excel_Reader($file, false);
        $data->setUTFEncoder("mb");
        $numRows = $data->rowcount();
        $numCols = $data->colcount();
        $headers = array();
        $objects = array();
        $errors = 0;
        for ($i = 1; $i < $numRows; $i++) {
            $object = null;
            for ($j = 1; $j < $numCols; $j++) {
                $value = $data->val($i, $j);
                if (empty($value)) continue;
                if ($i == 1) $headers[$j] = $value;
                else {
                    $property = $headers[$j];
                    try {
                        $object = empty($object) ? self::create($class,
                                        $trackRevisions) : $object;
                        call_user_func_array($callable,
                                array($object, $property, $value));
                    }
                    catch (\Exception $e) {
                        self::delete($object);
                        $errors++;
                        \Logger::getLogger("main")->error("Attempted to import row $i of $file, but there was an Exception: " . $e->getMessage());
                        break 2;
                    }
                }
            }
            if (!empty($object)) $objects[] = $object;
            if (empty($_SERVER['HTTP_USER_AGENT'])) {
                echo ".";
            }
        }
        $elapsed = util\Time::getElapsedTimeString($start);
        \Logger::getLogger("main")->info("Finished importing $file in $elapsed. Created " . count($objects) . " objects and encountered $errors errors.");
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            echo "\n";
        }
        return $objects;
    }

    /**
     * Index an object. Relevant existing indices are preserved while irrelevant ones are removed.
     * @param int $object
     * @param string $properties (optional) comma separated list of properties to index
     * @return array String[] array containing the object's indices
     * @since 1.1.0
     */
    public static function index($object,
            $properties = self::LOAD_ALL_OBJECT_PROPERTIES) {
        $properties = $properties == self::LOAD_ALL_OBJECT_PROPERTIES ?
                $properties : $properties . ",class"; //this ensures that we get an array even if user only requests one property
        $data = self::load($object, $properties);
        $class = $data['class'];
        unset($data['class']);
        self::createIndexTable($class);
        $table = self::generateIndexTableName($class);
        $hashes = array();
        foreach ($data as $property => $value) {
            $hashes = array_merge($hashes,
                    self::indexPropertyHelper($object, $property, $value, $table));
        }
        return $hashes;
    }

    /**
     * Index all the objects in a class. Relevant existing indices are preserved while irrelevant onces are removed.
     * @param string $class
     * @param string $properties (optional) comma separated list of properties to load
     * @return array String[] array containing all the created indices
     * @since 1.1.0
     */
    public static function indexAllObjectsOfClass($class,
            $properties = self::LOAD_ALL_OBJECT_PROPERTIES) {
        set_time_limit(0);
        $objects = self::getAllObjectsOfClass($class);
        $hashes = array();
        foreach ($objects as $object) {
            $hashes = array_merge($hashes, self::index($object, $properties));
        }
        return $hashes;
    }

    /**
     * Index a property value on an object. Relevant existing indices are preserved while irrelevant onces are removed.
     * @param int $object
     * @param string $property
     * @return array array of indices
     * @since 1.1.0
     */
    public static function indexProperty($object, $property) {
        $class = self::load($object, "class");
        self::createIndexTable($class);
        $value = self::load($object, $property);
        $table = self::generateIndexTableName($class);
        return self::indexPropertyHelper($object, $property, $value, $table);
    }

    /**
     * Helper function that inserts indices into an index
     * @param int $object
     * @param string $key
     * @param string $value
     * @param string $table the table in which to store the indices
     * @return array array of indices
     * @since 1.1.0
     */
    private static function indexPropertyHelper($object, $property, $value,
            $table) {
        $existingIndices = self::getIndices($object, $property);
        $hashes = array();
        $property = strtolower($property);
        $db = internal\Database::getPersistentHandlerForSearchDb();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (is_array($value))
                foreach ($value as $_value)
                    $hashes = array_merge($hashes,
                        self::indexPropertyHelper($object, $property, $_value,
                                $table));
        else {
            $value = strtolower($value);
            $valueHash = md5($value);
            for ($i = 0; $i < strlen($value); $i++) {
                for ($j = strlen($value) - $i; $j > 0; $j--) {
                    $string = substr($value, $i, $j);
                    if (strlen($string) == 0) continue;
                    $hash = md5($string);
                    $sql = "INSERT IGNORE INTO $table (" . self::$_SCHEMA_INDEX_TABLE_STRING_COLUMN . ", " .
                            self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN . ", " . self::$_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN . ") VALUES ('$hash', $object, '$property')";
                    $db->exec($sql);
                    $hashes[] = $hash;
                    $hashes = array_unique($hashes);
                }
            }
        }
        $deletableIndexes = array_diff($existingIndices, $hashes);
        foreach ($deletableIndexes as $index) {
            $sql = "DELETE FROM $table WHERE " . self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN . " = $object AND " .
                    self::$_SCHEMA_INDEX_TABLE_STRING_COLUMN . " = '$index' AND " . self::$_SCHEMA_INDEX_TABLE_PROPERTY_COLUMN .
                    " = '$property'";
            $db->exec($sql);
        }
        return $hashes;
    }

    /**
     * Insert a value for a property on an object in the database.
     * @param int $object
     * @param string $property
     * @param mixed $value
     * @param string $valueType (optional)
     * @return int
     * @since 1.1.0
     */
    private static function insertPropertyValue($object, $property, $value,
            $valueType = null) {
        $valueType = !empty($valueType) ? $valueType : self::getValueType($value);
        $hash = md5($value);
        $added = util\Time::now();
        $insertCount = internal\Database::insert(self::$_SCHEMA_DATA_TABLE,
                        "" . self::$_SCHEMA_DATA_TABLE_OBJECT_COLUMN . ", " .
                        self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . ", " . self::$_SCHEMA_DATA_TABLE_VALUE_COLUMN . ", " . self::$_SCHEMA_DATA_TABLE_VALUE_TYPE_COLUMN .
                        ", " . self::$_SCHEMA_DATA_TABLE_VALUE_HASH_COLUMN . ", " . self::$_SCHEMA_DATA_TABLE_ADDED_COLUMN . "",
                        array(array($object, $property, $value, $valueType, $hash,
                        $added)));
        internal\Database::update(self::$_SCHEMA_OBJECTS_TABLE,
                array(self::$_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN => $added),
                ""
                . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object");
        return $insertCount;
    }

    /**
     * Check to see if a value type if consistent with the other value types that a property takes on an object 
     * @param mixed $value
     * @param string $property
     * @param int $object
     * @return boolean
     * @since 1.1.0
     */
    private static function isConsistentValueTypeForPropertyOnObject($value,
            $property, $object) {
        $cacheKey = self::$_CACHE_KEY_PREFIX_ACCEPTED_VALUE_TYPE . "$property-on-$object";
        $acceptedValueType = internal\Cache::get($cacheKey);
        if (empty($acceptedValueType)) {
            $acceptedValueType = internal\Database::getPersistentHandlerForDb()->query("SELECT " . self::$_SCHEMA_DATA_TABLE_VALUE_TYPE_COLUMN . " FROM " .
                            self::$_SCHEMA_DATA_TABLE . " WHERE " . self::$_SCHEMA_DATA_TABLE_OBJECT_COLUMN . " = $object AND "
                            . self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . " = '$property'")->fetch(PDO::FETCH_COLUMN,
                    0);
            internal\Cache::put($cacheKey, $acceptedValueType);
        }
        $valueType = self::getValueType($value);
        return $valueType == $acceptedValueType || empty($acceptedValueType);
    }

    /**
     * Load an existing Koncourse_Object
     * @param int $object
     * @param string $properties (optional) comma separated list of properties to load. By default, all the object's properties will be loaded
     * @param boolean $byPassCache (optional) flag to indicate that the object should be loaded from the database and not the cache
     * @return mixed array if and only if two or more properties are requested by the caller. If the caller requests a single property, the value
     * of that single property is returned, if it exists.  
     * @since 1.1.0
     */
    public static function load($object,
            $properties = self::LOAD_ALL_OBJECT_PROPERTIES, $byPassCache = false) {
        if (self::exists($object)) {
            $properties = strtolower($properties);
            $cacheKey = self::$_CACHE_KEY_PREFIX . $object;
            $data = internal\Cache::get($cacheKey);
            if (is_null($data) || $byPassCache || self::hasStaleCache($object)) {
                $data = array();
                $row = internal\Database::select(self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN,
                                self::$_SCHEMA_OBJECTS_TABLE,
                                "" .
                                self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object",
                                true);
                assert(!empty($row));
                $data[self::$INTRINSIC_PROPERTIES["class"]] = $row[self::$_SCHEMA_OBJECTS_TABLE_CLASS_COLUMN];
                $rows = internal\Database::select("" . self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . ", " .
                                self::$_SCHEMA_DATA_TABLE_VALUE_COLUMN . ", " . self::$_SCHEMA_DATA_TABLE_VALUE_TYPE_COLUMN . "",
                                self::$_SCHEMA_DATA_TABLE,
                                "" . self::$_SCHEMA_DATA_TABLE_OBJECT_COLUMN . " = $object ORDER BY " . self::$_SCHEMA_DATA_TABLE_ADDED_COLUMN . " ASC");
                foreach ($rows as $row) {
                    $key = $row[self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN];
                    $value = $row[self::$_SCHEMA_DATA_TABLE_VALUE_COLUMN];
                    if ($row[self::$_SCHEMA_DATA_TABLE_VALUE_TYPE_COLUMN] == self::$VALUE_TYPES['datetime']) {
                        $value = util\Time::getDateComponents($value);
                    }
                    if (isset($data[$key])) {
                        $currentVal = $data[$key];
                        if (is_array($currentVal)) {
                            $data[$key][] = $value;
                        }
                        else {
                            $data[$key] = array();
                            $data[$key][] = $currentVal;
                            $data[$key][] = $value;
                        }
                    }
                    else {
                        $data[$key] = $value;
                    }
                }
                internal\Cache::put($cacheKey, $data);
                $cachedAt = util\Time::now();
                internal\Database::update(self::$_SCHEMA_OBJECTS_TABLE,
                        array(self::$_SCHEMA_OBJECTS_TABLE_LAST_CACHED_COLUMN => $cachedAt),
                        "" . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object");
            }
            if ($properties != self::LOAD_ALL_OBJECT_PROPERTIES) {
                $properties = util\Arrays::fromList($properties);
                $propertyCount = count($properties);
                $properties = array_flip($properties);
                $data = array_intersect_key($data, $properties);
                assert(count($data) <= $propertyCount);
                if ($propertyCount == 1) {
                    $data = array_values($data);
                    $data = count($data) == 1 ? $data[0] : $data;
                }
            }
            return $data;
        }
        else {
            throw new \Exception("Failed to load a Koncourse_Object identified by $object");
        }
    }

    /**
     * Load all the objects of a class
     * @param string $class
     * @param string $propertiesToLoad (optional)
     * @param boolean $byPassCache (optional)
     * @return array assoc array mapping object id to data array. To only retrive the object ids, see the get* methods
     * @since 1.1.0
     */
    public static function loadAllObjectsOfClass($class,
            $propertiesToLoad = self::LOAD_ALL_OBJECT_PROPERTIES,
            $byPassCache = false) {
        $_objects = self::getAllObjectsOfClass($class);
        $objects = array();
        foreach ($_objects as $object) {
            $objects[$object] = self::load($object, $propertiesToLoad,
                            $byPassCache);
        }
        return $objects;
    }

    /**
     * Load objects of a class that meet a criteria.
     * @param string $class
     * @param string $criteria a string that uses SQL syntax to describe the relationship between properties and values
     * using the 6 boolean operators (==, !=, <=, >=, >, <) or the MySQL LIKE operator, or the RANGE operator (RANGE is inclusive) 
     * <strong>Example</strong>
     * <code>
     * $criteria = "prop1 = val1 AND prop2 != val1 AND prop3 > 10 AND prop4 < 10 AND prop5 >= val5 AND prop6 <= val6 AND prop7 RANGE val7a val7b";
     * </code>
     * @param string $propertiesToLoad (optional)
     * @param boolean $byPassCache (optional)
     * @return array assoc array mapping object id to assoc array of properties. To only retrive object ids, see the get* methods
     * @since 1.1.0
     */
    public static function loadObjectsOfClassThatMeetCriteria($class, $criteria,
            $propertiesToLoad = self::LOAD_ALL_OBJECT_PROPERTIES,
            $byPassCache = false) {
        $objects = self::getObjectsOfClassThatMeetCriteria($class, $criteria);
        $_objects = array();
        foreach ($objects as $object) {
            $_objects[$object] = self::load($object, $propertiesToLoad,
                            $byPassCache);
        }
        return $_objects;
    }

    /**
     * Load an object's history of tracked revisions
     * @param int $object
     * @param int || float $rangeStart (optional) the earliest history to load, by default the earlist history is loaded
     * @param int || float  $rangeEnd (optional) the most recent history to load, by default the latest history is loaded
     * @return array ('time' => int || float, 'property' => string, 'value' => mixed, 'revision_type' => + || -)
     * @since 1.1.0
     */
    public static function loadRevisionHistory($object, $rangeStart = null,
            $rangeEnd = null) {
        $rangeStart = !empty($rangeStart) ? $rangeStart : 0;
        $rangeEnd = !empty($rangeEnd) ? "AND " . self::$_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN . " < $rangeEnd"
                    : "";
        $sql = "SELECT " . self::$_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN . ", " . self::$_SCHEMA_DATA_REVISIONS_TABLE_PROPERTY_COLUMN .
                ", " . self::$_SCHEMA_DATA_REVISIONS_TABLE_VALUE_COLUMN . ", " . self::$_SCHEMA_DATA_REVISIONS_TABLE_REVISION_TYPE_COLUMN . " FROM "
                . self::$_SCHEMA_DATA_REVISIONS_TABLE . " WHERE " . self::$_SCHEMA_DATA_REVISIONS_TABLE_OBJECT_COLUMN . " = $object AND "
                . self::$_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN . " > $rangeStart $rangeEnd ORDER BY " . self::$_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN . " DESC";
        $rows = internal\Database::getPersistentHandlerForDb()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * Reset Koncourse by deleting all the objects
     * @return void
     * @since 1.1.0
     */
    public static function reset() {
        set_time_limit(0);
        $objects = self::getAllObjects();
        foreach ($objects as $object) {
            self::delete($object);
        }
    }

    /**
     * Release a random unique id so that it can be used for other things.
     * @param int $object
     * @return boolean
     * @since 1.1.0
     */
    private static function releaseRandomUniqueId($object) {
        internal\Database::delete(self::$_SCHEMA_IDS_TABLE,
                "" . self::$_SCHEMA_IDS_TABLE_ID_COLUMN . " = $object");
        return !self::existsResourceWithId($object);
    }

    /**
     * Removed an assigned property or property value
     * @param int $object
     * @param string $property
     * @param mixed $value (optional) value to remove. If none is specified the entire property (and all of its values) ar removed from the object.
     * If a value is specified, only that value is removed from the property on the object
     * @return boolean
     * @since 1.1.0
     */
    public static function removePropertyValue($object, $property, $value = null) {
        $property = self::formatPropertyName($property);
        if (is_null($value)) {
            $shouldTrackRevisions = self::shouldTrackRevisions($object);
            if ($shouldTrackRevisions) {
                $currentValues = self::load($object, $property);
                $currentValues = is_array($currentValues) ? $currentValues : array(
                    $currentValues);
            }
            $count = internal\Database::delete(self::$_SCHEMA_DATA_TABLE,
                            "" . self::$_SCHEMA_DATA_TABLE_OBJECT_COLUMN . " = $object AND " .
                            self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . " = '$property'");
        }
        else {
            $currentValues = array($value);
            $hash = md5($value);
            $count = internal\Database::delete(self::$_SCHEMA_DATA_TABLE,
                            "" . self::$_SCHEMA_DATA_TABLE_OBJECT_COLUMN . " = $object AND " .
                            self::$_SCHEMA_DATA_TABLE_PROPERTY_COLUMN . " = '$property' AND " . self::$_SCHEMA_DATA_TABLE_VALUE_HASH_COLUMN . " = '$hash'");
        }
        if ($count > 0) {
            if ($shouldTrackRevisions) {
                foreach ($currentValues as $currentValue) {
                    self::trackRevision($object, $property, $currentValue,
                            self::$REVISION_TYPE_MINUS);
                }
            }
            $removed = util\Time::now();
            internal\Database::update(self::$_SCHEMA_OBJECTS_TABLE,
                    array(self::$_SCHEMA_OBJECTS_TABLE_LAST_REVISED_COLUMN => $removed),
                    "" . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object");
            $cacheKey = self::$_CACHE_KEY_PREFIX_ACCEPTED_VALUE_TYPE . "$property-on-$object";
            internal\Cache::remove($cacheKey);
            try {
                self::deletePropertyIndices($object, $property, $value);
            }
            catch (Exception $e) {
                //The source of this error is a nonexistent table, we can ignore this because it is expected that an index table would not exist
                //for a class that has never had any objects indexed.
            }
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Remove an object's history of tracked revisions
     * @param int $object
     * @param int || float $rangeStart (optional) the earliest history to remove, by default the earlist history is removed
     * @param int || float  $rangeEnd (optional) the most recent history to remove, by default the latest history is removed
     * @return boolean
     * @since 1.1.0
     */
    public static function removeRevisionHistory($object, $rangeStart = null,
            $rangeEnd = null) {
        $rangeStart = !empty($rangeStart) ? $rangeStart : 0;
        $rangeEnd = !empty($rangeEnd) ? "AND " . self::$_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN . " < $rangeEnd"
                    : "";
        $count = internal\Database::delete(self::$_SCHEMA_DATA_REVISIONS_TABLE,
                        "" . self::$_SCHEMA_DATA_REVISIONS_TABLE_OBJECT_COLUMN . " = $object AND " .
                        self::$_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN . " > $rangeStart $rangeEnd");
        return $count > 0;
    }

    /**
     * Rollback the current transaction and undo any changes therein.
     * NOTE: It is possible that changes to search indices may not be properly rolled back. Therefore, it is recommended that
     * any changes that involve indexing data, happen outside of a transaction.
     * @return boolean
     * @since 1.1.1
     */
    public static function rollbackTransaction() {
        return internal\Database::getPersistentHandlerForDb()->rollBack();
    }

    /**
     * Search Koncourse
     * @param string $query
     * @param string $class
     * @param string $properties (optional) comma separated list of properties to load for each result
     * @return array assoc array mapping object id to data
     * @since 1.0.0
     */
    public static function search($query, $class,
            $properties = self::LOAD_ALL_OBJECT_PROPERTIES) {
        $table = self::generateIndexTableName($class);
        $db = internal\Database::getPersistentHandlerForSearchDb();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare("SELECT " . self::$_SCHEMA_INDEX_TABLE_OBJECT_COLUMN . " FROM $table WHERE " . self::$_SCHEMA_INDEX_TABLE_STRING_COLUMN . " = ?");
        $query = strtolower($query);
        ;
        $ids = array();
        $tok = strtok($query, " ");
        $string = md5($tok);
        $stmt->execute(array($string));
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tok = strtok(" ");
        while ($tok != false) {
            $string = md5($tok);
            $stmt->execute(array($string));
            $newIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $ids = array_intersect($ids, $newIds);
            $tok = strtok(" ");
        }
        $ids = array_unique($ids);
        $objects = array();
        foreach ($ids as $id) {
            $object = self::load($id, $properties);
            $objects[$id] = $object;
        }
        return $objects;
    }

    /**
     * Assign a value to a property on an object. This function will remove any previously assigned values for the property on the
     * object and will assign the new value.
     * <strong>NOTE:</strong> This function is useful for adding/modifying properties that are meant to be single valued
     * @param int $object
     * @param string $property
     * @param mixed $value
     * @param boolean $index (optional) flag to indicate that the property should be indexed after the value is set
     * @return boolean
     * @since 1.1.0
     */
    public static function setPropertyValue($object, $property, $value,
            $index = false) {
        $property = self::formatPropertyName($property);
        if (!in_array($property, self::$INTRINSIC_PROPERTIES) && !empty($property)) {
            self::removePropertyValue($object, $property);
            if (self::insertPropertyValue($object, $property, $value) == 1) {
                if ($index) self::indexProperty($object, $property);
                return self::shouldTrackRevisions($object) ? self::trackRevision($object,
                                $property, $value, self::$REVISION_TYPE_ADDITION)
                            : true;
            }
            else {
                return false;
            }
        }
        else
                throw new \InvalidArgumentException($property,
            "$property is not a valid property name");
    }

    /**
     * Determine if an object should have its revisions tracked
     * @param int $object
     * @return boolean
     * @since 1.1.0
     */
    public static function shouldTrackRevisions($object) {
        return internal\Database::getPersistentHandlerForDb()->query("SELECT " . self::$_SCHEMA_OBJECTS_TABLE_TRACK_REVISIONS_COLUMN .
                        " FROM " . self::$_SCHEMA_OBJECTS_TABLE . " WHERE " . self::$_SCHEMA_OBJECTS_TABLE_ID_COLUMN . " = $object")->fetchColumn(0)
                == self::$TRACK_REVISIONS_TRUE;
    }

    /**
     * Track a revision
     * @param int $object
     * @param string $property
     * @param mixed $value
     * @param string $revisionType
     * @return boolean
     * @since 1.1.0
     */
    private static function trackRevision($object, $property, $value,
            $revisionType) {
        $time = util\Time::now();
        $count = internal\Database::insert(self::$_SCHEMA_DATA_REVISIONS_TABLE,
                        "" . self::$_SCHEMA_DATA_REVISIONS_TABLE_TIME_COLUMN .
                        ", " . self::$_SCHEMA_DATA_REVISIONS_TABLE_OBJECT_COLUMN . ", " . self::$_SCHEMA_DATA_REVISIONS_TABLE_PROPERTY_COLUMN .
                        ", " . self::$_SCHEMA_DATA_REVISIONS_TABLE_VALUE_COLUMN . ", " . self::$_SCHEMA_DATA_REVISIONS_TABLE_REVISION_TYPE_COLUMN .
                        "",
                        array(array($time, $object, $property, $value, $revisionType)));
        return $count == 1;
    }

}

?>