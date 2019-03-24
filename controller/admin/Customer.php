<?php 
// User controller uses for superadmin,tabqy employees,customer 
class Customer extends Controller{
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
	//show list of customers .
	Public function index(){
		$tbl = "tabqy_customer";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;		
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."resturant/customer/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number	
		$search=0;
		$get_total_rows = $this->db->count_all_results('tabqy_customer');
		
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
		$this->db->select($tbl.'.*');
		$this->db->from($tbl);
		$this->db->order_by($tbl.'.customer_id','DESC');		
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();		
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="User";
		$data['heading']="Customer";
		$data['page_number']=$page_number;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
	
		view_loader('admin/customer/index.html',$data);
		print_r($data['title']);die;	
	}
	public function check_username($username){
	    $this->db->select('tabqy_customer.*');
		$this->db->from('tabqy_customer');
		$this->db->where('tabqy_customer.customer_username',$username);
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];
	
	}
	
	public function check_email($email){
	    $this->db->select('tabqy_customer.*');
		$this->db->from('tabqy_customer');
		$this->db->where('tabqy_customer.customer_email',$email);
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];
	
	}
	
//add user with roles.
	Public function add(){
		$pre = "customer";
		$this->validation->validate($pre."_name","Name","required");
		$this->validation->validate($pre."_phone","Phone number","required|numeric");
		$this->validation->validate($pre."_email","Email","required|email");
		$this->validation->validate($pre."_username","Username","required|alpha_numeric");
		$this->validation->validate($pre."_gender","Gender","required");
		$this->validation->validate($pre."_address","Address","required");
		$this->validation->validate($pre."_dob","Date of birth","required");
		$this->validation->validate($pre."_city","City","required");
		$this->validation->validate($pre."_state","State","required");
		$this->validation->validate($pre."_country","Country","required");
		$this->validation->validate($pre."_zipcode","Zipcode","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']	=	$this->validation->error_msg;
			$data['set_data']	=	$_POST;
			$data['error']	=	"true";
			echo json_encode($data);die;
		}else{
		    if(!empty($_POST[$pre.'_username']) || !empty($_POST[$pre.'_email']) ){
				$username_array= $this->check_username($_POST[$pre.'_username']);
				if($username_array){
					$data['error_msg'][$pre.'_username']="Username already exists.";
				}
				$useremail_array= $this->check_email($_POST[$pre.'_email']);
				if($useremail_array){
					$data['error_msg'][$pre.'_email']="Email already exists.";
				}
			}
		    if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{
				unset($_POST['submit']);
				unset($_POST[$pre.'_id']);
				$data['send_data']	=	$_POST;
				$username			=	$_POST[$pre.'_username'];
				$email				=	$_POST[$pre.'_email'];
				$name				=	$_POST[$pre.'_name'];
				$pass=$this->validation->random_string(7);
			    $password=$this->db->password_encrypt($pass);
				$data['send_data'][$pre.'_status'] = 1;
				$data['rest_data'][$pre.'_password'] =$password;
				//$data['send_data'][$pre.'_resturant_id'] = $restaurant_id;
				$data['send_data'][$pre.'_created'] = date('Y-m-d H:i:s');
				$this->db->insert('tabqy_'.$pre, $data['send_data']);
				
				require "core/lib/phpmailer/PHPMailerAutoload.php";
        
        $mail = new PHPMailer;
        $mail->setFrom('tabqy@askonlinetraning.com', 'TABQY');
        $mail->addAddress($email, 'Recepient Name');
        $mail->isHTML(true);
        $mail->Subject = 'Welcome To Tabqy';
        $msg_admin = "Thank you for selecting Tabqy, you have successfully 
registered with us.";
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
<b> Customer,</b></td>
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
             		<b>Resturant Username:</b> '.$username.'<br>
             		<b>Resturant Password:</b> '.$pass.'<br><br><br>
             	
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
		}else{
		    $data['error']="false";
			$data['msg']="true";
			$data['success_message']="New customer added successfully,We sent login details on your Mail";
			echo json_encode($data);die;
		}
			}
		}
    }
	
	// Delete user Module
	Public function delete($id){
			$this->db->where('customer_id', $id);
            $this->db->delete('tabqy_customer'); 
			die;
	}
	
	// Edit user Module
	Public function edit($id){	
			$pre = "customer";
			$tbl = "tabqy_customer";
				$this->validation->validate($pre."_name","Name","required");
				$this->validation->validate($pre."_password","Password","required");
				$this->validation->validate($pre."_phoneno","Phone number","required|numeric");
				$this->validation->validate($pre."_email","Email","required|email");
				$this->validation->validate($pre."_username","Username","required|alpha_numeric");
				$this->validation->validate($pre."_gender","Gender","required");
				$this->validation->validate($pre."_address","Address","required");
				$this->validation->validate($pre."_city","City","required");
				$this->validation->validate($pre."_dob","Date of birth","required");
				$this->validation->validate($pre."_zipcode","Zipcode","required");
				if($this->validation->validation_check()== FALSE){
						$data['error_msg']=$this->validation->error_msg;
						$data['set_data']= $_POST;
						$data['set_data'][$pre.'_created']=date('Y-m-d H:i:s');
						$data['set_data'][$pre.'_status']=1;
						$data['error']="true";
						echo json_encode($data);die;
				}else{
						unset($_POST['submit']);
						$data['set_data']= $_POST;
						$this->db->where($pre.'_id', $id);
						$this->db->update($tbl, $data['set_data']);
						$data['error']="false";
						$data['msg']="true";
						$data['success_message']="Customer updated successfully";
						echo json_encode($data);die;
				}
		
		
	}
			
	Public function edit_view($id){			
			$tbl = "tabqy_customer";
			$pre = "customer";
			$this->db->flush_cache();
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($pre.'_id='.$id);		
			$query = $this->db->get();
			$data['edit_view']=$this->db->result($query);
			echo json_encode($data);die;
		}
	function updateStatus($id, $value){
		$this->db->flush_cache();
		$this->db->where('customer_id', $id);
		$this->db->update('tabqy_customer',array('customer_status'=>$value));
		$this->db->flush_cache();
		$this->db->select('customer_status,customer_id');
		$this->db->from('tabqy_customer');
		$this->db->where('customer_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	 }
}

?>