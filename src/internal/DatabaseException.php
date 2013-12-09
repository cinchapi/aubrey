<?php

namespace org\cinchapi\aubrey\internal;

require_once \dirname(__FILE__) . '/../require.php';

/**
 * An Exception encountered during a Database operation.
 */
class DatabaseException extends \Exception {

    /**
     * Create a new DatabaseException
     * @param string $message
     * @param mixed $code (optional)
     */
    public function __construct($message, $code = null) {
        parent::__construct($message, $code);
    }
}

?>
