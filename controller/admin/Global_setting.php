<?php 

class Global_setting extends Controller{
	var $language; 
	var $session;
	var $name;
	public function __construct()
    {
        parent::__construct();
		
		if (is_logged_in()==FALSE)
        {
            redirect('admin/dashboard');
        }
        $this->privillage = $this->privillage();
             if ($_SESSION['userdata']['user_role'] != 0) {
            if (empty($this->privillage['add']) && empty($this->privillage['edit']) && empty($this->privillage['delete'])) {
                redirect('admin/dashboard');
            }else {
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
		DEVELOPER: Sonu 
		DATE: 22-05-2018
		FUNCTION: index
		DESCRIPTION: list of floor 
	    *************************************/
	Public function index(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success_massage']= flesh('success_message');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/floor/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number
		$sort='resturant_setting_id';
		if(isset($_POST['search']) && $_POST['search'] !="")
		{
			$search = trim($this->input->post('search'));
		}
		else{
			$search = str_replace("%20",' ',($this->url->segment(5))?$this->url->segment(5):'0');
		}
		if($search!='0'){
			$this->db->flush_cache();
			$this->db->where("_name LIKE '%$search%'");
		}
		$get_total_rows  = $this->db->count_all_results('tabqy_floor'); //Total no. of values
		
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
		$this->db->select('tabqy_resturant_setting.*');
		$this->db->from('tabqy_resturant_setting');
		if($search!='0'){
			$this->db->where("resturant_setting_name LIKE '%$search%'");
		}
		
		$this->db->order_by('tabqy_resturant_setting.'.$sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		// /yprint_r($data['results']);die();
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="User";
		$data['heading']="Floor";
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
		view_loader('admin/globalsetting/add.html',$data);
	}
		/*************************************
			DEVELOPER: Sonu 
		     DATE: 24-05-2018
			FUNCTION: add
			DESCRIPTION: For Add a Global Setting 
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

        $this->db->flush_cache();
		$this->db->select('tabqy_paymentmethod.*');
		$this->db->from('tabqy_paymentmethod');
        $query = $this->db->get();
		$data['resultss']=$this->db->result($query);
         // print_r($data['resultss']);die();
        $data['title']="Global Settings";
		$data['heading']="Settings";
		$data['success'] = flesh('success');
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action'] = 'add';
		$data['error']   = flesh('error');
		//echo "string";

		if(isset($_POST['add'])){
			//$adds = $_POST;
		//echo "string"; die();

		   	$name                     = $_POST['name'];
		   	$address                  = $_POST['address'];
		   	$website 				  = $_POST['website'];
		   	$phone_number   		  = $_POST['phone_number'];
		   	$starttime 				  = $_POST['starttime'];
		   	$closetime 				  = $_POST['closetime'];
		   	$dilevery 				  = $_POST['dilevery'];
		   	$pickup  				  = $_POST['pickup'];
		   	
		   	$payement_option  		  = $_POST['payement_option'];
		   	$weekday                  = $_POST['weekday'];
		   	
		   	$charity 				  = $_POST['charity'];
		   //	$order_outside_delivery_area =    $_POST['order_outside_delivery_area'];

			//$this->validation->validate("name"," name","required");
		     
			$add_data = array(
				 'resturant_setting_name'           =>$name,
				 'resturant_setting_address'        =>$address,
				 'resturant_setting_website'        =>$website,
				 'resturant_setting_dilevery'       =>$dilevery,
			     'resturant_setting_pickup'         =>$pickup,
			     'resturant_setting_phone_number'   =>$phone_number,
				 'resturant_setting_payement_option'=>implode(',', $payement_option),
				// 'resturant_setting_order_outside_delivery_area'=>$order_outside_delivery_area,
				 'resturant_setting_opening_hours_time' =>$starttime,
				 'resturant_setting_closing_hours_time'=>$closetime,
			     'resturant_setting_opening_hours_day' =>implode(',', $weekday),
			     'resturant_setting_charity'           =>$charity,
			);
			     //  echo"<pre>";print_r($add_data);die();	  
				$this->db->insert("tabqy_resturant_setting",$add_data);
			
				$data['msg']="true";
				$data['success_message']="Global Setting Added Successfully";
				
				redirect('admin/global_setting/add');
		
		
		}else{
			view_loader('admin/globalsetting/add.html',$data);
		}
    }
	
}?>