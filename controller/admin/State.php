<?php
class State extends Controller{
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
		$data['title']	 = "State";
		$data['heading'] = "State";
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/state/index/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'state_id';
		$sort_c			 = 'country_name';
		$search	= '0';
		$get_total_rows  = $this->db->count_all_results('tabqy_states'); //Total no. of values
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
		$this->db->select('a.country_name,b.country_id,b.state_id,b.state_name,b.state_status,b.state_created');
		$this->db->from('tabqy_countries as a');
		$this->db->join('tabqy_states as b', "a.country_id=b.country_id", "inner");
		$this->db->order_by('b.'.$sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		
		$this->db->flush_cache();
		$this->db->select('tabqy_countries.*');
		$this->db->from('tabqy_countries');
		$this->db->order_by('tabqy_countries.'.$sort_c,'ASC');
		$query_c = $this->db->get();
		$data['related_country'] = $this->db->result($query_c);
		
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="State";
		$data['heading']="Add State";
		$data['user_id']="";
		$data['page_number']=$page_number;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		view_loader('admin/country/view_state.html',$data);		
	}
	
	//Add Country
	public function add(){
		$this->validation->validate("state_name","Name","required");
		$this->validation->validate("country_rel","Country","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{	
			unset($_POST['submit']);
			unset($_POST['state_id']);
			$data['send_data']= $_POST;

			$data['send_data']['state_status']=1;
			$data['send_data']['state_created']=date('Y-m-d H:i:s');
			$this->db->insert('tabqy_states', $data['send_data']);
			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="State Added Successfully";
			echo json_encode($data);die;
		}
    }
	
	// Delete State Module
	Public function delete($user_id){
		
			$this->db->where('state_id', $user_id);
            $this->db->delete('tabqy_states'); 
			die;
		
	}
	
	// Edit State Module
	Public function edit($state_id){
		$this->validation->validate("state_name","State Name","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']= $_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			$data['set_data']= $_POST;
			$this->db->where('state_id', $state_id);
			$this->db->update('tabqy_states', $data['set_data']);
			 $data['error']="false";
				$data['msg']="true";
				$data['success_message']="State Updated Successfully";
				echo json_encode($data);die;
		}		
		
	}
}