<?php 
class Branchesaccess extends Controller{
    var $language; 
    var $session;
    var $name;
    var $resturants;
    public function __construct()
    {
        parent::__construct();
        
        if (is_logged_in()==FALSE)
        {
            redirect('admin/dashboard');
        }
       $this->privillage = $this->privillage();
        // print_r($this->privillage);
      if ($this->privillage['locationstatus'] == true) {
                $_SESSION['locationcode'] = $this->privillage['locationcode'];
            }


        if(empty($_SESSION['lang_code'])) {
            $_SESSION['lang_code']="en";
            include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
        } else{
            include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
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
        $userid=$_SESSION['userdata']['user_id'];
        $this->db->where('tabqy_user.user_id', $userid);
        $user = $this->db->get();
        $name = $this->db->result($user);
        $_SESSION['userdata']['user_name']=$name[0]['user_name'];
        if(empty($_SESSION['lang_code'])) {
            $_SESSION['lang_code']="en";
            include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
        } else{
            include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
        }
    
    }


   
    public function index(){
        $data['title'] = "Branches";
        $data['session']            = $_SESSION;
        $data['language']           = $this->language;
        $data['success_massage']    = flesh('success_message');
        //print_r( $_SESSION['locationcode']);
        $location = "'" . implode ( "', '",  $_SESSION['locationcode'] ) . "'";
        //print_r($location);
        
        $this->db->flush_cache();
        $this->db->select('*');
        $this->db->from('tabqy_resturantbrand');
        $l= "resturantbrand_location IN ($location)";
        $this->db->where($l,null,false);
        $resquery = $this->db->get();
       // echo $this->db->last_query();
       $data['results']=$this->db->result($resquery);
       // echo'<pre>';
       // print_r($data['results']);
        $data['privillage']            = $this->privillage;
        view_loader('admin/resturant/branchaccess.html', $data);
        
       
    }
    
    

    

    
    
}
?>