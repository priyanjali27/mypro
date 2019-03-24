<?php 
// Brand controller 
class Brand extends Controller{
	var $language; 
	var $session;
	var $name;
	public function __construct()
    {
      parent::__construct();
		
		if (is_logged_in_resturant()==FALSE)
        {
            redirect('resturant/admin_login');
        }
		$this->db->flush_cache();
		$this->db->select('tabqy_language.language_name,tabqy_language.language_code,tabqy_language.language_flag');
		$this->db->from('tabqy_language');
		$this->db->order_by('tabqy_language.language_id','DESC');
		$query = $this->db->get();
		$this->language=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('tabqy_user.user_name,tabqy_resturantbrand.resturantbrand_displayname as resturant_name');
		$this->db->from('tabqy_user');
		$this->db->join('tabqy_resturantbrand','tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id');
	    $user_id=$_SESSION['resturant_userdata']['user_id'];
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		$this->db->where('tabqy_user.user_id', $user_id);
		$this->db->where('tabqy_user.user_restaurant_id', $restaurant_id);
		
		$user = $this->db->get();
		$name=$this->db->result($user);
		$_SESSION['resturant_userdata']['user_name']=$name[0]['user_name'];
		$_SESSION['resturant_userdata']['resturant_name']=$name[0]['resturant_name'];

		if(empty($_SESSION['lang_code'])) {
			$_SESSION['lang_code']="en";
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		} else {
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		}
    }
	
	
}
?>
		