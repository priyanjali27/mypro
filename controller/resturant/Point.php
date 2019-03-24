<?php 

// Point controller 

class Point extends Controller{

	var $language; 

	var $session;

	var $name;
	var $get_data;

	public function __construct()

    {

      parent::__construct();

	  require "check_login.php";

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


	public function index(){ 
        $restaurant_id		 = $this->url->segment(4);
		$data['restaurant_id']=$restaurant_id ;
		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "Point";

		$data['heading'] = "Point";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."resturant/point/index/".$restaurant_id; //URL

		$pageno			 = $this->url->segment(5); // Get page number

		$sort 			 = 'id';

      
      
		$search = '0';

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

		$data['title']="Resturant";

		$data['heading']="Point";

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

		view_loader('resturant/point/point.html',$data);		

	}

	

	//Add point

	public function add(){
         $restaurant_id		 = $this->url->segment(4);
		$data['restaurant_id']=$restaurant_id ;
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


			$data['send_data']['setpoint_resturant_id'] = $restaurant_id;

			$this->db->insert('tabqy_setpoint', $data['send_data']);

			

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="Point  Added Successfully";

			echo json_encode($data);die;

		}

    }

	

	// Delete Country Module

	Public function delete($point_id){

		

			$this->db->where('setpoint_id', $point_id);

            $this->db->delete('tabqy_setpoint'); 

			die;

		

	}

	

	// Edit Country Module

	Public function edit($user_id){

		

		$this->validation->validate("setpoint_country_id","Name","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

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

		

	}

	function updateStatus($id, $value){

		  $this->db->flush_cache();

		  $this->db->where('setpoint_id', $id);

		  $this->db->update('tabqy_setpoint',array('setpoint_status'=>$value));

		  $this->db->flush_cache();

		  $this->db->select('setpoint_status,setpoint_id');

		  $this->db->from('tabqy_setpoint');

		  $this->db->where('setpoint_id', $id);

		  $query = $this->db->get();

		  $data['cur_status'] = $this->db->result($query);

		  echo json_encode($data);die;

	 }

}