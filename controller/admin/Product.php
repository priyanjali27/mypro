<?php 
// Resturant controller 
class Product extends Controller{
	
	public function __construct()
    {
        parent::__construct();
		
		if (is_logged_in()==FALSE)
        {
            redirect('admin/admin_login');
        }
    }
	
	//show list of resturant .
	Public function index(){
		
		$data=array();
		$data['success_massage']= flesh('success_message');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/resturant/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number
		if(isset($_POST['search']) && $_POST['search'] !="")
		{
			$search = trim($this->input->post('search'));
		}
		else{
			$search = str_replace("%20",' ',($this->url->segment(5))?$this->url->segment(5):'0');
		}
		if($search!='0'){
			$this->db->flush_cache();
			$this->db->where("resturantbrand_name LIKE '%$search%'");
		}
		$get_total_rows  = $this->db->count_all_results('tabqy_resturantbrand'); //Total no. of values
		
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
		$this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
		$this->db->from('tabqy_resturantbrand t2');
		if($search!='0'){
			$this->db->where("t1.resturantbrand_name LIKE '%$search%'");
		}
		$this->db->join('tabqy_resturantbrand t1','t1.resturantbrand_type=t2.resturantbrand_id','RIGHT');
		$this->db->order_by('t1.resturantbrand_id','DESC');
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
		$data['action']="";
		$data['title']="Resturant";
		$data['heading']="Resturant";
		$data['user_id']="";
		view_loader('admin/resturant/resturant.html',$data);
	}
	
	//add resturant with roles.
	Public function add(){
		$data=array();
		$data['success_massage']= flesh('success_message');
		$data['action']="add";
		$data['title']="Resturant";
		$data['heading']="Add Resturant";
		$data['user_id']="";
		$this->validation->validate("resturantbrand_name","Name","required");
		$this->validation->validate("resturantbrand_displayname","Display Name","required");
		$this->validation->validate("resturantbrand_phoneno","Phone number","required|numeric");
		$this->validation->validate("resturantbrand_email","Email","required|email");
		$this->validation->validate("resturantbrand_countusers","Total number of users","required");
		$this->validation->validate("resturantbrand_address","Address","required");
		$this->validation->validate("resturantbrand_countpos","Total number of POS","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			view_loader('admin/resturant/resturant.html',$data);
		}else{
			unset($_POST['submit']);
			unset($_POST['resturantbrand_id']);
			$data['send_data']= $_POST;
			if(empty($_POST['resturantbrand_type']) && $_POST['resturantbrand_type']=="0" ){
				$data['send_data']['resturantbrand_type']=0;
			}
			$data['send_data']['resturantbrand_created']=date('Y-m-d H:i:s');
			$this->db->insert('tabqy_resturantbrand', $data['send_data']);
		    set_flesh('success_message', 'New Resturant added successfully');
			redirect('admin/resturant/index');
		}
    }
	
	// Delete resturant Module
	Public function delete(){
		$user_id=$this->url->segment(4);
		if(isset($user_id) && !empty($user_id)){
			$this->db->where('resturantbrand_id', $user_id);
            $this->db->delete('tabqy_resturantbrand'); 
			redirect('admin/resturant/index');
		}
	}
	
	// Edit resturant Module
	Public function edit(){
		$data=array();
		$data['success_massage']= flesh('success_message');
		$data['action']="edit";
		$data['title']="Resturant";
		$data['heading']="Edit Resturant";
		$user_id=$this->url->segment(4);
		$data['user_id']=$user_id;
		if(isset($user_id) && !empty($user_id)){
		    if($_POST){
				$this->validation->validate("resturantbrand_name","Name","required");
				$this->validation->validate("resturantbrand_displayname","Display Name","required");
				$this->validation->validate("resturantbrand_phoneno","Phone number","required|numeric");
				$this->validation->validate("resturantbrand_email","Email","required|email");
				$this->validation->validate("resturantbrand_countusers","Total number of users","required");
				$this->validation->validate("resturantbrand_address","Address","required");
				$this->validation->validate("resturantbrand_countpos","Total number of POS","required");
				if($this->validation->validation_check()== FALSE){
					$data['error_msg']=$this->validation->error_msg;
					$data['set_data']= $_POST;
					$data['set_data']['resturantbrand_updated']=date('Y-m-d H:i:s');
					view_loader('admin/resturant/resturant.html',$data);
				}else{
					unset($_POST['submit']);
					$data['set_data']= $_POST;
					unset($data['set_data']['resturantbrand_id']);
					$this->db->where('resturantbrand_id', $user_id);
					$this->db->update('tabqy_resturantbrand', $data['set_data']);
					set_flesh('success_message', 'Resturant updated successfully');
					redirect('admin/resturant/index');
				}
		}else{
			$this->db->select('tabqy_resturantbrand.*');
			$this->db->from('tabqy_resturantbrand');
			$this->db->where('tabqy_resturantbrand.resturantbrand_id',$user_id);
			$query = $this->db->get();
			$results= $this->db->result($query);
			$data['set_data']=$results[0];
			view_loader('admin/resturant/resturant.html',$data);	
		}
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
}
?>
		