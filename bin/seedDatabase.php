#!/usr/bin/php
<?php
require_once dirname(__FILE__) . "/../src/require.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

use \org\cinchapi\aubrey\util\System;
use \org\cinchapi\aubrey\util\Time;
use org\cinchapi\aubrey\internal\Database;
use \PDO;

$options = array(
    array('host', 'o', 'the database host, defaults to the host in prefs.php', System::OPTION_OPTIONAL),
    array('name', 'n', 'the database name, defaults to the name in prefs.php', System::OPTION_OPTIONAL),
    array('user', 'u', 'the database user, defaults to the user in prefs.php', System::OPTION_OPTIONAL),
    array('pass', 'p', 'the database password, defaults to the password in prefs.php',
        System::OPTION_OPTIONAL)
);
$inputs = System::getCliArgs($options);
extract($inputs);
$host = !empty($host) ? $host : DB_HOST;
$name = !empty($name) ? $name : DB_NAME;
$user = !empty($user) ? $user : DB_USER;
$pass = !empty($pass) ? $pass : DB_PASS;
$start = microtime(true);
seed_database($host, $name, $user, $pass);
$elapsed = Time::getElapsedTimeString($start);
System::println("Seeded database $name at $host with the Koncourse schema in $elapsed");
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
    }
    catch (\PDOException $e) {
        $handler = new PDO("mysql:host=$host", $user, $pass,
                array(PDO::ATTR_PERSISTENT => true));
        $sql = "CREATE DATABASE $name";
        $handler->exec($sql);
        $handler = Database::getHandler($host, $name, $user, $pass);
    }
    $sqls = file(dirname(__FILE__) . "/../conf/schema.sql");
    $rowCount = 0;
    foreach ($sqls as $sql) {
        $rowCount+=!empty($sql) && !org\cinchapi\aubrey\util\Strings::startsWith("---",
                        $sql) ? $handler->exec($sql) : 0;
    }
}
?>