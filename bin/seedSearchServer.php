#!/usr/bin/php
<?php
require_once dirname(__FILE__) . "/../src/require.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

use \org\cinchapi\aubrey\util\System;
use \org\cinchapi\aubrey\util\Time;
use org\cinchapi\aubrey\internal\Database;

$options = array(
    array('host', 'o', 'the database host, defaults to the host in prefs.php', System::OPTION_OPTIONAL),
    array('name', 'n', 'the database name, defaults to the name in prefs.php', System::OPTION_OPTIONAL),
    array('user', 'u', 'the database user, defaults to the user in prefs.php', System::OPTION_OPTIONAL),
    array('pass', 'p', 'the database password, defaults to the password in prefs.php',
        System::OPTION_OPTIONAL)
);
$inputs = System::getCliArgs($options);
extract($inputs);
$host = !empty($host) ? $host : SEARCH_DB_HOST;
$name = !empty($name) ? $name : SEARCH_DB_NAME;
$user = !empty($user) ? $user : SEARCH_DB_USER;
$pass = !empty($pass) ? $pass : SEARCH_DB_PASS;
$start = microtime(true);
seed_database($host, $name, $user, $pass);
$elapsed = Time::getElapsedTimeString($start);
System::println("Created database $name at $host for Koncourse search server in $elapsed");
exit(0);

/**
 * Seed a database with the Koncourse schema
 * @param string $host
 * @param string $name
 * @param string $user
 * @param string $pass
 * @return void
 * @since 1.0.0
 * @ignore
 */
function seed_database($host, $name, $user, $pass) {
    try {
        $handler = Database::getHandler($host, $name, $user, $pass);
        $sql = "DROP DATABASE $name";
        $handler->exec($sql);
    }
    catch (\PDOException $e) {
        $handler = new PDO("mysql:host=$host", $user, $pass,
                array(PDO::ATTR_PERSISTENT => true));
    }
    $sql = "CREATE DATABASE $name";
    $handler->exec($sql);
    $handler = Database::getHandler($host, $name, $user, $pass);
}
?>