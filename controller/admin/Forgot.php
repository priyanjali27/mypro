<?php 
// login controller uses for superadmin,tabqy employees,customer 
class Forgot extends Controller{
	
	public function __construct()
    {
        parent::__construct();
		
		if (is_logged_in()==true)
        {
            redirect('admin/admin_login');
        }
    }
	//login user
	Public function index(){
		$this->validation->validate("user_email","Email","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			view_loader('admin/login/forgot.html',$data);
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
       $mail->Body = '<html>
		<head>
			<meta charset="utf-8">
				<title>Test</title>
				<style> 	     
					  .invoice-box{ 	 
					  background-color: #FFFFFF;         max-width:800px;         margin:30px 0;         padding:30px;         border:1px solid #eee;       
					  box-shadow:0 0 10px rgba(0, 0, 0, .15);         font-size:16px;         line-height:24px;       
					  font-family:"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;         color:#555;     } 	 	
					  .invoice-boxx{ 	    background-image: url("http://images.alphacoders.com/458/458169.jpg");       
					  max-width:800px;         margin:auto;         padding:30px;         border:1px solid #eee;        
					  box-shadow:0 0 10px rgba(0, 0, 0, .15);         font-size:16px;         line-height:24px;        
					  font-family:"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;         color:#f7f7f7;     }
					  .btn {   background: #3cb0fd;   background-image: -webkit-linear-gradient(top, #3cb0fd, #3cb0fd); 
					  background-image: -moz-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -ms-linear-gradient(top, #3cb0fd, #3cb0fd);   
					  background-image: -o-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: linear-gradient(to bottom, #3cb0fd, #3cb0fd); 
					  -webkit-border-radius: 4;   -moz-border-radius: 4;   border-radius: 4px;   font-family: Arial;   color: #ffffff;   font-size: 35px;   
					  padding: 6px 16px 10px 20px;   text-decoration: none; } 
					  .btn:hover {   background: #3cb0fd;   background-image: -webkit-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -moz-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -ms-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -o-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: linear-gradient(to bottom, #3cb0fd, #3cb0fd);   text-decoration: none; }  	     .invoice-box table{         width:100%;         line-height:inherit;         text-align:left;     }          .invoice-box table td{         padding:5px;         vertical-align:top;     }          .invoice-box table tr td:nth-child(2){         text-align:;     }          .invoice-box table tr.top table td{         padding-bottom:20px;     }          .invoice-box table tr.top table td.title{         font-size:45px;         line-height:45px;         color:#333;     }          .invoice-box table tr.information table td{         padding-bottom:40px;     }          .invoice-box table tr.heading td{         background:#EEEEE0;         border-bottom:1px solid #ddd;         font-weight:bold;     }          .invoice-box table tr.details td{         padding-bottom:20px;     }          .invoice-box table tr.item td{         border-bottom:1px solid #eee;     }          .invoice-box table tr.item.last td{         border-bottom:none;     }          .invoice-box table tr.total td:nth-child(2){         border-top:2px solid #eee;         font-weight:bold;     }          @media only screen and (max-width: 600px) {         .invoice-box table tr.top table td{             width:100%;             display:block;             text-align:center;         }                  .invoice-box table tr.information table td{             width:100%;             display:block;             text-align:center;         }     }    
					#customers {
					    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
					    border-collapse: collapse;
					    width: 100%;
						margin-top:20px;
					}

					#customers td, #customers th {
					    border: 1px solid #ddd;
					    padding: 8px;
						text-align:center;
					}

					#customers tr:nth-child(even){background-color: #f2f2f2;}

					#customers tr:hover {background-color: #ddd;}

					#customers th {
					    padding-top: 12px;
					    padding-bottom: 12px;
					    text-align: left;
					    background-color: #4CAF50;
					    color: white;
					}
            </style>
			</head>
			<body>
				<div class="invoice-boxx">
					<h1 align = "center"><img src="http://askonlinetraning.com/tabqy1/assets/images/logo.png" class="" style="width:200px;" alt=""></h1>
					<div class="invoice-box">
						<table cellpadding="0" cellspacing="0">
							<table> 
									<tr><td style="color: #333;font-size: 18px;font-weight: 600;text-align:center;">
										Dear  '.ucfirst($name).',</td></tr>
										<tr><td style="color: #777;font-size:16px;font-weight: 600;text-align:center;">There was request to change your password.</td> </tr>
									<tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;">Thank you for your new password request. Your password has been successfully generated.</td> 
									</tr>
									<tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;"><br></td> 
									</tr>
									<tr><td style="color: #333;font-size:14px;font-weight: 600;text-align:center;">	
									                    <p><strong>Your Username is</strong> : '.$username.'</p>
								                        <p><strong>Your new password is</strong> : '.$pass.'</p></td> </tr>
							</table>
																
															</div>
														</hr>
														<div>
														</hr>
													</div>
													<div class="invoice-box">
														<h3 style="text-align:center;">THANKS & REGARDS</h3>			
														<p style="text-align:center;">Tabqy Team</p>
														<p style="text-align:center;"><a href="mailto:support@laundry.com">support@tabqy.com</a></p>
														</div>
														</table>
																		</body>
																			</html>';
        
        
                if(!$mail->send()) 
                {
                    $msg= "Mailer Error: " . $mail->ErrorInfo;
                      set_flesh('error_message', $msg);
                			$data=array();
                			$data['error_message']= flesh('error_message');
                		    view_loader('admin/login/forgot.html',$data);
                }else{
                		
                		
                    	set_flesh('sucess_message', 'New Password Sent to your email  successfully');
                			$data=array();
                			$data['sucess_message']= flesh('sucess_message');
                			
                		    view_loader('admin/login/login.html',$data);
                }
			    }else{
		            set_flesh('error_message', 'Email does not exist in Tabqy Account.');
					$data=array();
					$data['error_message']= flesh('error_message');
				    view_loader('admin/login/forgot.html',$data);	
			}
			}else{
				$data=array();
		        view_loader('admin/login/forgot.html',$data);	
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