<?php

class Region extends Controller{

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
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  index
	  DESCRIPTION : fetch all record 
     **********************************/

	public function index(){

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/region/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort[] 		 = array();

		$sort['reg']	 = 'region_id';

		$sort['country'] = 'country_name';

		$sort['city']	 = 'city_name';

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

		$this->db->select('reg.*, co.country_code, co.country_name');

		$this->db->from('tabqy_regions as reg');

		$this->db->join('tabqy_countries as co', "co.country_code = reg.country_code", "inner");

		$this->db->order_by('reg.'.$sort['reg'],'DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();

		$data['results'] = $this->db->result($query);	

		

		$this->db->flush_cache();

		$this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

		$this->db->from('tabqy_countries as co');
                $this->db->join('tabqy_countries_language as l',"l.country_language_country_id=co.country_id","inner");
		$this->db->where('l.country_language_language_code',$_SESSION['lang_code']);

		$query_c = $this->db->get();

		$data['related_country'] = $this->db->result($query_c);

				

		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['title']="Location";

		$data['heading']="Region";

		$data['user_id']="";

		$data['page_number']=$page_number;

		$start=$page_position+1;

		$data['start']=$start;

		$data['end']=($start+count($data['results'])-1);

		view_loader('admin/country/view_region.html',$data);		

	}
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  check_existence
	  DESCRIPTION : 
     **********************************/

	public function check_existence($field,$value,$id=''){
	    $this->db->select('*');
		$this->db->from('tabqy_regions');
		$this->db->where($field,$value);
		if($id!=''){
			$this->db->where('region_id<>',$id);
		}
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];		
	}

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
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
		$this->validation->validate("country_code","Country","required");

		$this->validation->validate("region_code","Region Code","required");

		$this->validation->validate("region_name","Region Name","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg'] = $this->validation->error_msg;

			$data['set_data'] = $_POST;

			$data['error'] = "true";

			echo json_encode($data);die;

		}else{	
			if(!empty($_POST['region_name']) || !empty($_POST['region_code'])){				
				$duplicate_region_name = $this->check_existence('region_name',$_POST['region_name']);
				if($duplicate_region_name){
						$data['error_msg']['region_name']="Region Name already exists.";
				}
				$duplicate_region_code = $this->check_existence('region_code',$_POST['region_code']);
				if($duplicate_region_code){
						$data['error_msg']['region_code']="Region Code already exists.";
				}
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
					unset($_POST['submit']);

					unset($_POST['region_id']);

					$data['send_data']= $_POST;
					
					$citydata = $data['send_data']['city_code'];
					
					foreach($citydata as $cdata){
						$this->db->flush_cache();
						$this->db->where('city_code',$cdata);
						$this->db->update('tabqy_cities', array('is_region'=>1));
						$data['city_success_message']="City's region status updated Successfully";
					}

					if($data['send_data']['city_code'] !='')

					$data['send_data']['city_code'] = implode(',', $data['send_data']['city_code']);				

					$data['send_data']['region_status']=1;

					$data['send_data']['region_created'] = date('Y-m-d H:i:s');

					$insert_id = $this->db->insert('tabqy_regions', $data['send_data']);
					
					foreach ($language as $languages) {
                                            $edit              = ($languages['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                                            $add_data_language = array(
                                                                "region_language_region_id"   => $insert_id,
                                                                "region_language_language_code" => $languages['language_code'],
                                                                "region_language_region_name" =>       $_POST['region_name'],
                                                                "region_language_edit"          => $edit,
                                                                "region_language_status"        => '1'
                                                        );
                
                                            $this->db->insert('tabqy_regions_language', $add_data_language);
                                        }

					$data['error']="false";

					$data['msg']="true";

					$data['success_message']="Region Added Successfully";

					echo json_encode($data);die;

		}
		}

    }
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  delete
	  DESCRIPTION : delete record of table
     **********************************/

	Public function delete($reg_id){
		
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
			$this->db->select('city_code');
			$this->db->from('tabqy_regions');
			$this->db->where('region_id',$reg_id);
			$que = $this->db->get();
			$res = $this->db->result($que);
			$explode_old_city = explode(',', $res[0]['city_code']);
				foreach($explode_old_city as $oldcity){
					$this->db->flush_cache();
					$this->db->where('city_code',$oldcity);
					$this->db->update('tabqy_cities',array('is_region'=>0));
				}
			$this->db->flush_cache();
			$this->db->where('region_id', $reg_id);

                        $this->db->delete('tabqy_regions'); 
			$this->db->where('region_language_region_id', $reg_id);
                        $this->db->delete('tabqy_regions_language');
                        $data['error']           = "false";
                        $data['msg']             = "false";
                        $data['success_message'] = "Region successfully deleted";
                        echo json_encode($data);
                        exit();	

	}

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  related_cities
	  DESCRIPTION : fetch record of city related to country
        **********************************/

	Public function related_cities($co_id=''){

		$this->db->flush_cache();

		$this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');

		$this->db->from('tabqy_cities as city');
                $this->db->join('tabqy_cities_language as lang','lang.city_language_city_id=city.city_id','inner');
		$this->db->where('city.country_code',$co_id);
		$this->db->where('lang.city_language_language_code',$_SESSION['lang_code']);
		$this->db->where('city.is_region<>',1);

		$this->db->order_by('city.city_name','ASC');

		$query_c = $this->db->get();

		$data['related_cities'] = $this->db->result($query_c);
        
		echo json_encode($data);die;          
	}

	

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  edit
	  DESCRIPTION : updating record of table
     **********************************/

	Public function edit($reg_id){
		
	if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['edit'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }

		$this->validation->validate("region_name","Region Name","required");

		$this->validation->validate("country_code","Country","required");

		$this->validation->validate("region_code","Region Code","required");		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{
			if(!empty($_POST['region_name']) ){				
				$duplicate_region_name = $this->check_existence('region_name',$_POST['region_name'],$reg_id);
				if($duplicate_region_name){
						$data['error_msg']['region_name']="Region Name already exists.";
				}
				/*$duplicate_region_code = $this->check_existence('region_code',$_POST['region_code'],$reg_id);
				if($duplicate_region_code){
						$data['error_msg']['region_code']="Region Code already exists.";
				}*/
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
                        }else{
				$this->db->flush_cache();
				$this->db->select('city_code');
				$this->db->from('tabqy_regions');
				$this->db->where('region_id',$reg_id);
				$que = $this->db->get();
				$res = $this->db->result($que);
				$explode_old_city = explode(',', $res[0]['city_code']);
				foreach($explode_old_city as $oldcity){
					$this->db->flush_cache();
					$this->db->where('city_code',$oldcity);
					$this->db->update('tabqy_cities',array('is_region'=>0));
				}
				unset($_POST['submit']);
                                $language_code = $_POST['language_code'];
                                $region_name = $_POST['region_name'];
                                unset($_POST['language_code']);
                                
                                if ($_SESSION['lang_code'] != $language_code) {
                                    unset($_POST['region_name']);
                                }
                                $data['set_data']= $_POST;
                                $new_city_data = $data['set_data']['city_code'];
                                
                                foreach($new_city_data as $cdata){
                                        $this->db->flush_cache();
                                        $this->db->where('city_code',$cdata);
                                        $this->db->update('tabqy_cities', array('is_region'=>1));
                                        $data['city_success_message']="City's region status updated Successfully";
                                }
				$data['set_data']['city_code'] = implode(',', $data['set_data']['city_code']);
				$this->db->where('region_id', $reg_id);

				$this->db->update('tabqy_regions', $data['set_data']);
                                
                                $edit_data_language = array(
                                            "region_language_region_name" => $region_name,
                                            "region_language_edit" => '1'
                                        );            
            
                                $this->db->where('region_language_region_id', $reg_id);
                                $this->db->where('region_language_language_code', $language_code);
                                $this->db->update('tabqy_regions_language', $edit_data_language);

				$data['error']="false";

				$data['msg']="true";

				$data['success_message']="Region Updated Successfully";

				echo json_encode($data);
                                die;
			}
		}		

	} 


     /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  updateStatus
	  DESCRIPTION : status update
     **********************************/
	 
	function updateStatus($id, $value){

		$this->db->flush_cache();

		$this->db->where('region_id', $id);

		$this->db->update('tabqy_regions',array('region_status'=>$value));

		$this->db->flush_cache();

		$this->db->select('region_status,region_id');

		$this->db->from('tabqy_regions');

		$this->db->where('region_id', $id);

		$query = $this->db->get();

		$data['cur_status'] = $this->db->result($query);

		echo json_encode($data);die;

	 }

	 /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  30-04-2018
	  FUNCTION :  edit_view
	  DESCRIPTION : fetch record 
     **********************************/

	function edit_view($id){
         
                $region_id = $this->url->segment(4);
		$language_code = $this->url->segment(5);
		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_regions');
                if($language_code!='')
		$this->db->join('tabqy_regions_language', "tabqy_regions_language.region_language_region_id = tabqy_regions.region_id", 'inner');

		$this->db->where('tabqy_regions.region_id',$region_id);
                if($language_code!='')
                $this->db->where('tabqy_regions_language.region_language_language_code', $language_code);

		$query = $this->db->get();

		$data['edit_view'] = $this->db->result($query);

		$selected_cities = explode(',',$data['edit_view'][0]['city_code']);
		
		foreach($selected_cities as $selected){
			$this->db->select('*');
			$this->db->from('tabqy_cities');
			$this->db->where('tabqy_cities.city_code',$selected);
			$que = $this->db->get();
			$data['selected_cities'][] = $this->db->result($que);
		}

		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_cities');

		$this->db->where('country_code',$data['edit_view'][0]['country_code']);
		
		$this->db->where('is_region',0);

		$query1 = $this->db->get();

		$data['all_free_cities'] = $this->db->result($query1);
		 
        echo json_encode($data);die;

		

	}

}