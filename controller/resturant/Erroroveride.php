<?php 

// Category controller uses for superadmin,tabqy employees,customer 
class Erroroveride extends Controller{
	var $language; 
	var $session;
	var $name;
	public function __construct()
    {	
         parent::__construct();
		if (is_logged_in_resturant()== FALSE)
        {

            redirect('resturant/user');
        }
		$this->db->flush_cache();
		$this->db->select('tabqy_language.language_name,tabqy_language.language_code,tabqy_language.language_flag');
		$this->db->from('tabqy_language');
		$this->db->order_by('tabqy_language.language_id','DESC');
		$query = $this->db->get();
		$this->language=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('tabqy_user.user_name');
		$this->db->from('tabqy_user');
		$userid=$_SESSION['resturant_userdata']['user_id'];
		$this->db->where('tabqy_user.user_id', $userid);
		$user = $this->db->get();
		$name=$this->db->result($user);
		$_SESSION['resturant_userdata']['user_name']=$name[0]['user_name'];
		if(empty($_SESSION['lang_code'])) {
			$_SESSION['lang_code']="en";
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		} else{
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		}
    }
	//show list of users .
	Public function index(){
		$data['title'] = "Error";
		$data['heading'] = "Dashboard";
		$data['action'] = 'Dashboard';
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
 	
		view_loader('resturant/erroroveride/404.html',$data);
	}
	
	
	
	
	
	
}

?>