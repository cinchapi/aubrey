<?php
/**
 * THIS FILE MUST BE INCLUDED IN EVERY OTHER FILE:
 * require_once dirname(__FILE__) . '/require.php';
 */
require_once dirname(__FILE__) . '/../conf/prefs.php';
require_once dirname(__FILE__) . '/../lib/log4php/Logger.php';

// Configure the logging system
Logger::configure(dirname(__FILE__) . '/../conf/logging.php');

// Set initial system state
ini_set('date.timezone', TIMEZONE);
set_error_handler("handleError");
register_shutdown_function('handleShutdown');
spl_autoload_register('autoload');

/**
 * Autoload $class from this directory.
 * @param string $class
 */
function autoload($class) {
    $file = end(explode("\\", $class)) . ".php"; //remove namespace;
    require_once dirname(__FILE__) . "/$file";
}

/**
 * Handle PHP Errors
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return boolean
 * @ignore
 */
function handleError($errno, $errstr, $errfile, $errline) {
    $logger = Logger::getLogger("main");
    $message = "[#$errno]: $errstr \n error on line $errline in file $errfile";
    switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:
            $logger->error($message);
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $logger->warn($message);
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $logger->info($message);
            break;
        default:
            if (ENABLE_DEBUG_LOGGING) {
                $logger->debug($message);
            }
            break;
    }
    return false;
}

/**
 * Handles PHP Fatal Errors
 * @return boolean
 * @ignore
 */
function handleShutdown() {
    $error = error_get_last();
    if ($error != NULL) {
        return handleError($error['type'], $error['message'], $error['file'],
                $error['line']);
    }
    return false;
}

?>