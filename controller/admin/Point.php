<?php

class Point extends Controller{

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
            } else{
                  if ($this->privillage['locationstatus'] == true) {
                $_SESSION['locationcode'] = $this->privillage['locationcode'];
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

	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  index()
	   DESCRIPTION: Shows list of all Points
	 *************************************/
	public function index(){ 

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "System Tool Settings";

		$data['heading'] = "Point";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/point/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort 			 = 'country_id';

		$search = '0';

		$this->db->from('tabqy_setpoint');

		$this->db->where('tabqy_setpoint.setpoint_resturant_id',0);

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

		$this->db->select('tabqy_setpoint.*,tabqy_countries.country_name,tabqy_countries.country_code');

		$this->db->from('tabqy_setpoint');

		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_setpoint.setpoint_country_id", "inner");

		$this->db->where('tabqy_setpoint.setpoint_resturant_id',0);

		$this->db->order_by('tabqy_setpoint.setpoint_'.$sort,'DESC');

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

		view_loader('admin/country/Point.html',$data);		

	}

	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  resturant(restaurant id, country id)
	   DESCRIPTION: Shows to list of all points brandwise
	 *************************************/
	public function resturant($restaurant_id,$country_id){ 
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

		$data['title']	 = "System Tool Settings";

		$data['heading'] = "Point";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/point/resturant/$restaurant_id/$country_id/$company_id"; //URL
        $data['company_id']=$company_id; 
		$pageno			 = $this->url->segment(7); // Get page number

		$sort 			 = 'country_id';

		$search = '0';
         
		$data['restaurant_id']=$restaurant_id; 
		
		$data['country_id']=$country_id; 
		 
		$this->db->from('tabqy_setpoint');

		$this->db->where('tabqy_setpoint.setpoint_resturant_id',$restaurant_id);

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

		$this->db->select('tabqy_setpoint.*,tabqy_countries.country_name');

		$this->db->from('tabqy_setpoint');

		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_setpoint.setpoint_country_id", "inner");

		$this->db->where('tabqy_setpoint.setpoint_resturant_id',$restaurant_id);

		$this->db->order_by('tabqy_setpoint.setpoint_'.$sort,'DESC');

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

		view_loader('admin/country/resturant_point.html',$data);		

	}

	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  add()
	   DESCRIPTION: Used to add point
	 *************************************/

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
		
		$this->validation->validate("setpoint_country_id","Name","required");		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			$regx = '/^[-+]?[0-9]*\.?[0-9]*$/';

        if(!preg_match($regx, $_POST['setpoint_value'])){		

            $data['error_msg']['setpoint_value']="Enter integer or decimal value";

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}
			$data['send_data']=$_POST;			

			$data['send_data']['setpoint_status'] = 1;			

			$data['send_data']['setpoint_created'] = date('Y-m-d H:i:s');

			$this->db->insert('tabqy_setpoint', $data['send_data']);

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="Point  Added Successfully";

			echo json_encode($data);die;

		}

    }	

	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  delete(point id)
	   DESCRIPTION: Used to delete point
	 *************************************/

	Public function delete($point_id){	
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
	
			$this->db->where('setpoint_id', $point_id);

            $this->db->delete('tabqy_setpoint'); 

			$data['error']           = "false";
			$data['msg']             = "false";
			$data['success_message'] = "Discount successfully deleted";
			echo json_encode($data);
			exit();
	}

	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  edit(point id)
	   DESCRIPTION: Used to edit point
	 *************************************/

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

			$regx = '/^[-+]?[0-9]*\.?[0-9]*$/';

        if(!preg_match($regx, $_POST['setpoint_value'])){			

            $data['error_msg']['setpoint_value1']="Enter integer or decimal value";

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}

			unset($_POST['submit']);

			$data['set_data']= $_POST;

			$this->db->where('setpoint_id', $user_id);

			$this->db->update('tabqy_setpoint', $data['set_data']);

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="Your row Updated Successfully";

			echo json_encode($data);die;

		
	}
	
	/*************************************
	   DEVELOPER: Priyanjali  
	   DATE:  30-04-2018
	   FUNCTION:  updateStatus(point id)
	   DESCRIPTION: Used to change point status
	 *************************************/
	public function updateStatus($id, $value){
		$pre = 'setpoint';
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