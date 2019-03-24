<?php
class Paymentmethod extends Controller{
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
	  DEVELOPER: Priyanjali	  
	  DATE: 30-04-2018
	  FUNCTION:  index()
	  DESCRITION: used for listing of all payment methods 
	*************************************/
	public function index(){ 
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['title']	 = "Global Settings";
		$data['heading'] = "Payment Method";
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/paymentmethod/index/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'paymentmethod_id';

		$search = '0';
		$get_total_rows  = $this->db->count_all_results('tabqy_paymentmethod'); //Total no. of values
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
		$this->db->select('tabqy_paymentmethod.*');
		$this->db->from('tabqy_paymentmethod');
		$this->db->order_by('tabqy_paymentmethod.'.$sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
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
		view_loader('admin/country/paymentmethod.html',$data);		
	}
	
	/*************************************
	  DEVELOPER: Priyanjali	  
	  DATE: 30-04-2018
	  FUNCTION:  add()
	  DESCRITION: used to add payment method 
	*************************************/
	public function add(){
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
		
		$this->validation->validate("paymentmethod_name","Name","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit'], $_POST['paymentmethod_id']);
			$data['send_data']= $_POST;
			
			$data['send_data']['paymentmethod_status'] = 1;			
			$data['send_data']['paymentmethod_created'] = date('Y-m-d H:i:s');
			$this->db->insert('tabqy_paymentmethod', $data['send_data']);
			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Payment Method Added Successfully";
			echo json_encode($data);die;
		}
    }
	
	/*************************************
	  DEVELOPER: Priyanjali	  
	  DATE: 30-04-2018
	  FUNCTION:  delete('payment method id')
	  DESCRITION: used to delete payment method 
	*************************************/
	Public function delete($paymentmethod_id){
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
		//privillage checking and user validating end here.
		
			$this->db->where('paymentmethod_id', $paymentmethod_id);
            $this->db->delete('tabqy_paymentmethod'); 
			$data['error']           = "false";
			$data['msg']             = "false";
			$data['success_message'] = "Payment Method successfully deleted";
			echo json_encode($data);
			exit();	
		
	}
	
	/*************************************
	  DEVELOPER: Priyanjali	  
	  DATE: 30-04-2018
	  FUNCTION:  edit('payment method id')
	  DESCRITION: used to edit/update payment method 
	*************************************/
	Public function edit($user_id){
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
		$this->validation->validate("paymentmethod_name","Name","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']= $_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			$data['set_data']= $_POST;
			$this->db->where('paymentmethod_id', $user_id);
			$this->db->update('tabqy_paymentmethod', $data['set_data']);
			 $data['error']="false";
				$data['msg']="true";
				$data['success_message']="Payment Method Updated Successfully";
				echo json_encode($data);die;
		}		
		
	}
	
	/*************************************
	  DEVELOPER: Priyanjali	  
	  DATE: 30-04-2018
	  FUNCTION:  updateStatus('Payment method id', 'new status value')
	  DESCRITION: used to change status of payment method 
	*************************************/
	function updateStatus($id, $value){
		$this->db->flush_cache();
		$this->db->where('paymentmethod_id', $id);
		$this->db->update('tabqy_paymentmethod',array('paymentmethod_status'=>$value));
		$this->db->flush_cache();
		$this->db->select('paymentmethod_status,paymentmethod_id');
		$this->db->from('tabqy_paymentmethod');
		$this->db->where('paymentmethod_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	 }
}