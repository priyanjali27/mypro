<?php

class Deliveryarea extends Controller{


	var $language; 

	var $session;

	var $name;

	public function __construct()

    {

        parent::__construct();

		

		if (is_logged_in_resturant()==FALSE)

        {

            redirect('resturant/admin_login');

        }

		$this->db->flush_cache();

		$this->db->select('tabqy_language.language_name,tabqy_language.language_code,tabqy_language.language_flag');

		$this->db->from('tabqy_language');

		$this->db->order_by('tabqy_language.language_id','DESC');

		$query = $this->db->get();

		$this->language=$this->db->result($query);

		$this->db->flush_cache();

		$this->db->select('tabqy_user.user_name,tabqy_resturantbrand.resturantbrand_displayname as resturant_name');

		$this->db->from('tabqy_user');

		$this->db->join('tabqy_resturantbrand','tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id');

	    $userid=$_SESSION['resturant_userdata']['user_id'];

		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];

		$this->db->where('tabqy_user.user_id', $userid);

		$this->db->where('tabqy_user.user_restaurant_id', $restaurant_id);

		

		$user = $this->db->get();

		$name=$this->db->result($user);

		$_SESSION['resturant_userdata']['user_name']=$name[0]['user_name'];

		$_SESSION['resturant_userdata']['resturant_name']=$name[0]['resturant_name'];



		if(empty($_SESSION['lang_code'])) {

			$_SESSION['lang_code']="en";

			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";

		} else {

			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";

		}

    }


public function add(){



	$this->validation->validate("deliveryarea_distance","Distance","required");

	

	

	if($this->validation->validation_check()== FALSE){

		$data['error_msg']=$this->validation->error_msg;

		$data['set_data']=$_POST;

		$data['error']="true";

		echo json_encode($data);die;

	}else{

$this->db->flush_cache();

		$this->db->select('tabqy_resturantbrand.resturantbrand_country');

		$this->db->from('tabqy_resturantbrand');

		$this->db->where('tabqy_resturantbrand.resturantbrand_id',$_POST['deliveryarea_restaurant_id']);

		$query_c = $this->db->get();

		$resturantbrand= $this->db->row($query_c);

		$data['send_data']=$_POST;			

		$data['send_data']['deliveryarea_status'] = 1;	
     	$data['send_data']['deliveryarea_country_id'] =$resturantbrand['resturantbrand_country'];		

		$data['send_data']['deliveryarea_created'] = date('Y-m-d H:i:s');

		$this->db->insert('tabqy_deliveryarea', $data['send_data']);

		$data['error']="false";

		$data['msg']="true";

		$data['success_message']="Delivery area Added Successfully";

		echo json_encode($data);die;

	}

}



// Delete Country Module

Public function delete($deliveryarea_id){

	

		$this->db->where('deliveryarea_id', $deliveryarea_id);

		$this->db->delete('tabqy_deliveryarea'); 

		die;

	

}

public function index($restaurant_id ){ 
	$data['session'] = $_SESSION;
	$data['language'] = $this->language;
	$data['success'] = flesh('success');
	$data['error']   = flesh('error');
	$data['restaurant_id'] = $restaurant_id;

	$item_per_page   = 10; //item to display per page
	$page_url        = baseurl."admin/deliveryarea/index/$restaurant_id"; //URL
	$pageno			 = $this->url->segment(5); // Get page number
	$sort 			 = 'id';
	$search = '0';

	$this->db->from('tabqy_deliveryarea');
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
	$this->db->select('md.*,co.country_name,re.resturantbrand_name');
	$this->db->from('tabqy_deliveryarea as md');
	$this->db->join('tabqy_countries as co', "co.country_code = md.deliveryarea_country_id", "inner");
	$this->db->join('tabqy_resturantbrand as re', "re.resturantbrand_id = md.deliveryarea_restaurant_id", "inner");
	$this->db->where('md.deliveryarea_restaurant_id',$restaurant_id);

	$this->db->order_by('md.deliveryarea_'.$sort,'DESC');
	$this->db->limit($item_per_page,$page_position);
	$query = $this->db->get();

	$data['results']=$this->db->result($query);
	$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

	$data['title']="System Tool Settings";
	$data['heading']="deliveryarea";

	$data['user_id']="";
	$data['page_number']=$page_number;
	$start=$page_position+1;
	$data['start']=$start;
	$data['end']=($start+count($data['results'])-1);
	view_loader('resturant/deliveryarea/restaurant_deliveryarea.html',$data);	

}


// Edit Country Module

Public function edit($user_id){	

	$this->validation->validate("deliveryarea_distance","Distance","required");


	if($this->validation->validation_check()== FALSE){

		$data['error_msg']=$this->validation->error_msg;

		$data['set_data']= $_POST;

		$data['error']="true";

		echo json_encode($data);die;

	}else{
		unset($_POST['submit']);

		$data['set_data']= $_POST;

		$this->db->where('deliveryarea_id', $user_id);

		$this->db->update('tabqy_deliveryarea', $data['set_data']);

		$data['error']="false";

		$data['msg']="true";

		$data['success_message']="Your row Updated Successfully";

		echo json_encode($data);die;

	}
}

	function updateStatus($id, $value){
		$pre = 'deliveryarea';
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