<?php

namespace org\cinchapi\aubrey\util;

require_once \dirname(__FILE__) . '/require.php';

/**
 * Time related functions.
 */
class Time {

    /**
     * Return a string that describes how much time has elapsed from $start to $end
     *
     * @param number $start timestamp from gettimeofday(true)
     * @param number $end timestamp from gettimeofday(true)
     * @return string
     */
    public static function getElapsedTimeString($start, $end = null) {
        Preconditions::checkArgumentError(!is_numeric($start),
                "first argument must be numeric");

        $end = !empty($end) ? $end : gettimeofday(true);
        $elapsed = $end - $start;
        if ($elapsed < 60) {
            return "$elapsed seconds";
        }
        else if ($elapsed >= 60 && $elapsed < 3600) {
            $elapsed = $elapsed / 60;
            if ($elapsed > 1) {
                return "$elapsed minutes";
            }
            else {
                return "$elapsed minute";
            }
        }
        else if ($elapsed >= 3600 && $elapsed < 86400) {
            $elapsed = $elapsed / 3600;
            if ($elapsed > 1) {
                return "$elapsed hours";
            }
            else {
                return "$elapsed hour";
            }
        }
        else {
            $elapsed = $elapsed / 86400;
            if ($elapsed > 1) {
                return "$elapsed days";
            }
            else {
                return "$elapsed day";
            }
        }
    }

    /**
     * Return the current number of milliseconds since the unix epoch as a float.
     * @return float
     */
    public static function now() {
        $time = gettimeofday();
        return (float) $time['sec'] . "." . $time['usec'];
    }

    /**
     * Get the components of a date (day of week, month, year, etc) from a unix timestamp
     * @param int $timestamp
     * @return array
     * @version 1.1.0
     */
    static function getDateComponents($timestamp) {
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        $components = getdate($timestamp);
        $components['timestamp'] = $components['0'];
        $components['timestring'] = self::timestring($components['timestamp']);
        unset($components['0']);
        return $components;
    }

}

?>
