<?php

namespace org\cinchapi\aubrey\util;

require_once \dirname(__FILE__) . '/require.php';

/**
 * System level operations.
 */
class System {

    const OPTION_NO_INPUT = "";
    const OPTION_OPTIONAL = "::";
    const OPTION_REQUIRED = ":";

    /**
     * @return bool <code>true</code> if the current execution occurs from a cli
     */
    public static function isCliRequest() {
        return empty($_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Get the name of the caller function
     * Adapted from http://stackoverflow.com/a/4767754
     *
     * @param string $function (optional) function on the current execution stack, defaults to the function at the top of the stack
     * @param array $stack (optional) execution stack to examine, defaults to the current executiton stack
     * @return string Class::function
     */
    public static function getCallerFunction($function = null, $stack = null) {
        $stack = empty($stack) ? debug_backtrace() : $stack;
        $function = empty($function) ? System::getCallerFunction(__FUNCTION__,
                        $stack) : $function;
        if (Strings::contains("::", $function)) { //static function
            $function = explode("::", $function);
        }
        else { //member function
            $function = explode("->", $function);
        }
        $function = count($function) > 1 ? $function[1] : $function[0];
        $caller = null;
        if (!empty($function) && is_string($function)) {
            $index = 0;
            while ($index < count($stack) - 1 && empty($caller)) {
                $trace = $stack[$index];
                if ($trace['function'] == $function) {
                    $previousTrace = $stack[$index + 1];
                    @$caller = $previousTrace['class'] . $previousTrace['type'] .
                            $previousTrace['function'];
                }
                $index++;
            }
        }

        return $caller;
    }

    /**
     * Get the command line arguments or display a usage message.
     * This function simplifies the process of writing PHP CLIs. A CLI only needs to pass in its options to this function. The function will validate the user input (making sure all required options have input) and return them in an alphabetically sorted array.
     *
     * @param array $options 2d array describing the cli's options. Each option should be described by an array in the following form:
     * <pre>
     * array('long_name', 'short_name', 'description', System::OPTION_NO_INPUT || System::OPTION_OPTIONAL || System::OPTION_REQUIRED)
     * </pre>
     * <strong>Note:</strong> This function will automatically declare 'help' and 'h' options to print a formatted usage message
     * @return array 2d array mapping option name to user input or true/false (for options that do not take input) or null (for optional options that did not get user input)
     * Each user input will be an array in the following form:
     * <pre>
     * array('long_name' => input_valu || NULL || true || false)
     * </pre>
     * @example
     * <pre>
     * $options = array(
     *     array('name', 'n', 'user full name', System::OPTION_REQUIRED),
     *     array('email', 'e', 'user email address', System::OPTION_OPTION),
     *     array('adult', 'a', 'the user is an adult', System::OPTION_NO_INPUT)
     * );
     * $userInput = System::getCliArgs($options);
     * list($name, $email, $adult) = array_values($userInput);
     * </pre>
     * @since 1.0.0
     */
    public static function getCliArgs($options) {
        $shortOpts = "h";
        $longOpts = array('help');
        $requiredOpts = array();
        $nonRequiredOpts = array();
        $blankOpts = array();
        $inputs = array();
        $usage = "Please specify an option\n";
        foreach ($options as $option) {
            $longname = $option[0];
            $shortname = $option[1];
            $description = $option[2];
            $required = $option[3];
            if (strlen($shortname) != 1 && !empty($shortname)) {
                trigger_error($shortname,
                        "Each option short name must be 1 charater in length",
                        E_USER_ERROR);
            }
            if (strtolower($longname) == "help" || strtolower($shortname) == "h"
            ) {
                trigger_error("help || h are not valid option names",
                        E_USER_ERROR);
            }
            $shortname = !empty($shortname) ? $shortname : " ";
            switch ($required) {
                case self::OPTION_NO_INPUT:
                    $shortOpts .= $shortname . self::OPTION_NO_INPUT;
                    $longOpts[] = $longname . self::OPTION_NO_INPUT;
                    $blankOpts[] = array($longname, $shortname);
                    break;
                case self::OPTION_REQUIRED:
                    $shortOpts .= $shortname . self::OPTION_REQUIRED;
                    $longOpts[] = $longname . self::OPTION_REQUIRED;
                    $requiredOpts[] = array($longname, $shortname);
                    break;
                case self::OPTION_OPTIONAL:
                    $shortOpts .= $shortname .
                            ":"; //this is hack so that the user does not need an = sign when specifying optional params
                    $longOpts[] = $longname .
                            ":"; //this is hack so that the user does not need an = sign when specifying optional params
                    $description = "(optional) $description";
                    $nonRequiredOpts[] = array($longname, $shortname);
                    break;
                default:
                    trigger_error("$required is not a valid indication of whether option $longname requires user input. For each option subarray, please specify either Koncourse_Std_Cli::OPTION_NO_INPUT or Koncourse_Std_Cli::OPTION_REQUIRED or Koncourse_Std_Cli::OPTION_OPTIONAL at the last index.",
                            E_USER_ERROR);
                    break;
            }
            $usage .= "-$shortname,  --$longname \t $description\n";
            $usage = str_replace("- ,  ", "     ", $usage);
        }
        $usage .= "-h,  --help \t print this message\n";
        $args = getopt($shortOpts, $longOpts);
        if (isset($args['h']) || isset($args['help'])) {
            System::println($usage);
            exit(-1);
        }
        foreach ($requiredOpts as $opt) {
            $long = $opt[0];
            $short = $opt[1];
            if (!empty($args[$long])) {
                $inputs[$long] = $args[$long];
            }
            else if (!empty($args[$short])) {
                $inputs[$long] = $args[$short];
            }
            else {
                System::println($usage);
                System::println("ERROR: Missing required option $short");
                exit(-1);
            }
        }
        foreach ($nonRequiredOpts as $opt) {
            $long = $opt[0];
            $short = $opt[1];
            if (!empty($args[$long])) {
                $inputs[$long] = $args[$long];
            }
            else if (!empty($args[$short])) {
                $inputs[$long] = $args[$short];
            }
            else {
                $inputs[$long] = null;
            }
        }
        foreach ($blankOpts as $opt) {
            $long = $opt[0];
            $short = $opt[1];
            if (isset($args[$long]) || isset($args[$short])) {
                $inputs[$long] = true;
            }
            else {
                $inputs[$long] = false;
            }
        }
        ksort($inputs);

        return $inputs;
    }

    /**
     * Display a string followed by a newline. This function displays the correct newline
     * depending on whether the current execution is from a cli or http request.
     *
     * @param mixed $string
     */
    public static function println($string) {
        if (is_array($string)) {
            $string = Print_R($string, true);
        }
        else if (is_object($string)) {
            $string = (object) $string;
            $string = $string->__toString();
        }
        echo $string;
        echo System::isCliRequest() ? "\n" : "<br />";
    }

    /**
     * Read a password from the command line
     *
     * @param boolean $showStars (optional) flag to show stars as the user types the password
     * @return string the password
     */
    public static function promptCliPassword($showStars = false) {
        $currentShellStyle = shell_exec('stty -g');
        if (!$showStars) {
            shell_exec('stty -echo');
            $password = System::readStdIn();
        }
        else {
            shell_exec('stty -icanon -echo min 1 time 0');
            $password = '';
            while (true) {
                $char = fgetc(STDIN);
                if ($char === "\n") {
                    break;
                }
                else if (ord($char) === 127) {
                    if (strlen($password) > 0) {
                        fwrite(STDOUT, "\x08 \x08");
                        $password = substr($password, 0, -1);
                    }
                }
                else {
                    fwrite(STDOUT, "*");
                    $password .= $char;
                }
            }
            System::println('');
        }
        shell_exec("stty $currentShellStyle");

        return $password;
    }

    /**
     * Read user input from stdin
     *
     * @return string
     */
    public static function readStdIn() {
        $handle = fopen("php://stdin", "r");
        $input = fgets($handle);
        fclose($handle);

        return trim($input);
    }

    /**
     * Require all the PHP files in a directory.
     *
     * @param string $directory full path to directory
     * @param boolean $recursive (optional) flag to require subdirectories
     */
    public static function requireDirectory($directory, $recursive = false) {
        Preconditions::checkEmptyError($directory);

        $directory = Strings::append("/", $directory);
        $files = scandir($directory);
        foreach ($files as $file) {
            $_file = $file;
            $file = $directory . $file;
            if (Strings::endsWith(".php", $file)) {
                $class = substr($_file, 0,
                        strlen($_file) -
                        4); //check to see if a corresponding $class has already been included
                if (!in_array($class, get_declared_classes())) {
                    /** @noinspection PhpIncludeInspection */
                    require_once $file;
                }
            }
            else if (is_dir($file) && !Strings::endsWith(".", $file) && $recursive
            ) {
                static::requireDirectory($file, $recursive);
            }
            else {
                continue;
            }
        }
    }

}

?>
