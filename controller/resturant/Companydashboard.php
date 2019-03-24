<?php 
// Dashboard controller 
class Companydashboard extends Controller{
	var $language; 
	var $session;
	var $name;
	var $get_data;
	public function __construct()
    {
      parent::__construct();
	  require "check_login.php";

    }
    
     /*************************************
		  DEVELOPER: Meenu  
		  DATE:  27/04/2018
		  FUNCTION:  get_data()
		  DESCRIPTION: Get detail data in session
	*************************************/

	function get_data(){

        $data['user_id']=$_SESSION['resturant_userdata']['user_id'];
		$data['restaurant_id']=$_SESSION['resturant_userdata']['restaurant_id'];
		$data['company_id']=$_SESSION['resturant_userdata']['user_company_id'];
		$data['language_code']=$_SESSION['lang_code'];
        return $data;
    }
	function index(){

        $data['user_id']=$_SESSION['resturant_userdata']['user_id'];
		$data['restaurant_id']=$_SESSION['resturant_userdata']['restaurant_id'];
		$data['company_id']=$_SESSION['resturant_userdata']['user_company_id'];
		$data['language_code']=$_SESSION['lang_code'];
        return $data;
    }
}?>