<?php
require_once 'config.php';

class ObjectTest extends UnitTestCase{
	
	public $startTime;
	
	function testSetUp(){
		$this->startTime = Koncourse_Std_DateTime::microtime();
		TestUtilities::emptyDb();
	}
	
	function testAddPropertyValue(){
		//Test that a property can be added to an object
		$property = strtolower(Koncourse_Std_String::generateRandomString(mt_rand(1, 100)));
		$value = null;
		while(empty($value) || Koncourse::getValueType($value) == Koncourse::$VALUE_TYPES['datetime']){
			$value = TestUtilities::generateRandomValue();
		}
		$value1 = $value;
		$id = Koncourse::create(Koncourse_Std_String::generateRandomString(mt_rand(1, 100)));
		$this->assertTrue(Koncourse::addPropertyValue($id, $property, $value));
		//Test that loading a multi-valued property with only one value does not return an array (unless the value type is datetime)
		$object = Koncourse::load($id);
		$value1Type = Koncourse::getValueType($value);
		$this->assertFalse(is_array($object[$property]));
		//Test that duplicate values for the same object on the same property cannot be added
		$this->assertFalse(Koncourse::addPropertyValue($id, $property, $value));
		$object = Koncourse::load($id);
		$this->assertFalse(is_array($object[$property]));
		//Test loading a property after multiple values have been added produces an array with the correct count
		$id = Koncourse::create(Koncourse_Std_String::generateRandomString(mt_rand(1, 100)));
		$added = 0;
		foreach(range(0, mt_rand(5, 50)) as $num){
			$value = TestUtilities::generateRandomValue();
			try{
				$added = Koncourse::addPropertyValue($id, $property, $value) ? $added+1 : $added;
			}
			catch(Koncourse_Std_Err_IllegalArgumentException $e){
				$added = $added;
			}
		}
		$object = Koncourse::load($id);
		if($added > 1){
			$this->assertTrue(is_array($object[$property]));
			$this->assertEqual($added, count($object[$property]));
		}
		else{
			$this->assertFalse(is_array($object[$property]));
		}
		//Test that the consistency of the value-type for a property is enforced
		$id = Koncourse::create(Koncourse_Std_String::generateRandomString(mt_rand(1, 100)));
		Koncourse::addPropertyValue($id, $property, $value1);
		$value2 = TestUtilities::generateRandomValue();
		$value2 = $value1 != $value2 ? $value2 : false;
		if(Koncourse::getValueType($value1) == Koncourse::getValueType($value2)){
			Koncourse::addPropertyValue($id, $property, $value2);
			$object = Koncourse::load($id);
			$this->assertTrue(is_array($object[$property]));
			$this->assertEqual(2, count($object[$property]));
		}
		else{
			try{
				Koncourse::addPropertyValue($id, $property, $value2);
				$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException because value types are not consistent");
			}
			catch(Koncourse_Std_Err_IllegalArgumentException $e){
				$this->pass();
			}
		}
	}
	
	function testCreate(){
		//Test that no objects of class exist if none have been created
		$class = Koncourse_Std_String::generateRandomString(mt_rand(1, 100));
		$this->assertEqual(0, count(Koncourse::getAllObjectsOfClass($class)));
		$id = Koncourse::create($class);
		//Test that objects of class exist if one has been created
		$this->assertTrue(Koncourse::exists($id, $class));
		$this->assertNotEqual(0, count(Koncourse::getAllObjectsOfClass($class)));
		//Test that objects with classes that are named after value types cannot be created
		Koncourse::delete($id);
		$reserved = array_values(Koncourse::$VALUE_TYPES);
		$class = $reserved[mt_rand(0, count($reserved)-1)];
		try{
			Koncourse::create($class);
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $class is a reserved keyword");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
		//Test that objects with empty class names cannot be created
		try{
			Koncourse::create("");
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $class is an empty class name");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
		try{
			Koncourse::create(" ");
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $class is an empty class name");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
		try{
			Koncourse::create(null);
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $class is an empty class name");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
	}
	
	function testDelete(){
		//Test that deleted objects no longer exist
		$id = Koncourse::create(Koncourse_Std_String::generateRandomString(mt_rand(0, 100)));
		Koncourse::delete($id);
		$this->assertFalse(Koncourse::exists($id));
	}
	
	function testDisableRevisionTracking(){
		$object = Koncourse::create(TestUtilities::generateRandomClassName(), true);
		$this->assertTrue(Koncourse::shouldTrackRevisions($object));
		Koncourse::disableRevisionTracking($object);
		$this->assertFalse(Koncourse::shouldTrackRevisions($object));
		Koncourse::setPropertyValue($object, TestUtilities::generateRandomProperty(), TestUtilities::generateRandomValue());
		$history = Koncourse::loadRevisionHistory($object);
		$this->assertTrue(empty($history));	
	}
	
	function testExists(){
		//Test that objects exist if created
		$existingClass = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		$nonExistingClass = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		$id = Koncourse::create($existingClass);
		$this->assertTrue(Koncourse::exists($id));
		//Test that objects of a certain class exist if an object of that class has been create 
		$this->assertTrue(Koncourse::exists($id, $existingClass));
		//Test that class name casing does not matter
		$this->assertTrue(Koncourse::exists($id, strtoupper($existingClass)));
		$this->assertTrue(Koncourse::exists($id, strtolower($existingClass)));
		//Test that objects of a certain class do not exist of an object of that class has not been created 
		$this->assertFalse(Koncourse::exists($id, $nonExistingClass));
		Koncourse::delete($id);
		//Test that deleted objects do not exist
		$this->assertFalse(Koncourse::exists($id, $existingClass));
		$this->assertFalse(Koncourse::exists($id));
	}
	
	function testGetAllObjectsOfClass(){
		$class = Koncourse_Std_String::generateRandomString(mt_rand(1, 100));
		$objsInClass = array();
		$objsOutClass = array();
		$countIn = mt_rand(5,50);
		$countOut = mt_rand(5,50);
		for($i = 0; $i < $countIn; $i++){
			$objsInClass[] = Koncourse::create($class);
		}
		for($i = 0; $i < $countOut; $i++){
			$objsOutClass[] = Koncourse::create(Koncourse_Std_String::generateRandomString(mt_rand(21,30)));
		}
		foreach($objsInClass as $obj){
			$this->assertTrue(in_array($obj, Koncourse::getAllObjectsOfClass($class)));
		}
		foreach($objsOutClass as $obj){
			$this->assertFalse(in_array($obj, Koncourse::getAllObjectsOfClass($class)));
		}
	}
	
	function testGetValueType(){
		$float = mt_rand(-999,999).".".mt_rand(1, 999);
		$string = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		$int = mt_rand(-999, 999);
		$objectClass = Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
		$object = Koncourse::create($objectClass);
		$boolean = time()%2 ? true : false;
		$boolean2 = time()%2 ? "false" : "true";
		$datetime = date("M-d-Y H:i:s");
		$this->assertEqual(Koncourse::getValueType($float), Koncourse::$VALUE_TYPES['float']);
		$this->assertEqual(Koncourse::getValueType($string), Koncourse::$VALUE_TYPES['string']);
		$this->assertEqual(Koncourse::getValueType($int), Koncourse::$VALUE_TYPES['integer']);
		$this->assertEqual(Koncourse::getValueType($object), strtoupper($objectClass));
		$this->assertEqual(Koncourse::getValueType($boolean), Koncourse::$VALUE_TYPES['boolean']);
		$this->assertEqual(Koncourse::getValueType($boolean2), Koncourse::$VALUE_TYPES['boolean']);
		$this->assertEqual(Koncourse::getValueType($datetime), Koncourse::$VALUE_TYPES['datetime']);
	}
	
	function testSetPropertyValue(){
		//Test that properties named after intrinsic properties cannot be created
		$id = Koncourse::create(Koncourse_Std_String::generateRandomString(mt_rand(1, 100)));
		$reserved = array_values(Koncourse::$INTRINSIC_PROPERTIES);
		$property = $reserved[mt_rand(0, count($reserved)-1)];
		$value = TestUtilities::generateRandomValue();
		try{
			Koncourse::setPropertyValue($id, $property, $value);
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $property is a reserved keyword");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
		//Test that properties with empty key names cannot be created
		try{
			Koncourse::setPropertyValue($id, null, $value);
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $property is has an empty key name");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
		try{
			Koncourse::setPropertyValue($id, "", $value);
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $property is has an empty key name");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
		try{
			Koncourse::setPropertyValue($id, " ", $value);
			$this->fail("Expecting Koncourse_Std_Err_IllegalArgumentException exception because $property is has an empty key name");
		}
		catch(Koncourse_Std_Err_IllegalArgumentException $e){
			$this->pass();
		}
		//Test that values are set correctly
		$property = Koncourse_Std_String::generateRandomString(mt_rand(1, 100));
		Koncourse::setPropertyValue($id, $property, $value);
		if(Koncourse::getValueType($value) == Koncourse::$VALUE_TYPES['datetime']){
			$array = Koncourse::load($id, $property);
			$this->assertEqual($value, $array['timestring']);
		}
		else{
			$this->assertEqual($value, Koncourse::load($id, $property));
		}
		//Test that previously set properties are overridden
		$value2 = TestUtilities::generateRandomValue();
		$value2 = $value != $value2 ? $value2 : false;
		Koncourse::setPropertyValue($id, $property, $value2);
		if(Koncourse::getValueType($value) == Koncourse::$VALUE_TYPES['datetime']){
			$array = Koncourse::load($id, $property);
			$this->assertNotEqual($value, $array['timestring']);
			$this->assertEqual($value2, $array['timestring']);
		}
		else{
			$this->assertNotEqual($value, Koncourse::load($id, $property));
			$this->assertEqual($value2, Koncourse::load($id, $property));		
		}
	}
	
	function testTearDown(){
		$elapsed = Koncourse_Std_DateTime::getExecutionTimeString($this->startTime);
		println("Run time was $elapsed");
	}
}
?>