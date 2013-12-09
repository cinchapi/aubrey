#!/usr/bin/php
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once dirname(__FILE__)."/../Testable.php";

$id = createKoncourseObjectBenchmark();
setKoncourseObjectPropertiesBookmark($id);
addKoncourseObjectPropertiesBookmark($id);

function addKoncourseObjectPropertiesBookmark($id){
	$total = 10000;
	println("Running benchmark for adding $total properties on a Koncourse_Object");
	$start = microtime(true);
	$property = Koncourse_Std_String::generateRandomString(mt_rand(10, 1000));
	foreach(range(0, $total) as $num){
		$value = Koncourse_Std_String::generateRandomString(mt_rand(10, 1000));
		Testable::addPropertyValue($id, $property, $value);
	}
	$elapsed = Koncourse_Std_DateTime::getExecutionTimeString($start);
	println("Added $total properties on a Koncourse_Object in $elapsed");
	println("...");
}

/**
 * Create a single Koncourse_Object
 * @return int the object's id
 */
function createKoncourseObjectBenchmark(){
	println("Running benchmark for creating a Koncourse_Object");
	$class = Koncourse_Std_String::generateRandomString();
	$start = microtime(true);
	$id = Testable::create($class);
	$elapsed = Koncourse_Std_DateTime::getExecutionTimeString($start);
	println("Created a Koncourse_Object in $elapsed");
	println("...");	
	return $id;
}

/**
 * Set 10k properties on an object
 */
function setKoncourseObjectPropertiesBookmark($id){
	$total = 10000;
	println("Running benchmark for setting $total properties on a Koncourse_Object");
	$start = microtime(true);
	foreach(range(0, $total) as $num){
		$property = Koncourse_Std_String::generateRandomString(mt_rand(10, 1000));
		$value = Koncourse_Std_String::generateRandomString(mt_rand(10, 1000));
		Testable::setPropertyValue($id, $property, $value);
	}
	$elapsed = Koncourse_Std_DateTime::getExecutionTimeString($start);
	println("Set $total properties on a Koncourse_Object in $elapsed");
	println("...");
}
?>