<?php

class Location extends Controller{

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

		$this->db->select('lang.language_name,lang.language_code,lang.language_flag');

		$this->db->from('tabqy_language as lang');

		$this->db->order_by('lang.language_id','DESC');

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

	public function home($city_code){ 
		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "Location";

		$data['heading'] = "Home Location ";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');
		$perpage = $this->perpage;;
		$offset = 0;
	
		$sort = 'location_name';
		
		$this->db->flush_cache();

		$this->db->select('loc.*, count(r.resturantbrand_id) as Totalrest');

		$this->db->from('tabqy_locations loc');
		
		$this->db->join('tabqy_resturantbrand as r','r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0','left');
		$this->db->group_by('loc.location_code');
		$this->db->where('city_code',$city_code);
	
		$this->db->order_by('loc.'.$sort,'ASC');

		$query = $this->db->get();
		$data['actual_count'] = $query->rowCount();

    	$this->db->flush_cache();

		$this->db->select('loc.*, count(r.resturantbrand_id) as Totalrest');

		$this->db->from('tabqy_locations loc');
		
		$this->db->join('tabqy_resturantbrand as r','r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0','left');
		$this->db->group_by('loc.location_code');
		$this->db->where('city_code',$city_code);
	
		$this->db->order_by('loc.'.$sort,'ASC');
		$this->db->limit($perpage,$offset);
		$query = $this->db->get();
		$data['location_count'] = $query->rowCount();

		$data['results']=$this->db->result($query);
		
		if($data['actual_count'] > $perpage)
			$data['load_more'] = true;
		
		$this->db->flush_cache();
		$this->db->select('ci.city_name,co.country_name,co.country_code,ci.city_code');
		$this->db->from('tabqy_cities as ci');
		$this->db->join('tabqy_countries as co', 'ci.country_code=co.country_code','inner');
		$this->db->where('city_code',$city_code);
		$que = $this->db->get();
		$country_name = $this->db->row($que);
		$data['breadcrumb'] = $country_name;
		
		$this->db->flush_cache();

		$this->db->select('tabqy_countries.*');

		$this->db->from('tabqy_countries');

		$this->db->order_by('tabqy_countries.country_name','ASC');

		$query_c = $this->db->get();

		$data['related_country'] = $this->db->result($query_c);
		
		$data['zone_list'] = $this->all_zones($city_code);

    	view_loader('admin/country/location_home.html',$data);	
	}
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  all_zones
          PARAMETER:   CITY CODE
	  DESCRIPTION : Used to get all zones
     **********************************/
	public function all_zones($city_code){
		$this->db->flush_cache();
		$this->db->select('zone_code,zone_name');
		$this->db->from('tabqy_zone');
		$this->db->where('city_code',$city_code);
		$this->db->order_by('zone_name','ASC');
		$query = $this->db->get();
		$res = $this->db->result($query);
		return $res;
	}
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  index
	  DESCRIPTION :  Fetch all record
     **********************************/
	public function index(){

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "Location";

		$data['heading'] = "Location";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/location/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort[] 		 = array();

		$sort['loc']	 = 'location_id';

		$sort['country'] = 'country_name';

		$sort['city']	 = 'city_name';

		$search = '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_locations'); //Total no. of values

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

		$this->db->select('loc.*, ci.city_code,ci.city_name, co.country_code, co.country_name');

		$this->db->from('tabqy_locations as loc');

		$this->db->join('tabqy_cities as ci', "ci.city_code=loc.city_code","inner");

		$this->db->join('tabqy_countries as co', "co.country_code=loc.country_code", "inner");

		$this->db->order_by('loc.'.$sort['loc'],'DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();

		$data['results']=$this->db->result($query);		
     //echo"<pre>";  print_r($data['results']);die();
		$this->db->flush_cache();

		$this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

		$this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l',"l.country_language_country_id=co.country_id","inner");
		$this->db->where('l.country_language_language_code',$_SESSION['lang_code']);
            $query_c = $this->db->get();
		$data['related_country'] = $this->db->result($query_c);
		//echo"<pre>"; print_r($data['related_country']);die;

		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['user_id']="";

		$data['page_number']=$page_number;

		$start=$page_position+1;

		$data['start']=$start;

		$data['end']=($start+count($data['results'])-1);

		view_loader('admin/country/view_location.html',$data);		

	}

	

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  add
	  DESCRIPTION : Insert Location
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
		$this->validation->validate("location_name","Location Name","required");
		
		$this->validation->validate("location_code","Location Code","required");

		$this->validation->validate("country_code","Country","required");

		$this->validation->validate("city_code","City","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{	
			
			if(!empty($_POST['location_name']) || !empty($_POST['location_code'])){				
				$duplicate_loc_name = $this->check_existence('location_name',$_POST['location_name']);
				if($duplicate_loc_name){
						$data['error_msg']['location_name']="Location Name already exists.";
				}
				$duplicate_loc_code = $this->check_existence('location_code',$_POST['location_code']);
				if($duplicate_loc_code){
						$data['error_msg']['location_code']="Location Code already exists.";
				}
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
				unset($_POST['submit']);

				unset($_POST['location_id']);

				$data['send_data']= $_POST;
				$data['send_data']['location_status']=1;

				$data['send_data']['location_created']=date('Y-m-d H:i:s');

			$insert_id = $this->db->insert('tabqy_locations', $data['send_data']);
				
				 foreach ($language as $languages) {
                $edit              = ($languages['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                $add_data_language = array(
                    "location_language_location_id"   => $insert_id,
                    "location_language_language_code" => $languages['language_code'],
                    "location_language_location_name" =>       $_POST['location_name'],
                    "location_language_edit"          => $edit,
                    "location_language_status"        => '1'
                );
                
               $this->db->insert('tabqy_locations_language', $add_data_language);
           }

				$data['error']="false";

				$data['msg']="true";

				$data['success_message']="Location Added Successfully";

				echo json_encode($data);die;
			}
		}

    }

	

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  delete
	  DESCRIPTION : Delete data 
     **********************************/

	Public function delete($loc_id){
		
	if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['delete'] != 1) {
                $data['error']           = "true";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }

			$this->db->where('location_id', $loc_id);

            $this->db->delete('tabqy_locations'); 

			 $this->db->where('location_language_location_id', $loc_id);
             $this->db->delete('tabqy_locations_language');

			 $data['error']           = "false";
			  $data['msg']             = "false";
			  $data['success_message'] = "Location success fully deleted";
			   echo json_encode($data);
			   exit();		

	}
    /***********************************
	  DEVELOPER:  Priyanjali
	  DATE     :  01/05/2018
	  FUNCTION :  related_cities
	  DESCRIPTION : Get country related cities
     **********************************/
	

	Public function related_cities($co_id){
		
		$location_id = $this->url->segment(4);
		$language_code = $this->url->segment(5);

		$this->db->flush_cache();

		$this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');
		$this->db->from('tabqy_cities as city');
                $this->db->join('tabqy_cities_language as lang','lang.city_language_city_id=city.city_id','inner');
		$this->db->where('city.country_code',$co_id);
                $this->db->where('lang.city_language_language_code',$_SESSION['lang_code']);
		$this->db->order_by('city.city_name','ASC');

		$query_c = $this->db->get();

		$data['related_cities'] = $this->db->result($query_c);
            // print_r($data['related_cities']);die();
		echo json_encode($data);die;

	}

	

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  edit
	  DESCRIPTION : updating record of table
     **********************************/

	Public function edit($loc_id){
         
        if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['edit'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
		$this->validation->validate("location_name","Location Name","required");
		
		$this->validation->validate("location_code","Location Code","required");

		$this->validation->validate("country_code","Country Name","required");

		$this->validation->validate("city_code","City Name","required");		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{
			if(!empty($_POST['location_name'])){				
				$duplicate_location_name = $this->check_existence('location_name',$_POST['location_name'],$loc_id);
				if($duplicate_location_name){
						$data['error_msg']['location_name']="Location Name already exists.";
				}
				/*$duplicate_location_code = $this->check_existence('location_code',$_POST['location_code'],$loc_id);
				if($duplicate_location_code){
						$data['error_msg']['location_code']="Location Code already exists.";
				}*/
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
                        }else{
                            $loc_id =$this->url->segment(4);
                            unset($_POST['submit']);
                            $language_code = $_POST['language_code'];
                            $location_name = $_POST['location_name'];
                            unset($_POST['language_code']);
                            if ($_SESSION['lang_code'] == $language_code) {
                                $data['set_data']= $_POST;
                                $this->db->where('location_id', $loc_id);
                                $this->db->update('tabqy_locations', $data['set_data']);
                            }

                            $this->db->flush_cache();
			    $edit_data_language = array(
                                                "tabqy_locations_language.location_language_location_name" => $location_name,
                                                "tabqy_locations_language.location_language_edit" => '1'
                                            );   
                    $this->db->where('location_language_location_id', $loc_id);
	            $this->db->where('location_language_language_code', $language_code);
	            $this->db->update('tabqy_locations_language', $edit_data_language);

                    $data['error']="false";
                    $data['msg']="true";
                    $data['success_message']="Location Updated Successfully";
                 echo json_encode($data);die;;
			}
		}		

	} 
 
     /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  edit_view
	  DESCRIPTION : Update data
     **********************************/
	 public function edit_view($id){
     
	    $location_id = $this->url->segment(4);
		$language_code = $this->url->segment(5);
		$ci_id = $this->url->segment(6);
		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_locations');
		 $this->db->join('tabqy_locations_language', "tabqy_locations_language.location_language_location_id = tabqy_locations.location_id", 'inner');

		$this->db->where('tabqy_locations.location_id',$location_id);
        $this->db->where('tabqy_locations_language.location_language_language_code', $language_code);		
           
		$query = $this->db->get();

		$data['edit_view']=$this->db->result($query);
		
		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_cities');

		$this->db->where('country_code',$ci_id);

		$this->db->order_by('city_name','ASC');

		$query_c = $this->db->get();

		$data['related_cities'] = $this->db->result($query_c);

        $data['error']="false";
	    $data['msg']="true";
	    $data['success_message']="Location Updated Successfully";
		echo json_encode($data);die;

	} 
     /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  updateStatus
	  DESCRIPTION : status update
     **********************************/
	function updateStatus($id, $value){

		$this->db->flush_cache();

		$this->db->where('location_id', $id);

		$this->db->update('tabqy_locations',array('location_status'=>$value));

		$this->db->flush_cache();

		$this->db->select('location_status,location_id');

		$this->db->from('tabqy_locations');

		$this->db->where('location_id', $id);

		$query = $this->db->get();

		$data['cur_status'] = $this->db->result($query);

		echo json_encode($data);die;

	 }
	   /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  check_existence
	  DESCRIPTION : 
     **********************************/
	public function check_existence($field,$value,$id=''){
	    $this->db->select('*');
		$this->db->from('tabqy_locations');
		$this->db->where($field,$value);
		if($id!=''){
			$this->db->where('location_id<>',$id);
		}
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];		
	}
	 /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  restaurants
	  DESCRIPTION : 
     **********************************/
	public function restaurants($location_code){
		//$this->db->flush_cache();
			$data['title']	 = "Location";

		$data['heading'] = "Location";

		$data['action']  ='list';
		$this->db->flush_cache();
		$this->db->select('ci.city_name,ci.city_code,co.country_name,co.country_code,lo.location_name,lo.location_code');
		$this->db->from('tabqy_locations as lo');
		$this->db->join('tabqy_cities as ci', 'ci.city_code=lo.city_code','inner');
		$this->db->join('tabqy_countries as co', 'ci.country_code=lo.country_code','inner');
		$this->db->where('location_code',$location_code);
		$que = $this->db->get();
		$country_name = $this->db->result($que);
		$data['breadcrumb'] = $country_name[0];
    		$this->db->flush_cache();
		$this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name,t2.resturantbrand_id as t2resturantbrand_id');
		$this->db->from('tabqy_resturantbrand t2');
		$this->db->join('tabqy_resturantbrand t1','t1.resturantbrand_type=t2.resturantbrand_id','RIGHT');
		$this->db->order_by('t1.resturantbrand_id','DESC');
		$this->db->where('t1.resturantbrand_location',$location_code);
		$this->db->where('t1.resturantbrand_type !=',0);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
	//	echo "<pre>";
	//	print_r($data);die;
		view_loader('admin/country/restaurant_home.html',$data);	
	}
      /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  04/27/2018
	  FUNCTION :  zone_filter
	  DESCRIPTION : 
     **********************************/
	public function zone_filter($zone_code=''){
		$city_code = $_POST['city'];
		$perpage = $this->perpage;
		$offset = $_POST['counter'];
		
		if($zone_code!=''){
			$where = 'zo.zone_code="'.$zone_code.'" AND loc.city_code="'.$city_code.'"';
		}else{
			$where = 'loc.city_code="'.$city_code.'"';
		}
			$this->db->flush_cache();
			
			//$sql='SELECT loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest FROM tabqy_locations as loc LEFT JOIN tabqy_zone as zo ON FIND_IN_SET(loc.location_code,zo.location_code) LEFT JOIN tabqy_resturantbrand as r ON r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0 where ' .$where. ' group by loc.location_code order by loc.location_name LIMIT '.$offset.','.$perpage;
			
			$this->db->flush_cache();			            
			$this->db->select("loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest");
			$this->db->from("tabqy_locations as loc");
			$this->db->join("tabqy_zone as zo","FIND_IN_SET(loc.location_code,zo.location_code) !=0","LEFT");
			$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0","left");
			$this->db->where($where);
			$this->db->group_by('loc.location_code');
			$this->db->order_by('loc.location_name');
			$this->db->limit($perpage,$offset);
            $result = $this->db->get();
            $data['filtered_locations'] = $this->db->result($result);
			
			$this->db->flush_cache();
			
			//$sql1='SELECT loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest FROM tabqy_locations as loc LEFT JOIN tabqy_zone as zo ON FIND_IN_SET(loc.location_code,zo.location_code) LEFT JOIN tabqy_resturantbrand as r ON r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0 where ' .$where. ' group by loc.location_code order by loc.location_name';
			
			$this->db->flush_cache();			            
			$this->db->select("loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest");
			$this->db->from("tabqy_locations as loc");
			$this->db->join("tabqy_zone as zo","FIND_IN_SET(loc.location_code,zo.location_code) !=0","LEFT");
			$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0","left");
			$this->db->where($where);
			$this->db->group_by('loc.location_code');
			$this->db->order_by('loc.location_name');
            $result1 = $this->db->get();
            $data['actual_count'] = $result1->rowCount();

		echo json_encode($data);die;
	}
	
	public function load_more(){
		$zone_code ='';
		$perpage = $this->perpage;
		if(!empty($_POST['city']))
		$city_code = $_POST['city'];
		if(!empty($_POST['offset']))
		$counter = $_POST['offset'];
		$offset = ( $counter * $perpage);
		$sort = 'location_name';
		if(!empty($_POST['zone_code'])){ 
			$zone_code =$_POST['zone_code']; 
		}
		
		if($zone_code!=''){
			$where = 'zo.zone_code="'.$zone_code.'" AND loc.city_code="'.$city_code.'"';
		}else{
			$where = 'loc.city_code="'.$city_code.'"';
		}
			$this->db->flush_cache();
			
			//$sql='SELECT loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest FROM tabqy_locations as loc LEFT JOIN tabqy_zone as zo ON FIND_IN_SET(loc.location_code,zo.location_code) LEFT JOIN tabqy_resturantbrand as r ON r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0 where ' .$where. ' group by loc.location_code order by loc.location_name LIMIT '.$offset.','.$perpage;
			
			$this->db->flush_cache();			            
			$this->db->select("loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest");
			$this->db->from("tabqy_locations as loc");
			$this->db->join("tabqy_zone as zo","FIND_IN_SET(loc.location_code,zo.location_code) !=0","LEFT");
			$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0","left");
			$this->db->where($where);
			$this->db->group_by('loc.location_code');
			$this->db->order_by('loc.location_name');
			$this->db->limit($perpage,$offset);
            $result = $this->db->get();
            $data['next_locations'] = $this->db->result($result);
			
			$this->db->flush_cache();
			
			//$sql1='SELECT loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest FROM tabqy_locations as loc LEFT JOIN tabqy_zone as zo ON FIND_IN_SET(loc.location_code,zo.location_code) LEFT JOIN tabqy_resturantbrand as r ON r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0 where ' .$where. ' group by loc.location_code order by loc.location_name';
			
			$this->db->flush_cache();			            
			$this->db->select("loc.location_name,loc.location_code, count(r.resturantbrand_id) as Totalrest");
			$this->db->from("tabqy_locations as loc");
			$this->db->join("tabqy_zone as zo","FIND_IN_SET(loc.location_code,zo.location_code) !=0","LEFT");
			$this->db->join("tabqy_resturantbrand as r","r.resturantbrand_location=loc.location_code AND r.resturantbrand_type !=0","left");
			$this->db->where($where);
			$this->db->group_by('loc.location_code');
			$this->db->order_by('loc.location_name');
            $result1 = $this->db->get();
            $data['actual_count'] = $result1->rowCount();		
		
		echo json_encode($data);die;		
	}

}