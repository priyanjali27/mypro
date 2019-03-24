<?php

class Faq extends Controller{

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

		$page_url        = baseurl."admin/faq/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort 			 = 'faq_id';

		$sort_c			 = 'cat_name';

		$search	= '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_faq'); //Total no. of values

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

		$this->db->select('a.*,b.cat_name,b.cat_id');

		$this->db->from('tabqy_faq as a');

		$this->db->join('tabqy_faq_categories as b', "a.faq_category=b.cat_id", "left");

		$this->db->order_by('a.'.$sort,'DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();

		$data['results']=$this->db->result($query);

		

		$this->db->flush_cache();

		$this->db->select('tabqy_faq_categories.*');

		$this->db->from('tabqy_faq_categories');

		$this->db->order_by('tabqy_faq_categories.'.$sort_c,'ASC');

		$cat = $this->db->get();

		$data['category'] = $this->db->result($cat);

		

		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['title']="System Tool Settings";
		
		$data['heading']="FAQs";

		$data['user_id']="";

		$data['page_number']=$page_number;

		$start=$page_position+1;

		$data['start']=$start;

		$data['end']=($start+count($data['results'])-1);

		view_loader('admin/faq/view.html',$data);		

	}

	

	//Add FAQ

	public function add(){		

		$this->validation->validate("faq_category","Category","required");

		$this->validation->validate("faq_que","Question","required");

		$this->validation->validate("faq_ans","Answer","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{	

			unset($_POST['submit']);

			unset($_POST['faq_id']);

			$data['send_data']= $_POST;

			$data['send_data']['faq_status']=1;

			$data['send_data']['faq_created'] = date('Y-m-d H:i:s');

			$this->db->insert('tabqy_faq', $data['send_data']);

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="Question added successfully";

			echo json_encode($data);die;

		}

    }

	

	// Delete FAQ Module

	Public function delete($faq_id){	

			$this->db->where('faq_id', $faq_id);

            $this->db->delete('tabqy_faq'); 

			die;		

	}

	

	// Edit FAQ Module

	Public function edit($faq_id){

		$this->validation->validate("faq_category","Category","required");

		$this->validation->validate("faq_que","Question","required");

		$this->validation->validate("faq_ans","Answer","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			unset($_POST['submit']);

			$data['set_data']= $_POST;

			$this->db->where('faq_id', $faq_id);

			$this->db->update('tabqy_faq', $data['set_data']);

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="FAQ updated successfully";

			echo json_encode($data);die;

		}		

		

	}

	

	Public function edit_view($id){

		$this->db->flush_cache();

		$this->db->select('a.faq_que,a.faq_ans,a.faq_category,b.cat_name,b.cat_id');

		$this->db->from('tabqy_faq as a');

		$this->db->where('a.faq_id='.$id);

		$this->db->join('tabqy_faq_categories as b', "b.cat_id=a.faq_category", "left");		

		$query = $this->db->get();

		$data['edit_view']=$this->db->result($query);

		echo json_encode($data);die;

	}

}