<?php 
// User controller uses for superadmin,tabqy employees,customer 
class User extends Controller{
	var $language;
    var $session;
    var $name;
    var $departments;
    var $resturants;	
	public function __construct()
    {
        parent::__construct();
		
		if (is_logged_in()==FALSE)
        {
            redirect('admin/admin_login');
        }
        $this->privillage = $this->privillage();
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
		} else {
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		}
		$this->db->flush_cache();
		$this->db->select('tabqy_department.department_id,tabqy_department.department_name');
		$this->db->from('tabqy_department');
		$this->db->order_by('tabqy_department.department_id','DESC');
		$department = $this->db->get();
		$this->departments=$this->db->result($department);
		$this->db->flush_cache();
		$this->db->select('resturantbrand_id,resturantbrand_name');
		$this->db->from('tabqy_resturantbrand');
		$this->db->where('resturantbrand_type',0);
		$this->db->order_by('resturantbrand_id','DESC');
		$query = $this->db->get();
		$this->resturants=$this->db->result($query);
	
    }
    /*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: index
	DESCRIPTION: show list of users .
    *************************************/
	Public function index(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['departments'] = $this->departments;
		$data['resturantss'] = $this->resturants;
		
       if(!empty($this->url->segment(6)) && $this->url->segment(6)!=0){
			$data['sort']=$this->url->segment(6);
		}else{
			$data['sort']='user_id';
		}
		if($this->url->segment(7)=='ASC'){
			$data['sort_type']='DESC';
		}elseif($this->url->segment(7)=='DESC'){
			$data['sort_type']='ASC';
		}else{
			$data['sort_type']='DESC';
		}
		
		$data['success_massage']= flesh('success_massage');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/user/index/"; //URL
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
			$this->db->where("user_username LIKE '%$search%'");
			$this->db->or_where("user_name LIKE '%$search%'");
			$this->db->or_where("user_email LIKE '%$search%'");
		}
		
		$this->db->where('tabqy_user.user_role',1);
		$this->db->from('tabqy_user');
		$get_total_rows = $this->db->count_all_results();
		
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
		$this->db->select('tabqy_user.*,tabqy_resturantbrand.resturantbrand_name,dept.department_name');
		$this->db->from('tabqy_user');
		if($search!='0'){
			$this->db->where("user_username LIKE '%$search%'");
			$this->db->or_where("user_name LIKE '%$search%'");
			$this->db->or_where("user_email LIKE '%$search%'");
		}
		$this->db->join('tabqy_resturantbrand','tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id','LEFT');
		$this->db->join('tabqy_department as dept', 'tabqy_user.user_department_id=dept.department_id','left');
		$this->db->order_by('tabqy_user.'.$data['sort'],$data['sort_type']);
		$this->db->where('tabqy_user.user_role',1);
		
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="User";
		$data['heading']="Staff";
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
        view_loader('admin/user/user.html',$data);
	}
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: resturant
	DESCRIPTION: Fetching Users for Particular Brand
    *************************************/
	Public function resturant(){
		if(empty($this->url->segment(4)) || empty($this->url->segment(5)) || empty($this->url->segment(6)))
        {
				$last_url="admin/company";
				redirect($last_url);
		}
		$company_id=$this->url->segment(6);
		$restaurant_id=$this->url->segment(4);
		$country_id=$this->url->segment(5);
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['departments'] = $this->departments;
		$data['resturantss'] = $this->resturants;
		$data['restaurant_id'] = $restaurant_id; 
		$data['country_id'] = $country_id;
		$data['company_id'] = $company_id;
		$data['success_massage']= flesh('success_massage');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/user/resturant/$restaurant_id/$country_id/$company_id"; //URL
		$pageno=$this->url->segment(7); // Get page number
			
		$this->db->where('tabqy_user.user_role',1);
		$this->db->from('tabqy_user');
		$get_total_rows = $this->db->count_all_results();
		
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
		$this->db->select('tabqy_user.*,tabqy_resturantbrand.resturantbrand_name,dept.department_name');
		$this->db->from('tabqy_user');
	
		$this->db->join('tabqy_resturantbrand','tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id','LEFT');
		$this->db->join('tabqy_department as dept', 'tabqy_user.user_department_id=dept.department_id','left');
		
		$this->db->where('tabqy_user.user_restaurant_id',$restaurant_id);
		$this->db->where_in('tabqy_user.user_role',array(4,6));
		$this->db->where('tabqy_user.user_role!=',0);
		$this->db->where('tabqy_user.user_company_id',$company_id);
		$this->db->where('tabqy_user.user_restaurant_id!=',0);
		$this->db->order_by('tabqy_user.user_role','ASC');
		$search="0";
		
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="User";
		$data['heading']="Staff";
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
            $this->db->flush_cache();
            $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
            $this->db->from('tabqy_countries as co');
            $this->db->join('tabqy_countries_language as l',"l.country_language_country_id=co.country_id","inner");
            $this->db->where('l.country_language_language_code',$_SESSION['lang_code']);
            $query_c = $this->db->get();
            $data['related_country'] = $this->db->result($query_c);
            $this->db->flush_cache();
		
		view_loader('admin/user/user_resturant.html',$data);
	}
	
	Public function restaurant_branch(){
		if(empty($this->url->segment(4)) || empty($this->url->segment(5)) || empty($this->url->segment(6)))
        {
				$last_url="admin/company";
				redirect($last_url);
		}
		$company_id=$this->url->segment(6);
		$restaurant_id=$this->url->segment(4);
		$country_id=$this->url->segment(5);
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['departments'] = $this->departments;
		$data['resturantss'] = $this->resturants;
		$data['restaurant_id']=$restaurant_id; 
		$data['country_code']=$country_id;
		$data['company_id']=$company_id;
		$data['success_massage']= flesh('success_massage');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/user/restaurant_branch/$restaurant_id/$country_id/$company_id"; //URL
		$pageno=$this->url->segment(7); // Get page number
			
		$this->db->where('tabqy_user.user_role',1);
		$this->db->from('tabqy_user');
		$get_total_rows = $this->db->count_all_results();
		
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
		$this->db->select('tabqy_user.*,tabqy_resturantbrand.resturantbrand_name,tabqy_resturantbrand.resturantbrand_type,dept.department_name');
		$this->db->from('tabqy_user');	
		$this->db->join('tabqy_resturantbrand','tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id','LEFT');
		$this->db->join('tabqy_department as dept', 'tabqy_user.user_department_id=dept.department_id','left');		
		$this->db->where('tabqy_user.user_restaurant_id',$restaurant_id);
		$this->db->where_in('tabqy_user.user_role',array(5,7));
		$this->db->where('tabqy_user.user_role!=',0);
		$this->db->where('tabqy_user.user_restaurant_id!=',0);
		$this->db->order_by('tabqy_user.user_role','ASC');
		$search="0";
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		
		$this->db->flush_cache();
		$this->db->select('resturantbrand_type');
		$this->db->from('tabqy_resturantbrand');
		$this->db->where('resturantbrand_id',$restaurant_id);
		$type_query = $this->db->get();
		$ress = $this->db->row($type_query);
		$data['brand_type'] = $ress['resturantbrand_type'];
		
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="User";
		$data['heading']="Staff";
		$data['user_id']="";
		$data['page_number']=$page_number;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		
		view_loader('admin/user/user_restaurant_branch.html',$data);
	}
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: add
	DESCRIPTION: add user with roles.
    *************************************/
	Public function add(){
        $this->validation->validate("user_name","Name","required");
		$this->validation->validate("user_password","Password","required");
		$this->validation->validate("user_phoneno","Phone number","required|numeric");
		$this->validation->validate("user_email","Email","required|email");
		$this->validation->validate("user_username","Username","required|alpha_numeric");
		$this->validation->validate("user_gender","Gender","required");
		$this->validation->validate("user_address","Address","required");
		$this->validation->validate("user_dob","Date of birth","required");
		$this->validation->validate("user_city","City","required");
		$this->validation->validate("user_zipcode","Zipcode","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
		    if(!empty($_POST['user_username']) || !empty($_POST['user_email']) ){
		      $username_array= $this->already_exist('user_username',$_POST['user_username']);
		     if($username_array){
		        $data['error_msg']['user_username']="Username is already exist.";
             }
		     $useremail_array= $this->already_exist('user_email',$_POST['user_email']);
		      if($useremail_array){
		          	$data['error_msg']['user_email']="Email is already exist.";
			   }
		     }
		    if(!empty($data['error_msg'])){
               $data['error']="true";
			    $data['msg']="false";
			echo json_encode($data);die;
		    }else{
		    unset($_POST['submit']);
			unset($_POST['user_id']);
			$data['send_data']= $_POST;
            $pass=$this->validation->random_string(7);
			$pwd=$this->db->password_encrypt($pass);
			$username=$_POST['user_username'];
			$email=$_POST['user_email']; 
			$name=$_POST['user_name'];
			$data['send_data']['user_password'] =$pwd;
			$data['send_data']['user_created']=date('Y-m-d H:i:s');
			$this->db->insert('tabqy_user', $data['send_data']);
			unset($data['send_data']);
			require "core/lib/phpmailer/PHPMailerAutoload.php";
        
        $mail = new PHPMailer;
        $mail->setFrom('tabqy@askonlinetraning.com', 'TABQY');
        $mail->addAddress($email, 'Recepient Name');
        $mail->isHTML(true);
        $mail->Subject = 'Welcome To Tabqy';
        $msg_admin = "Thank you for selecting Tabqy, you have successfully registered with us.";
        $mail->Body = '<div style="margin:0;padding:0">
	             	<div style="max-width:720px;margin:0 
	auto;background:#f4f4f4;height:559px">
	             	<div style="max-width:600px;margin:0 auto">
	             	<table width="100%" bgcolor="#f4f4f4" border="0" 
	cellspacing="0" cellpadding="0">
	             	  <tbody><tr>
	                 <td style="padding:15px 0 0 5px"><table width="99%" 
	border="0" cellspacing="0" cellpadding="0">
	                   <tbody><tr><td 
	style="padding:0px;vertical-align:middle">Welcome To Tabqy</td><td 
	style="padding:0px;vertical-align:top"><div style="float:right"><img 
	src="'.baseurl.'upload/logo/07.png" alt="" width="160" height="80" draggable="true" 
	data-bukket-ext-bukket-draggable="true" class="CToWUd"></div></td></tr>
	                 </tbody></table></td>
	               </tr>
	               <tr><td height="15px" style="padding:0"></td></tr>
	               <tr>
	                 <td bgcolor="#fff"><table width="100%" border="0" 
	cellspacing="0" cellpadding="0">
	                   <tbody><tr>
	                     <td style="padding:22px 16px 
	7px;font-family:arial;font-size:18px;color:#333">Dear 
	<b>'.$name.',</b></td>
	                   </tr>
	             		<tr><td style="padding:15px 16px 
	13px;font-family:arial;font-size:16px;color:#333">'.$msg_admin.'</td>
	                   </tr></tbody></table></td></tr>
	                     <tr>
	                     <td style="padding:6px 16px 
	8px;font-family:arial;font-size:16px;color:#333;line-height:23px"><b>Your 
	Details:-</b></td>
	                   </tr>
	                   <tr>
	                     <td style="padding:6px 16px 
	8px;font-family:arial;font-size:16px;color:#333;line-height:23px"><b>Email:</b> 
	<a href="mailto:'.$email.'" target="_blank">'.$email.'</a><br>
	             		<b>Username:</b> '.$username.'<br>
	             		<b>Password:</b> '.$pwd.'<br><br><br>
	             	
	             		</td>
	                   </tr>
					   <tr>
							<td style="text-align:left; vertical-align:top;" colspan="4" >
							<br/><br/><br/>
								<div style="max-width:750px; margin:0 auto; color:##333;">
								<strong>Thanks & regards</strong><br/><br/>

								Tabqy Team<br/>
								tabqy@askonlinetraning.com<br/>


								</div>
							</td>
							</tr>
	                 </tbody></table>

	             	 <div class="yj6qo"></div><div class="adL">
	             	</div></div><div class="adL">
	             	</div></div><div class="adL">
	             	</div></div>';
        if(!$mail->send()) 
        {
           $msg= "Mailer Error: " . $mail->ErrorInfo.' but your data is saved';
             $data['error']="true";
			  $data['msg']="true";
			$data['success_message']=$msg;
			echo json_encode($data);die;
        } 
             $data['error']="false";
			  $data['msg']="true";
			$data['success_message']="New User added successfully";
			echo json_encode($data);die;
		}
		}
    }
	
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: delete
	DESCRIPTION: Delete user Module
    *************************************/
	Public function delete($user_id){
        $this->db->where('user_id', $user_id);
        $this->db->delete('tabqy_user'); 
		die;
	}
	
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: edit
	DESCRIPTION: Edit user Module
    *************************************/
	Public function edit($user_id){
	           $this->validation->validate("user_name","Name","required");
				$this->validation->validate("user_password","Password","required");
				$this->validation->validate("user_phoneno","Phone number","required|numeric");
				$this->validation->validate("user_email","Email","required|email");
			    $this->validation->validate("user_gender","Gender","required");
				$this->validation->validate("user_address","Address","required");
				$this->validation->validate("user_city","City","required");
				$this->validation->validate("user_dob","Date of birth","required");
				$this->validation->validate("user_zipcode","Zipcode","required");
				if($this->validation->validation_check()== FALSE){
					$data['error_msg']=$this->validation->error_msg;
					$data['set_data']= $_POST;
					$data['set_data']['user_updated']=date('Y-m-d H:i:s');
					$data['error']="true";
			        echo json_encode($data);die;
				}else{
						if(!empty($_POST['user_email']) ){						
							$useremail_array= $this->already_exist('user_email',$_POST['user_email'],$user_id);
							if($useremail_array){
								$data['error_msg']['user_email']="Email is already exist.";
							}
						}
						if(!empty($data['error_msg'])){
							$data['error']="true";
							$data['msg']="false";
						echo json_encode($data);die;
						}else{
							unset($_POST['submit']);
							$data['set_data']= $_POST;
							$this->db->where('user_id', $user_id);
							$this->db->update('tabqy_user', $data['set_data']);
							$data['error']="false";
							$data['msg']="true";
							$data['success_message']="User updated successfully";
							echo json_encode($data);die;
				}
		
				}
	}
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: view
	DESCRIPTION: view user 
    *************************************/
	function view(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$id = $this->url->segment(4);
		$data['title'] = "User";
		$data['heading'] = "User";
		$data['action'] = 'view';
		$data['success'] = flesh('success');
		$this->db->select('tabqy_user.*,tabqy_department.department_name');
			$this->db->from('tabqy_user');
			$this->db->join('tabqy_department','tabqy_user.user_department_id=tabqy_department.department_id','LEFT');
			$this->db->where('tabqy_user.user_id',$id);
			$query = $this->db->get();
			$results= $this->db->result($query);
			$data['user']=$results[0];
			view_loader('admin/user/view.html',$data);
	}	
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: dashboard
	DESCRIPTION:  dashboard view  
    *************************************/
	function dashboard(){
	    if (is_logged_in()==FALSE)
        {
            redirect('admin/admin_login');
        }
	      $this->privillage = $this->privillage();
              if ($this->privillage['locationstatus'] == true) {
	      redirect('admin/branchesaccess');}
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
        $id = $this->url->segment(4);
		$data['title'] = "Dashboard";
		$data['heading'] = "User";
		$data['action'] = 'Dashboard';
		$data['departments'] = $this->departments;
		$data['resturantss'] = $this->resturants;
		$data['success'] = flesh('success');
		$this->db->flush_cache();
		$this->db->from('tabqy_user');
		$data['get_total_users'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->where('tabqy_resturantbrand.resturantbrand_type',0);
		$this->db->from('tabqy_resturantbrand');
		$data['get_total_resturants'] = $this->db->count_all_results();
		$this->db->flush_cache();
	//	$this->db->where('tabqy_user.user_role',1);
		$this->db->from('tabqy_user');
		$data['get_total_tbusers'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->from('tabqy_language');
		$data['get_total_languages'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->from('tabqy_countries');
		$data['get_total_countries'] = $this->db->count_all_results();
		$this->db->flush_cache();
        $this->db->select('tabqy_user.*,tabqy_department.department_name');
        $this->db->from('tabqy_user');
        $this->db->join('tabqy_department','tabqy_user.user_department_id=tabqy_department.department_id','LEFT');
        $this->db->order_by("tabqy_user.user_id", "desc");
        $this->db->limit(9,0);
        $query = $this->db->get();
        $results= $this->db->result($query);
      	$data['users']=$results;
        $this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
        $this->db->from('tabqy_resturantbrand t2');
        $this->db->join('tabqy_resturantbrand t1','t1.resturantbrand_type=t2.resturantbrand_id','RIGHT');
        $this->db->order_by("t1.resturantbrand_id", "desc");
        $this->db->limit(9,0);
        $query = $this->db->get();
        $resturants= $this->db->result($query);
        $data['resturants']=$resturants;

        $this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l',"l.country_language_country_id=co.country_id","inner");
		$this->db->where('l.country_language_language_code',$_SESSION['lang_code']);
        $this->db->order_by('co.country_name','ASC');
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);
    	view_loader('admin/user/dashboard.html',$data);
	}	
    /*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: change_password
	DESCRIPTION:  To change the user's password  
    *************************************/
	public function change_password(){
			$id=$_SESSION['userdata']['user_id'];
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="Change";
		$data['user_id']="";
		$data['title']="User";
		$data['heading']="Change Password";
		$data['error_massage']=flesh('error_massage');
		$data['success_massage']=flesh('success_massage');
		$this->validation->validate("old_password","Old Password","required");
		$this->validation->validate("new_password","New Password","required");
		$this->validation->validate("confirm_password","Confirm Password","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
            $data['privillage'] = $this->privillage;
	        
            view_loader('admin/user/change.html',$data);
		}else{
			if($_POST){
			
				if(!empty($_POST['old_password'])){
					$old=$this->db->password_encrypt($_POST['old_password']);
					$this->db->select('tabqy_user.*');
					$this->db->from('tabqy_user');
					$this->db->where('tabqy_user.user_id',$id);
					$this->db->where('tabqy_user.user_password', $old);
					$query = $this->db->get();
					$results= $this->db->result($query);
					
					if(empty($results[0])){
			
					$data['error_msg']['old_password']="Old Password is not vaild.";
					set_flesh('error_massage', $data['error_msg']['old_password']);
					$data['set_data']=$_POST;

					view_loader('admin/user/change.html',$data);
					}
				}
				if($_POST['new_password'] != $_POST['confirm_password']){
								
				    $data['error_msg']['confirm_password']="Password does not matches.";
					$data['set_data']=$_POST;
					view_loader('admin/user/change.html',$data);
				}else{
							
					$old=$this->db->password_encrypt($_POST['old_password']);
					$new=$this->db->password_encrypt($_POST['new_password']);
					$new_array['user_password']=$new;
					$this->db->where('user_id', $id);
					$this->db->where('user_password', $old);
					$this->db->update('tabqy_user',$new_array);
					set_flesh('success_massage', 'Password changed successfully !!!');
					redirect('admin/user/change_password');
			    }
			}
		
		}
	}


	public function change_country()
	{
	


		$id=$_SESSION['userdata']['user_id'];
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="Change";
		$data['user_id']="";
		$data['title']="Default Country";
		$data['heading']="Change Country";
		//$data['send_data']=$_POST;

		  if (isset($_POST['user_default_country'])) {
    
         $edit_data = array(
                "user_default_country" => $_POST['user_default_country']
                
            );

        $this->db->where('user_id',  $id);
        $this->db->update('tabqy_user', $edit_data);
        if (!empty($_SESSION['userdata']['user_default_country'])) {
        	  unset($_SESSION['userdata']['user_default_country']);
        	   $_SESSION['userdata']['user_default_country']=$_POST['user_default_country'];
        }
      
        
         if (!empty($_SESSION['selected_country'])) {
         	 unset($_SESSION['selected_country']);
         	  $_SESSION['selected_country']=$_POST['user_default_country'];
         }
      
      
       redirect('admin/user/dashboard');



       }
	
	    $this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('co.country_name', 'ASC');
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);
         $data['privillage'] = $this->privillage;



       // echo $this->db->last_query();

	   view_loader('admin/user/change_country.html',$data);

	
			    }
			
		
		


	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: already_exist
	DESCRIPTION:  To check the user 
	           already exist or not 
    *************************************/
	
	public function already_exist($field,$value,$id=''){
	    $this->db->select('*');
		$this->db->from('tabqy_user');
		$this->db->where($field,$value);
		if($id!=''){
			$this->db->where('user_id<>',$id);
		}
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];	
	}
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: profile
	DESCRIPTION:  To user profile 
    *************************************/
	Public function profile(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action']="profile";
		$data['title']="User";
		$data['heading']="PROFILE";
		$data['success_massage']= flesh('success_massage');
		//$data['success_massage']=flesh('success_message');
		$user_id=$_SESSION['userdata']['user_id'];
		$data['user_id']=$user_id;

          
	         $data['privillage'] = $this->privillage;
	        
		
		if(isset($user_id) && !empty($user_id)){
		    if($_POST){
				$this->validation->validate("user_name","Name","required");
				$this->validation->validate("user_phoneno","Phone number","required|numeric");
				$this->validation->validate("user_email","Email","required|email");
				$this->validation->validate("user_username","Username","required|alpha_numeric");
				$this->validation->validate("user_gender","Gender","required");
				$this->validation->validate("user_address","Address","required");
				$this->validation->validate("user_city","City","required");
				$this->validation->validate("user_dob","Date of birth","required");
				$this->validation->validate("user_zipcode","Zipcode","required");
				if($this->validation->validation_check()== FALSE){
					$data['error_msg']=$this->validation->error_msg;
					$data['set_data']= $_POST;
					$data['set_data']['user_updated']=date('Y-m-d H:i:s');
					view_loader('admin/user/my-profile.html',$data);
				}else{
					unset($_POST['submit']);
					$data['set_data']= $_POST;
					$this->db->where('user_id', $user_id);
					$this->db->update('tabqy_user', $data['set_data']);
					$data['success_message'] = 'User updated successfully';
					$data['error']=false;
					//set_flesh('success_massage', 'User updated successfully');
					//redirect('admin/user/profile');
					echo json_encode($data);die;
				}
		}else{
			$this->db->select('tabqy_user.*');
			$this->db->from('tabqy_user');
			$this->db->where('tabqy_user.user_id',$user_id);
			$query = $this->db->get();
			$results= $this->db->result($query);
			$data['set_data']=$results[0];
			view_loader('admin/user/my-profile.html',$data);	
		}
		}
	}
	
    public function dashboard_list(){
	    $this->db->flush_cache();
		$this->db->from('tabqy_user');
		$data['get_total_users'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->where('tabqy_resturantbrand.resturantbrand_type',0);
		$this->db->from('tabqy_resturantbrand');
		$data['get_total_resturants'] = $this->db->count_all_results();
		$this->db->flush_cache();
	//	$this->db->where('tabqy_user.user_role',1);
		$this->db->from('tabqy_user');
		$data['get_total_users'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->from('tabqy_language');
		$data['get_total_languages'] = $this->db->count_all_results();
		$this->db->flush_cache();
        $this->db->select('tabqy_user.*,tabqy_department.department_name');
        $this->db->from('tabqy_user');
        $this->db->join('tabqy_department','tabqy_user.user_department_id=tabqy_department.department_id','LEFT');
        $this->db->order_by("tabqy_user.user_id", "desc");
        $this->db->limit(9,0);
        $query = $this->db->get();
        $results= $this->db->result($query);
      	$data['users']=$results;
        $this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
        $this->db->from('tabqy_resturantbrand t2');
        $this->db->join('tabqy_resturantbrand t1','t1.resturantbrand_type=t2.resturantbrand_id','RIGHT');
        $this->db->order_by("t1.resturantbrand_id", "desc");
        $this->db->limit(9,0);
        $query = $this->db->get();
        $resturants= $this->db->result($query);
        $data['resturants']=$resturants;
		echo json_encode($data);die;
	}
	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: updateStatus
	DESCRIPTION:  To update the staus value
    *************************************/
	function updateStatus($id, $value){
		$this->db->flush_cache();
		$this->db->where('user_id', $id);
		$this->db->update('tabqy_user',array('user_status'=>$value));
		$this->db->flush_cache();
		$this->db->select('user_status,user_id');
		$this->db->from('tabqy_user');
		$this->db->where('user_id',$id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	 }
	 	/*************************************
	DEVELOPER: Shivank Mittal  
	DATE: 01-05-2018
	FUNCTION: access_now
	DESCRIPTION:  fetch all restaurent
    *************************************/
	 public function access_now($restaurant_id){
	    $this->db->select('*');
	    $this->db->from('tabqy_user');
		$this->db->where('tabqy_user.user_restaurant_id',$restaurant_id);
          
		$query = $this->db->get();
		$user = $this->db->row($query);
		$userdata['user_role']=$user['user_role'];
				    $userdata['user_id']=$user['user_id'];
					$userdata['restaurant_id']=$user['user_restaurant_id'];
					$userdata['superadmin']='1';
					$userdata['url']= $_SERVER['HTTP_REFERER'];
					$_SESSION['resturant_userdata']=$userdata;
					redirect('resturant/dashboard');
	//	print_r($user);die;
	 }
	 public function restaurant_logout(){
	     $url=$_SESSION['resturant_userdata']['url'];
	    unset($_SESSION['resturant_userdata']); // will delete 
	     $url1 = str_replace(baseurl, "", $url);
	    // echo $url1;die;
	     if($url){
	       redirect($url1);  

	     }else{
	       	redirect('admin/dashboard');  
	     }
	//	print_r($user);die;
	 }
	
}

?>