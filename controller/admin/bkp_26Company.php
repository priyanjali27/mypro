<?php 
class Company extends Controller{
	var $language; 
	var $session;
	var $name;
	public function __construct()
    {
        parent::__construct();
		
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
		$name = $this->db->result($user);
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
		$data['success_massage']= flesh('success_message');
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."admin/company/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number
			$this->validation->validate("search","Search","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
		}
		$this->db->flush_cache();
		$this->db->from('tabqy_company');
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
		$this->db->select('co.*, a.no_of_days,p.plan_name,p.plan_price');
		$this->db->from('tabqy_company as co');
		$this->db->join('tabqy_company_plan_assign as a', 'co.company_id=a.company_id','left');
		$this->db->join('tabqy_plan as p', 'a.plan_id=p.plan_id', 'left');
		$this->db->order_by('co.company_id','DESC');
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		//echo "<pre>";print_r($data['results']);exit;
		$search = '0';
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);

		$data['action']="";

		$data['title']="Company";

		$data['heading']="Company";

		$data['user_id']="";

		$data['page_number']=$page_number;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		view_loader('admin/company/index.html',$data);
	}
	
	public function check_existence($field,$value){
	    $this->db->select('*');
		$this->db->from('tabqy_company');
		$this->db->where($field,$value);
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];		
	}
	
	public function add(){
		$this->validation->validate("company_name","Name","required");
		$this->validation->validate("company_address","Address","required");
		$this->validation->validate("company_email","Email","required");
		$this->validation->validate("company_phone","Phone","required");
		$this->validation->validate("company_code","Company Code","required");
		$this->validation->validate("company_vat_no","Company VAT No.","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			if(!empty($_POST['company_code'])  || !empty($_POST['company_vat_no']) ){
				$duplicate_code = $this->check_existence('company_code',$_POST['company_code']);
				if($duplicate_code){
						$data['error_msg']['company_code']="Company Code already exists.";
				}
				$duplicate_vat = $this->check_existence('company_vat_no',$_POST['company_vat_no']);
				if($duplicate_vat){ 
						$data['error_msg']['company_vat_no']="VAT No. already exists.";
				}
			}
			if(!empty($data['error_msg'])){
				$data['error']="true";
			    $data['msg']="false";
				echo json_encode($data);die;
		    }else{			
					unset($_POST['submit']);
					unset($_POST['company_id']);
					$data['send_data']= $_POST;

					$email= $data['send_data']['company_email'];
					$pass=$this->validation->random_string(7);
					$password=$this->db->password_encrypt($pass);
					$break_email=explode('@',$email);
					$username=$break_email[0];
					$data['error_msg']="";
					if(!empty($email) ){ 
						$useremail_array= $this->check_email($email);
							if($useremail_array){
								$data['error_msg']['company_email']="Email is already exist.";
							}
					}
					if(!empty($data['error_msg'])){
						$data['error']="true";
						$data['msg']="false";
						echo json_encode($data);die;
					}
					$data['send_data']['company_created'] = date('Y-m-d H:i:s');
					$data['send_data']['company_status'] = 1;
					
					$insert_id = $this->db->insert('tabqy_company', $data['send_data']);
					 
					$data['user_data']['user_company_id'] = $insert_id; 
					$data['user_data']['user_password'] =$password;
					$data['user_data']['user_role'] = 2;
					$data['user_data']['user_email'] =$email;
					$data['user_data']['user_username'] =$username;
					$data['user_data']['user_phoneno'] = $data['send_data']['company_phone'];
					$data['user_data']['user_created']=date('Y-m-d H:i:s');
					$this->db->insert('tabqy_user', $data['user_data']);
					require "core/lib/phpmailer/PHPMailerAutoload.php";
				
				$mail = new PHPMailer;
				$mail->setFrom('tabqy@askonlinetraning.com', 'TABQY');
				$mail->addAddress($email, 'Recepient Name');
				$mail->isHTML(true);
				$mail->Subject = 'Welcome To Tabqy';
				$msg_admin = "Thank you for selecting Tabqy, you have successfully registered with us.";
				$mail->Body = '<div style="margin:0;padding:0">
						<div style="max-width:720px;margin:0 auto;background:#f4f4f4;height:559px">
							<div style="max-width:600px;margin:0 auto">
								<table width="100%" bgcolor="#f4f4f4" border="0" cellspacing="0" cellpadding="0">
								<tbody>
								<tr>
									<td style="padding:15px 0 0 5px"><table width="99%" border="0" cellspacing="0" cellpadding="0">
								<tbody><tr><td style="padding:0px;vertical-align:middle">Welcome To Tabqy</td>
								<td style="padding:0px;vertical-align:top"><div style="float:right">
								<img src="'.baseurl.'upload/logo/07.png" alt="" width="160" height="80" draggable="true" data-bukket-ext-bukket-draggable="true" class="CToWUd"></div>
								</td></tr>
								</tbody></table></td>
							</tr>
									   <tr><td height="15px" style="padding:0"></td></tr>
									   <tr>
										 <td bgcolor="#fff"><table width="100%" border="0" cellspacing="0" cellpadding="0">
										   <tbody><tr>
											 <td style="padding:22px 16px 7px;font-family:arial;font-size:18px;color:#333">Dear 
									<b> Customer,</b></td>
							   </tr>
								<tr><td style="padding:15px 16px 13px;font-family:arial;font-size:16px;color:#333">'.$msg_admin.'</td>
							   </tr></tbody></table></td></tr>
								 <tr>
								 <td style="padding:6px 16px 8px;font-family:arial;font-size:16px;color:#333;line-height:23px"><b>Your Details:-</b></td>
							   </tr>
							   <tr>
								 <td style="padding:6px 16px 8px;font-family:arial;font-size:16px;color:#333;line-height:23px"><b>Email:</b> 
								<a href="mailto:'.$email.'" target="_blank">'.$email.'</a><br>
								<b>Login Url:</b> '.baseurl.'resturant<br>
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
			//$data['plan_detail'] = $this->fetch_plan_details();
			$data['last_inserted_company_id'] = $insert_id;
			$data['success_message']=$msg;
			echo json_encode($data);die;
		}else{
		    $data['error']="false";
			$data['msg']="true";
			$data['last_inserted_company_id'] = $insert_id;
			$data['success_message']="New Company added successfully,We sent login details on your Mail";
			echo json_encode($data);die;
		}
		}
	}
	}
	
	function fetch_plan_details(){
		$this->db->flush_cache();
		$this->db->select('plan_id,plan_name,plan_price')->from('tabqy_plan')->order_by('plan_name','ASC');
		$query = $this->db->get();
		$all_plans = $this->db->result($query);
		$company_id = $this->url->segment(4);
		foreach($all_plans as $key=>$value)
		{			
			$this->db->select('pd.*,pm.plan_model_name');
			$this->db->from('tabqy_plan_detail as pd');
			$this->db->join('tabqy_plan_model as pm','pd.plan_detail_model_id=pm.plan_model_id','left');
			$this->db->where('pd.plan_detail_plan_id',$value['plan_id']);
			$data1 = $query1 = $this->db->get();
			$all_plan_data[] = array(
								'plan_id'=>$value['plan_id'],
								"plan"=>$value,
								"plandetail"=>$this->db->result($query1),
						);					
		}
		$data['all_plan_data'] = $all_plan_data;
		$data['company_id'] = $company_id;
		//echo "<pre>"; print_r($data);exit;	
		
		view_loader('admin/company/plan_assign.html', $data);
		
	}
	public function update($id){
		$this->validation->validate("company_name","Name","required");
		$this->validation->validate("company_address","Address","required");
		$this->validation->validate("company_email","Email","required");
		$this->validation->validate("company_phone","Phone","required");
		$this->validation->validate("company_logo","Logo","required");
		$this->validation->validate("company_code","Company Code","required");
		$this->validation->validate("company_vat_no","Company VAT No.","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			unset($_POST['company_id']);
			$data['send_data']= $_POST;


			$data['error_msg'] = "";

			if(!empty($data['error_msg'])){
				$data['error']="true";
				$data['msg']="false";
				echo json_encode($data);die;
		    }
			//$data['send_data']['company_last_updated'] = date('Y-m-d H:i:s');
			
			$this->db->where('company_id',$id);
			$this->db->update('tabqy_company', $data['send_data']);

			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Company Details Updated successfully";
			echo json_encode($data);die;
		}
	}
	
	public function view($company_id){
		$this->db->flush_cache();
		$this->db->select('*');
		$this->db->from('tabqy_company');
		$this->db->where('company_id',$company_id);
		$query = $this->db->get();
		$data['view_result'] = $this->db->result($query);
		//echo "<pre>"; print_r($data['view_result']);die;
		echo json_encode($data);die;
	}
	
	public function delete($company_id){
		$this->db->where('company_id', $company_id);
		$this->db->delete('tabqy_company'); 
		die;
	}
	
	public function check_email($email){
	    $this->db->select('tabqy_user.*');
		$this->db->from('tabqy_user');
		$this->db->where('tabqy_user.user_email',$email);
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];	
	}
	function updateStatus($id, $value){
		$this->db->flush_cache();
		$this->db->where('company_id', $id);
		$this->db->update('tabqy_company',array('company_status'=>$value));
		$this->db->flush_cache();
		$this->db->select('company_status,company_id');
		$this->db->from('tabqy_company');
		$this->db->where('company_id', $id);
		$query = $this->db->get();
		$data['cur_status'] = $this->db->result($query);
		echo json_encode($data);die;
	 }
	
	function plan_assign(){
		//$this->validation->validate("plan_id","Plan","required");
		$this->validation->validate("no_of_days","No. of days","required|number");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['submit']);
			$data['send_data']= $_POST;
			//echo "<pre>"; print_r($_POST);exit;
			$last_insertion = $this->db->insert('tabqy_company_plan_assign', $data['send_data']);
			//print_r($last_insertion);exit;
			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Plan Assigned to Company successfully";
			
			echo json_encode($data);die;
		}
	}
	
	function custom_plan(){
		$data['title']="Company";
		$data['heading']="Company";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action'] = 'edit_detail';
		$sort 			 = 'plan_detail_id';
		$plan_id = '';
		$data['plan_id'] = $plan_id;	
		$data['company_id'] = $this->url->segment(4);		

		/* Fetching Plan Details */
        $this->db->select('*');
		$this->db->from('tabqy_plan_detail');
		$this->db->where("plan_detail_plan_id",$plan_id);
		$query = $this->db->get();
		$data['plandetaildatas'] =$this->db->result($query);
		$planmodelID = array();
		$planmodelqunatity = array();
		if(!empty($data['plandetaildatas']))
		{
			foreach($data['plandetaildatas'] as $plandetaildata){
				
				$planmodelID[] = $plandetaildata['plan_detail_model_id'];
				$planmodelqunatity[] = $plandetaildata['plan_detail_quantity'];
			}
		}
		$data['planmodelID'] = $planmodelID;	
		$data['planmodelqunatity'] = $planmodelqunatity;
		
		/* Fetching All Models */
		$this->db->select('m.*');
		$this->db->from('tabqy_plan_model as m');
		$query = $this->db->get();
		$data['model'] = $this->db->result($query);
		if(isset($_POST['update']) && !empty($_POST)){		
			$this->db->where("plan_detail_plan_id",$plan_id);
			$this->db->delete('tabqy_plan_detail');
			
			$quantity = $_POST['quantity'];
			$plan      = $_POST['plan'];
			$plan_model  =$_POST['plan_model'];			 

			foreach ($plan_model as $key => $value) {
				 $add_data = array(
						'plan_detail_plan_id'       =>$plan,
						'plan_detail_model_id'      =>$value,
						'plan_detail_quantity'      =>$quantity[$value],
				 );
				
				$this->db->where("plan_detail_plan_id",$plan_id);
				$edit = $this->db->insert('tabqy_plan_detail',$add_data);
			}
		if($edit){					
				redirect('admin/company');					  
				}
		}else{
			view_loader('admin/company/custom_plan.html',$data);
		}
		
	}
	
	function add_custom_plan(){
		$company_id = $this->url->segment(4);
		$this->validation->validate("plan_name"," Plan name","required");
		$this->validation->validate("plan_price"," Plan price","required");
		
		if($this->validation->validation_check()== FALSE){ 
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			unset($_POST['update']);
			unset($_POST['company_id']);
		$data['send_data']= $_POST;
		//echo "<pre>";print_r($_POST);exit;
		//if(isset($_POST['update']) && !empty($_POST)){
		$add_data = array(
					'plan_name'        =>$_POST['plan_name'],
					'plan_price'       =>$_POST['plan_price'],
					'plan_description' =>$_POST['plan_description'],
					'plan_status'	   => '1',
				);
		$data['add_plan_data']= $add_data;
		//echo "<pre>";print_r($data['add_plan_data']);exit;
		$last_insertid = $this->db->insert("tabqy_plan",$data['add_plan_data']);			

			$quantity = $_POST['quantity'];
			$plan      = $last_insertid;
			$plan_model  =$_POST['plan_model'];			 

			foreach ($plan_model as $key => $value) {
				 $add_data = array(
						'plan_detail_plan_id'       =>$plan,
						'plan_detail_model_id'      =>$value,
						'plan_detail_quantity'      =>$quantity[$value],						
				 );
				
				$this->db->where("plan_detail_plan_id",$last_insertid);
				$edit = $this->db->insert('tabqy_plan_detail',$add_data);
			}
			$plan_assigndata = array(
					'company_id'       => $company_id,
					'plan_id'       => $last_insertid,
					'no_of_days' => $_POST['no_of_days'],					
				);
			$ca = $this->db->insert('tabqy_company_plan_assign',$plan_assigndata);
			if($ca){					
				redirect('admin/company');					  
				}
		//}	
		}	
	}
}
?>