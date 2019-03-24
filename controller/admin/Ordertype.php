<?php
class Ordertype extends Controller{
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
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: index() 
	  DESCRITION: Used for listing of order type
	*************************************/
	public function index(){ 
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['title']	 = "Global Settings";
		$data['heading'] = "Order type";
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/Ordertype/index/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'ordertype_id';

		$search = '0';
		$get_total_rows  = $this->db->count_all_results('tabqy_ordertype'); //Total no. of values
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
		$this->db->select('t1.*,t2.ordertype_name as t2ordertype_name');
		$this->db->from('tabqy_ordertype t2');
		$this->db->join('tabqy_ordertype t1','t1.ordertype_orderby_id=t2.ordertype_id','RIGHT');
		$this->db->order_by('t1.ordertype_id','DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$this->db->flush_cache();
		$this->db->select('tabqy_ordertype.ordertype_id,tabqy_ordertype.ordertype_name');
		$this->db->from('tabqy_ordertype');
		$this->db->where('tabqy_ordertype.ordertype_status',1);
		$this->db->where('tabqy_ordertype.ordertype_orderby_id',0);
		$this->db->order_by('tabqy_ordertype.ordertype_id','DESC');
		$query1 = $this->db->get();
		$data['orderby']=$this->db->result($query1);
		
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
		view_loader('admin/country/Ordertype.html',$data);		
	}
	
	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: add() 
	  DESCRITION: Used to add order type
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
		$this->validation->validate("ordertype_name","Name","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit'], $_POST['ordertype_id']);
	
			$data['send_data']['ordertype_name']=$_POST['ordertype_name'];
			$data['send_data']['ordertype_orderby_id']=$_POST['ordertype_orderby_id'];
			$data['send_data']['ordertype_status'] = 1;			
			$data['send_data']['ordertype_created'] = date('Y-m-d H:i:s');
			$insert_id=$this->db->insert('tabqy_ordertype', $data['send_data']);
			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Order type Added Successfully";
			echo json_encode($data);die;
		}
    }
	
	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: delete('order type id') 
	  DESCRITION: Used to delete order type
	*************************************/
	Public function delete($Ordertype_id){
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
			$this->db->where('Ordertype_id', $Ordertype_id);
            $this->db->delete('tabqy_Ordertype'); 
			$data['error']           = "false";
			$data['msg']             = "false";
			$data['success_message'] = "Order Type successfully deleted";
			echo json_encode($data);
			exit();
		
	}
	
	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: edit('order type id') 
	  DESCRITION: Used to edit/update order type
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
		$this->validation->validate("ordertype_name","Name","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']= $_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			$data['set_data']= $_POST;
			$this->db->where('ordertype_id', $user_id);
			$this->db->update('tabqy_ordertype', $data['set_data']);
			 $data['error']="false";
				$data['msg']="true";
				$data['success_message']="Order type Updated Successfully";
				echo json_encode($data);die;
		}		
		
	}
	
	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: updateStatus('order type id', 'new status value') 
	  DESCRITION: Used to change status of order type
	*************************************/
	function updateStatus($id, $value){
		  $this->db->flush_cache();
		  $this->db->where('ordertype_id', $id);
		  $this->db->update('tabqy_ordertype',array('ordertype_status'=>$value));
		  
		  $this->db->flush_cache();
		  $this->db->select('ordertype_status,ordertype_id');
		  $this->db->from('tabqy_ordertype');
		  $this->db->where('ordertype_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }
}