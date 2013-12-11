<?php

namespace org\cinchapi\aubrey\internal;

require_once \dirname(__FILE__) . '/../require.php';

/**
 * Contains static utilities that facilitate interactions with the underlying
 * cache.
 */
class Cache {

    /**
     * Flush the cache
     * @param string $host
     * @param int $port
     * @return boolean
     * @since 1.0.0
     */
    public static function flush($host = CACHE_HOST, $port = CACHE_PORT) {
        return false;
//        $cache = self::getHandler($host, $port);
//        return \memcache_flush($cache);
    }

    /**
     * Get an item from the cache.
     * @param string $key
     * @param string $host (optional)
     * @param string $port (optional)
     * @param string $accessToken (optional)
     * @param boolean $throwExceptionIfKeyNotFound (optional) flag to throw an exception if the key isn't found. By default, null will be returned.
     * @throws Koncourse_Std_Err_Exception
     * @return mixed
     * @since 1.0.0
     */
    public static function get($key, $host = CACHE_HOST, $port = CACHE_PORT,
            $accessToken = null, $throwExceptionIfKeyNotFound = false) {
//        $key = self::translateKey($key, $accessToken);
//        $cache = self::getHandler($host, $port);
//        $value = \memcache_get($cache, $key);
//        if ($value == false) {
//            if ($throwExceptionIfKeyNotFound) {
//                memcache_close($cache);
//                throw new \Exception("Could not find key $key in the cache.");
//            }
//            else {
//                $value = null;
//            }
//        }
//        \memcache_close($cache);
//        return $value;
        return null;
    }

    /**
     * Connect to the cache server and get a handler
     * @param string $host
     * @param int $port
     * @param boolean $makePersistentConnection (optional) flag to specify that the connection should be persistent, default is true
     * @throws Koncourse_Std_Err_Exception
     * @return resource
     * @since 1.0.0
     */
    private static function getHandler($host, $port,
            $makePersistentConnection = true) {
        if ($makePersistentConnection) {
            $cache = \memcache_pconnect($host, $port);
        }
        else {
            $cache = \memcache_connect($host, $port);
        }
        if ($cache == false) {
            throw new \Exception("Could not connect to the cache server at $host on $port");
        }
        return $cache;
    }

    /**
     * Get the status of the memcache server
     * @param string $host
     * @param int $port
     * @return int
     * @since 1.0.0
     */
    public static function getServerStatus($host = CACHE_HOST, $port = CACHE_PORT) {
        return \memcache_get_server_status(self::getHandler($host, $port), $host,
                $port);
    }

    /**
     * Put an item in the cache.
     * @param string $key
     * @param mixed $value
     * @param int $timeUntilExpirationInSeconds (optional) number of seconds until the data expires from the cache. Default is 3600 (1 hr)
     * @param string $host (optional) string specify the location of the cache server. Defaul is localhost
     * @param int $port (optional) port number of the cache server. Default is 11211
     * @param boolean $doNotOverwriteExistingValueForKey (optional) flag to indicate that any existing value for they key should not be overwritten
     * @param string $accessToken (optional) access token to associate the cached data with a particular user
     * @throws Koncourse_Std_Err_Exception
     * @return boolean
     * @since 1.0.0
     */
    public static function put($key, $value, $timeUntilExpirationInSeconds = 3600,
            $host = CACHE_HOST, $port = CACHE_PORT,
            $doNotOverwriteExistingValueForKey = false, $accessToken = null) {
//        $key = self::translateKey($key, $accessToken);
//        if (is_bool($value) || is_int($value) || is_float($value)) {
//            $compress = FALSE;
//        }
//        else {
//            $compress = MEMCACHE_COMPRESSED;
//        }
//        $cache = self::getHandler($host, $port);
//        if (!$doNotOverwriteExistingValueForKey) {
//            $added = \memcache_set($cache, $key, $value, $compress,
//                    $timeUntilExpirationInSeconds);
//            \memcache_close($cache);
//            if (!$added) {
//                $value = print_r($value, TRUE);
//                throw new \Exception("Could not add key $key with value $value to the cache.");
//            }
//        }
//        else {
//            $added = \memcache_add($cache, $key, $value, $compress,
//                    $timeUntilExpirationInSeconds);
//            \memcache_close($cache);
//            if (!$added) {
//                $value = print_r($value, TRUE);
//                throw new \Exception("Could not add key $key with value $value to the cache. Its possible that $key may already have a value in the cache");
//            }
//        }
//        return true;
        return false;
    }

    /**
     * Remove an item from the cache.
     * @param string $key
     * @param string $host (optional)
     * @param string $port (optional)
     * @param string $accessToken (optional) user access token
     * @throws Koncourse_Std_Err_Exception
     * @return boolean
     * @since 1.0.0
     */
    public static function remove($key, $host = CACHE_HOST, $port = CACHE_PORT,
            $accessToken = null) {
//        $key = self::translateKey($key, $accessToken);
//        $cache = self::getHandler($host, $port);
//        if (!\memcache_delete($cache, $key)) {
//            if (!\memcache_set($cache, $key, null, MEMCACHE_COMPRESSED, -1)) {
//                \memcache_close($cache);
//                throw new \Exception("Could not delete key $key from the cache.");
//            }
//        }
//        \memcache_close($cache);
//        return TRUE;
        return false;
    }

    /**
     * Translate a human readable key to one that is easily indexed.
     * @param string $key
     * user (thus making the cached data private).
     * @return string
     * @since 1.0.0
     */
    private static function translateKey($key) {
        return md5($key);
    }

}

?>
