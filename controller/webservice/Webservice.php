<?php
class Webservice extends Controller{
	
	public function __construct(){	
		 parent::__construct();
		//require APPPATH . 'third_party/server.php';
		//header("Content-type: application/json");
		//error_reporting(-1);
		//ini_set('display_errors',1);
		
	}

	public function index(){
		exit(__METHOD__);
	}
	
	
	/*==========
	function name : Login
	parameter     : keyword
	result        : city airport list
	table         : car_city,car_city_language,car_airport,car_airport_language,
	date          : 20-01-2018
	written by    : pravin kumar
	============*/
	public function login(){
		header('Content-Type: application/json');
		$inputdata = json_decode(file_get_contents("php://input"),true);
		print_r($inputdata);
		
		echo json_encode($data);
		
	}
	
	
		
			 	 		
}
?>