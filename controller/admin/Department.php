<?php 
// Department controller uses for superadmin,tabqy employees,customer 
class Department extends Controller{
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
                    $_SESSION['locationdata'] = array(
                        'status' => true,
                        'country' => $this->privillage['country'],
                        'region' => $this->privillage['region'],
                        'city' => $this->privillage['city'],
                        'zone' => $this->privillage['zone'],
                        'location' => $this->privillage['location']
                    );
                } else {
                    $_SESSION['locationdata']['status'] = false;
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
	   /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: index
		DESCRIPTION: list of department 
	    *************************************/
	Public function index(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success_massage']= flesh('success_message');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/department/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number
		$sort='department_id';
		if(isset($_POST['search']) && $_POST['search'] !="")
		{
			$search = trim($this->input->post('search'));
		}
		else{
			$search = str_replace("%20",' ',($this->url->segment(5))?$this->url->segment(5):'0');
		}
		if($search!='0'){
			$this->db->flush_cache();
			$this->db->where("department_name LIKE '%$search%'");
		}
		$get_total_rows  = $this->db->count_all_results('tabqy_department'); //Total no. of values
		
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
		$this->db->select('tabqy_department.*');
		$this->db->from('tabqy_department');
		if($search!='0'){
			$this->db->where("department_name LIKE '%$search%'");
		}
		
		$this->db->order_by('tabqy_department.'.$sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="User";
		$data['heading']="Department";
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
		view_loader('admin/department/user.html',$data);
	}
		/*************************************
			DEVELOPER: Shivank Mittal  
			DATE: 30-04-2018
			FUNCTION: add
			DESCRIPTION: For Add a department 
		 *************************************/
	Public function add(){
         //privillage checking and user validating 
        if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['add'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        $data['session'] = $_SESSION;
        $languages       = $this->language;
		
		$this->validation->validate("department_name","Name","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			unset($_POST['department_id']);
			$data['send_data']= $_POST;
			
			$data['send_data']['department_status']=1;
			$data['send_data']['department_created']=date('Y-m-d H:i:s');
			
			$insert_id = $this->db->insert('tabqy_department', $data['send_data']);
	
		  foreach ($languages as $language) {
                $edit              = ($language['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                $add_data_language = array(
                    "department_language_department_id" => $insert_id,
                    "department_language_language_code" => $language['language_code'],
                    "department_language_department_name"=>$_POST['department_name'],
                    "department_language_edit" => $edit,
                    "department_language_status" => '1'
                );
                
               $this->db->insert('tabqy_department_language', $add_data_language);
           }
			 $data['error']="false";
			$data['msg']="true";
			$data['success_message']="New Department added successfully";
			echo json_encode($data);die;
		 }
    }
       /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: delete
		DESCRIPTION: To Delete the department 
	    *************************************/
	Public function delete(){
		$department_id = $this->url->segment(4);

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
      	$this->db->where('department_id', $department_id);
            $this->db->delete('tabqy_department'); 
        $this->db->where('department_language_department_id', $department_id);
        $this->db->delete('tabqy_department_language');
        $data['error']           = "false";
        $data['msg']             = "false";
        $data['success_message'] = "Department successfully deleted";
        echo json_encode($data);
        exit();
       }

	   /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: edit_view
		DESCRIPTION: To edit the department 
	    *************************************/
	Public function edit_view(){
		$department_id = $this->url->segment(4);
		$language_code = $this->url->segment(5);
        $this->db->flush_cache();
        $this->db->from('tabqy_department');
        $this->db->join('tabqy_department_language', "department_language_department_id = department_id", 'inner');
        $this->db->where('department_id', $department_id);
        $this->db->where('department_language_language_code', $language_code);
        $query             = $this->db->get();
        $data['edit_view'] = $this->db->result($query);
		echo json_encode($data);
        die;
	}
        /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: edit
		DESCRIPTION: To edit department module
	    *************************************/
	Public function edit($department_id){  
			//privillage checking and user validating 
			if($_SESSION['userdata']['user_role'] != 0 ){ 

			if($this->privillage['edit'] != 1 ){
			$data['error']="permission";
			$data['msg']="false";
			$data['success_message']="You don't have permission to perform this action";
			echo json_encode($data);
			exit();
			}
			} 
        //privillage checking and user validating  end here.
		$this->validation->validate("department_name","Name","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']= $_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			 $language_code = $_POST['language_code'];
			 $department_name = $_POST['department_name'];
              unset($_POST['submit']);
              unset($_POST['language_code']);
            if ($_SESSION['lang_code'] == $language_code) {
                $data['set_data']= $_POST;
                $this->db->where('department_id', $department_id);
                $this->db->update('tabqy_department', $data['set_data']);
              }
               $this->db->flush_cache();
			    $edit_data_language = array(
                "tabqy_department_language.department_language_department_name" => $department_name,
                "tabqy_department_language.department_language_edit" => '1'
               );
                $this->db->where('department_language_department_id', $department_id);
	            $this->db->where('department_language_language_code', $language_code);
	            $this->db->update('tabqy_department_language', $edit_data_language);
                $data['error']="false";
				$data['msg']="true";
				$data['success_message']="Department updated successfully";
				echo json_encode($data);
				die;
		}		
	}
       /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: updateStatus
		DESCRIPTION: To Update the status of department 
	    *************************************/
	function updateStatus($id, $value){
		  $this->db->flush_cache();
		  $this->db->where('department_id', $id);
		  $this->db->update('tabqy_department',array('department_status'=>$value));
		  $this->db->flush_cache();
		  $this->db->select('department_status,department_id');
		  $this->db->from('tabqy_department');
		  $this->db->where('department_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }
}

?>