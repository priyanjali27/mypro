<?php

class otherSettings extends Controller {

    var $language;
    var $session;
    var $name;

    public function __construct() {
        parent::__construct();

        if (is_logged_in() == FALSE) {
            redirect('admin/dashboard');
        }
        $this->privillage = $this->privillage();
        if ($_SESSION['userdata']['user_role'] != 0) {
            if (empty($this->privillage['add']) && empty($this->privillage['edit']) && empty($this->privillage['delete'])) {
                redirect('admin/dashboard');
            } else {
                if ($this->privillage['locationstatus'] == true) {
                    $_SESSION['locationcode'] = $this->privillage['locationcode'];
                }
            }
        }
        $this->db->flush_cache();
        $this->db->select('tabqy_language.language_name,tabqy_language.language_code,tabqy_language.language_flag');
        $this->db->from('tabqy_language');
        $this->db->order_by('tabqy_language.language_id', 'DESC');
        $query = $this->db->get();
        $this->language = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('tabqy_user.user_name');
        $this->db->from('tabqy_user');
        $userid = $_SESSION['userdata']['user_id'];
        $this->db->where('tabqy_user.user_id', $userid);
        $user = $this->db->get();
        $name = $this->db->result($user);
        $_SESSION['userdata']['user_name'] = $name[0]['user_name'];
        if (empty($_SESSION['lang_code'])) {
            $_SESSION['lang_code'] = "en";
            include_once "core/lang/lang_" . $_SESSION['lang_code'] . ".php";
        } else {
            include_once "core/lang/lang_" . $_SESSION['lang_code'] . ".php";
        }
    }
    
    public function index(){
        $data['title']="User";
        $data['heading']="Floor";
	$data['session'] = $_SESSION;        
        
        if(isset($_POST['add_settings'])){
            unset($_POST['add_settings']);            
            if($_FILES['site_logo']['name']!=''){
                $this->upload();
                if($this->get_value('site_logo')==''){
                    $this->db->insert('tabqy_othersettings',array('othersettings_key'=>'site_logo','othersettings_value'=>$_FILES['site_logo']['name'],'othersettings_created'=>date('Y-m-d H:i:s')) );
                }else{
                    $this->db->where('othersettings_key','site_logo');
                    $this->db->update('tabqy_othersettings',array('othersettings_value'=>$_FILES['site_logo']['name']));
                }
            }
            $setting_data = $_POST;
            
            foreach($setting_data as $key=>$value){                
                if($this->get_value($key)==''){
                    $this->db->insert('tabqy_othersettings',array('othersettings_key'=>$key,'othersettings_value'=>$value,'othersettings_created'=>date('Y-m-d H:i:s')) );
                }else{
                    $this->db->where('othersettings_key',$key);
                    $this->db->update('tabqy_othersettings',array('othersettings_value'=>$value) );
                }
            }
         //   echo "<pre>"; print_r($_POST);exit;
        }
        $data['telephone'] = $this->get_value('telephone');
        $data['address'] = $this->get_value('address');
        $data['plan_emails'] = $this->get_value('plan_emails');
        $data['site_logo'] = $this->get_value('site_logo');
        $data['first_rem_days'] = $this->get_value('first_rem_days');
        $data['first_reminder_text'] = $this->get_value('first_reminder_text');
        $data['second_rem_days'] = $this->get_value('second_rem_days');
        $data['second_reminder_text'] = $this->get_value('second_reminder_text');
         
        
        view_loader('admin/othersettings/index.html',$data);
    }
    
    public function get_value($key){
        $this->db->select('othersettings_value');
        $this->db->from('tabqy_othersettings');
        $this->db->where('othersettings_key',$key);
        $query = $this->db->get();
        $value = $this->db->row($query);
        return $value;
    }
    
    	public function upload(){
		$target_dir = "upload/logo/";
		$target_file = $target_dir . basename($_FILES["site_logo"]["name"]);
		$uploadOk = 1;
		$upload_data['success'] =array();
                $upload_data['error'] = array();
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$check = getimagesize($_FILES["site_logo"]["tmp_name"]);
		// Check if image file is a actual image or fake image
		if($check !== false) {
			//echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			$upload_data['error'][] = "File is not an image.";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			$upload_data['error'][] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$upload_data['error'][] = "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["site_logo"]["tmp_name"], $target_file)) {
				$upload_data['success'][] = $_FILES["site_logo"];
			} else {
				$upload_data['error'][] = "Sorry, there was an error uploading your file.";
			}
		}
		return $upload_data;
	}
    
    
}
