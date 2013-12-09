<?php
require_once 'config.php';

class ArrayTest extends UnitTestCase{
	
	function testContainsEmptyValues(){
		$this->assertTrue(Koncourse_Std_Array::containsEmptyValues(TestUtilities::generateArrayWithEmptyValues(mt_rand(0, 100))));
		$this->assertFalse(Koncourse_Std_Array::containsEmptyValues(TestUtilities::generateArrayWithNoEmptyValues(mt_rand(0, 100))));
		$this->assertTrue(Koncourse_Std_Array::containsEmptyValues(null));
		$this->assertTrue(Koncourse_Std_Array::containsEmptyValues(array()));
	}
	
	function testRemoveEmptyValues(){
		$array = TestUtilities::generateArrayWithEmptyValues(mt_rand(0,100));
		$array = Koncourse_Std_Array::removeEmptyValues($array);
		$this->assertFalse(Koncourse_Std_Array::containsEmptyValues($array));
	}
}
?>