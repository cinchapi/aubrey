<?php

namespace org\cinchapi\aubrey\util;

require_once \dirname(__FILE__) . '/require.php';

/**
 * Functions to operate on Strings.
 */
class Strings {

    private static $SYMBOLS = array("!", "@", "#", "$", "%", "^", "&", "*", "(",
        ")", "-", "+", "_",
        "=", "~", "{", "}", "[", "]", "|", "\\", ",", "<", ">", ".",
        "?", "/", ";", ":", "\"", "'"
    );

    /**
     * Add $append to the end of $input if $input does not already end with $append.
     *
     * @param string $append
     * @param string $input
     * @return string the possibly altered $input
     */
    public static function append($append, $input) {
        return Strings::endsWith($append, $input) ? $input : $input . $append;
    }

    /**
     * Check to see if <code>$haystack</code> contains <code>$needle</code>.
     *
     * @param string $needle
     * @param string $haystack
     * @return bool <code>true</code> if <code>$needle</code> is contained within <code>$haystack</code>.
     */
    public static function contains($needle, $haystack) {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Count the number of occcurences for patterns in a string. The counted patterns are:
     * 1) Single uppercase character --> uppercase_count
     * 2) Single lowercaase character --> lowercase_count
     * 3) Single number --> number_count
     * 4) Single snymbol --> symbol_count
     * 5) Two consecutive uppercase characters --> consecutive_uppercase_count
     * 6) Two consecutive lowercase characters --> consecutive_lowercase_count
     * 7) 3-charactered forward or reverse letter sequence --> sequential_letter_count
     * 8) 3-charactered forward or reverse number sequence --> sequential_number_count
     * 9) 3-charactered forward or reverse symbol sequence --> sequential_symbol_count
     * 10) Single number or symbol in the middle (not beginning or end) of string --> middle_number_or_symbol_count
     * 11) Single character --> length
     * 12) Two consecutive numbers --> consecutive_number_count
     * 13) Two consecutive symbols --> consecutive_symbol_count
     *
     * @param string $string
     * @return array
     * <pre>
     * array(
     *            'uppercase_count' => number,
     *            'lowercase_count' => number,
     *            'number_count' => number,
     *            'symbol_count' => number,
     *            'consecutive_uppercase_count' => number,
     *            'consecutive_lowercase_count' => number,
     *            'sequential_letter_count' => number,
     *            'sequential_number_count' => number,
     *            'sequential_symbol_count' => number,
     *            'middle_number_or_symbol_count' => number,
     *            'length' => number,
     *            'consecutive_number_count' => number,
     *            'consecutive_symbol_count' => number
     *    );
     * </pre>
     */
    public static function describe($string) {
        $chars = str_split($string);
        $length = count($chars);

        //Relevant patterns
        $numCount = 0;
        $midNumOrSymCount = 0;
        $consNumCount = 0;
        $seqNumCount = 0;
        $symbol_count = 0;
        $conSymCount = 0;
        $seqSymCount = 0;
        $lowerCount = 0;
        $consLowerCount = 0;
        $upperCount = 0;
        $consUpperCount = 0;
        $seqLetterCount = 0;
        for ($i = 0; $i < $length; $i++) {
            $char = $chars[$i];
            if (is_numeric($char)) {
                $numCount++;
                //check for middle number
                if ($i != 0 && $i != $length - 1) {
                    $midNumOrSymCount++;
                }
                //check for consecutive numbers
                if ($i < $length - 1) {
                    $j = $i + 1;
                    if (is_numeric($chars[$j])) {
                        $consNumCount++;
                        //check for sequential numbers in both directions
                        if ($i < $length - 2 && $length > 2) {
                            $j = $i + 1;
                            $k = $i + 2;
                            if (((is_numeric($chars[$j]) && $chars[$j] == $char + 1)
                                    &&
                                    (is_numeric($chars[$k]) && $chars[$k] == $chars[$j]
                                    + 1)) || (
                                    (is_numeric($chars[$j]) && $char == $chars[$j]
                                    + 1) &&
                                    (is_numeric($chars[$k]) && $chars[$j] == $chars[$k]
                                    + 1))
                            ) {
                                $seqNumCount++;
                            }
                        }
                    }
                }
            }
            else if (ctype_lower($char)) {
                $lowerCount++;
                //check for consecutive lowercase
                if ($i < $length - 1) {
                    $j = $i + 1;
                    if (ctype_lower($chars[$j])) {
                        $consLowerCount++;
                    }
                    //check for sequential letters in both directions
                    if ($i < $length - 2 && $length > 2) {
                        $j = $i + 1;
                        $k = $i + 2;
                        if (((ctype_alpha($chars[$j]) &&
                                strtolower($chars[$j]) == strtolower(self::getNextLetter($char)))
                                && (
                                ctype_alpha($chars[$k]) && strtolower($chars[$k])
                                ==
                                strtolower(self::getNextLetter($chars[$j])))) ||
                                ((ctype_alpha($chars[$j]) &&
                                strtolower($char) == strtolower(self::getNextLetter($chars[$j])))
                                && (
                                ctype_alpha($chars[$k]) && strtolower($chars[$j])
                                ==
                                strtolower(self::getNextLetter($chars[$k]))))
                        ) {
                            $seqLetterCount++;
                        }
                    }
                }
            }
            else if (ctype_upper($char)) {
                $upperCount++;
                //check for consecutive uppercase in both directions
                if ($i < $length - 1) {
                    $j = $i + 1;
                    if (ctype_upper($chars[$j])) {
                        $consUpperCount++;
                    }
                    //check for sequential letters in both directions
                    if ($i < $length - 2 && $length > 2) {
                        $j = $i + 1;
                        $k = $i + 2;
                        if (((ctype_alpha($chars[$j]) &&
                                strtolower($chars[$j]) == strtolower(self::getNextLetter($char)))
                                && (
                                ctype_alpha($chars[$k]) && strtolower($chars[$k])
                                ==
                                strtolower(self::getNextLetter($chars[$j])))) ||
                                ((ctype_alpha($chars[$j]) &&
                                strtolower($char) == strtolower(self::getNextLetter($chars[$j])))
                                && (
                                ctype_alpha($chars[$k]) && strtolower($chars[$j])
                                ==
                                strtolower(self::getNextLetter($chars[$k]))))
                        ) {
                            $seqLetterCount++;
                        }
                    }
                }
            }
            else { //assume symbol
                $symbol_count++;
                //check for middle symbol
                if ($i != 0 && $i != $length - 1) {
                    $midNumOrSymCount++;
                }
                //check for consecutive symbol
                if ($i < $length - 1) {
                    $j = $i + 1;
                    if (!ctype_alpha($chars[$j]) && !is_numeric($chars[$j])) {
                        $conSymCount++;
                        //check for sequential symbol
                        if ($i < $length - 2 && $length > 2) {
                            $j = $i + 1;
                            $k = $i + 2;
                            if ((strcasecmp($chars[$j],
                                            self::getNextSymbol($char)) == 0 &&
                                    strcasecmp($chars[$k],
                                            self::getNextSymbol($chars[$j])) == 0)
                                    || (
                                    strcasecmp($char,
                                            self::getNextSymbol($chars[$j])) == 0
                                    &&
                                    strcasecmp($chars[$j],
                                            self::getNextSymbol($chars[$k])) == 0)
                            ) {
                                $seqSymCount++;
                            }
                        }
                    }
                }
            }
        }

        return array('uppercase_count'               => $upperCount,
            'lowercase_count'               => $lowerCount,
            'number_count'                  => $numCount,
            'symbol_count'                  => $symbol_count,
            'consecutive_uppercase_count'   => $consUpperCount,
            'consecutive_lowercase_count'   => $consLowerCount,
            'sequential_letter_count'       => $seqLetterCount,
            'sequential_number_count'       => $seqNumCount,
            'sequential_symbol_count'       => $seqSymCount,
            'middle_number_or_symbol_count' => $midNumOrSymCount,
            'length'                        => $length,
            'consecutive_number_count'      => $consNumCount,
            'consecutive_symbol_count'      => $conSymCount
        );
    }

    /**
     * Check to see if <code>$haystack</code> ends with <code>$needle</code>
     *
     * @param string $needle
     * @param string $haystack
     * @return bool <code>true</code> if <code>$needle</code> is the last substring in <code>$haystack</code>.
     */
    public static function endsWith($needle, $haystack) {
        $length = strlen($needle);
        $start = $length * -1;

        return (substr($haystack, $start) === $needle);
    }

    /**
     * Return the next letter character.
     *
     * @param string $char
     * @return string
     */
    private static function getNextLetter($char) {
        Preconditions::checkArgumentError(
                !is_string($char) || strlen($char) != 1,
                "Argument must be a string of length 1");

        return++$char;
    }

    /**
     * Return the next symbol character.
     *
     * @param $symbol
     * @return int|mixed
     */
    private static function getNextSymbol($symbol) {
        $index = array_search($symbol, self::$SYMBOLS);
        Preconditions::checkArgumentError($index === false,
                "$symbol is not a valid symbol");

        return $index == count(self::$SYMBOLS) - 1 ? 0 : $index + 1;
    }

    /**
     * Add $append to the end of $input if and only if <code>static::endsWith($input, $append)</code>
     * is <code>false</code>.
     *
     * @param string $prepend
     * @param string $input
     * @return string the possibly altered <code>$input</code>.
     */
    public static function prepend($prepend, $input) {
        return Strings::startsWith($prepend, $input) ? $input : $prepend . $input;
    }

    /**
     * Check to see if $haystack starts with $needle
     *
     * @param string $needle
     * @param string $haystack
     * @return boolean
     */
    public static function startsWith($needle, $haystack) {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Strip the non-numeric characters from <code>$string</code>.
     *
     * @param string $string
     * @return string
     */
    public static function stripNonNumericChars($string) {
        $string = preg_replace('/\D/', '', $string);

        //optionally use filter_var($string, FILTER_SANITIZE_NUMBER_INT) which should allow + or
        //- to stay which would be good for int'l numbers
        return $string;
    }

    /**
     * Convert <code>$string</code> to camelCase.
     *
     * @param string $string
     * @return string the converted <code>$string</code>.
     */
    public static function toCamelCase($string) {
        $string = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        $string = strtolower(substr($string, 0, 1)) . substr($string, 1);

        return $string;
    }

    /**
     * Tokenize a string by a delimeter.
     * @param string $string
     * @param string $delimeter (optional), default is a comma
     * @return Array
     */
    public static function tokenize($string, $delimeter = ",") {
        if (is_string($string)) {
            $string = str_replace("$delimeter ", $delimeter, $string);
            $string = preg_split('~(?<!\\\)' . preg_quote($delimeter, '~') . '~',
                    $string);
            return $string;
        }
        else {
            return $string;
        }
    }

    /**
     * Convert a string to underscore case
     *
     * @param string $string
     * @return string cameCase String => camel_case_string
     */
    public static function toUnderscoreCase($string) {
        return strtolower(str_replace(' ', '_',
                        preg_replace('/([a-z])([A-Z])/', '$1_$2', $string)));
    }

}

?>
