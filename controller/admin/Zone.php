<?php

class Zone extends Controller{

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

	public function index(){

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/zone/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$search = '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_regions'); //Total no. of values

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

		$this->db->select('z.*, co.country_code, co.country_name, ci.city_code, ci.city_name');

		$this->db->from('tabqy_zone as z');

		$this->db->join('tabqy_countries as co', "co.country_code=z.country_code", "inner");

		$this->db->join('tabqy_cities as ci', "ci.city_code=z.city_code", "inner");

		$this->db->order_by('z.zone_id','DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();

		$data['results'] = $this->db->result($query);

		$this->db->flush_cache();

		$this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

		$this->db->from('tabqy_countries as co');
                $this->db->join('tabqy_countries_language as l',"l.country_language_country_id=co.country_id","inner");
		$this->db->where('l.country_language_language_code',$_SESSION['lang_code']);

		$this->db->order_by('co.country_name','ASC');

		$query_c = $this->db->get();

		$data['related_country'] = $this->db->result($query_c);
				

		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['title']="Location";

		$data['heading']="Zone";

		$data['user_id']="";

		$data['page_number']=$page_number;

		$start=$page_position+1;

		$data['start']=$start;

		$data['end']=($start+count($data['results'])-1);

		view_loader('admin/country/view_zone.html',$data);		

	}

	public function check_existence($field,$value,$id=''){
	    $this->db->select('*');
		$this->db->from('tabqy_zone');
		$this->db->where($field,$value);
		if($id!=''){
			$this->db->where('zone_id<>',$id);
		}
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];		
	}

	//Add ZONE

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
		$this->validation->validate("country_code","Country","required");

		$this->validation->validate("city_code","City","required");

		$this->validation->validate("zone_name","Zone Name","required"); 
		
		$this->validation->validate("zone_code","Zone Code","required");
		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']  = $this->validation->error_msg;

			$data['set_data']   = $_POST;

			$data['error']      = "true";

			echo json_encode($data);die;

		}else{	
                        $language = $this->language;
                        if(!empty($_POST['zone_name']) || !empty($_POST['zone_code'])){				
                            $duplicate_zone_name = $this->check_existence('zone_name',$_POST['zone_name']);
                            if($duplicate_zone_name){
                                            $data['error_msg']['zone_name']="Zone Name already exists.";
                            }
                            $duplicate_zone_code = $this->check_existence('zone_code',$_POST['zone_code']);
                            if($duplicate_zone_code){
                                            $data['error_msg']['zone_code']="Zone Code already exists.";
                            }
                        }
                        if(!empty($data['error_msg'])){
                            $data['error']="true";
                            $data['msg']="false";
                            echo json_encode($data);die;
                        }else{
                                unset($_POST['submit']);

                                unset($_POST['zone_id']);

                                $data['send_data']= $_POST;

                                $locdata = $data['send_data']['location_code'];

                                foreach($locdata as $ldata){
                                        $this->db->flush_cache();
                                        $this->db->where('location_code',$ldata);
                                        $this->db->update('tabqy_locations', array('is_zone'=>1));						
                                }                       
                                
                                if($data['send_data']['location_code'] !='')
                                        $data['send_data']['location_code'] = implode(',', $data['send_data']['location_code']);				

                                $data['send_data']['zone_status']=1;

                                $data['send_data']['zone_created']=date('Y-m-d H:i:s');

                                $insert_id = $this->db->insert('tabqy_zone', $data['send_data']);                               
                               
                                foreach ($language as $languages) {
                                    $edit              = ($languages['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                                    $add_data_language = array(
                                                        "zone_language_zone_id"     => $insert_id,
                                                        "zone_language_code"        => $languages['language_code'],
                                                        "zone_language_zone_name"   => $_POST['zone_name'],
                                                        "zone_language_edit"        => $edit,
                                                        "zone_language_status"      => '1'
                                                );

                                    $this->db->insert('tabqy_zone_language', $add_data_language);
                                }

                                $data['error']="false";

                                $data['msg']="true";

                                $data['success_message']="Zone Added Successfully";

                                echo json_encode($data);die;
                        }
		}

    }

	

	// Delete ZONE

	Public function delete($id){
            if ($_SESSION['userdata']['user_role'] != 0) {
            
                if ($this->privillage['delete'] != 1) {
                    $data['error']           = "permission";
                    $data['msg']             = "false";
                    $data['success_message'] = "You don't have permission to perform this action";
                    echo json_encode($data);
                    exit();
                }
            }
            
            $this->db->flush_cache();
            $this->db->select('location_code');
            $this->db->from('tabqy_zone');
            $this->db->where('zone_id',$id);
            $que = $this->db->get();
            $res = $this->db->result($que);
            $explode_old_loc = explode(',', $res[0]['location_code']);
            foreach($explode_old_loc as $oldloc){
                    $this->db->flush_cache();
                    $this->db->where('location_code',$oldloc);
                    $this->db->update('tabqy_locations',array('is_zone'=>0));
            }

            $this->db->flush_cache();
            $this->db->where('zone_id', $id);
            $this->db->delete('tabqy_zone'); 
            
            $this->db->where('zone_language_zone_id', $id);
            $this->db->delete('tabqy_zone_language');
            $data['error']           = "false";
            $data['msg']             = "false";
            $data['success_message'] = "Zone successfully deleted";
            echo json_encode($data);
            exit();		

	}

	Public function related_cities($co_id){

		$this->db->flush_cache();

		$this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');

		$this->db->from('tabqy_cities as city');
                $this->db->join('tabqy_cities_language as lang','lang.city_language_city_id=city.city_id','inner');
		$this->db->where('city.country_code',$co_id);
		$this->db->where('lang.city_language_language_code',$_SESSION['lang_code']);

		$this->db->order_by('city.city_name','ASC');

		$query_c = $this->db->get();

		$data['related_cities'] = $this->db->result($query_c);

		echo json_encode($data);die;

	}

	

	Public function related_locations($ci_id){

		$this->db->flush_cache();
                $this->db->select('lang.location_language_location_name, lang.location_language_language_code,loc.*');

		$this->db->from('tabqy_locations as loc');
                $this->db->join('tabqy_locations_language as lang','lang.location_language_location_id=loc.location_id','inner');
		$this->db->where('loc.city_code',$ci_id);
		$this->db->where('loc.is_zone<>',1);
                $this->db->where('lang.location_language_language_code',$_SESSION['lang_code']);
		$this->db->order_by('loc.location_name','ASC');

		$query = $this->db->get();

		$data['related_locations'] = $this->db->result($query);

		echo json_encode($data);die;

	}

	

	// Edit ZONE

	Public function edit($id){
            
            if ($_SESSION['userdata']['user_role'] != 0) {            
                if ($this->privillage['edit'] != 1) {
                    $data['error']           = "permission";
                    $data['msg']             = "false";
                    $data['success_message'] = "You don't have permission to perform this action";
                    echo json_encode($data);
                    exit();
                }
            }

		$this->validation->validate("zone_name","Zone Name","required");
		
		$this->validation->validate("zone_code","Zone Code","required");

		$this->validation->validate("country_code","Country","required");

		$this->validation->validate("city_code","City","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{
                        if(!empty($_POST['zone_name'])){				

                                $duplicate_zone_name = $this->check_existence('zone_name',$_POST['zone_name'],$id);
                                if($duplicate_zone_name){
                                        $data['error_msg']['zone_name'] = "Zone Name already exists.";
                                }
                                /*$duplicate_zone_code = $this->check_existence('zone_code',$_POST['zone_code'],$id);
                                if($duplicate_zone_code){
                                                $data['error_msg']['zone_code']="Zone Code already exists.";
                                }*/
                        }
				
                        if(!empty($data['error_msg'])){

                                $data['error']="true";
                                $data['msg']="false";

                                echo json_encode($data);die;

                        }else{

                                $this->db->flush_cache();
                                $this->db->select('location_code');
                                $this->db->from('tabqy_zone');
                                $this->db->where('zone_id',$id);
                                $que = $this->db->get();
                                $res = $this->db->result($que);

                                $explode_old_loc = explode(',', $res[0]['location_code']);

                                foreach($explode_old_loc as $oldloc){
                                        $this->db->flush_cache();
                                        $this->db->where('location_code',$oldloc);
                                        $this->db->update('tabqy_locations',array('is_zone'=>0));
                                }

                                unset($_POST['submit']);
                                $language_code = $_POST['language_code'];
                                $zone_name = $_POST['zone_name'];
                                unset($_POST['language_code']);
                                
                                if ($_SESSION['lang_code'] != $language_code) {
                                    unset($_POST['zone_name']);
                                }

                                $data['set_data']= $_POST;

                                $new_loc_data = $data['set_data']['location_code'];

                                foreach($new_loc_data as $ldata){
                                        $this->db->flush_cache();
                                        $this->db->where('location_code',$ldata);
                                        $this->db->update('tabqy_locations', array('is_zone'=>1));						
                                }

                                if($data['set_data']['location_code'] != '')

                                        $data['set_data']['location_code'] = implode(',', $data['set_data']['location_code']);					

                                $this->db->where('zone_id', $id);

                                $this->db->update('tabqy_zone', $data['set_data']);
                                
                                $edit_data_language = array(
                                            "zone_language_zone_name" => $zone_name,
                                            "zone_language_edit" => '1'
                                        );            
            
                                $this->db->where('zone_language_zone_id', $id);
                                $this->db->where('zone_language_code', $language_code);
                                $this->db->update('tabqy_zone_language', $edit_data_language);

                                $data['error']="false";

                                $data['msg']="true";

                                $data['success_message']="Zone Updated Successfully";

                                echo json_encode($data);die;
                        }
		}		

	} 
        
	function updateStatus($id, $value){

		$this->db->flush_cache();

		$this->db->where('zone_id', $id);

		$this->db->update('tabqy_zone',array('zone_status'=>$value));

		$this->db->flush_cache();

		$this->db->select('zone_status,zone_id');

		$this->db->from('tabqy_zone');

		$this->db->where('zone_id',$id);

		$this->db->flush_cache();

		$query = $this->db->get();

		$data['cur_status'] = $this->db->result($query);

		echo json_encode($data);die;

	 }

	

	function edit_view($zone_id,$language_code=''){                
                
		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_zone');
                if($language_code)
                $this->db->join('tabqy_zone_language', "tabqy_zone_language.zone_language_zone_id = tabqy_zone.zone_id", 'inner');

		$this->db->where('zone_id', $zone_id);
                if($language_code)
                $this->db->where('tabqy_zone_language.zone_language_code', $language_code);	

		$query = $this->db->get();

		$data['edit_view'] = $this->db->result($query);

		$selected_locations = explode(',',$data['edit_view'][0]['location_code']);
		
		foreach($selected_locations as $selected){
			$this->db->select('*');
			$this->db->from('tabqy_locations');
			$this->db->where('location_code',$selected);
			$que = $this->db->get();
			$data['selected_locations'][] = $this->db->result($que);
		}

		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_cities');

		$this->db->where('country_code',$data['edit_view'][0]['country_code']);

		$query1 = $this->db->get();

		$data['all_cities'] = $this->db->result($query1);

		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_locations');
		
		$this->db->where('is_zone',0);

		$this->db->where('tabqy_locations.city_code',$data['edit_view'][0]['city_code']);

		$query2 = $this->db->get();

		$data['all_free_locations'] = $this->db->result($query2);



		echo json_encode($data);die;

		

	}

}