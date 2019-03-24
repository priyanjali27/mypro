<?php

class Plan extends Controller{
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
       /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: index
		DESCRIPTION: list of plan model
	   *************************************/
	public function index(){ 
			$data['session'] = $_SESSION;
			$data['language'] = $this->language;
			$data['title']	 = "Plan Management";
			$data['heading'] = "Point";
			$data['action']  ='list';
			$data['success'] = flesh('success');
			$data['error']   = flesh('error');
			$item_per_page   = 10; //item to display per page
			$page_url        = baseurl."admin/plan/index/"; //URL
			$pageno			 = $this->url->segment(4); // Get page number
			$sort 			 = 'plan_model_id';
			$this->db->flush_cache();
			$this->db->select('tabqy_plan_model.*');
			$this->db->from('tabqy_plan_model');
			$this->db->order_by('tabqy_plan_model.'.$sort);
			$query = $this->db->get();
			$data['results']=$this->db->result($query);
			$this->db->select('tabqy_plan.*');
			$this->db->from('tabqy_plan');
			$query = $this->db->get();
			$data['resultss']=$this->db->result($query);
			  $data['privillage'] = $this->privillage;
			view_loader('admin/plan/add.html',$data);		
			}
				 
	/*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 30-04-2018
		FUNCTION: add_plan
		DESCRIPTION: For Add a plan 
	 *************************************/
    public function add_plan(){
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
        $data['title']="Plan Management";
		$data['heading']="plan";
		$data['success'] = flesh('success');
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action'] = 'add';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		//echo "string";
		if(isset($_POST['add']) && !empty($_POST)){
			$adds = $_POST;
		
			$this->validation->validate("name"," name","required");
			$add_data = array(
					'plan_name'        =>$adds['name'],
					'plan_price'       =>$adds['price'],
					'plan_description' =>$adds['description'],
				);
						  
				$this->db->insert("tabqy_plan",$add_data);
				$data['msg']="true";
				$data['success_message']="Page Added Successfully";
				redirect('admin/plan');
		
		}else{
			view_loader('admin/plan/add.html',$data);
		}
    }
      
      /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 30-04-2018
		FUNCTION: delete
		DESCRIPTION: For Delete a plan 
	    *************************************/
	Public function delete($plan_id){	
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
			$this->db->where('plan_id', $plan_id);
			$this->db->delete('tabqy_plan'); 
			$this->db->where('plan_detail_plan_id',$plan_id);
			$this->db->delete('tabqy_plan_detail'); 
			$data['error']           = "false";
			$data['msg']             = "false";
			$data['success_message'] = "Plan successfully deleted";
			echo json_encode($data);
			 exit();
			}
     /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: updateStatus
		DESCRIPTION: To update the status of plan
	   *************************************/	
	function updateStatus($id, $value){
		  $this->db->where('plan_id', $id);
		  $this->db->update('tabqy_plan',array('plan_status'=>$value));
		  $this->db->select('plan_status,plan_id');
		  $this->db->from('tabqy_plan');
		  $this->db->where('plan_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }
       
      /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 30-04-2018
		FUNCTION: edit_plan
		DESCRIPTION: For Edit a plan 
	  *************************************/
    Public function edit_plan($plan_id){
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
        $this->validation->validate("name","Name","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']= $_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			$add_data = array(
					'plan_name'        =>$_POST['name'],
					'plan_price'       =>$_POST['price'],
					'plan_description' =>$_POST['description'],
				);
			$data['set_data']= $add_data;
			$this->db->where('plan_id', $plan_id);
			$this->db->update('tabqy_plan', $data['set_data']);
			 $data['error']="false";
				$data['msg']="true";
				$data['success_message']="Plan description updated successfully";
				echo json_encode($data);die;
			}
			
	}

}
?>