<?php

class Quantity extends Controller{

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

		$this->db->select('lang.language_name,lang.language_code,lang.language_flag');

		$this->db->from('tabqy_language as lang');

		$this->db->order_by('lang.language_id','DESC');

		$query = $this->db->get();

		$this->language=$this->db->result($query);

		$this->db->flush_cache();

		$this->db->select('user.user_name');

		$this->db->from('tabqy_user as user');

		$userid=$_SESSION['userdata']['user_id'];

		$this->db->where('user.user_id', $userid);

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

		$page_url        = baseurl."admin/quantity/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort 			 = 'quantity_id';



		$search = '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_quantity'); //Total no. of values

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

		$this->db->select('tabqy_quantity.*');

		$this->db->from('tabqy_quantity');

		$this->db->order_by('tabqy_quantity.'.$sort,'DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();

		$data['results']=$this->db->result($query);

		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['title']="Global Settings";
		
		$data['heading']="Quantity Terms";

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

		view_loader('admin/quantity/view_quantity.html',$data);		

	}

	

	//Add Quantity Term

	public function add(){

		$this->validation->validate("quantity_name","Name","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			unset($_POST['submit'], $_POST['quantity_id']);

			$data['send_data']= $_POST;

			

			$data['send_data']['quantity_status'] = 1;			

			$data['send_data']['quantity_created'] = date('Y-m-d H:i:s');

			$this->db->insert('tabqy_quantity', $data['send_data']);

			$data['error']="false";

			$data['msg']="true";

			$data['success_message']="Quantity Term Added Successfully";

			echo json_encode($data);die;

		}

    }

	

	// Delete Quantity Term

	Public function delete($quan_id){

		

			$this->db->where('quantity_id', $quan_id);

            $this->db->delete('tabqy_quantity'); 

			die;

		

	}

	

	// Edit Quantity Term

	Public function edit($quan_id){		

		$this->validation->validate("quantity_name","Name","required");

		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{

			unset($_POST['submit']);

			$data['set_data']= $_POST;

			$this->db->where('quantity_id', $quan_id);

			$this->db->update('tabqy_quantity', $data['set_data']);

			 $data['error']="false";

				$data['msg']="true";

				$data['success_message']="Quantity Updated Successfully";

				echo json_encode($data);die;

		}	

	}
	function updateStatus($id, $value){
		$pre = 'quantity';
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