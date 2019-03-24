<?php
class Discount extends Controller{
	var $language; 
	var $session;
	var $name;
	var $get_data;
	public function __construct()

    {

      parent::__construct();

	  require "check_login.php";

    }
    
     /*************************************
		  DEVELOPER: Meenu  
		  DATE:  27/04/2018
		  FUNCTION:  get_data()
		  DESCRIPTION: Get detail data in session
	*************************************/

	function get_data(){

        $data['user_id']=$_SESSION['resturant_userdata']['user_id'];
		$data['restaurant_id']=$_SESSION['resturant_userdata']['restaurant_id'];
		$data['company_id']=$_SESSION['resturant_userdata']['user_company_id'];
		$data['language_code']=$_SESSION['lang_code'];
        return $data;
    }
	public function index(){
	    $restaurant_id		 = $this->url->segment(4);
		$tbl = "tabqy_coupons";
		$data['restaurant_id']=$restaurant_id ;
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."resturant/discount/index/".$restaurant_id; //URL
		$pageno			 = $this->url->segment(5); // Get page number
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
		$this->db->where('tabqy_coupons.restaurant_id',$restaurant_id);
		$this->db->order_by($sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
				
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
			$this->db->flush_cache();
		$this->db->select('tabqy_countries.*');
		$this->db->from('tabqy_countries');
		$this->db->where('tabqy_countries.country_status','1');
		$query_c = $this->db->get();
		$data['related_country'] = $this->db->result($query_c);
		$data['action']="";
		$data['title']="Resturant";
		$data['heading']="DiscountList";
		$data['user_id']="";
		$data['page_number']=$page_number;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		view_loader('resturant/discount/view.html',$data);		
	}
	
	//Add Discount Coupon
	public function add(){
		$restaurant_id		 = $this->url->segment(4);
		$data['restaurant_id']=$restaurant_id ;
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
			//print_r($data['send_data']);exit;
			$data['send_data']['discount_status']	=	1;
			$data['send_data']['restaurant_id']	=	$restaurant_id;
			$data['send_data']['discount_created']	=	date('Y-m-d H:i:s');
			$this->db->flush_cache();
			$last_id = $this->db->insert('tabqy_coupons', $data['send_data']);
			$this->db->insert('tabqy_couponextend',array('discount_id'=>$last_id, 'no_of_coupons'=>$_POST['no_of_coupons']));
			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Discount Coupon Added Successfully";
			echo json_encode($data);die;
		}
    }
	
	// Delete City
	Public function delete($discount_id){
			$this->db->flush_cache();
			$this->db->where('discount_id', $discount_id);
            $this->db->delete('tabqy_coupons'); 
			die;
		
	}
	
	// Edit City
	Public function edit($dis_id){
		$restaurant_id = $_SESSION['resturant_userdata']['restaurant_id'];
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
			$data['send_data']['restaurant_id']	=	$restaurant_id;
			$this->db->flush_cache();
			$this->db->where('discount_id', $dis_id);
			$this->db->update('tabqy_coupons', array('to_date'=>$data['set_data']['to_date'], 'no_of_coupons'=>$data['new_cp'],'min_orderval'=>$data['set_data']['min_orderval']));
			if($data['set_data']['add_coupons'] >0){
				$id =$this->db->insert('tabqy_couponextend',array('discount_id'=>$dis_id, 'no_of_coupons'=>$data['set_data']['add_coupons']));
			}
				$data['error']="false";
				$data['msg']="true";
				$data['success_message']="Discount Updated Successfully";
				echo json_encode($data);die;
		}		
		
	}
	//View data
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
	
	 function updateStatus($id, $value){
		  $this->db->flush_cache();
		  $this->db->where('discount_id', $id);
		  $this->db->update('tabqy_coupons',array('discount_status'=>$value));
		  $this->db->flush_cache();
		  $this->db->select('discount_status,discount_id');
		  $this->db->from('tabqy_coupons');
		  $this->db->where('discount_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }
}