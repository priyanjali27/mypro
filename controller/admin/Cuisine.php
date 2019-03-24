<?php 
class Cuisine extends Controller{
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
            } else {
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
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  19-05-18
	  FUNCTION :  Index
	  DESCRIPTION : fetch all record 
     **********************************/
	Public function index(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success_massage']= flesh('success_message');
		$item_per_page      = 10;
		$page_url           = baseurl."admin/cuisine/index/"; //URL
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
			$this->db->where("tabqy_cuisine.cuisine_name LIKE '%$search%'");
		}
		
		$this->db->from('tabqy_cuisine');
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
		/*$this->db->flush_cache();
		$this->db->select('tabqy_cuisine.*');

		$this->db->from('tabqy_cuisine');
        $query_c = $this->db->get();*/

		//$data['results']=$this->db->result($query_c);


		$this->db->flush_cache();

		$this->db->select('l.cuisine_language_cuisine_name,l.cuisine_language_language_code,co.*');

		$this->db->from('tabqy_cuisine as co');
                $this->db->join('tabqy_cuisine_language as l',"l.cuisine_language_cuisine_id=co.cuisine_id","inner");
		$this->db->where('l.cuisine_language_language_code',$_SESSION['lang_code']);

		$query_c = $this->db->get();
         //echo $this->db->last_query();
         $data['results']=$this->db->result($query_c);
		
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="Cuisine";
		$data['heading']="Cuisine";
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
		 $data['privillage'] = $this->privillage;
		view_loader('admin/cuisine/index.html',$data);
	}
		
			
		
	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  19-05-18
	  FUNCTION :  add
	  DESCRIPTION : Insert data to table 
     **********************************/
	Public function add(){

		 if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['add'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
		 $language = $this->language;
	
		$this->validation->validate("cuisine_name","Name","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
		    $data['msg']="false";
			echo json_encode($data);die;
		}else{
			
			unset($_POST['submit']);
			unset($_POST['cuisine_id']);
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
				  $data['send_data']['cuisine_image']=$file_name;
				}
		}
		
			$data['send_data']['cuisine_status']=1;
			$data['send_data']['cuisine_name']=$_POST['cuisine_name'];
			$data['send_data']['cuisine_created']=date('Y-m-d H:i:s');
		 
		$insert_id =	$this->db->insert('tabqy_cuisine', $data['send_data']);
			
			
			//die();
			 foreach ($language as $languages) {
                $edit              = ($languages['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                $add_data_language = array(
                    "cuisine_language_cuisine_id" => $insert_id,
                    "cuisine_language_language_code" => $languages['language_code'],
                    "cuisine_language_cuisine_name"=>       $_POST['cuisine_name'],
                    "cuisine_language_edit" => $edit,
                    "cuisine_language_status" => '1'
                );
                $this->db->insert('tabqy_cuisine_language', $add_data_language);
           }
             $data['error']="false";
			 $data['msg']="true";
			$data['success_message']="Cuisine added successfully.";
			echo json_encode($data);die;
		}
    }
	

		/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  19-05-18
	  FUNCTION :  delete
	  DESCRIPTION : Delete data 
     **********************************/
       Public function delete($cuisine_id)
    {
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

			$this->db->where('cuisine_id', $cuisine_id);

            $this->db->delete('tabqy_cuisine'); 
			
			 $this->db->where('cuisine_language_cuisine_id', $cuisine_id);
             $this->db->delete('tabqy_cuisine_language');

			 $data['error']           = "false";
			  $data['msg']             = "false";
			  $data['success_message'] = "Cuisine success fully deleted";
			   echo json_encode($data);
			   exit();			

	}
 /*************************************
		DEVELOPER: Sonu 
		DATE: 19-05-2018
		FUNCTION: edit_view
		DESCRIPTION: To edit the department 
	    *************************************/
	Public function edit_view($id){

		$cuisine_id = $this->url->segment(4);
		$language_code = $this->url->segment(5);
        $this->db->flush_cache();
        $this->db->select('*');
        $this->db->from('tabqy_cuisine');
        $this->db->join('tabqy_cuisine_language',"tabqy_cuisine_language.cuisine_language_cuisine_id = tabqy_cuisine.cuisine_id", 'inner');
        $this->db->where('tabqy_cuisine.cuisine_id', $cuisine_id);
        $this->db->where('tabqy_cuisine_language.cuisine_language_language_code', $language_code);
        $query             = $this->db->get();
        $data['edit_view'] = $this->db->result($query);

        // echo $this->db->last_query();
		echo json_encode($data);
        die;
	}

	/***********************************
	  DEVELOPER:  Sonu
	  DATE     :  19-05-2018
	  FUNCTION :  edit
	  DESCRIPTION : updating record of table
     **********************************/
		Public function edit($user_id){

		
		 if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['edit'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        } 
             
		$this->validation->validate("cuisine_name","Name","required");		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{
			
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
                 $cuisine_id = $this->url->segment(4);
				unset($_POST['submit']);
				unset($_POST['cuisine_image']);
                $data['set_data']= $_POST;

              $language_code = $_POST['language_code'];
				$cuisine_name = $_POST['cuisine_name'];
				unset($_POST['language_code']);				
			
                    $data['set_data']= $_POST; 
                    if(!empty($_FILES['image']['name'])){ 
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
                                  $data['set_data']['cuisine_image'] = $file_name;
          
                                }
                            }
                    }

			if($_SESSION['lang_code'] == $language_code) {
                           
                           $this->db->where('cuisine_id', $cuisine_id);
                    $this->db->update('tabqy_cuisine', $data['set_data']);
                        }else{
                         unset($_POST['cuisine_name']);
                        }
                            
                    
                    $this->db->flush_cache();
                    $edit_data_language = array(
                                    "tabqy_cuisine_language.cuisine_language_cuisine_name" => $cuisine_name,
                                    "tabqy_cuisine_language.cuisine_language_edit" => '1'
                    );
                  // echo $cuisine_id;die();
                    $this->db->where('cuisine_language_cuisine_id', $cuisine_id);
                        
	            $this->db->where('cuisine_language_language_code', $language_code);
	            $this->db->update('tabqy_cuisine_language', $edit_data_language);
               //echo  $this->db->last_query();die;
				$data['error']="false";

				$data['msg']="true";

				$data['success_message']="Cuisine Updated Successfully";

				echo json_encode($data);die;

		}		

		}

	}
	 /***********************************
	  DEVELOPER:  Sonu
	  DATE     :  27-04-2018
	  FUNCTION :  updateStatus
	  DESCRIPTION : updating status particular 
     **********************************/

		public function updateStatus($id, $value){

		  $this->db->flush_cache();
		  $this->db->where('cuisine_id', $id);
		  $this->db->update('tabqy_cuisine',array('cuisine_status'=>$value));
		  $this->db->flush_cache();
		  $this->db->select('cuisine_status,cuisine_id');
		  $this->db->from('tabqy_cuisine');
		  $this->db->where('cuisine_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }
	
	
}

?>