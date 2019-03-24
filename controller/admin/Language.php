<?php 

class Language extends Controller{
	
	var $language; 
	var $session;
	var $name;	
	public function __construct()
    {
		
        parent::__construct();
		if (is_logged_in()==FALSE)
        {
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
		$this->db->order_by('tabqy_language.language_id','DESC');
		$query = $this->db->get();
		$this->language=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('tabqy_user.user_name');
		$this->db->from('tabqy_user');
		$userid=$_SESSION['userdata']['user_id'];
		$this->db->where('tabqy_user.user_id', $userid);
		$user = $this->db->get();
		$name=$this->db->result($user);
		$_SESSION['userdata']['user_name']=$name[0]['user_name'];
		if(empty($_SESSION['lang_code'])) {
			$_SESSION['lang_code']="en";
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		} else{
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		}
		
    }
	//show list of users .
	Public function index(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success_massage']= flesh('success_message');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/language/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number
		if(isset($_POST['search']) && $_POST['search'] !="")
		{
			$search = trim($this->input->post('search'));
		}
		else{
			$search = str_replace("%20",' ',($this->url->segment(5))?$this->url->segment(5):'0');
		}
		if($search!='0'){
			$this->db->flush_cache();
			$this->db->where("language_name LIKE '%$search%'");
		}
		$get_total_rows  = $this->db->count_all_results('tabqy_language'); //Total no. of values
		
		if(isset($pageno)){ //Get page number 
			$page_number = filter_var($pageno, FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
		if(!is_numeric($page_number)){
			die('Invalid page number!');} //incase of invalid page number
		}else{
			$page_number = 1; //if there's no page number, set it to 1
		}
		$total_pages = ceil($get_total_rows/$item_per_page);
		$page_position = (($page_number-1) * $item_per_page); //get starting position to fetch the records
		$this->db->flush_cache();
		$this->db->select('tabqy_language.*');
		$this->db->from('tabqy_language');
		if($search!='0'){
			$this->db->where("language_name LIKE '%$search%'");
		}
		
		$this->db->order_by('tabqy_language.language_id','DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="Language";
		$data['heading']="Add language";
		$data['user_id']="";
			$data['page_number']=$page_number;
		if($search=='0'){
			$data['searched']="";
        }else{
		$data['searched']=$search;	
		}
		$data['search']=$search;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		 $data['privillage'] = $this->privillage;
		view_loader('admin/language/user.html',$data);
	}
	
	//add user with roles.
	Public function add(){

		
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="add";
		$data['user_id']="";
		$data['title']="language";
		$data['heading']="Add language";
		$this->validation->validate("language_name","Name","required");
		$this->validation->validate("language_code","Code","required");
		if($this->validation->validation_check()== FALSE){
			if(!empty($this->validation->error_msg)){
			$data['error_msg']=$this->validation->error_msg;
			$data['error_msg']['language_flag']="Image field is required";
			}
			$data['set_data']=$_POST;
			view_loader('admin/language/add.html',$data);
		}else{
			unset($_POST['submit']);
			unset($_POST['language_id']);
			$data['send_data']= $_POST;
			if ($_FILES["language_flag"]["error"] > 0) {//Checking file has error or not
				$data['error_msg']['language_flag']="Return Code: " . $_FILES["language_flag"]["error"] . "<br>";
				$data['set_data']=$_POST;
				view_loader('admin/language/add.html',$data);
			} else {
				$data['error_msg']['language_flag'] = "Upload: " . $_FILES["language_flag"]["name"] . "<br>";
				$data['error_msg']['language_flag'] .="Type: " . $_FILES["language_flag"]["type"] . "<br>";
				$data['error_msg']['language_flag'] .="Size: " . ($_FILES["language_flag"]["size"] / 1024) . " kB<br>";
				$data['error_msg']['language_flag'] .="Temp file: " . $_FILES["language_flag"]["tmp_name"] . "<br>";

    if (($_FILES["language_flag"]["type"] == "image/gif") || ($_FILES["language_flag"]["type"] == "image/jpeg") || ($_FILES["language_flag"]["type"] == "image/png") || ($_FILES["language_flag"]["type"] == "image/pjpeg")) { //Checking file is an image or not

        if ($_FILES["language_flag"]["size"] < 2097152) { //checking file size is less than 2MB or not
            if (file_exists("upload/lang_flags/" . $_FILES["language_flag"]["name"])) { //Checking file already exists or not
				$data['error_msg']['language_flag'] = $_FILES["language_flag"]["name"] . " already exists. ";
				$data['set_data']=$_POST;
				view_loader('admin/language/add.html',$data);
            } else {
                move_uploaded_file($_FILES["language_flag"]["tmp_name"], "upload/lang_flags/" . $_FILES["language_flag"]["name"]);
                $data['send_data']['language_flag']=$_FILES["language_flag"]["name"];
				//$data['send_data']['language_status']=1;
				$data['send_data']['language_created']=date('Y-m-d H:i:s');
				$data['send_data']['language_updated']=date('Y-m-d H:i:s');
				
				$this->db->insert('tabqy_language', $data['send_data']);
			    set_flesh('success_message', 'New language added successfully');
			    redirect('admin/language/index');
            }
        } else {
           $data['error_msg']['language_flag'] ="ERROR: Your file was larger than 2MB in file size.";
		   $data['set_data']=$_POST;
			view_loader('admin/language/add.html',$data);
        }
    } else {
        $data['error_msg']['language_flag'] ="Please upload only Image.";
		$data['set_data']=$_POST;
		view_loader('admin/language/add.html',$data);
    }
}
		
     		
			}
		}
    
	
	// Delete user Module
	Public function delete($user_id){

		 if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['delete'] != 1) {
                $data['error']           = "true";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
		
			$this->db->where('language_id', $user_id);
            $this->db->delete('tabqy_language'); 
		die;
	}
	
	// Edit user Module
	Public function edit(){

		 if ($_SESSION['userdata']['user_role'] != 0) {             
                if ($this->privillage['edit'] != 1) {
                    $data['error']           = "permission";
                    $data['msg']             = "false";
                    $data['success_message'] = "You don't have permission to perform this action";
                    echo json_encode($data);
                    exit();
                }
            }
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="edit";
		$data['title']="language";
		$data['heading']="Edit language";
		//$data['success_massage']=flesh('success_message');
		$user_id=$this->url->segment(4);
		$data['language_id']=$user_id;
		
		if(isset($user_id) && !empty($user_id)){
		    if($_POST){
				$this->validation->validate("language_name","Name","required");
				$this->validation->validate("language_code","Code","required");
				
				if($this->validation->validation_check()== FALSE){
					$data['error_msg']=$this->validation->error_msg;
					$data['set_data']= $_POST;
					$data['set_data']['language_updated']=date('Y-m-d H:i:s');
					view_loader('admin/language/user.html',$data);
				}else{
					unset($_POST['submit']);
					$data['set_data']= $_POST;
					$this->db->where('language_id', $user_id);
					$this->db->update('tabqy_language', $data['set_data']);
					set_flesh('success_message', 'language updated successfully');
					redirect('admin/language/index');
				}
		}else{
			$this->db->select('tabqy_language.*');
			$this->db->from('tabqy_language');
			$this->db->where('tabqy_language.language_id',$user_id);
			$query = $this->db->get();
			$results= $this->db->result($query);
			$data['set_data']=$results[0];
			
			view_loader('admin/language/user.html',$data);	
		}
		}
	}
	Public function change_language(){
		$last_url=$_SERVER['HTTP_REFERER'];
		if(empty($last_url)){
			$last_url=baseurl."admin/user";
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
	function updateStatus($id, $value){
		$this->db->flush_cache();
		$this->db->where('language_id', $id);
		$this->db->update('tabqy_language',array('language_status'=>$value));
		$this->db->flush_cache();
		$this->db->select('language_status,language_id');
		$this->db->from('tabqy_language');
		$this->db->where('language_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	 }	
}

?>