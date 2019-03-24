<?php 

class Language extends Controller{
	
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
	
	Public function change_language(){

		$last_url=$_SERVER['HTTP_REFERER'];
		if(empty($last_url)){
			$last_url=baseurl."resturant/user";
		}else{
			$last_url=str_replace(baseurl,"",$last_url);
		}
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="Change";
		$data['title']="language";
		$data['heading']="Change language";
		$lang_code=$this->url->segment(4);
		$_SESSION['lang_code']=$lang_code;
		redirect($last_url);
	}
	
	
	
	
}

?>