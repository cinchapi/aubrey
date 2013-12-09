<?php

namespace org\cinchapi\aubrey\util;

require_once \dirname(__FILE__) . '/require.php';

/**
 * Array based operations.
 */
class Arrays {

    /**
     * Recursively check to see if <code>$input</code> contains any empty values.
     *
     * @param array $input
     * @return bool <code>true</code> if at least one element of the array is
     * empty or contains an empty value.
     */
    public static function containsEmptyValues($input) {
        if (empty($input)) {
            return true;
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                return Arrays::containsEmptyValues($value);
            }
            else if (trim($value) == "" || is_null($value)) { //not using empty() here because
                //"0" string should not be considered
                //empty
                return true;
            }
            else {
                continue;
            }
        }

        return false;
    }

    /**
     * Return an array that holds the contents of $file.
     *
     * @param string $file full path to file
     * @param string $delimiter (optional), default is ","
     * @param string $enclosure (optional), default is """
     * @param string $escape (optional), default is "\"
     * @return array where each row is, itself an assoc array mapping the column name to the row
     * value
     */
    public static function fromCsvFile($file, $delimiter = ",",
            $enclosure = "\"", $escape = "\\") {
        Preconditions::checkArgumentError(!is_file($file),
                "$file is not a valid file");

        $handle = fopen($file, "r");
        if ($handle) {
            $headers = array();
            $rows = array();
            while ($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) {
                if (empty($headers)) {
                    $headers = $row;
                }
                else {
                    $_row = array();
                    foreach ($row as $index => $value) {
                        $key = $headers[$index];
                        if (empty($value)) {
                            continue;
                        }
                        else {
                            $_row[$key] = $value;
                        }
                    }
                    $rows[] = $_row;
                }
            }

            return $rows;
        }
        else {
            trigger_error("Could not open $file", E_USER_ERROR);

            return null;
        }
    }

    /**
     * Return an array that contains the contents of a <code>$delimiter</code> separated list.
     *
     * @param $list
     * @param string $delimiter
     * @return array
     */
    public static function fromList($list, $delimiter = ",") {
        Preconditions::checkNullError($list);

        if (is_array($list)) {
            return $list;
        }

        return preg_split('~(?<!\\\)' . preg_quote($delimiter, '~') . '~',
                str_replace("$delimiter ", $delimiter, $list));
    }

    /**
     * Ensure that $input is an array by converting if necessary.
     *
     * @param mixed $input
     * @return array.
     */
    public static function fromValue($input) {
        return !is_array($input) ? array($input) : $input;
    }

    /**
     * Recursively remove empty values from $input.
     *
     * @param array $input
     * @return array
     * @author http://www.jonasjohn.de/snippets/php/array-remove-empty.htm
     */
    public static function removeEmptyValues($input) {
        Preconditions::checkNullError($input);

        $narr = array();
        while (list($key, $val) = each($input)) {
            if (is_array($val)) {
                $val = Arrays::removeEmptyValues($val);
                if (count($val) != 0) {
                    $narr[$key] = $val;
                }
            }
            else {
                if (trim($val) != "") {
                    $narr[$key] = $val;
                }
            }
        }
        unset($input);

        return $narr;
    }

    /**
     * Sort an array of arrays (2D) by a key that is used in each subarray.
     *
     * @param array $input
     * @param string $key
     * @param int $sortFlag
     * @return array
     * @example
     * <code>
     * <p>$array = array(array('key1' => 'banana', 'key2' => 'wine), array('key1' => apple', 'key2' => 'zebra'), array('key1' => 'carrot', 'key2' => 'wine');</p>
     * <p>Arrays::sortBySharedKey($array, "key1"); =
     * array(array('key1' => apple', 'key2' => 'zebra'), array('key1' => 'banana', 'key2' => 'wine), array('key1' => 'carrot', 'key2' => 'wine'))</p>
     * <p>Arrays::sortBySharedKey($array, "key2"); =
     * array(array('key1' => carrot', 'key2' => 'wine'), array('key1' => 'banana', 'key2' => 'yell), array('key1' => 'apple', 'key2' => 'zebra'))</p>
     * </code>
     */
    public static function sortBySharedKey($input, $key, $sortFlag = SORT_STRING) {
        Preconditions::checkEmptyError($input);
        Preconditions::checkEmptyError($key);
        Preconditions::checkEmptyError($sortFlag);

        $sortArray = array();
        $count = 0;
        foreach ($input as $_array) {
            $_key = $_array[$key];
            $_key = array_key_exists($_key, $sortArray) ? $_key . $count : $_key;
            $sortArray[$_key] = $_array;
            $count++;
        }
        ksort($sortArray, $sortFlag);

        return array_values($sortArray);
    }

    /**
     * Return the PHP statement used to initialize $input.
     *
     * @param array $input
     * @return string
     */
    public static function toInitString($input) {
        Preconditions::checkEmptyError($input);

        $string = "array(";
        foreach ($input as $key => $value) {
            $string .= (!is_string($key) ? "" : "'$key' => ") .
                    (is_array($value) ? Arrays::toInitString($value) :
                            (is_string($value) ? "'$value'" : $value)) . ", ";
        }
        if (Strings::endsWith(", ", $string)) {
            $string = substr($string, 0, strlen($string) - 2);
        }
        $string .= ")";

        return $string;
    }

}

?>
