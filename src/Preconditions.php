<?php

namespace org\cinchapi\aubrey\util;

/**
 * A framework for checking method preconditions and triggering appropriate 
 * warnings or åerrors.
 */
class Preconditions {

    private static $EMPTY_ARG_ERROR_MSG = "Argument is empty";
    private static $NULL_ARG_ERROR_MGS = "Argument is null";

    /**
     * Trigger and E_USER_ERROR if <code>$expression</code> is <code>true</code>.
     * @param bool $expression
     * @param $errorMessage
     */
    public static function checkArgumentError($expression, $errorMessage) {
        self::checkArgument($expression, $errorMessage, E_USER_ERROR);
    }

    /**
     * Trigger and E_USER_WARNING if <code>$expression</code> is <code>true</code>.
     * @param $expression
     * @param $errorMessage
     */
    public static function checkArgumentWarning($expression, $errorMessage) {
        self::checkArgument($expression, $errorMessage, E_USER_WARNING);
    }

    /**
     * Trigger an E_USER_ERROR if <code>$arg</code> is empty. This function 
     * adheres to the empty () construct in PHP except that it will not 
     * return <code>true</code> for the "0" string.
     * @param $arg
     * @see http://php.net/manual/en/function.empty.php
     */
    public static function checkNotEmptyError($arg) {
        if (is_string($arg)) {
            self::checkArgumentError($arg != "" || !is_null($arg),
                    self::$EMPTY_ARG_ERROR_MSG);
        }
        else {
            self::checkArgumentError(empty($arg), self::$EMPTY_ARG_ERROR_MSG);
        }
    }

    /**
     * Trigger an E_USER_WARNING if <code>$arg</code> is empty. This function 
     * adheres to the empty() construct in PHP except that it will not return 
     * <code>true</code> for the "0" string.
     * @param $arg
     * @see http://php.net/manual/en/function.empty.php
     */
    public static function checkNotEmptyWarning($arg) {
        if (is_string($arg)) {
            self::checkArgumentWarning($arg != "" || !is_null($arg),
                    self::$EMPTY_ARG_ERROR_MSG);
        }
        else {
            self::checkArgumentWarning(empty($arg), self::$EMPTY_ARG_ERROR_MSG);
        }
    }

    /**
     * Trigger an E_USER_ERROR if <code>$arg</code> is <code>null</code>
     * @param $arg
     */
    public static function checkNotNullError($arg) {
        self::checkArgumentError(!is_null($arg), self::$NULL_ARG_ERROR_MGS);
    }

    /**
     * Trigger an E_USER_WARNING if <code>$arg</code> is <code>null</code>
     * @param $arg
     */
    public static function checkNotNullWarning($arg) {
        self::checkArgumentWarning($arg, self::$NULL_ARG_ERROR_MGS);
    }

    /**
     * Trigger the appropriate <code>$errorType</code> if <code>$expression</code> is
     * <code>true</code>.
     * @param $expression
     * @param $errorMessage
     * @param $errorType
     */
    private static function checkArgument($expression, $errorMessage, $errorType) {
        if ($expression) {
            trigger_error($errorMessage, $errorType);
        }
    }

}

?>
