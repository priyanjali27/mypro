<?php 

// Category controller uses for superadmin,tabqy employees,customer 
class Category extends Controller{
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
	//show list of users .
	Public function index(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success_massage']= flesh('success_message');
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."resturant/category/index/"; //URL
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
		if($search!='0'){
			$this->db->flush_cache();
			$this->db->where("tabqy_categories.category_name LIKE '%$search%'");
		}
		$this->db->where("tabqy_categories.category_brand_id",$restaurant_id);
		$this->db->from('tabqy_categories');
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
		$this->db->select('tabqy_categories.*');
		$this->db->from('tabqy_categories');
		if($search!='0'){
			$this->db->where("category_name LIKE '%$search%'");
		}
		$this->db->where("tabqy_categories.category_brand_id",$restaurant_id);
		$this->db->order_by('tabqy_categories.category_id','DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="Menu";
		$data['heading']="List category";
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
		view_loader('resturant/category/user.html',$data);
	}
		
	//add user with roles.
	Public function add(){
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		$this->validation->validate("category_name","Name","required");
		//var_dump($this->validation->validation_check());die;
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
				 $data['msg']="false";
			echo json_encode($data);die;
		}else{
			
			unset($_POST['submit']);
			unset($_POST['category_id']);
			$data['send_data']= $_POST;
			$name=$_FILES['image']['name'];
		$type=$_FILES['image']['type'];
		$tmp_name=$_FILES['image']['tmp_name'];
		$size=$_FILES['image']['size'];
	    $filename  = basename($_FILES['image']['name']);
         $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name       = time()."_".$_FILES['image']['name'];
	   $allowedExts = array("gif", "jpeg", "jpg", "png");
		
		if ((($_FILES["image"]["type"] == "image/gif")
		|| ($_FILES["image"]["type"] == "image/jpeg")
		|| ($_FILES["image"]["type"] == "image/jpg")
		|| ($_FILES["image"]["type"] == "image/png"))
		&& in_array($extension, $allowedExts)){
				if (move_uploaded_file($_FILES['image']['tmp_name'], "upload/catimages/".$file_name)) 
				{
				  $data['send_data']['category_image']=$file_name;
				}
		}
			$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
			$data['send_data']['category_status']=1;
			$data['send_data']['category_name']=$_POST['category_name'];
			$data['send_data']['category_brand_id']=$restaurant_id;
			$data['send_data']['category_created']=date('Y-m-d H:i:s');
		
			$this->db->insert('tabqy_categories', $data['send_data']);
			
			 $data['error']="false";
			 $data['msg']="true";
			$data['success_message']="New Category added successfully.";
			
			$this->db->flush_cache();
		$this->db->select('tabqy_categories.category_id,tabqy_categories.category_name');
		$this->db->from('tabqy_categories');
			$this->db->where("tabqy_categories.category_brand_id",$restaurant_id);
		$this->db->order_by('tabqy_categories.category_id','DESC');
		$this->db->limit(9,0);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		
			echo json_encode($data);die;
		}
    }
	

		Public function delete($user_id){
            $this->db->where('category_id', $user_id);
            $this->db->delete('tabqy_categories'); 
		die;
	}
	
	// Edit user Module
	Public function edit(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="edit";
		$data['title']="Category";
		$data['heading']="Edit Category";
		//$data['success_massage']=flesh('success_message');
		$user_id=$this->url->segment(4);
		$data['category_id']=$user_id;
		
				$this->validation->validate("category_name","Name","required");
				
				if($this->validation->validation_check()== FALSE){
					$data['error_msg']=$this->validation->error_msg;
					$data['set_data']= $_POST;
					$data['set_data']['category_updated']=date('Y-m-d H:i:s');
				$data['error']="true";
				 $data['msg']="false";
			echo json_encode($data);die;
				}else{
					unset($_POST['submit']);
					$data['set_data']= $_POST;
					if($_FILES){
						$name=$_FILES['image']['name'];
		$type=$_FILES['image']['type'];
		$tmp_name=$_FILES['image']['tmp_name'];
		$size=$_FILES['image']['size'];
	    $filename  = basename($_FILES['image']['name']);
         $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name       = time()."_".$_FILES['image']['name'];
	   $allowedExts = array("gif", "jpeg", "jpg", "png");
		
		if ((($_FILES["image"]["type"] == "image/gif")
		|| ($_FILES["image"]["type"] == "image/jpeg")
		|| ($_FILES["image"]["type"] == "image/jpg")
		|| ($_FILES["image"]["type"] == "image/png"))
		&& in_array($extension, $allowedExts)){
				if (move_uploaded_file($_FILES['image']['tmp_name'], "upload/catimages/".$file_name)) 
				{
				  $data['set_data']['category_image']=$file_name;
				}
		}
					}
					$this->db->where('category_id', $user_id);
					$this->db->update('tabqy_categories', $data['set_data']);
					$data['error']="false";
			 $data['msg']="true";
			$data['success_message']="Category updated successfully.";
			echo json_encode($data);die;
				}
		
		
	}
	
	Public function web_category(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="edit";
		$data['title']="Category";
		$data['heading']="Edit Category";
		view_loader('admin/category/web_category.html',$data);
	}
		function updateStatus($id, $value){
		  $this->db->flush_cache();
		  $this->db->where('category_id', $id);
		  $this->db->update('tabqy_categories',array('category_status'=>$value));
		  $this->db->flush_cache();
		  $this->db->select('category_status,category_id');
		  $this->db->from('tabqy_categories');
		  $this->db->where('category_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }
	
	
}

?>