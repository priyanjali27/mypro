<?php

class Tax extends Controller{

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

	public function index(){ 

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

	

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."resturant/point/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort 			 = 'id';

                $restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];

		$search = '0';

		$this->db->from('tabqy_taxes');

		$this->db->where('tabqy_taxes.tax_resturant_id',$restaurant_id);

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

		$this->db->select('tabqy_taxes.*,tabqy_countries.country_name');

		$this->db->from('tabqy_taxes');

		$this->db->join('tabqy_countries', "tabqy_countries.country_country_code=tabqy_taxes.tax_country", "inner");

		$this->db->where('tabqy_taxes.tax_resturant_id',$restaurant_id);

		$this->db->order_by('tabqy_taxes.tax_'.$sort,'DESC');

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

		$data['title']="Resturant";

		$data['heading']="Tax";

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

		view_loader('resturant/point/tax.html',$data);		

	}

	

	//Add Country

	public function add(){

	
		$this->validation->validate("tax_percent","Tax percent","required");	

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			$regx = '/^([+-]?[1-9]\d*|0)$/';

        if(!preg_match($regx, $_POST['tax_percent'])){

			

            $data['error_msg']['tax_percent']="Enter integer value";

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}

		//$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
	    $this->db->flush_cache();

		$this->db->select('tabqy_resturantbrand.resturantbrand_country');

		$this->db->from('tabqy_resturantbrand');

		$this->db->where('tabqy_resturantbrand.resturantbrand_id',$_POST['tax_resturant_id']);

		$query_c = $this->db->get();

		$resturantbrand= $this->db->row($query_c);
			

			$data['send_data']=$_POST;			

			$data['send_data']['tax_status'] = 1;
			
			$data['send_data']['tax_country'] =$resturantbrand['resturantbrand_country'];

            //$data['send_data']['tax_resturant_id']=$_POST['restaurant_id'];			

			$data['send_data']['tax_created'] = date('Y-m-d H:i:s');

			$this->db->insert('tabqy_taxes', $data['send_data']);

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="Tax Added Successfully";

			echo json_encode($data);die;

		}

    }

	

	// Delete Country Module

	Public function delete($tax_id){

		

			$this->db->where('tax_id', $tax_id);

            $this->db->delete('tabqy_taxes'); 

			die;

		

	}

	

	// Edit Country Module

	Public function edit($user_id){

		

		$this->validation->validate("tax_country","Country name","required");

	    $this->validation->validate("tax_name","Tax name","required");

		$this->validation->validate("tax_percent","Tax percent","required");

        

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			$regx = '/^[-+]?[0-9]*\.?[0-9]*$/';

        if(!preg_match($regx, $_POST['tax_percent'])){

			

            $data['error_msg']['tax_percent1']="Enter integer or decimal value";

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}

			unset($_POST['submit']);

			$data['set_data']= $_POST;

			$this->db->where('tax_id', $user_id);

			$this->db->update('tabqy_taxes', $data['set_data']);

			 $data['error']="false";

				$data['msg']="true";

				$data['success_message']="Your row Updated Successfully";

				echo json_encode($data);die;

		}		

		

	}

	function updateStatus($id, $value){

		  $this->db->flush_cache();

		  $this->db->where('tax_id', $id);

		  $this->db->update('tabqy_taxes',array('tax_status'=>$value));

		  $this->db->flush_cache();

		  $this->db->select('tax_status,tax_id');

		  $this->db->from('tabqy_taxes');

		  $this->db->where('tax_id', $id);

		  $query = $this->db->get();

		  $data['cur_status'] = $this->db->result($query);

		  echo json_encode($data);die;

	 }
	 public function resturant_branch($restaurant_id){ 
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;	
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/tax/resturant/$restaurant_id"; //URL
		$pageno			 = $this->url->segment(5); // Get page number
		$sort 			 = 'id';
        $data['restaurant_id']=$restaurant_id; 
		$search = '0';
		$this->db->from('tabqy_taxes');
		$this->db->where('tabqy_taxes.tax_resturant_id',0);
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
		$this->db->select('tx.*,co.country_name,re.resturantbrand_name,re.resturantbrand_type');
		$this->db->from('tabqy_taxes as tx');
		$this->db->join('tabqy_countries as co', "co.country_code=tx.tax_country", "inner");
		$this->db->join('tabqy_resturantbrand as re', "re.resturantbrand_id=tx.tax_resturant_id", "inner");
		$this->db->where('tx.tax_resturant_id',$restaurant_id);
		$this->db->where('tx.tax_status','1');
		//$this->db->where('tx.taxes_set_admin','1');
		$this->db->order_by('tx.tax_'.$sort,'DESC');
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
		$data['heading']="Tax";;
		$data['user_id']="";
		$data['page_number']=$page_number;

		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		view_loader('resturant/point/resturant_branch_tax.html',$data);		
	}


}