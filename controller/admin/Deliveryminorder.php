<?php

class Deliveryminorder extends Controller{

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

public function index(){ 

	$data['session'] = $_SESSION;

	$data['language'] = $this->language;



	$data['success'] = flesh('success');

	$data['error']   = flesh('error');

	$item_per_page   = 10; //item to display per page

	$page_url        = baseurl."admin/mindeliveryorder/index/"; //URL

	$pageno			 = $this->url->segment(4); // Get page number

	$sort 			 = 'id';



	$search = '0';

	$this->db->from('tabqy_mindeliveryorder');

	$get_total_rows  = $this->db->count_all_results(); //Total no. of values

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

	$this->db->select('tabqy_mindeliveryorder.*,tabqy_countries.country_name,tabqy_resturantbrand.resturantbrand_name');

	$this->db->from('tabqy_mindeliveryorder');

	$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_mindeliveryorder.mindeliveryorder_country_id", "inner");

	$this->db->join('tabqy_resturantbrand', "tabqy_resturantbrand.resturantbrand_id=tabqy_mindeliveryorder.mindeliveryorder_restaurant_id", "inner");

	//$this->db->where('tabqy_mindeliveryorder.mindeliveryorder_restaurant_id',0);

	$this->db->order_by('tabqy_mindeliveryorder.mindeliveryorder_'.$sort,'DESC');

	$this->db->limit($item_per_page,$page_position);

	$query = $this->db->get();

	$data['results']=$this->db->result($query);

	//echo "<pre>";

	//print_r($data['results']);die;

	$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

	$this->db->flush_cache();

	$this->db->select('tabqy_countries.*');

	$this->db->from('tabqy_countries');

	$this->db->where('tabqy_countries.country_status','1');

	$query_c = $this->db->get();

	$data['related_country'] = $this->db->result($query_c);

	$this->db->flush_cache();

	$this->db->select('resturantbrand_id,resturantbrand_name');

	$this->db->from('tabqy_resturantbrand');

	$this->db->where('resturantbrand_type',0);

	$this->db->where('resturantbrand_status','1');

	$this->db->order_by('resturantbrand_id','DESC');

	$query = $this->db->get();

	$data['resturants']=$this->db->result($query);

	$data['title']="System Tool Settings";
	$data['heading']="mindeliveryorder";

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

	view_loader('admin/mindeliveryorder/index.html',$data);		

}

public function restaurant_branch($restaurant_id, $country_code){ 
    if(empty($this->url->segment(4)) || empty($this->url->segment(5)) || empty($this->url->segment(6)))
        {
				$last_url="admin/company";
				redirect($last_url);
		}
		$company_id=$this->url->segment(6);
		$restaurant_id=$this->url->segment(4);
		$country_id=$this->url->segment(5);
	$data['session'] = $_SESSION;
	$data['language'] = $this->language;
	$data['success'] = flesh('success');
	$data['error']   = flesh('error');
	$data['restaurant_id'] = $restaurant_id;
	$data['country_id'] = $country_id;
	$data['company_id']=$company_id;

	$item_per_page   = 10; //item to display per page
	$page_url        = baseurl."admin/mindeliveryorder/index/$restaurant_id/$country_code/$company_id"; //URL
	$pageno			 = $this->url->segment(7); // Get page number
	$sort 			 = 'id';
	$search = '0';

	$this->db->from('tabqy_mindeliveryorder');
	$get_total_rows  = $this->db->count_all_results(); //Total no. of values

	if(isset($pageno)){ //Get page number 

		$page_number = filter_var($pageno, FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number

		if(!is_numeric($page_number)){
			die('Invalid page number!');
		} //incase of invalid page number
	}else{
		$page_number = 1; //if there's no page number, set it to 1
	}
	$total_pages = ceil($get_total_rows/$item_per_page);
	$page_position = (($page_number-1) * $item_per_page); //get starting position to fetch the records
	
	$this->db->flush_cache();
	$this->db->select('md.*,co.country_name,re.resturantbrand_name,re.resturantbrand_type');
	$this->db->from('tabqy_mindeliveryorder as md');
	$this->db->join('tabqy_countries as co', "co.country_code = md.mindeliveryorder_country_id", "inner");
	$this->db->join('tabqy_resturantbrand as re', "re.resturantbrand_id = md.mindeliveryorder_restaurant_id", "inner");
	$this->db->where('md.mindeliveryorder_restaurant_id',$restaurant_id);
	$this->db->where('md.mindeliveryorder_country_id', $country_code);
	$this->db->order_by('md.mindeliveryorder_'.$sort,'DESC');
	$this->db->limit($item_per_page,$page_position);
	$query = $this->db->get();

	$data['results']=$this->db->result($query);
	
	$this->db->flush_cache();
	$this->db->select('resturantbrand_type');
	$this->db->from('tabqy_resturantbrand');
	$this->db->where('resturantbrand_id',$restaurant_id);
	$type_query = $this->db->get();
	$ress = $this->db->row($type_query);
	$data['brand_type'] = $ress['resturantbrand_type'];
	
	$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

	$data['title']="System Tool Settings";
	$data['heading']="mindeliveryorder";

	$data['user_id']="";
	$data['page_number']=$page_number;
	$start=$page_position+1;
	$data['start']=$start;
	$data['end']=($start+count($data['results'])-1);
	view_loader('admin/mindeliveryorder/restaurant_mindelivery.html',$data);	

}


//Add mindeliveryorder

public function add(){

	$this->validation->validate("mindeliveryorder_country_id","Country name","required");

	$this->validation->validate("mindeliveryorder_restaurant_id","Restaurant","required");

	$this->validation->validate("mindeliveryorder_name","Minimum Value","required");

	$this->validation->validate("mindeliveryorder_percentage","Percentage Value","required");

	

	

	if($this->validation->validation_check()== FALSE){

		$data['error_msg']=$this->validation->error_msg;

		$data['set_data']=$_POST;

		$data['error']="true";

		echo json_encode($data);die;

	}else{

	



		$data['send_data']=$_POST;			

		$data['send_data']['mindeliveryorder_status'] = 1;			

		$data['send_data']['mindeliveryorder_created'] = date('Y-m-d H:i:s');

		$this->db->insert('tabqy_mindeliveryorder', $data['send_data']);

		$data['error']="false";

		$data['msg']="true";

		$data['success_message']="Mimimum Order value Added Successfully";

		echo json_encode($data);die;

	}

}



// Delete Country Module

Public function delete($mindeliveryorder_id){

	

		$this->db->where('mindeliveryorder_id', $mindeliveryorder_id);

		$this->db->delete('tabqy_mindeliveryorder'); 

		die;

	

}



// Edit Country Module

Public function edit($user_id){	

	$this->validation->validate("mindeliveryorder_country_id","Country name","required");

	$this->validation->validate("mindeliveryorder_restaurant_id","Restaurant","required");

	$this->validation->validate("mindeliveryorder_name","Minimum Value","required");

	$this->validation->validate("mindeliveryorder_percentage","Percentage Value","required");	

	if($this->validation->validation_check()== FALSE){

		$data['error_msg']=$this->validation->error_msg;

		$data['set_data']= $_POST;

		$data['error']="true";

		echo json_encode($data);die;

	}else{
		unset($_POST['submit']);

		$data['set_data']= $_POST;

		$this->db->where('mindeliveryorder_id', $user_id);

		$this->db->update('tabqy_mindeliveryorder', $data['set_data']);

		$data['error']="false";

		$data['msg']="true";

		$data['success_message']="Your row Updated Successfully";

		echo json_encode($data);die;

	}
}

	function updateStatus($id, $value){
		$pre = 'mindeliveryorder';
		$this->db->flush_cache();
		$this->db->where($pre.'_id', $id);
		$this->db->update('tabqy_'.$pre,array($pre.'_status'=>$value));
		$this->db->flush_cache();
		$this->db->select($pre.'_status,'.$pre.'_id');
		$this->db->from('tabqy_'.$pre);
		$this->db->where($pre.'_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	}

}