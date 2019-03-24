<?php 
// login controller uses for superadmin,tabqy employees,customer 
class Forgot extends Controller{
	
	public function __construct()
    {
        parent::__construct();
		
	
    }
	//login user
	Public function index(){
		$this->validation->validate("user_email","Email","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			view_loader('resturant/login/forgot.html',$data);
		}else{
			
			if($_POST){
				$user_email=$_POST['user_email'];
				$email_array=$this->check_email($user_email);
				if($email_array){
				$pass=$this->validation->random_string(7);
				$name=$email_array['user_name'];
				$user_id=$email_array['user_id'];
				$username=$email_array['user_username'];
				$password=$this->db->password_encrypt($pass);
				$data['data1']['user_password']= $password;
					$this->db->where('user_id', $user_id);
					$this->db->update('tabqy_user', $data['data1']);
					require "core/lib/phpmailer/PHPMailerAutoload.php";
        
        $mail = new PHPMailer;
        $mail->setFrom('tabqy@askonlinetraning.com', 'TABQY');
        $mail->addAddress($user_email, 'Name');
        $mail->isHTML(true);
        $mail->Subject = 'Forgot Password';
       
         $mail->Body = '
						<div style="max-width:750px; margin:0 auto; color:##333; font-size:16px; font-family:open sans;">
						<table>
						<tr>
						<td style="text-align:center; padding:0px;" colspan="4">
						<div style="max-width:650px; margin:0 auto; color:##333;">
						<img src="'.baseurl.'upload/logo/07.png" alt="" width="160" height="80"/>
						</div>
						</td>
						</tr>
						<tr>
						<td colspan="4">
						<div style="max-width:750px; margin:0 auto; color:##333;">
						<h3 style="font-size:18px; margin-top:20px; margin-bottom:10px;">Forgot Password</h3>
						<p>Hi '.$name.',</p>

						<p>Thank you for your new password request. Your password has been successfully generated.</p>
							<p><strong>Your Username is</strong> : '.$username.'</p>
						<p><strong>Your new password is</strong> : '.$pass.'</p>
						<p>Questions? Suggestions? Insightful shower thoughts? Shoot us an email at tabqy@askonlinetraning.com</p>
						</div>
						</td>
						</tr>
						<tr>
						<td style="text-align:left; vertical-align:top;" colspan="4" >
							<div style="max-width:750px; margin:0 auto; color:##333;">
							<strong>Thanks & regards</strong><br/>

							Tabqy Team<br/>
							tabqy@askonlinetraning.com<br/>


							</div>
						</td>
						</tr>

						</table>
						</div>
						';
        
        
                if(!$mail->send()) 
                {
                    $msg= "Mailer Error: " . $mail->ErrorInfo;
                      set_flesh('error_message', $msg);
                			$data=array();
                			$data['error_message']= flesh('error_message');
                		    view_loader('resturant/login/forgot.html',$data);
                }else{
                		
                		
                    	set_flesh('sucess_message', 'New Password Sent to your email  successfully');
                			$data=array();
                			$data['sucess_message']= flesh('sucess_message');
                			
                		    view_loader('resturant/login/login.html',$data);
                }
			    }else{
		            set_flesh('error_message', 'Email does not exist in Tabqy Account.');
					$data=array();
					$data['error_message']= flesh('error_message');
				    view_loader('resturant/login/forgot.html',$data);	
			}
			}else{
				$data=array();
		        view_loader('resturant/login/forgot.html',$data);	
			}
		}
	}
	
		public function check_email($email){
	    $this->db->select('tabqy_user.*');
		$this->db->from('tabqy_user');
		$this->db->where('tabqy_user.user_email',$email);
    	$query = $this->db->get();
		$results= $this->db->result($query);
		return $results[0];
	
	}
}

?>