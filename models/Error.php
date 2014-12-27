<?php

if (class_exists("Error")) return; //protect against what seems to be multi-thread related error

class Error {
  public static $test_mode;
  public static $codePath;
  static $header;
  
  
  //
  static function log() {
  
  }
  
  //
  static function message($message,$method="") {    
    //if (!$method) 
      return $message;
    //else 
      //return $method ."(". $message .")";    
  }
  
  //
  static function halt($message,$method="") {     
    @header("Content-Type: text/plain");
    if (self::$codePath) {
      include self::$codePath;
      $codeNum = $map[$method];
      $status = $codes[$codeNum];
      exit(json_encode(array("code"=>$codeNum,"status"=>$status,"message"=>$message)));
    }
    else exit(json_encode(array("status"=>"Error","message"=>$message)));
    //exit(json_encode(array("status"=>"Error","message"=>$method ."(". $message .")")));   
  }  
	
	
	static function http($numCode, $mssg="") {
		if (function_exists("http_response_code")) http_response_code($numCode);
		else header("HTTP/1.1 $numCode");
		
		exit(json_encode($mssg));
	}
}

?>