<?php 
// Email controller 
class Email extends Controller{
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
		$name=$this->db->result($user);
		$_SESSION['userdata']['user_name']=$name[0]['user_name'];
		if(empty($_SESSION['lang_code'])) {
			$_SESSION['lang_code']="en";
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		} else{
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		}
    }
	public function index(){
	require "core/lib/phpmailer/PHPMailerAutoload.php";
        
        $mail = new PHPMailer;
        $mail->setFrom('tabqy@askonlinetraning.com', 'First Last');
        $mail->addAddress('meenu@askonlinesolutions.com', 'Recepient Name');
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
src=""http://askonlinetraning.com/tabqy/upload/logo/07.png"" alt="" draggable="true" 
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
            echo "Mailer Error: " . $mail->ErrorInfo;
        } 
        else 
        {
            echo "Message has been sent successfully";
        }
}
}
?>