<?php
require_once "config.php";

class TestUtilities{
	
	static function emptyDb(){
		return Koncourse_Std_Database::getHandler()->exec("DELETE FROM koncourse_ids WHERE 1=1") > 0;
	}
	
	static function generateArrayWithEmptyValues($size=10){
		$size--;
		$array = array();
		for($count=0; $count < $size; $count++){
			$num = rand(0,10);
			if($num == 3 || $num == 6 || $num == 9){
				$value = Koncourse_Std_String::generateRandomString();
			}
			else if($num == 2 || $num == 4){
				$value = "";
			}
			else if($num == 8){
				$value = self::generateArrayWithEmptyValues($size/2);
			}
			else if($num == 10){
				$value = null;
			}
			else{ //1,5.7
				$value = rand(-100,100);
			}
			$array[] = $value;
		}
		$array[] = null;
		return $array;
	}
	
	static function generateArrayWithNoEmptyValues($size=10){
		$array = array();
		for($count=0; $count < $size; $count++){
			$num = rand(0,10);
			if($num%2){
				$value = Koncourse_Std_String::generateRandomString();
			}
			else{
				$value = rand(-100,100); 
			}
			$array[] = $value;
		}
		return $array;
	}
	
	
	static function generateRandomAddress(){
		$addressNumber = rand(0, 9999);
		$addressStreet = KStringUtils::generateRandomString();
		$directions = array('','N','S', 'E', 'W');
		$dirIndex = rand(0, count($directions)-1);
		$direction = $directions[$dirIndex];
		$suffices = array("Ave", "St.", "Dr.", "Blvd", "Ctr", "Pkwy");
		$suffixIndx = rand(0, count($suffices)-1);
		$suffix = $suffices[$suffixIndx];
		return "$addressNumber $direction $addressStreet $suffix";
	}
	
	static function generateRandomClassName(){
		return Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
	}
	
	static function generateRandomProperty(){
		return Koncourse_Std_String::generateRandomString(mt_rand(0, 100));
	}
	
	static function generateRandomSecurePassword(){
		return KStringUtils::generateRandomString(10)."1!";
	}
	
	static function generateRandomUnsecurePassword(){
		return KStringUtils::generateRandomString(6);
	}
	
	static function generateRandomValue(){
		$class = Koncourse_Std_String::generateRandomString(mt_rand(1, 100));
		$value = time()%2 ? (time()%4 ? mt_rand(-999,999) : (time()%6 ? mt_rand(-999,999).".".mt_rand(1,99999) : 
				Koncourse_Std_String::generateRandomString(mt_rand(1,100)))) : (time()%5 ? Koncourse::create($class) : 
						(time()%3 ? true : date("M-d-Y H:i:s")));
		return $value;
	}
}
?>