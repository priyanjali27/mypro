<?php
class Discount extends Controller{
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
	   DATE:  30-04-2018
	   FUNCTION:  index()
	   DESCRIPTION: Shows list of all discounts
	 *************************************/
	public function index(){
		$tbl = "tabqy_coupons";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/discount/index/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'discount_id';
		$search = '0';
		$get_total_rows  = $this->db->count_all_results($tbl); //Total no. of values
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
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_coupons.discount_country_id", "inner");
		$this->db->join('tabqy_resturantbrand', "tabqy_resturantbrand.resturantbrand_id=tabqy_coupons.restaurant_id", "left");
		$this->db->order_by($sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		//echo $this->db->last_query();
				
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		// 	$this->db->flush_cache();
		// $this->db->select('tabqy_countries.*');
		// $this->db->from('tabqy_countries');
		// $this->db->where('tabqy_countries.country_status','1');
		// $query_c = $this->db->get();
		// $data['related_country'] = $this->db->result($query_c);
		$this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('co.country_name', 'ASC');
        $query_c = $this->db->get();

        $data['related_country'] = $this->db->result($query_c);
		$data['action']="";
		$data['title']="System Tool Settings";
		$data['heading']="Discount";
		$data['user_id']="";
		$data['page_number']=$page_number;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		view_loader('admin/discount/view.html',$data);		
	}
	
	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  resturant()
	   DESCRIPTION: Shows to list of all discounts brandwise
	 *************************************/
	public function resturant(){
			if(empty($this->url->segment(4)) || empty($this->url->segment(5)) || empty($this->url->segment(6)))
        {
				$last_url="admin/company";
				redirect($last_url);
		}
		$company_id=$this->url->segment(6);
		$restaurant_id=$this->url->segment(4);
		$country_id=$this->url->segment(5);
		$tbl = "tabqy_coupons";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/discount/resturant/$restaurant_id/$country_id/$company_id"; //URL
		$data['restaurant_id']=$restaurant_id; 
		$data['country_id']=$country_id;
		$data['company_id']=$company_id; 
		$pageno			 = $this->url->segment(7); // Get page number
		$sort 			 = 'discount_id';
		$search = '0';
		$get_total_rows  = $this->db->count_all_results($tbl); //Total no. of values
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
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->where($tbl.'.restaurant_id',$restaurant_id);
		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_coupons.discount_country_id", "inner");
		$this->db->join('tabqy_resturantbrand', "tabqy_resturantbrand.resturantbrand_id=tabqy_coupons.restaurant_id", "inner");
		$this->db->order_by($sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		//print_r($query);die;
		$data['results']=$this->db->result($query);
				
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
			$this->db->flush_cache();
		$this->db->select('tabqy_countries.*');
		$this->db->from('tabqy_countries');
		$this->db->where('tabqy_countries.country_status','1');
		$query_c = $this->db->get();
		$data['related_country'] = $this->db->result($query_c);
		$data['action']="";
		$data['title']="System Tool Settings";
		$data['heading']="Discount";
		$data['user_id']="";
		$data['page_number']=$page_number;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		 $this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_countries');

		$this->db->order_by('country_name','ASC');

		$query_c = $this->db->get();

		$data['all_countries'] = $this->db->result($query_c);
		view_loader('admin/discount/resturant_index.html',$data);		
	}
	
	
	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  add()
	   DESCRIPTION: Used to add discount
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
		$this->validation->validate("discount_name","Discount Name","required");
		$this->validation->validate("discount_code","Discount Code","required");
		$this->validation->validate("no_of_coupons","No. of Coupons","required|numeric");
		$this->validation->validate("value_type","Value Type","required");
		$this->validation->validate("use_type","Use Type","required");
		$this->validation->validate("from_date","From Date","required");
		$this->validation->validate("to_date","To Date","required");
		$this->validation->validate("min_orderval","Minimum Order Value","required|numeric");
		$this->validation->validate("discount_value","Discount Value","required|numeric");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{	

			unset($_POST['submit']);
			unset($_POST['discount_id']);
		
			$data['send_data']= $_POST;
			$data['send_data']['discount_status']	=	1;
			$data['send_data']['discount_created']	=	date('Y-m-d H:i:s');
			$last_id = $this->db->insert('tabqy_coupons', $data['send_data']);
			$this->db->insert('tabqy_couponextend',array('discount_id'=>$last_id, 'no_of_coupons'=>$_POST['no_of_coupons']));
			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Discount Coupon Added Successfully";
			echo json_encode($data);die;
		}
    }
	
	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  delete(discount id)
	   DESCRIPTION: Used to delete discount
	 *************************************/
	Public function delete($discount_id){
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
	
		$this->db->flush_cache();
		$this->db->where('discount_id', $discount_id);
		$this->db->delete('tabqy_coupons'); 
		$data['error']           = "false";
        $data['msg']             = "false";
        $data['success_message'] = "Discount successfully deleted";
        echo json_encode($data);
        exit();		
		
	}
	
	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  edit(discount id)
	   DESCRIPTION: Used to edit discount
	 *************************************/
	Public function edit($dis_id){
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
		$this->validation->validate("to_date","To Date","required");
		$this->validation->validate("min_orderval","Minimum Order Value","required|numeric");
		
		if($this->validation->validation_check()== FALSE){ 	
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']= $_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			$this->db->flush_cache();
			$this->db->select('no_of_coupons');
			$this->db->from('tabqy_coupons');
			$this->db->where('discount_id',$dis_id);
			$que = $this->db->get();
			$data['old_cp'] = $this->db->result($que);
			if($_POST['add_coupons']>0){
				$data['new_cp'] = $data['old_cp'][0]['no_of_coupons'] + $_POST['add_coupons'];
			}else{
				$data['new_cp'] = $data['old_cp'][0]['no_of_coupons'];
			}
			$data['set_data']= $_POST; 
			$this->db->flush_cache();
			$this->db->where('discount_id', $dis_id);
			$this->db->update('tabqy_coupons', array('to_date'=>$data['set_data']['to_date'], 'no_of_coupons'=>$data['new_cp'], 'min_orderval'=>$data['set_data']['min_orderval']));
			if($data['set_data']['add_coupons'] >0){
				$id =$this->db->insert('tabqy_couponextend',array('discount_id'=>$dis_id, 'no_of_coupons'=>$data['set_data']['add_coupons']));
			}
				$data['error']="false";
				$data['msg']="true";
				$data['success_message']="Discount Updated Successfully";
				echo json_encode($data);die;
		}		
		
	}
	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  view(discount id)
	   DESCRIPTION: Used to View discount
	 *************************************/
	public function view($id){
		$this->db->flush_cache();
		$this->db->select('tabqy_coupons.*,tabqy_countries.country_name');
		$this->db->from('tabqy_coupons');
		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_coupons.discount_country_id", "inner");
		$this->db->where('discount_id',$id);
		$query = $this->db->get();
		$data['view_data'] = $this->db->result($query);	
		
		echo json_encode($data);die;
	}
	
	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  updateStatus(discount id)
	   DESCRIPTION: Used to change discount status
	 *************************************/
	function updateStatus($id, $value){
		$pre = 'discount';
		$this->db->flush_cache();
		$this->db->where($pre.'_id', $id);
		$this->db->update('tabqy_coupons',array($pre.'_status'=>$value));
		$this->db->flush_cache();
		$this->db->select($pre.'_status,'.$pre.'_id');
		$this->db->from('tabqy_coupons');
		$this->db->where($pre.'_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	}
}