<?php

class Country extends Controller{

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
                    $_SESSION['locationdata'] = array(
                        'status' => true,
                        'country' => $this->privillage['country'],
                        'region' => $this->privillage['region'],
                        'city' => $this->privillage['city'],
                        'zone' => $this->privillage['zone'],
                        'location' => $this->privillage['location']
                    );
                } else {
                    $_SESSION['locationdata']['status'] = false;
                }
                
            }
        }
		
		
		$tbl = 'tabqy_countries';
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
	public function home(){ 
	    
		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "Location";

		$data['heading'] = "Home Country ";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

	
		$sort = 'country_id';

    	$this->db->flush_cache();

		$this->db->select('c.country_id,c.country_code,c.country_name,c.country_file, count(r.resturantbrand_id) as Totalrest');

		$this->db->from('tabqy_countries c');
		$this->db->join('tabqy_resturantbrand as r','r.resturantbrand_country=c.country_code AND r.resturantbrand_type !=0','left');
		$this->db->group_by('c.country_code');
		$this->db->order_by('c.'.$sort,'DESC');		

		$query = $this->db->get();

		$data['results'] =$this->db->result($query);
		$data['country_count'] = $query->rowCount();

    	view_loader('admin/country/country_home.html',$data);	
	}

	public function index(){ 

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['title']	 = "Location";

		$data['heading'] = "Country";

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/country/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$sort 			 = 'country_id';



		$search = '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_countries'); //Total no. of values

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

		$this->db->select('tabqy_countries.*');

		$this->db->from('tabqy_countries');

		$this->db->order_by('tabqy_countries.'.$sort,'DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();

		$data['results']=$this->db->result($query);

		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

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

		view_loader('admin/country/view.html',$data);		

	}

	

	//Add Country

	   public function add(){
		 
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
      

		$this->validation->validate("country_name","Country Name","required");

		$this->validation->validate("country_code","Country Code","required");

		$this->validation->validate("country_phone_code","Phone Code","required");

		$this->validation->validate("country_currency_code","Currency Code","required");
		
       // $this->validation->validate("country_language_assign","country_language_assign","required");
       
		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']=$_POST;
			//print_r($_POST);die();

			$data['error']="true";

			echo json_encode($data);die;

		}else{
			if(!empty($_POST['country_code']) || !empty($_POST['country_name']) || !empty($_POST['country_phone_code']) || !empty($_POST['country_currency_code'])){
				$duplicate_country_code = $this->check_existence('country_code',$_POST['country_code']);
				if($duplicate_country_code){
						$data['error_msg']['country_code']="Country Code already exists.";
				}
				$duplicate_country_name = $this->check_existence('country_name',$_POST['country_name']);
				if($duplicate_country_name){
						$data['error_msg']['country_name']="Country Name already exists.";
				}
				$duplicate_ph_code = $this->check_existence('country_phone_code',$_POST['country_phone_code']);
				if($duplicate_ph_code){
						$data['error_msg']['country_phone_code']="Phone Code already exists.";
				}
				$duplicate_cur_code = $this->check_existence('country_currency_code',$_POST['country_currency_code']);
				if($duplicate_cur_code){
						$data['error_msg']['country_currency_code']="Currency Code already exists.";
				}
				
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
				
				unset($_POST['submit'], $_POST['country_id']);
				$data['send_data']= $_POST;						
                                $name=$_FILES['image']['name'];
				$type=$_FILES['image']['type'];
				$tmp_name=$_FILES['image']['tmp_name'];
				$size=$_FILES['image']['size'];
				$filename  = basename($_FILES['image']['name']);
				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				$file_name = time()."_".$_FILES['image']['name'];
				$allowedExts = array("gif", "jpeg", "jpg", "png");
		
				if ((($_FILES["image"]["type"] == "image/gif")
				|| ($_FILES["image"]["type"] == "image/jpeg")
				|| ($_FILES["image"]["type"] == "image/jpg")
				|| ($_FILES["image"]["type"] == "image/png"))
				&& in_array($extension, $allowedExts)){
						if (move_uploaded_file($_FILES['image']['tmp_name'], "upload/flagimages/".$file_name)) 
						{
						  $data['send_data']['country_file']=$file_name;
						}
				}
				

				$data['send_data']['country_status'] = 1;		
				$data['send_data']['country_created'] = date('Y-m-d H:i:s');
                
				$insert_id = $this->db->insert('tabqy_countries', $data['send_data']);
			   
				   foreach ($language as $languages) {
                $edit              = ($languages['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                $add_data_language = array(
                    "country_language_country_id" => $insert_id,
                    "country_language_language_code" => $languages['language_code'],
                    "country_language_country_name"=>       $_POST['country_name'],
                    "country_language_edit" => $edit,
                    "country_language_status" => '1'
                );
                
               $this->db->insert('tabqy_countries_language', $add_data_language);
           }

				$data['error']="false";

				$data['msg']="true";

				$data['success_message']="Country Added Successfully";

				echo json_encode($data);die;

		}
		}
    }   
	     
	
	  public function check_existence($field,$value,$id=''){
		  
	    $this->db->select('*');
		$this->db->from('tabqy_countries');
		$this->db->where($field,$value);
		if($id!=''){
			$this->db->where('country_id<>',$id);
		}
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];		
	}

	

	// Delete Country Module
       Public function delete($country_id)
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

			$this->db->where('country_id', $country_id);

            $this->db->delete('tabqy_countries'); 
			
			 $this->db->where('country_language_country_id', $country_id);
        $this->db->delete('tabqy_countries_language');

			 $data['error']           = "false";
          $data['msg']             = "false";
          $data['success_message'] = "Country success fully deleted";
           echo json_encode($data);
        exit();			

	}	



	// Edit Country Module

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

		$this->validation->validate("country_name","Name","required");		

		if($this->validation->validation_check()== FALSE){

			$data['error_msg']=$this->validation->error_msg;

			$data['set_data']= $_POST;

			$data['error']="true";

			echo json_encode($data);die;

		}else{
			if(!empty($_POST['country_code'])){
				$duplicate_country_code = $this->check_existence('country_code',$_POST['country_code'],$user_id);
				if($duplicate_country_code){
						$data['error_msg']['country_code']= "Country Code already exists.";
				}
				$duplicate_country_name = $this->check_existence('country_name',$_POST['country_name'],$user_id);
				if($duplicate_country_name){
						$data['error_msg']['country_name']= "Country Name already exists.";
				}
				$duplicate_ph_code = $this->check_existence('country_phone_code',$_POST['country_phone_code'],$user_id);
				if($duplicate_ph_code){
						$data['error_msg']['country_phone_code']= "Phone Code already exists.";
				}
				$duplicate_cur_code = $this->check_existence('country_currency_code',$_POST['country_currency_code'],$user_id);
				if($duplicate_cur_code){
						$data['error_msg']['country_currency_code']= "Currency Code already exists.";
				}
				
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
                 $country_id=$this->url->segment(4);
				unset($_POST['submit']);
                $data['set_data']= $_POST;
				$language_code = $_POST['language_code'];
				$country_name = $_POST['country_name'];
				unset($_POST['language_code']);				
				
			if($_SESSION['lang_code'] != $language_code) {
                           unset($_POST['country_name']);
                        }
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
                                if (move_uploaded_file($_FILES['image']['tmp_name'], "upload/flagimages/".$file_name)) 
                                {
                                  $data['set_data']['country_file'] = $file_name;
          
                                }
                            }
                    }
                            
                    $this->db->where('country_id', $country_id);
                    $this->db->update('tabqy_countries', $data['set_data']);
                    $this->db->flush_cache();
                    $edit_data_language = array(
                                    "tabqy_countries_language.country_language_country_name" => $country_name,
                                    "tabqy_countries_language.country_language_edit" => '1'
                    );

                    $this->db->where('country_language_country_id', $country_id);
	            $this->db->where('country_language_language_code', $language_code);
	            $this->db->update('tabqy_countries_language', $edit_data_language);

				$data['error']="false";

				$data['msg']="true";

				$data['success_message']="Country Updated Successfully";

				echo json_encode($data);die;

		}		

		}

	}

	Public function edit_view($id){
     
	   $country_id = $this->url->segment(4);
		$language_code = $this->url->segment(5);
		$this->db->flush_cache();

		$this->db->select('*');

		$this->db->from('tabqy_countries');
		 $this->db->join('tabqy_countries_language', "tabqy_countries_language.country_language_country_id = tabqy_countries.country_id", 'inner');

		$this->db->where('tabqy_countries.country_id',$country_id);
        $this->db->where('tabqy_countries_language.country_language_language_code', $language_code);		
           
		$query = $this->db->get();

		$data['edit_view']=$this->db->result($query);
		
        //print_r($query);die;
		echo json_encode($data);die;

	}

	function updateStatus($id, $value){

		$this->db->flush_cache();

		$this->db->where('country_id', $id);

		$this->db->update('tabqy_countries',array('country_status'=>$value));

		$this->db->flush_cache();

		$this->db->select('country_status,country_id');

		$this->db->from('tabqy_countries');

		$this->db->where('country_id', $id);

		$query = $this->db->get();

		$data['cur_status'] = $this->db->result($query);

		echo json_encode($data);die;

	 }

}