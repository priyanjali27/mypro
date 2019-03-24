<?php 

// logout controller uses for superadmin,tabqy employees,customer 
class Logout extends Controller{
	
	//logout user
	Public function index(){
	    unset($_SESSION['userdata']); // will delete 
        session_destroy();
          redirect('admin/admin_login');
	}
}

?>