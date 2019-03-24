<?php

class City extends Controller{

	var $language; 

	var $session;

	var $name;
	public $perpage; 

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
		$this->perpage = 10;
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
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  home
	  DESCRIPTION : 
     **********************************/

	public function home($country_code){ 
	
		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "Location";

		$data['heading'] = "Home City ";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error'] = flesh('error');
		$perpage = $this->perpage;;
		$offset = 0;
	
		$sort = 'city_name';
		
		$this->db->flush_cache();

		$this->db->select('c.*, count(r.resturantbrand_id) as Totalrest');

		$this->db->from('tabqy_cities c');
		
		$this->db->join('tabqy_resturantbrand as r','r.resturantbrand_city=c.city_code AND r.resturantbrand_type !=0','left');
		$this->db->group_by('c.city_code');
		$this->db->where('country_code',$country_code);
		$this->db->order_by('c.'.$sort,'ASC');
		$query1 = $this->db->get();
		$data['actual_count'] = $query1->rowCount();

    	$this->db->flush_cache();

		$this->db->select('c.*, count(r.resturantbrand_id) as Totalrest');

		$this->db->from('tabqy_cities c');
		
		$this->db->join('tabqy_resturantbrand as r','r.resturantbrand_city=c.city_code AND r.resturantbrand_type !=0','left');
		$this->db->group_by('c.city_code');
		$this->db->where('country_code',$country_code);
		$this->db->order_by('c.'.$sort,'ASC');
		$this->db->limit($perpage,$offset);

		$query = $this->db->get();
		$data['city_count'] = $query->rowCount();
		$data['results'] = $this->db->result($query);
		
		if($data['actual_count'] > $perpage)
			$data['load_more'] = true;
		
		$this->db->flush_cache();
		$this->db->select('country_name,country_code');
		$this->db->from('tabqy_countries');
		$this->db->where('country_code',$country_code);
		$que = $this->db->get();
		$country_name = $this->db->result($que);
		$data['country_sel'] = $country_name[0];
		
		$this->db->flush_cache();

		$this->db->select('tabqy_countries.*');

		$this->db->from('tabqy_countries');

		$this->db->order_by('tabqy_countries.country_name','ASC');

		$query_c = $this->db->get();

		$data['related_country'] = $this->db->result($query_c);
		//echo"<pre>"; print_r($data['related_country']);die();
		
		$data['all_regions'] = $this->all_regions($country_code);
		
    	view_loader('admin/country/city_home.html',$data);	
	}
	
	 /***********************************
	  DEVELOPER:  Sonu
	  DATE	   :  04/27/2018
	  FUNCTION :  all_regions
	  DESCRIPTION : Used to get all regions	
     **********************************/
	 
	public function all_regions($country_code){
		$this->db->flush_cache();
		$this->db->select('region_code,region_name');
		$this->db->from('tabqy_regions');
		$this->db->where('country_code',$country_code);
		$this->db->order_by('region_name','ASC');
		$query = $this->db->get();
		$res = $this->db->result($query);
		return $res;
	}
	
	/***********************************
	  DEVELOPER   :  Sonu
	  DATE	      :  04/27/2018
	  FUNCTION    :  Index
	  DESCRIPTION :  Listing All Record
        **********************************/
	public function index(){

		$data['session'] = $_SESSION;
		//echo"<pre>";print_r($data['session']);die;

		$data['language'] = $this->language;

		$data['title']	 = "Location";

		$data['heading'] = "City";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/city/index/"; //URL

		$pageno		 = $this->url->segment(4); // Get page number

		$sort 		 = 'city_id';

		$sort_c		 = 'country_name';

		$search = '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_cities'); //Total no. of values

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

		$this->db->select('a.country_name,a.country_code,c.*');

		$this->db->from('tabqy_cities as c');

		$this->db->join('tabqy_countries as a', "a.country_code=c.country_code", "inner");

		$this->db->order_by('c.'.$sort,'DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();

		$data['results']=$this->db->result($query);		
        // echo"<pre>";print_r($data['results']);
		$this->db->flush_cache();

		$this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

		$this->db->from('tabqy_countries as co');
                $this->db->join('tabqy_countries_language as l',"l.country_language_country_id=co.country_id","inner");
		$this->db->where('l.country_language_language_code',$_SESSION['lang_code']);

		$query_c = $this->db->get();

		$data['related_country'] = $this->db->result($query_c);
         
		 $data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['user_id']="";

		$data['page_number']=$page_number;

		$start=$page_position+1;

		$data['start']=$start;

		$data['end']=($start+count($data['results'])-1);

		view_loader('admin/country/view_city.html',$data);		

	}

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  check_existence
	  DESCRIPTION : 
     **********************************/
	public function check_existence($field,$value,$id=''){
	    $this->db->select('*');
		$this->db->from('tabqy_cities');
		$this->db->where($field,$value);
		if($id!=''){
			$this->db->where('city_id<>',$id);
		}
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];		
	}
	
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  add
	  DESCRIPTION : Add Data
     **********************************/

	public function add(){
		
		if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['add'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
		
		 
         $language = $this->language;
         
		$this->validation->validate("city_name","City Name","required");

		$this->validation->validate("country_code","Country","required");	
		
		$this->validation->validate("city_code","City Code","required");		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{	
			if(!empty($_POST['city_name']) || !empty($_POST['city_code'])){				
				$duplicate_city_name = $this->check_existence('city_name',$_POST['city_name']);
				if($duplicate_city_name){
						$data['error_msg']['city_name']="City Name already exists.";
				}
				$duplicate_city_code = $this->check_existence('city_code',$_POST['city_code']);
				if($duplicate_city_code){
						$data['error_msg']['city_code']="City Code already exists.";
				}
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
			unset($_POST['submit']);

			unset($_POST['city_id']);
            
			$data['send_data']= $_POST;

			$data['send_data']['city_status']=1;

			$data['send_data']['city_created']=date('Y-m-d H:i:s');

			$insert_id = $this->db->insert('tabqy_cities', $data['send_data']);
           
 			 foreach ($language as $languages) {
                $edit              = ($languages['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                $add_data_language = array(
                    "city_language_city_id" => $insert_id,
                    "city_language_language_code" => $languages['language_code'],
                    "city_language_name"=>       $_POST['city_name'],
                    "city_language_edit" => $edit,
                    "city_language_status" => '1'
                );
                
               $this->db->insert('tabqy_cities_language', $add_data_language);
           }
			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="City Added Successfully";

			echo json_encode($data);die;

		}
		}
    }
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  27-04-2018
	  FUNCTION :  delete
	  DESCRIPTION : Delete data 
     **********************************/
       Public function delete($city_id)
    {
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

			$this->db->where('city_id', $city_id);

            $this->db->delete('tabqy_cities'); 
			
			 $this->db->where('city_language_city_id', $city_id);
             $this->db->delete('tabqy_cities_language');

			 $data['error']           = "false";
			  $data['msg']             = "false";
			  $data['success_message'] = "Country success fully deleted";
			   echo json_encode($data);
			   exit();			

	}
	

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  27-04-2018
	  FUNCTION :  edit
	  DESCRIPTION : updating record of table
     **********************************/

	  Public function edit($city_id){
		  
		  if ($_SESSION['userdata']['user_role'] != 0) {
            
                    if ($this->privillage['edit'] != 1) {
                        $data['error']           = "permission";
                        $data['msg']             = "false";
                        $data['success_message'] = "You don't have permission to perform this action";
                        echo json_encode($data);
                        exit();
                    }
                }

		$this->validation->validate("city_name","City Name","required");
                $this->validation->validate("country_code","Country Name","required");
		$this->validation->validate("city_code","City Code","required");
		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{
			if(!empty($_POST['city_name'])){				
				$duplicate_city_name = $this->check_existence('city_name',$_POST['city_name'],$city_id);
				if($duplicate_city_name){
						$data['error_msg']['city_name']="City Name already exists.";
				}
				/*$duplicate_city_code = $this->check_existence('city_code',$_POST['city_code'],$city_id);
				if($duplicate_city_code){
						$data['error_msg']['city_code']="City Code already exists.";
				}*/
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
				 $city_id=$this->url->segment(4);
				unset($_POST['submit']);
                
				//$data['set_data']= $_POST;
                $language_code = $_POST['language_code'];
		$city_name = $_POST['city_name'];
		unset($_POST['language_code']);
                if ($_SESSION['lang_code'] == $language_code) {
                    $data['set_data']= $_POST;
                    $this->db->where('city_id', $city_id);
                    $this->db->update('tabqy_cities', $data['set_data']);
                  }				
				
            $this->db->flush_cache();
            $edit_data_language = array(
                "tabqy_cities_language.city_language_name" => $city_name,
                "tabqy_cities_language.city_language_edit" => '1'
            );   
			    $this->db->where('city_language_city_id', $city_id);
	            $this->db->where('city_language_language_code', $language_code);
	            $this->db->update('tabqy_cities_language', $edit_data_language);

				 $data['error']="false";
				 $data['msg']="true";
				 $data['success_message']="City Updated Successfully";
                 echo json_encode($data);die;
			}		

		}

	 }
	 /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  27-04-2018
	  FUNCTION :  UpdateStatus
	  DESCRIPTION : Change Status 
     **********************************/

	function updateStatus($id, $value){

		  $this->db->flush_cache();

		  $this->db->where('city_id', $id);

		  $this->db->update('tabqy_cities',array('city_status'=>$value));

		  $this->db->flush_cache();

		  $this->db->select('city_status,city_id');

		  $this->db->from('tabqy_cities');

		  $this->db->where('city_id', $id);

		  $query = $this->db->get();

		  $data['cur_status'] = $this->db->result($query);

		  echo json_encode($data);die;

	 }
	 
	 /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  edit_view
	  DESCRIPTION : Update data
     **********************************/
	 public function edit_view($id){
     
	    $city_id = $this->url->segment(4);
		$language_code = $this->url->segment(5);
		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_cities');
		 $this->db->join('tabqy_cities_language', "tabqy_cities_language.city_language_city_id = tabqy_cities.city_id", 'inner');

		$this->db->where('tabqy_cities.city_id',$city_id);
        $this->db->where('tabqy_cities_language.city_language_language_code', $language_code);		
           
		$query = $this->db->get();

		$data['edit_view']=$this->db->result($query);
		
        //print_r($query);die;
		echo json_encode($data);die;

	}
	 
	 /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  region_filter
	  DESCRIPTION : search data
     **********************************/
	 
      public function region_filter($region_code=''){
		 
		$perpage = $this->perpage;
		$offset = $_POST['counter'];
		$country_code = $_POST['country'];
		
		if($region_code!=''){
			$where = 're.region_code="'.$region_code.'" AND c.country_code="'.$country_code.'"';
		}else{
			$where = 'c.country_code="'.$country_code.'"';
		}
		
			$this->db->flush_cache();			            
			$this->db->select("c.city_name,c.city_code,count(r.resturantbrand_city) as Totalrest");
			$this->db->from("tabqy_cities as c");
			$this->db->join("tabqy_regions as re","FIND_IN_SET(c.city_code,re.city_code) !=0","LEFT");
			$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_city=c.city_code AND r.resturantbrand_type !=0","left");
			$this->db->where($where);
			$this->db->group_by('c.city_code');
			$this->db->order_by('c.city_name');
			$this->db->limit($perpage,$offset);
            $result = $this->db->get();
            $data['filtered_cities']=$this->db->result($result);	            

            $this->db->flush_cache();
			$this->db->select("c.city_name,c.city_code,count(r.resturantbrand_city) as Totalrest");
			$this->db->from("tabqy_cities as c");
			$this->db->join("tabqy_regions as re","FIND_IN_SET(c.city_code,re.city_code) !=0","LEFT");
			$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_city=c.city_code AND r.resturantbrand_type !=0","left");
			$this->db->where($where);
			$this->db->group_by('c.city_code');
			$this->db->order_by('c.city_name');
            $query1 = $this->db->get();	
            $data['actual_count']=$query1->rowCount();
		
		
		echo json_encode($data);die;
	}
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  load_more
	  DESCRIPTION : 
     **********************************/
     public function load_more(){
		
		$perpage = $this->perpage;
		$country_code = $_POST['country'];
		$counter = $_POST['offset'];
		$offset = ( $counter * $perpage);
		$region_code='';
		if(!empty($_POST['region_code'])){ $region_code =$_POST['region_code']; }
		$sort = 'city_name';

		if($region_code){ 
			$where = 're.region_code="'.$region_code.'" AND c.country_code="'.$country_code.'"';
		}else{
			$where = 'c.country_code="'.$country_code.'"';
		}
		$this->db->flush_cache();			            
		$this->db->select("c.city_name,c.city_code,count(r.resturantbrand_city) as Totalrest");
		$this->db->from("tabqy_cities as c");
		$this->db->join("tabqy_regions as re","FIND_IN_SET(c.city_code,re.city_code) !=0","LEFT");
		$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_city=c.city_code AND r.resturantbrand_type !=0","left");
		$this->db->where($where);
		$this->db->group_by('c.city_code');
		$this->db->order_by('c.city_name');
		$this->db->limit($perpage,$offset);
		$result = $this->db->get();
		$data['next_cities']=$this->db->result($result);
		
		$this->db->flush_cache();			            
		$this->db->select("c.city_name,c.city_code,count(r.resturantbrand_city) as Totalrest");
		$this->db->from("tabqy_cities as c");
		$this->db->join("tabqy_regions as re","FIND_IN_SET(c.city_code,re.city_code) !=0","LEFT");
		$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_city=c.city_code AND r.resturantbrand_type !=0","left");
		$this->db->where($where);
		$this->db->group_by('c.city_code');
		$this->db->order_by('c.city_name');
		$actual_q = $this->db->get();
		$data['actual_count'] = $actual_q->rowCount();
		echo json_encode($data);die;		
	}

}