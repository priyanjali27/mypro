<?php

class Orderstatus extends Controller{

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
	  DESCRITION: Listing of order status
	*************************************/
	public function index(){ 

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "Global Settings";
		
		$data['heading'] = "Order status";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/Orderstatus/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort 			 = 'Orderstatus_id';



		$search = '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_orderstatus'); //Total no. of values

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

		$this->db->select('tabqy_orderstatus.*');

		$this->db->from('tabqy_orderstatus');

		$this->db->order_by('tabqy_orderstatus.'.$sort,'DESC');

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

		view_loader('admin/country/Orderstatus.html',$data);		

	}

	

	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: add() 
	  DESCRITION: Add order status term
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

		$this->validation->validate("orderstatus_name","Name","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			unset($_POST['submit'], $_POST['orderstatus_id']);

			$data['send_data']= $_POST;

			

			$data['send_data']['orderstatus_status'] = 1;			

			$data['send_data']['orderstatus_created'] = date('Y-m-d H:i:s');

			$this->db->insert('tabqy_orderstatus', $data['send_data']);

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="Order Status Added Successfully";

			echo json_encode($data);die;

		}

    }

	

	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: delete() 
	  DESCRITION: Delete order status term
	*************************************/

	Public function delete($Orderstatus_id){

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

			$this->db->where('Orderstatus_id', $Orderstatus_id);

            $this->db->delete('tabqy_Orderstatus'); 

			$data['error']           = "false";
			$data['msg']             = "false";
			$data['success_message'] = "Order Status successfully deleted";
			echo json_encode($data);
			exit();
	}

	

	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION: edit() 
	  DESCRITION: Edit order status
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
		
		$this->validation->validate("orderstatus_name","Name","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			unset($_POST['submit']);

			$data['set_data']= $_POST;

			$this->db->where('orderstatus_id', $user_id);

			$this->db->update('tabqy_orderstatus', $data['set_data']);

			 $data['error']="false";

				$data['msg']="true";

				$data['success_message']="Order Status Updated Successfully";

				echo json_encode($data);die;

		}		

		

	}
	
	/*************************************
	  DEVELOPER: Priyanjali 	 
	  DATE: 30-4-2018
	  FUNCTION:  updateStatus('order status id', 'new status value') 
	  DESCRITION: Used to change status of order status
	*************************************/
	function updateStatus($id, $value){
		  $this->db->flush_cache();
		  $this->db->where('orderstatus_id', $id);
		  $this->db->update('tabqy_orderstatus',array('orderstatus_status'=>$value));
		  $this->db->flush_cache();
		  $this->db->select('orderstatus_status,orderstatus_id');
		  $this->db->from('tabqy_orderstatus');
		  $this->db->where('orderstatus_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }

}