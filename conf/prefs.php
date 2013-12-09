<?php

#########################
######### CACHE #########
#########################

/**
 * Cache server host
 */
define('CACHE_HOST', '');

/**
 * Cache server port; the default for memcache is 11211
 */
define('CACHE_PORT', '11211');

#########################
####### DATABASE ########
#########################

/**
 * Database host
 * @var string
 */
define('DB_HOST', 'localhost'); 

/**
 * Database name
 * @var string
 */
define('DB_NAME', 'aubrey');

/**
 * Database username
 * @var string
 */
define('DB_USER', 'root');

/**
 * Database password
 * @var string
 */
define('DB_PASS', 'root');

#########################
####### DATETIME ########
#########################

/**
 * The default timezone
 * @var string
 * @see http://php.net/manual/en/timezones.php
 */
define('TIMEZONE', 'America/Los_Angeles');

#########################
####### GEODATA #########
#########################

/**
 * Get an app id from http://developer.yahoo.com/geo/placefinder/
 * @var string
 */
define('YAHOO_GEOCODE_APP_ID', '');

#########################
######## LOGGER #########
#########################

/**
 * Indication of whether debug logging should be enabled. Doing so will slow down performance and increase disk usage
 * @var boolean
 */
define('ENABLE_DEBUG_LOGGING', false);

#########################
####### PROCESSES #######
#########################

/**
 * Base directory under which temporary process files are stored
 * @var string
 */
define('TEMP_PROCESS_FILE_DIRECTORY', '/Users/jnelson');

#########################
##### SEARCH_SERVER #####
#########################

/**
 * Search database host
* @var string
*/
define('SEARCH_DB_HOST', 'localhost');

/**
 * Search database name
 * @var string
 */
define('SEARCH_DB_NAME', 'aubrey_search');

/**
 * Search database username
 * @var string
 */
define('SEARCH_DB_USER', 'root');

/**
 * Search database password
 * @var string
 */
define('SEARCH_DB_PASS', 'root');

?>