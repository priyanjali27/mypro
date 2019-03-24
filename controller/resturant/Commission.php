<?php
class Commission extends Controller{
	
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
		$data['title']	 = "Fee";
		$data['heading'] = "Commission";
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/Commission/commission/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'country_id';
        $restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		$search = '0';
		$this->db->from('tabqy_commission');
		$this->db->where('tabqy_commission.commission_status',1);
		//$this->db->where('tabqy_commission.commission_country_id',$country_id);
		$this->db->where('tabqy_commission.commission_brand_id',$restaurant_id);
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
		$this->db->select('tabqy_commission.*,tabqy_resturantbrand.resturantbrand_name,tabqy_countries.country_name');
		$this->db->from('tabqy_commission');
		$this->db->join('tabqy_resturantbrand', "tabqy_resturantbrand.resturantbrand_id=tabqy_commission.commission_brand_id", "inner");
		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_commission.commission_country_id", "inner");
		$this->db->where('tabqy_commission.commission_status',1);
		//$this->db->where('tabqy_commission.commission_country_id',$country_id);
		$this->db->where('tabqy_commission.commission_brand_id',$restaurant_id);
		
		$this->db->order_by('tabqy_commission.commission_'.$sort,'DESC');
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
		$this->db->from('tabqy_resturantbrand');
		$this->db->where("tabqy_resturantbrand.resturantbrand_type",'0');
	
		$this->db->order_by('tabqy_resturantbrand.resturantbrand_id','DESC');
	    $query = $this->db->get();
		$data['resturants']=$this->db->result($query);
		$data['action']="";

		$data['user_id']="";
		$data['page_number']=$page_number;
		if($search=='0'){
			$data['searched']="";
        }else{
		$data['searched']=$search;	
		}
		$data['restaurant_id']=$restaurant_id;
		$data['search']=$search;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		view_loader('resturant/commission/index.html',$data);
	}
	
	//Add Country
	public function add(){
		$this->validation->validate("commission_country_id","Name","required");
	    if($this->validation->validation_check()== FALSE){
				$data['error_msg']=$this->validation->error_msg;
				$data['set_data']=$_POST;
				$data['error']="true";
				echo json_encode($data);die;
		}else{
			foreach($_POST['commission_brand_id'] as $brand){
				$data['send_data']['commission_brand_id'] =$brand;		
				$data['send_data']['commission_country_id'] =$_POST['commission_country_id'];		
				$data['send_data']['commission_value'] =$_POST['commission_value'];	
				$data['send_data']['commission_type'] =$_POST['commission_type'];			
				$data['send_data']['commission_status'] = '1';			
				$data['send_data']['commission_created'] = date('Y-m-d H:i:s');
				$this->db->insert('tabqy_commission', $data['send_data']);
			}
				$data['error']="false";
				$data['msg']="true";
				$data['success_message']="Commission's Added Successfully";
			echo json_encode($data);die;
		}
    }
	
public function add_commission(){
	
	$this->db->flush_cache();
        $this->db->select('tabqy_commission.*');
        $this->db->from('tabqy_commission');
        $this->db->where('tabqy_commission.commission_brand_id',$_POST['commission_brand_id']);
		$this->db->where('tabqy_commission.commission_country_id',$_POST['commission_country_id']);
		$query = $this->db->get();

		$data_row=$this->db->row($query);
		if(empty($data_row)){
			
		
			//foreach($_POST['commission_brand_id'] as $brand){
				$data['send_data']['commission_brand_id'] =$_POST['commission_brand_id'];		
				$data['send_data']['commission_country_id'] =$_POST['commission_country_id'];		
				$data['send_data']['commission_value'] =$_POST['commission_value'];	
				$data['send_data']['commission_type'] =$_POST['commission_type'];			
				$data['send_data']['commission_status'] = '1';			
				$data['send_data']['commission_created'] = date('Y-m-d H:i:s');
				$this->db->insert('tabqy_commission', $data['send_data']);
			//}
				$data['error']="false";
				$data['msg']="true";
				$data['success_message']="Commission's Added Successfully";
			echo json_encode($data);die;
		
		}else{
					

			$this->db->flush_cache();
            $data['set_data']['commission_status']=0;
			$this->db->where('tabqy_commission.commission_brand_id',$_POST['commission_brand_id']);
		    $this->db->where('tabqy_commission.commission_country_id',$_POST['commission_country_id']);
			$this->db->update('tabqy_commission', $data['set_data']);
		
				$data['send_data']['commission_brand_id'] =$_POST['commission_brand_id'];		
				$data['send_data']['commission_country_id'] =$_POST['commission_country_id'];		
				$data['send_data']['commission_value'] =$_POST['commission_value'];	
				$data['send_data']['commission_type'] =$_POST['commission_type'];			
				$data['send_data']['commission_status'] = '1';			
				$data['send_data']['commission_created'] = date('Y-m-d H:i:s');
				$this->db->insert('tabqy_commission', $data['send_data']);
			$data['error']="false";
				$data['msg']="true";
				$data['success_message']="Commission's Added already and updated Successfully";
			echo json_encode($data);die;
		
		}
		
    }
	
	// Edit Country Module
	Public function edit($user_id){
		
		$this->validation->validate("commission_country_id","Name","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']= $_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
	
			unset($_POST['submit']);
			$this->db->from('tabqy_commission');
			$this->db->where('commission_id', $user_id);
			$this->db->where('commission_status','1');
			$query = $this->db->get();
		    $commission=$this->db->row($query);
		
			$data['set_data']['commission_status']=0;
			$this->db->flush_cache();
			$this->db->where('commission_id', $user_id);
			$this->db->update('tabqy_commission', $data['set_data']);
			$this->db->flush_cache();
                $data['send_data']['commission_country_id']=$commission['commission_country_id'];
		     	$data['send_data']['commission_brand_id']=$commission['commission_brand_id'];
				$data['send_data']['commission_value'] =$_POST['commission_value'];	
				$data['send_data']['commission_type'] =$_POST['commission_type'];			
				$data['send_data']['commission_status'] = 1;			
				$data['send_data']['commission_created'] = date('Y-m-d H:i:s');
				$this->db->insert('tabqy_commission', $data['send_data']);
			    $data['error']="false";
				$data['msg']="true";
				$data['success_message']="Your row Commission Updated Successfully";
				echo json_encode($data);die;
		}		
		
	}
	
	public function view($id){
		$this->db->flush_cache();
		$this->db->select('tabqy_commission.commission_brand_id,tabqy_commission.commission_country_id');
		$this->db->from('tabqy_commission');
		$this->db->where('tabqy_commission.commission_id',$id);
		$query_c = $this->db->get();
		$commission = $this->db->row($query_c);
	
		if($commission){
		$commission_brand_id=$commission['commission_brand_id'];
		$commission_country_id=$commission['commission_country_id'];
		$this->db->flush_cache();
		$this->db->select('tabqy_commission.*,tabqy_countries.country_name,tabqy_resturantbrand.resturantbrand_name');
		$this->db->from('tabqy_commission');
		$this->db->join('tabqy_resturantbrand', "tabqy_resturantbrand.resturantbrand_id=tabqy_commission.commission_brand_id", "inner");
		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_commission.commission_country_id", "inner");
		$this->db->where('tabqy_commission.commission_country_id',$commission_country_id);
		$this->db->where('tabqy_commission.commission_brand_id',$commission_brand_id);
		$this->db->where('tabqy_commission.commission_status',0);
		$query = $this->db->get();
		$commissions = $this->db->result($query);
		echo json_encode($commissions);die;
		}
		
		 
	}

	public function select_resturant($id){
		$this->db->flush_cache();
		$this->db->select('tabqy_commission.commission_brand_id');
		$this->db->from('tabqy_commission');
		$this->db->where('tabqy_commission.commission_country_id',$id);
		$query_c = $this->db->get();
		$data['commission_brand_id'] = $this->db->result($query_c);
		$this->db->flush_cache();
		if($data['commission_brand_id']){
		foreach($data['commission_brand_id'] as $brand){
			$rows[]=$brand['commission_brand_id'];
		}
		$this->db->select('tabqy_resturantbrand.resturantbrand_id,tabqy_resturantbrand.resturantbrand_name');
		$this->db->from('tabqy_resturantbrand');
		$this->db->where('tabqy_resturantbrand.resturantbrand_type',0);
		$this->db->where_not_in('tabqy_resturantbrand.resturantbrand_id',$rows);
		$this->db->order_by('tabqy_resturantbrand.resturantbrand_id','desc');
	    $query_c = $this->db->get();
		$resturants = $this->db->result($query_c);
		echo json_encode($resturants);die;
		}else{
			$this->db->select('tabqy_resturantbrand.resturantbrand_id,tabqy_resturantbrand.resturantbrand_name');
		$this->db->from('tabqy_resturantbrand');
		$this->db->where('tabqy_resturantbrand.resturantbrand_type',0);
		$this->db->order_by('tabqy_resturantbrand.resturantbrand_id','desc');
		 $query_c = $this->db->get();
		$resturants = $this->db->result($query_c);
		echo json_encode($resturants);die;
		}
	}
}