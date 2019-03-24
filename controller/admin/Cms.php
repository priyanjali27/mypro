<?php
class Cms extends Controller{
	var $language; 
	var $session;
	var $name;
	public function __construct()
    {
        parent::__construct();
		date_default_timezone_set("Asia/Calcutta");
		if (is_logged_in()==FALSE)
        {
            redirect('admin/dashboard');
        }
        $this->privillage = $this->privillage();
            if ($_SESSION['userdata']['user_role'] != 0) {
            if (empty($this->privillage['add']) && empty($this->privillage['edit']) && empty($this->privillage['delete'])) {
                redirect('admin/dashboard');
            } else{
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
        $userid = $_SESSION['userdata']['user_id'];
        $this->db->where('tabqy_user.user_id', $userid);
        $user = $this->db->get();
        $name=$this->db->result($user);
        $_SESSION['userdata']['user_name'] = $name[0]['user_name'];
        if(empty($_SESSION['lang_code'])) {
                $_SESSION['lang_code']="en";
                include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
        } else{
                include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
        } 
	}
	public function index(){ 

		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/cms/index/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'page_id';

		$search = '0';
		$get_total_rows  = $this->db->count_all_results('tabqy_page'); //Total no. of values
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
		$this->db->select('tabqy_page.*');
		$this->db->from('tabqy_page');
		$this->db->order_by('tabqy_page.'.$sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="Fee";
		$data['heading']="cms";
		$data['user_id']="";
		$data['page_number']=$page_number;
		if($search=='0'){
			$data['searched']="";
                }else{
                    $data['searched']=$search;	
		}
                    $data['search']=$search;
                    $start = $page_position+1;
                    $data['start'] = $start;
                    $data['end'] = $start+count($data['results'])-1;
		view_loader('admin/cms/index.html',$data);		
	}
	public function upload(){
		$target_dir = "upload/cms_images/";
		$target_file = $target_dir . basename($_FILES["page_banner"]["name"]);
		$uploadOk = 1;
		$upload_data['success'] =array();
                $upload_data['error'] = array();
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$check = getimagesize($_FILES["page_banner"]["tmp_name"]);
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
			if (move_uploaded_file($_FILES["page_banner"]["tmp_name"], $target_file)) {
				$upload_data['success'][] = $_FILES["page_banner"];
			} else {
				$upload_data['error'][] = "Sorry, there was an error uploading your file.";
			}
		}
		return $upload_data;
	}
	
	//Add Page
	public function add(){
		$data['title']="Fee";
		$data['heading']="cms";
		$data['success'] = flesh('success');
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action'] = 'add';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
                $languages       = $this->language;
		
		if(isset($_POST['add']) && !empty($_POST)){
                    //privillage checking and user validating 
                    if ($_SESSION['userdata']['user_role'] != 0) {

                        if ($this->privillage['add'] != 1) {
                            $data['error']           = "permission";
                            $data['msg']             = "false";
                            $permission_msg = set_flesh('permission_message', "You don't have permission to perform this action");
                            $data['permission_message'] = flesh('permission_message'); 
                            view_loader('admin/cms/add.html',$data);
                            exit;
                        }
                    }
			$adds = $_POST;
			$this->validation->validate("page_title","Page Title","required");
			if($_FILES['page_banner']['name']!=''){
                            $this->upload();                            
                        }
			if($this->validation->validation_check() == true){  
                            $add_data = array('page_title'=>$adds['page_title'],
                                            'page_slug'=>$adds['page_slug'],
                                            'page_desc'=>$adds['page_desc'],
                                            'page_banner_image'=>$_FILES['page_banner']['name'],
                                            'page_status'=>1,
                                            'page_meta_title'=>$adds['page_meta_title'],
                                            'page_meta_key'=>$adds['page_meta_key'],
                                            'page_meta_desc'=>$adds['page_meta_desc'],
                                            'page_created'=>date('Y-m-d H:i:s'),
                                            'page_last_updated'=>date('Y-m-d H:i:s'));
                            $insert_id = $this->db->insert("tabqy_page",$add_data);
                                
                        foreach ($languages as $language) {
                            $edit              = ($language['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                            $add_data_language = array(
                                "page_language_page_id" => $insert_id,
                                "page_language_language_code" => $language['language_code'],
                                "page_language_page_title"=> $adds['page_title'],
                                "page_language_page_desc"=>  $adds['page_desc'],
                                "page_language_edit" => $edit,
                                "page_language_status" => '1'
                            );

                            $this->db->insert('tabqy_page_language', $add_data_language);
                        }
                    $data['msg']="true";
                    $data['success_message']="Page Added Successfully";
                    redirect('admin/cms');
		}else{
                        echo "error";                            
                }
		}else{
			view_loader('admin/cms/add.html',$data);
		}
    }
	
	// Delete Country Module
	Public function delete($page_id){
            //privillage checking and user validating 
            if ($_SESSION['userdata']['user_role'] != 0) {

                if ($this->privillage['delete'] != 1) {
                    $data['error']           = "true";
                    $data['msg']             = "false";
                    $data['success_message'] = "You don't have permission to perform this action";
                    echo json_encode($data);
                    exit();
                }
            }
            //privillage checking and user validating  end here.
            $this->db->where('page_id', $page_id);
            $this->db->delete('tabqy_page'); 
            $this->db->where('page_language_page_id', $page_id);
            $this->db->delete('tabqy_page_language');
            $data['error']           = "false";
            $data['msg']             = "false";
            $data['success_message'] = "CMS Page successfully deleted";
            echo json_encode($data);
            exit();	
	}
	
	// Edit/update Page
	Public function edit($page_id){	
		$data['title']="Fee";
		$data['heading']="cms";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action'] = 'edit';
                $lang = $this->url->segment(5);
		$data['edit_data']  = $this->edit_data($page_id,$lang);
                $data['edit_data'][0]['language_code'] = $lang; 
		
		if(isset($_POST['update']) && !empty($_POST)){
                    //privillage checking and user validating 
			if($_SESSION['userdata']['user_role'] != 0 ){ 

			if($this->privillage['edit'] != 1 ){
			$data['error']="permission";
			$data['msg']="false";
			$per = set_flesh('permission_msg', "You don't have permission to perform this action");
                        $data['permission_issue'] = flesh('permission_msg');
			view_loader('admin/cms/edit.html',$data);
                        exit;
			}
			} 
                    //privillage checking and user validating  end here.			
			$this->validation->validate("page_title","Page Title","required");
			$this->validation->validate("page_slug","Page Slug","required");
			$this->validation->validate("page_desc","Page Description","required");
			if($this->validation->validation_check() == true){  
				if($_FILES['page_banner']['name'] !=""){
					$img_data = $this->upload();
					if(isset($img_data['success'][0]['name']) && $img_data['success'][0]['name'] !=''){
						$file_name = $img_data['success'][0]['name'];					
				 	}
				}else{
					$file_name = $data['edit_data'][0]['page_banner_image'];
				}
                             
                                if ($_SESSION['lang_code'] != $lang) {
                                    $new_data = array(                                                  
                                                    'page_meta_title'=>$_POST['page_meta_title'],
                                                    'page_meta_key'=>$_POST['page_meta_key'],
                                                    'page_meta_desc'=>$_POST['page_meta_desc'],
                                                    'page_banner_image'=>$file_name,
                                                );
                                }else{
                                    $new_data = array(  
                                                    'page_title'=>$_POST['page_title'],
                                                    'page_desc'=>$_POST['page_desc'],
                                                    'page_meta_title'=>$_POST['page_meta_title'],
                                                    'page_meta_key'=>$_POST['page_meta_key'],
                                                    'page_meta_desc'=>$_POST['page_meta_desc'],
                                                    'page_banner_image'=>$file_name,
                                                    'page_last_updated'=>date('Y-m-d H:i:s')
                                                );
                                }	
                                //echo $lang;echo "<pre>"; print_r($new_data);exit;
                        $this->db->where("page_id",$page_id);
                        $edit_main = $this->db->update('tabqy_page',$new_data);
                                        
                        $this->db->flush_cache();
                        $edit_data_language = array(
                                            "page_language_page_title" => $_POST['page_title'],
                                            "page_language_page_desc" => $_POST['page_desc'],
                                            "page_language_edit" => '1'
                            );
                        $this->db->where('page_language_page_id', $page_id);
                        $this->db->where('page_language_language_code', $lang);
                        $edit_lang = $this->db->update('tabqy_page_language', $edit_data_language);

                        if($edit_main || $edit_lang){
                                redirect('admin/cms');					  
                            }
			}			
		}else{
			view_loader('admin/cms/edit.html',$data);
		}	
	}
	
	public function edit_data($id,$lang)
	{
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		//$this->db->select('*');
		$this->db->from('tabqy_page');
                $this->db->join('tabqy_page_language', "page_language_page_id = page_id", 'inner');
                $this->db->where("page_id",$id);
                $this->db->where('page_language_language_code', $lang);
		
		$query = $this->db->get();
		$data = $this->db->result($query);	
                //echo "<pre>";print_r($data);exit;
		return $data;
	}
	
	public function check_slug(){
		
		$str = $_GET['str'];
		$this->db->flush_cache();
		$this->db->select('tabqy_page.page_id');
		$this->db->from('tabqy_page');
		$this->db->like('page_slug',$str);
		return $this->db->count_all_results();
		exit;
	}
	function view(){
		$id = $this->url->segment(4);
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['title'] = "Fee";
		$data['heading'] = "cms";
		$data['action'] = 'view';
		//$data['edit_data']  = $this->edit_data($id);

		$this->db->from('tabqy_page');
                $this->db->where("page_id",$id);
		
		$query = $this->db->get();
		$data['edit_data'] = $this->db->result($query);	
                //echo "<pre>";print_r($data);exit;
		//return $data;
				
		view_loader('admin/cms/view.html',$data);
	}
	function updateStatus($id, $value){
		$this->db->flush_cache();
		$this->db->where('page_id', $id);
		$this->db->update('tabqy_page',array('page_status'=>$value));
		$this->db->flush_cache();
		$this->db->select('page_status,page_id');
		$this->db->from('tabqy_page');
		$this->db->where('page_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	 }
}