<?php 
// login controller uses for superadmin,tabqy employees,customer 
class Admin_login extends Controller{
	
	public function __construct()
    {
        parent::__construct();
		
		if (is_logged_in()==true)
        {
            redirect('admin/dashboard');
        }
    }

	/*************************************
	  DEVELOPER:Meenu  
	  DATE:  27-04-2018
	  FUNCTION:  index()
	  DESCRIPTION:Login Superadmin/staff
	*************************************/

	Public function index(){
		$data['cookie']=$_COOKIE;	
	    $this->validation->validate("user_username","Username","required");
		$this->validation->validate("user_password","Password","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			view_loader('admin/login/login.html',$data);
		}else{
		if($_POST){
				$username=$_POST['user_username'];
				$password=$this->db->password_encrypt($_POST['user_password']);
				if(!empty($_POST["admin_remember"])) {
				setcookie ("admin_login",$_POST["user_username"],time()+ (10 * 365 * 24 * 60 * 60));
				setcookie ("admin_password",$_POST["user_password"],time()+ (10 * 365 * 24 * 60 * 60));
				setcookie ("admin_remember",$_POST["admin_remember"],time()+ (10 * 365 * 24 * 60 * 60));
				} else {
					if(isset($_COOKIE["admin_login"])) {
						setcookie ("admin_login","");
					}
					if(isset($_COOKIE["admin_password"])) {
						setcookie ("admin_password","");
					}
					if(isset($_COOKIE["admin_remember"])) {
						setcookie ("admin_remember","");
					}
				}
				$this->db->flush_cache();
				$this->db->select('tabqy_user.*');
				$this->db->from('tabqy_user');
				$this->db->where("tabqy_user.user_username",$username);
				$this->db->where("tabqy_user.user_password",$password);
        		$query = $this->db->get();
				$results= $this->db->result($query);
			if(!empty($results[0]) && ($results[0]['user_role']=='0' || $results[0]['user_role']=='1' ) ){
				    $userdata['user_role']=$results[0]['user_role'];
				    $userdata['user_id']=$results[0]['user_id'];
					$userdata['user_staffrole']=$results[0]['user_staffrole'];
					$userdata['user_default_country']=$results[0]['user_default_country'];
					$_SESSION['userdata']=$userdata;
					redirect('admin/dashboard');
			    }else{
		            set_flesh('error_message', ' Error: Username & password does not match.');
					$data['error_message']= flesh('error_message');
					view_loader('admin/login/login.html',$data);	
			}
			}else{
				 view_loader('admin/login/login.html',$data);	
			}
		}
	}		
}

?>