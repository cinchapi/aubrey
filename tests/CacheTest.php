<?php
require_once 'config.php';

class CacheTest extends UnitTestCase{
	
	function testPut(){
		$key = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		$value = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		Koncourse_Std_Cache::put($key, $value);
		$this->assertEqual($value, Koncourse_Std_Cache::get($key));
		Koncourse_Std_Cache::remove($key);
	}
	
	function testGet(){
		$key = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		$this->assertNull(Koncourse_Std_Cache::get($key));
		$value = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		Koncourse_Std_Cache::put($key, $value);
		$this->assertNotNull(Koncourse_Std_Cache::get($key));
		$this->assertEqual($value, Koncourse_Std_Cache::get($key));
		Koncourse_Std_Cache::remove($key);
	}
	
	function testRemove(){
		$key = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		$value = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		Koncourse_Std_Cache::put($key, $value);
		$this->assertNotNull(Koncourse_Std_Cache::get($key));
		Koncourse_Std_Cache::remove($key);
		$this->assertNull(Koncourse_Std_Cache::get($key));
	}
}
?>