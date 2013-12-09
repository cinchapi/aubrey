<?php

namespace org\cinchapi\aubrey\util;

require_once \dirname(__FILE__) . '/require.php';

/**
 * Security related functions.
 */
class Security {

    private static $DEFAULT_MIN_PASSWORD_STRENGTH = 35;

    /**
     * Calculate the strength of a string, provided it meets the meets the minimum requirements:
     * 1) Min. 8 characters
     * 2) 3/4 of the following:
     * a) Upper case chars
     * b) Lowercase chars
     * c) Numbers
     * d) Symbols
     *
     * @param string $string
     * @return int
     * @see Algorithim adapted from http://www.passwordmeter.com/
     */
    public static function calculateStringStrength($string) {
        $stats = Strings::describe($string);
        //additions
        $length = $stats['length'];
        $uppercaseCount = $stats['uppercase_count'];
        $lowercaseCount = $stats['lowercase_count'];
        $numberCount = $stats['number_count'];
        $symbolCount = $stats['symbol_count'];
        $middleNumberOrSymbolCount = $stats['middle_number_or_symbol_count'];
        //deductions
        $consecutiveUppercaseCount = $stats['consecutive_uppercase_count'];
        $consecutiveLowercaseCount = $stats['consecutive_lowercase_count'];
        $consecutiveNumberCount = $stats['consecutive_number_count'];
        $sequentialLetterCount = $stats['sequential_letter_count'];
        $sequentialNumberCount = $stats['sequential_number_count'];
        $sequentialSymbolCount = $stats['sequential_symbol_count'];
        //Count requirements
        $requirementsCount = 0;
        if ($length >= 8) {
            $requirementsCount++;
        }
        else {
            return 0;
        }
        $requirementsCount += $numberCount > 0 ? 1 : 0;
        $requirementsCount += $symbolCount > 0 ? 1 : 0;
        $requirementsCount += $uppercaseCount > 0 ? 1 : 0;
        $requirementsCount += $lowercaseCount > 0 ? 1 : 0;
        if ($requirementsCount < 4) {
            return 0;
        }
        else {
            $additions =
                    ($length * 1) + ($uppercaseCount * 3) + ($lowercaseCount * 2)
                    + ($numberCount * 4) +
                    ($symbolCount * 6) + ($middleNumberOrSymbolCount * 5) + ($requirementsCount
                    * 2);
            $deductions = ($consecutiveUppercaseCount * 2) + ($consecutiveLowercaseCount
                    * 3) +
                    ($consecutiveNumberCount * 1) + ($sequentialLetterCount * 3)
                    +
                    ($sequentialNumberCount * 4) + ($sequentialSymbolCount * 3);
            $score = $additions - $deductions;

            return $score;
        }
    }

    /**
     * Return the fingerprint of the client.
     *
     * @return string
     */
    public static function getClientFingerprint() {
        Preconditions::checkArgumentError(!System::isCliRequest(),
                "Cannot get client fingerprint for a CLI request");
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ipAddress = self::getClientIpAddress();
        $fingerprint = md5($userAgent . $ipAddress);

        return $fingerprint;
    }

    /**
     * Get the IP Address of the client.
     *
     * @return string
     * @author http://www.kavoir.com/2010/03/php-how-to-detect-get-the-real-client-ip-address-of-website-visitors.html
     */
    public static function getClientIpAddress() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
    'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
    'REMOTE_ADDR'
        ) as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Return the hash of a id/password/salt combination.
     *
     * @param mixed $id
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function hashPassword($id, $password, $salt) {
        $salt = hash("sha256", $salt);
        $hashBase = $password . $salt . $id;

        return hash("whirlpool", $hashBase);
    }

    /**
     * Return <code>true</code> is <code>$password</code> is secure.
     *
     * @param string $password
     * @param $minStrength
     * @return bool
     */
    public static function isSecurePassword($password, $minStrength = null) {
        $minStrength = is_null($minStrength) ? self::$DEFAULT_MIN_PASSWORD_STRENGTH
                    : $minStrength;
        $strength = self::calculateStringStrength(trim($password));

        return $strength >= $minStrength;
    }

    /**
     * Generate a secure random number
     *
     * @param int $bytes (optional) the max number of bytes to read, default is 8
     * @return number
     */
    public static function srand($bytes = 8) {
        $bytes = !is_numeric($bytes) ? 8 : $bytes;
        $command = "od -An -N$bytes  -D /dev/random";
        $number = str_replace(' ', '', shell_exec($command));

        return trim($number);
    }

}

?>
