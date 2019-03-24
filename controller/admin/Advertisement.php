<?php

class Advertisement extends Controller{

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

		$this->db->select('lang.language_name,lang.language_code,lang.language_flag');

		$this->db->from('tabqy_language as lang');

		$this->db->order_by('lang.language_id','DESC');

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
          DATE     :  17-05-2018
          FUNCTION :  index
          DESCRIPTION : Listing all data 
      **********************************/

	public function index(){

		$data['session'] = $_SESSION;

		$data['language'] = $this->language;

		$data['action']  ='list';

		$data['success'] = flesh('success');

		$data['error']   = flesh('error');

		$item_per_page   = 10; //item to display per page

		$page_url        = baseurl."admin/advertisement/index/"; //URL

		$pageno			 = $this->url->segment(4); // Get page number

		$search = '0';

		$get_total_rows  = $this->db->count_all_results('tabqy_regions'); //Total no. of values

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

		$this->db->select('a.*, co.company_id, co.company_name, re.resturantbrand_id, re.resturantbrand_name');

		$this->db->from('tabqy_advertisement as a');

		$this->db->join('tabqy_company as co', "co.company_id=a.advertisement_company_id", "inner");

		$this->db->join('tabqy_resturantbrand as re', "re.resturantbrand_id=a.advertisement_resturant_id", "inner");

		$this->db->order_by('a.advertisement_id','DESC');

		$this->db->limit($item_per_page,$page_position);

		$query = $this->db->get();
   //echo $this->db->last_query();

		$data['results'] = $this->db->result($query);
    //echo "<pre>";   print_r($data['results']);die();
		$this->db->flush_cache();

        $this->db->select('tabqy_company.*');

        $this->db->from('tabqy_company');

        $this->db->where('tabqy_company.company_status','1');

        $query_c = $this->db->get();

       $data['related_company'] = $this->db->result($query_c);
			//echo"<pre>";print_r($data['related_company']);die();	

		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['title']="Advertisement";

		$data['heading']="Advertisement";

		$data['user_id']="";

		$data['page_number']=$page_number;

		$start=$page_position+1;

		$data['start']=$start;

		$data['end']=($start+count($data['results'])-1);
    $data['privillage'] = $this->privillage;
		view_loader('admin/advertisement/index.html',$data);		

	}

  	public function check_existence($field,$value,$id=''){
  	    $this->db->select('*');
  		$this->db->from('tabqy_advertisement');
  		$this->db->where($field,$value);
  		if($id!=''){
  			$this->db->where('zone_id<>',$id);
  		}
      	$query = $this->db->get();
  		$results= $this->db->result($query);
  		return $results[0];		
  	}

	
       /***********************************
          DEVELOPER:  Sonu
          DATE     :  17-05-2018
          FUNCTION :  related_restaurant
          DESCRIPTION : Listing restaurant
      **********************************/
	

	Public function related_restaurant($ci_id){
       

        $this->db->select('*');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_company_id',$ci_id);
        $this->db->where('resturantbrand_type',0);
        $query_c = $this->db->get();

		$data['related_restuartant'] = $this->db->result($query_c);
         echo json_encode($data);die;

	}

	/***********************************
      DEVELOPER:  Sonu
      DATE     :  17-05-2018
      FUNCTION :  related_branch
      DESCRIPTION : Listing branch
     **********************************/

	Public function related_branch($ci_id){
        

        $this->db->select('re.resturantbrand_id,r.*');

        $this->db->from('tabqy_resturantbrand as r');

        $this->db->join('tabqy_resturantbrand as re', "re.resturantbrand_id=r.resturantbrand_type", "inner");
        $this->db->where('re.resturantbrand_id',$ci_id);
         $query = $this->db->get();
        // echo $this->db->last_query();
     
		
		$data['related_branch'] = $this->db->result($query);
         echo json_encode($data);die;

	}

    /***********************************
      DEVELOPER:  Sonu
      DATE     :  17-05-2018
      FUNCTION :  add
      DESCRIPTION : Insert data
     **********************************/

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
            
        $this->validation->validate("advertisement_company_id","Company","required");
        
        $this->validation->validate("resturant_code","Restaurant","required");

        $this->validation->validate("branch_code","Branch","required");

        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data']  = $_POST;
            $data['error']     = "true";
             $data['msg']      ="false";
            echo json_encode($data);
            die;
        }else{  
              
              unset($_POST['submit']);
              unset($_POST['advertisement_id']);
              $data['send_data']  = $_POST;
              
                //print_r($data['send_data']);die();
                $name=$_FILES['image']['name'];
                //echo $name; die();
                $type=$_FILES['image']['type'];
                //echo $type;
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
                        if (move_uploaded_file($_FILES['image']['tmp_name'], "upload/advertisment_image/".$file_name)) 
                        {
                          $data['send_data']['advertisement_image']=$file_name;
                         
                        }
                }

                $data['send_data']= $_POST;
                //$branch_data = $data['send_data']['advertisement_branch_id'];


                $data['send_data']['advertisement_status'] = 1;
                $data['send_data']['advertisement_image']=$file_name;
                $insert_id = $this->db->insert('tabqy_advertisement', $data['send_data']);
               
                 $data['error']="false";
                 $data['msg']="true";
                $data['success_message']="Advertisement Added Successfully";
               // redirect('admin/advertisement');
            
                echo json_encode($data);die;
            }
        

    }

    /*************************************
       DEVELOPER: Sonu  
       DATE:     18-05-2018
       FUNCTION:  delete
       DESCRIPTION: Used to delete data
     *************************************/

    Public function delete($id){  
      //echo"hello";
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
        //privillage checking and user validating  end here.
    
            $this->db->where('advertisement_id', $id);

            $this->db->delete('tabqy_advertisement'); 
            

            $data['error']           = "false";
            $data['msg']             = "false";
            $data['success_message'] = "Advertisement successfully deleted";
            echo json_encode($data);
            exit();
    }

     /*************************************
       DEVELOPER: Sonu  
       DATE:  18-05-2018
       FUNCTION:  edit
       DESCRIPTION: Update record 
     *************************************/
    Public function edit($id){
            
          if ($_SESSION['userdata']['user_role'] != 0) {             
                if ($this->privillage['edit'] != 1) {
                    $data['error']           = "permission";
                    $data['msg']             = "false";
                    $data['success_message'] = "You don't have permission to perform this action";
                    echo json_encode($data);
                    exit();
                }
            }

      $this->validation->validate("advertisement_company_id","Company","required");
        
        $this->validation->validate("resturant_code","Restaurant","required");

        $this->validation->validate("branch_code","Branch","required");  

      if($this->validation->validation_check()== FALSE){

        $data['error_msg']=$this->validation->error_msg;

        $data['set_data']= $_POST;

        $data['error']="true";

        echo json_encode($data);die;

      }else{
        
       
         
          unset($_POST['submit']);
unset($_POST['advertisement_image']);
          $data['set_data']= $_POST;
        if($_FILES && $_FILES['image']['name']){
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
                if (move_uploaded_file($_FILES['image']['tmp_name'], "upload/advertisment_image/".$file_name)) 
                {
                  $data['set_data']['advertisement_image']=$file_name;
                }
            }
          }       
              //  $data['set_data']['advertisement_branch_id'] =implode(",",$data['set_data']['advertisement_branch_id']);
   //print_r($data['set_data']);die;
           $this->db->where('advertisement_id', $id);

          $this->db->update('tabqy_advertisement', $data['set_data']);

          $data['error']="false";

          $data['msg']="true";

          $data['success_message']="Advertisement Updated Successfully";

          echo json_encode($data);die;
        }
      }   

    
    /*************************************
       DEVELOPER:  Sonu  
       DATE     :  18-05-2018
       FUNCTION :  updaetstatus
       DESCRIPTION:Used to update status 
     *************************************/
         public function updateStatus($id, $value){

                $this->db->flush_cache();

                $this->db->where('advertisement_id', $id);

                $this->db->update('tabqy_advertisement',array('advertisement_status'=>$value));

                $this->db->flush_cache();

                $this->db->select('advertisement_status,advertisement_id');

                $this->db->from('tabqy_advertisement');

                $this->db->where('advertisement_id',$id);

                $this->db->flush_cache();

                $query = $this->db->get();
                $data['cur_status'] = $this->db->result($query);

                echo json_encode($data);die;

         }

     /*************************************
       DEVELOPER:  Sonu  
       DATE     :  18-05-2018
       FUNCTION :  edit_view
       DESCRIPTION: Fetch record  
     *************************************/

       Public function edit_view($id){

        $this->db->flush_cache();

        $this->db->select('tabqy_advertisement.*');

        $this->db->from('tabqy_advertisement');


        $this->db->where('advertisement_id='.$id);        

        $query = $this->db->get();


        $data['edit_view']=$this->db->result($query);

        $c_id=$data['edit_view']['0']['advertisement_company_id'];
        $b_id=$data['edit_view']['0']['advertisement_resturant_id'];

  $this->db->flush_cache();

        $this->db->select('tabqy_resturantbrand.resturantbrand_id,tabqy_resturantbrand.resturantbrand_name');

        $this->db->from('tabqy_resturantbrand');

        $this->db->where('tabqy_resturantbrand.resturantbrand_type=0');        

        $this->db->where('tabqy_resturantbrand.resturantbrand_company_id='.$c_id);        

        $query = $this->db->get();


        $data['brands']=$this->db->result($query);
      
 $this->db->flush_cache();

        $this->db->select('tabqy_resturantbrand.resturantbrand_id,tabqy_resturantbrand.resturantbrand_name');

        $this->db->from('tabqy_resturantbrand');

        $this->db->where('tabqy_resturantbrand.resturantbrand_type='.$b_id);        

        $this->db->where('tabqy_resturantbrand.resturantbrand_company_id='.$c_id);        

        $query = $this->db->get();


        $data['branches']=$this->db->result($query);

            
        $branch_data = explode(',',$data['edit_view'][0]['advertisement_branch_id']);
        

          foreach($branch_data as $selected){
          $this->db->select('*');
          $this->db->from('tabqy_resturantbrand');
          $this->db->where('tabqy_resturantbrand.resturantbrand_name',$selected);
          $que = $this->db->get();
          $data['branch_data'][] = $this->db->result($que);

          }    
        //  echo $this->db->last_query();
        echo json_encode($data);die;

    }
}
?>