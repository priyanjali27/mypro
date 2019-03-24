<?php 
// Userlocation controller 
class Userlocation extends Controller{
	var $language; 
	var $session;
	var $name;
	var $get_data;
	public function __construct()
    {
      parent::__construct();
	  require "check_login.php";
	  $data = $this->privillageRes();
	 // print_r($data); 
	  
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
	//show list of resturant .
	Public function index(){
		$sessiondata=$this->get_data();
		$company_id=$sessiondata['company_id'];
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success_massage']= flesh('success_message');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/userlocation/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number
			$this->validation->validate("search","Search","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
		}
		if(isset($_POST['search']) && $_POST['search'] !="")
		{
			$search = trim($_POST['search']);
		}
		elseif(!empty($this->url->segment(5))){
			$search = str_replace("%20",' ',($this->url->segment(5))?$this->url->segment(5):'0');
		}else{
			$search=0;
		}
		$this->db->flush_cache();
		
		$this->db->from('tabqy_userlocations');
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
		$this->db->select('t1.*,t2.user_name,t2.user_username,t2.user_email,t2.user_phoneno');
		$this->db->from('tabqy_userlocations t1');
		$this->db->join('tabqy_user t2','t2.user_id=t1.userlocation_user_id','Inner');
		$this->db->where('t2.user_role',3);
		$this->db->where('t1.userlocation_company_id',$company_id);
		$this->db->order_by('t1.userlocation_id','DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		
		$this->db->flush_cache();
		$this->db->select('resturantbrand_id,resturantbrand_name');
		$this->db->from('tabqy_resturantbrand');
		$this->db->where('resturantbrand_type',0);
		$this->db->order_by('resturantbrand_id','DESC');
		$query = $this->db->get();
		$data['resturants']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search); 
		$sql="SELECT * FROM tabqy_user a WHERE a.user_id NOT IN (SELECT userlocation_user_id FROM tabqy_userlocations b where b.userlocation_company_id=1) and a.user_company_id=1 and a.user_role=3 and a.user_status='1'"; 
		$query = $this->db->query($sql);
		$data['users']=$this->db->result($query);
		$data['action']="";
		$data['title']="Resturant";
		$data['heading']="User location list";
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
		
		view_loader('resturant/userlocation/index.html',$data);
	}
	
	
	//add userlocation with roles.
	Public function add(){
	
		
		$this->validation->validate("userlocation_country","Country","required");
	    $this->validation->validate("userlocation_user_id","Staff","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			
			$data['send_data']['userlocation_country']=$_POST['userlocation_country'];
			$data['send_data']['userlocation_user_id']=$_POST['userlocation_user_id'];
			if(isset($_POST['userlocation_region']) && !empty($_POST['userlocation_region'])){
			$data['send_data']['userlocation_region']=implode(',',$_POST['userlocation_region']);
			}
			if(isset($_POST['userlocation_city']) && !empty($_POST['userlocation_city'])){
			$data['send_data']['userlocation_city']=implode(',',$_POST['userlocation_city']);
			}
			if(isset($_POST['userlocation_zone']) && !empty($_POST['userlocation_zone'])){
			$data['send_data']['userlocation_zone']=implode(',',$_POST['userlocation_zone']);
			}
			if(isset($_POST['userlocation_location']) && !empty($_POST['userlocation_location'])){
			$data['send_data']['userlocation_location']=implode(',',$_POST['userlocation_location']);
			}
			$data['send_data']['userlocation_status']=1;
			$data['send_data']['userlocation_created']=date('Y-m-d H:i:s');

		    $insert_id=$this->db->insert('tabqy_userlocations', $data['send_data']);
		  
			
		    $data['error']="false";
			$data['msg']="true";
			$data['success_message']="User-Location Assignment successfully done";
			echo json_encode($data);die;
		
		}
    }
	
	// Delete resturant Module
	Public function delete($userlocation_id){
		
			$this->db->where('userlocation_id', $userlocation_id);
            $this->db->delete('tabqy_userlocations'); 
			die;
		
	}
	
	// Edit resturant Module
	Public function edit($user_id){
		
		$this->validation->validate("userlocation_country","Country","required");
	    $this->validation->validate("userlocation_user_id","Staff","required");
		
				
				if($this->validation->validation_check()== FALSE){
					$data['error_msg']=$this->validation->error_msg;
					$data['set_data']= $_POST;
					$data['error']="true";
			        echo json_encode($data);die;
				}else{
					unset($_POST['submit']);
					$data['send_data']['userlocation_country']=$_POST['userlocation_country'];
			$data['send_data']['userlocation_user_id']=$_POST['userlocation_user_id'];
			if(isset($_POST['userlocation_region']) && !empty($_POST['userlocation_region'])){
			$data['send_data']['userlocation_region']=implode(',',$_POST['userlocation_region']);
			}
			if(isset($_POST['userlocation_city']) && !empty($_POST['userlocation_city'])){
			$data['send_data']['userlocation_city']=implode(',',$_POST['userlocation_city']);
			}
			if(isset($_POST['userlocation_zone']) && !empty($_POST['userlocation_zone'])){
			$data['send_data']['userlocation_zone']=implode(',',$_POST['userlocation_zone']);
			}
			if(isset($_POST['userlocation_location']) && !empty($_POST['userlocation_location'])){
			$data['send_data']['userlocation_location']=implode(',',$_POST['userlocation_location']);
			}
					
					$this->db->where('userlocation_id', $user_id);
					$this->db->update('tabqy_userlocations', $data['send_data']);
					 $data['error']="false";
			$data['msg']="true";
			$data['success_message']="Your location assignment updated successfully";
			echo json_encode($data);die;
				}
		
	}
	
	function view(){
		$id = $this->url->segment(4);
		$data['title'] = "Resturant";
		$data['heading'] = "Resturant";
		$data['action'] = 'view';
		$data['success'] = flesh('success');
		$this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
		$this->db->from('tabqy_resturantbrand t2');
		$this->db->join('tabqy_resturantbrand t1','t1.resturantbrand_type=t2.resturantbrand_id','RIGHT');
			$this->db->where('t1.resturantbrand_id',$id);
			$query = $this->db->get();
			$results= $this->db->result($query);
			$data['resturant']=$results[0];
			
		    view_loader('admin/resturant/view.html',$data);
	}
	public function check_username($username){
	    $this->db->select('tabqy_user.*');
		$this->db->from('tabqy_user');
		$this->db->where('tabqy_user.user_username',$username);
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];
	
	}
	
	public function check_email($email){
	    $this->db->select('tabqy_user.*');
		$this->db->from('tabqy_user');
		$this->db->where('tabqy_user.user_email',$email);
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];
	
	}
	public function rest(){
	     view_loader('admin/resturant/rest.html');
	
	}
	function updateStatus($id, $value){
		$this->db->flush_cache();
		$this->db->where('userlocation_id', $id);
		$this->db->update('tabqy_userlocations',array('userlocation_status'=>$value));
		$this->db->flush_cache();
		$this->db->select('userlocation_status,userlocation_id');
		$this->db->from('tabqy_userlocations');
		$this->db->where('userlocation_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	 }
	 
	function related_regions($country_code){
		$this->db->flush_cache();
		$this->db->select('region_name,region_code');
		$this->db->from('tabqy_regions');
		$this->db->where('country_code',$country_code);
		$query = $this->db->get();
		$data['related_regions'] = $this->db->result($query);
		echo json_encode($data);die;
	}
	function related_cities($region_code){
		$this->db->flush_cache();
		$this->db->select('city_code');
		$this->db->from('tabqy_regions');
		$this->db->where('region_code',$region_code);
		$query = $this->db->get();
		$city_str = $this->db->result($query);
		$selected_cities = explode(',',$city_str[0]['city_code']);
		foreach($selected_cities as $city_code){
			$this->db->flush_cache();
			$this->db->select('city_name,city_code');
			$this->db->from('tabqy_cities');
			$this->db->where('city_code',$city_code);
			$que = $this->db->get();
			$data['related_cities'][] = $this->db->result($que);			
		}
		echo json_encode($data);die;
	}
	
	function related_zone($city){
		$this->db->flush_cache();
		$this->db->select('zone_name,zone_code,location_code');
		$this->db->from('tabqy_zone');
		$this->db->where('city_code',$city);
		$query = $this->db->get();
		$data['related_zone'] = $this->db->result($query);
		
		$rel_location = $data['related_zone'][0]['location_code'];
		$explode_loc = explode(',', $rel_location);
		foreach($explode_loc as $loc_code){
			$this->db->flush_cache();
			$this->db->select('location_name,location_code');
			$this->db->from('tabqy_locations');
			$this->db->where('location_code',$loc_code);
			$que = $this->db->get();
			$data['related_locations'][] = $this->db->result($que);
		}		
		echo json_encode($data);die;
	}
	
		function related_locations($rel_zone){
		$this->db->flush_cache();
		$this->db->select('location_code');
		$this->db->from('tabqy_zone');
		$this->db->where('zone_code',$rel_zone);
		$que = $this->db->get();
		$location_str = $this->db->result($que);
		
		$explode_loc = explode(',', $location_str[0]['location_code']);
		foreach($explode_loc as $loc_code){
			$this->db->flush_cache();
			$this->db->select('location_name,location_code');
			$this->db->from('tabqy_locations');
			$this->db->where('location_code',$loc_code);
			$que = $this->db->get();
			$data['related_locations'][] = $this->db->result($que);
		}
		echo json_encode($data);die;
	}
	function edit_view($rest_id){

		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_userlocations');

		$this->db->where('userlocation_id', $rest_id);

		$query = $this->db->get();

		$data['edit_view'] = $this->db->result($query);
		
		//Fetching Regions of selected country

		$this->db->flush_cache();
		$this->db->select('region_name,region_code');
		$this->db->from('tabqy_regions');
		$this->db->where('country_code',$data['edit_view'][0]['userlocation_country']);
		$query = $this->db->get();
		$data['related_regions'] = $this->db->result($query);

		//Fetching cities for selected region
		$this->db->flush_cache();
		$this->db->select('city_code');
		$this->db->from('tabqy_regions');
		$this->db->where('region_code',$data['edit_view'][0]['userlocation_region']);
		$query = $this->db->get();
		$city_str = $this->db->result($query);
		$selected_cities = explode(',',$city_str[0]['city_code']);
		foreach($selected_cities as $city_code){
			$this->db->flush_cache();
			$this->db->select('city_name,city_code');
			$this->db->from('tabqy_cities');
			$this->db->where('city_code',$city_code);
			$que = $this->db->get();
			$data['related_cities'][] = $this->db->result($que);			
		}
			//Fetching zone from city
		$this->db->flush_cache();
		$this->db->select('zone_name,zone_code,location_code');
		$this->db->from('tabqy_zone');
		$this->db->where('city_code',$data['edit_view'][0]['userlocation_city']);
		$query = $this->db->get();
		$data['related_zone'] = $this->db->result($query);
		
		//Fetching Locations from zone
		$this->db->flush_cache();
		$this->db->select('location_code');
		$this->db->from('tabqy_zone');
		$this->db->where('zone_code',$data['edit_view'][0]['userlocation_zone']);
		$que = $this->db->get();
		$location_str = $this->db->result($que);
		
		$explode_loc = explode(',', $location_str[0]['location_code']);
		foreach($explode_loc as $loc_code){
			$this->db->flush_cache();
			$this->db->select('location_name,location_code');
			$this->db->from('tabqy_locations');
			$this->db->where('location_code',$loc_code);
			$que = $this->db->get();
			$data['related_locations'][] = $this->db->result($que);
		}
		echo json_encode($data);die;

		

	}
	
}
?>
		