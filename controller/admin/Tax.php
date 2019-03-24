<?php
class Tax extends Controller{
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
	public function index(){ 
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
	
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/point/index/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'id';

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
		$this->db->select('tabqy_taxes.*,tabqy_countries.country_name,tabqy_resturantbrand.resturantbrand_name');
		$this->db->from('tabqy_taxes');
		$this->db->join('tabqy_countries', "tabqy_countries.country_id=tabqy_taxes.tax_country", "inner");
		$this->db->join('tabqy_resturantbrand', "tabqy_resturantbrand.resturantbrand_id=tabqy_taxes.tax_resturant_id", "inner");
		
		$this->db->where('tabqy_taxes.tax_status','1');
		$this->db->where('tabqy_taxes.taxes_set_admin','1');
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
		$this->db->flush_cache();
		$this->db->select('resturantbrand_id,resturantbrand_name');
		$this->db->from('tabqy_resturantbrand');
		$this->db->where('resturantbrand_type',0);
		$this->db->where('resturantbrand_status','1');
		$this->db->order_by('resturantbrand_id','DESC');
		$query = $this->db->get();
		$data['resturants']=$this->db->result($query);
	//	print_r($data['resturants']);die;
		$data['title']="System Tool Settings";
		$data['heading']="Tax";;
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
		view_loader('admin/country/tax.html',$data);		
	}
	
	
	public function resturant(){ 
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
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/point/resturant/$restaurant_id/$country_id/$company_id"; //URL
		$data['company_id']=$company_id; 
		$pageno			 = $this->url->segment(7); // Get page number
		$sort 			 = 'id';
        $data['restaurant_id']=$restaurant_id; 
		$data['country_id']=$country_id;
		
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
		$this->db->select('tabqy_taxes.*,tabqy_countries.country_name,tabqy_resturantbrand.resturantbrand_name');
		$this->db->from('tabqy_taxes');
		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_taxes.tax_country", "inner");
		$this->db->join('tabqy_resturantbrand', "tabqy_resturantbrand.resturantbrand_id=tabqy_taxes.tax_resturant_id", "inner");
		
		$this->db->where('tabqy_taxes.tax_status','1');
		$this->db->where('tabqy_taxes.taxes_set_admin','1');

        if (!empty($_SESSION['selected_country'])) {
            $this->db->where('tabqy_taxes.tax_country', $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
            $this->db->where('tabqy_taxes.tax_country', $_SESSION['userdata']['user_default_country']);
        }

		



		$this->db->order_by('tabqy_taxes.tax_'.$sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		//echo $this->db->last_query();
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
	//	print_r($data['resturants']);die;
		$data['title']="System Tool Settings";
		$data['heading']="Tax";;
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
		 $this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_countries');

		$this->db->order_by('country_name','ASC');

		$query_c = $this->db->get();

		$data['all_countries'] = $this->db->result($query_c);
		view_loader('admin/country/resturant_tax.html',$data);		
	}
	
	public function resturant_branch(){
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
		
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/tax/resturant/$restaurant_id/$country_id/$company_id"; //URL
		$pageno			 = $this->url->segment(7); // Get page number
		$sort 			 = 'id';
        $data['restaurant_id']=$restaurant_id; 
		$data['country_id']=$country_id;
		$data['company_id']=$company_id;
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
		view_loader('admin/country/resturant_branch_tax.html',$data);		
	}

	//Add Country
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
		
		 $this->validation->validate("tax_name","Tax name","required");
		$this->validation->validate("tax_percent","Tax percent","required");
        
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			$regx = '/^[-+]?[0-9]*\.?[0-9]*$/';
        if(!preg_match($regx, $_POST['tax_percent'])){
			
            $data['error_msg']['tax_percent']="Enter integer or decimal value";
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}
		$this->db->flush_cache();

		$this->db->select('tabqy_resturantbrand.resturantbrand_country');

		$this->db->from('tabqy_resturantbrand');

		$this->db->where('tabqy_resturantbrand.resturantbrand_id',$_POST['tax_resturant_id']);

		$query_c = $this->db->get();

		$resturantbrand= $this->db->row($query_c);
				
			
			$data['send_data']=$_POST;			
			$data['send_data']['tax_status'] = 1;	
			$data['send_data']['taxes_set_admin'] = 1;	
			$data['send_data']['tax_country'] =$resturantbrand['resturantbrand_country'];
			$data['send_data']['tax_created'] = date('Y-m-d H:i:s');
			$this->db->insert('tabqy_taxes', $data['send_data']);
			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Tax  Added Successfully";
			echo json_encode($data);die;
		}
    }
	
	// Delete Country Module
	Public function delete($tax_id){
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
		
			$this->db->where('tax_id', $tax_id);
            $this->db->delete('tabqy_taxes'); 
			die;
		
	}
	
	// Edit Country Module
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
	
	public function updateStatus($id, $value){
		$pre = 'tax';
		$this->db->flush_cache();
		$this->db->where($pre.'_id', $id);
		$this->db->update('tabqy_taxes',array($pre.'_status'=>$value));
		$this->db->flush_cache();
		$this->db->select($pre.'_status,'.$pre.'_id');
		$this->db->from('tabqy_taxes');
		$this->db->where($pre.'_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	}
}