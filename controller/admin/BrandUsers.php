<?php

//Controller for Brand users
class BrandUsers extends Controller {

    var $language;
    var $session;
    var $name;
    var $departments;
    var $resturants;

    public function __construct() {
        parent::__construct();

        if (is_logged_in() == FALSE) {
            redirect('admin/admin_login');
        }
        $this->db->flush_cache();
        $this->db->select('tabqy_language.language_name,tabqy_language.language_code,tabqy_language.language_flag');
        $this->db->from('tabqy_language');
        $this->db->order_by('tabqy_language.language_id', 'DESC');
        $query = $this->db->get();
        $this->language = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('tabqy_user.user_name');
        $this->db->from('tabqy_user');
        $userid = $_SESSION['userdata']['user_id'];
        $this->db->where('tabqy_user.user_id', $userid);
        $user = $this->db->get();
        $name = $this->db->result($user);
        $_SESSION['userdata']['user_name'] = $name[0]['user_name'];
        if (empty($_SESSION['lang_code'])) {
            $_SESSION['lang_code'] = "en";
            include_once "core/lang/lang_" . $_SESSION['lang_code'] . ".php";
        } else {
            include_once "core/lang/lang_" . $_SESSION['lang_code'] . ".php";
        }
        $this->db->flush_cache();
        $this->db->select('tabqy_department.department_id,tabqy_department.department_name');
        $this->db->from('tabqy_department');
        $this->db->order_by('tabqy_department.department_id', 'DESC');
        $department = $this->db->get();
        $this->departments = $this->db->result($department);
        $this->db->flush_cache();
        $this->db->select('resturantbrand_id,resturantbrand_name');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->order_by('resturantbrand_id', 'DESC');
        $query = $this->db->get();
        $this->resturants = $this->db->result($query);
    }

    /* Fetching Users for Particular Brand */

    Public function index($restaurant_id, $country_id, $company_id) {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['departments'] = $this->departments;
        $data['resturantss'] = $this->resturants;
        $data['restaurant_id'] = $restaurant_id;
        $data['country_id'] = $country_id;
        $data['company_id'] = $company_id;

        $data['success_massage'] = flesh('success_massage');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "admin/brandusers/index/"; //URL
        $pageno = $this->url->segment(7); // Get page number

        $this->db->where('tabqy_user.user_role', 1);
        $this->db->from('tabqy_user');
        $get_total_rows = $this->db->count_all_results();

        if (isset($pageno)) { //Get page number 
            $page_number = filter_var($pageno, FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
            if (!is_numeric($page_number)) {
                die('Invalid page number!');
            } //incase of invalid page number
        } else {
            $page_number = 1; //if there's no page number, set it to 1
        }
        $total_pages = ceil($get_total_rows / $item_per_page);
        $page_position = (($page_number - 1) * $item_per_page); //get starting position to fetch the records
        $this->db->flush_cache();
        $this->db->select('u.*,tabqy_resturantbrand.resturantbrand_name,dept.department_name,role.user_role_name');
        $this->db->from('tabqy_user u');

        $this->db->join('tabqy_resturantbrand', 'u.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id', 'LEFT');
        $this->db->join('tabqy_department as dept', 'u.user_department_id=dept.department_id', 'left');
        $this->db->join('tabqy_user_role as role', 'u.user_role=role.user_role_id', 'left');

        $this->db->where('u.user_restaurant_id', $restaurant_id);
        $this->db->where_in('u.user_role', array(4, 6));
        $this->db->where('u.user_role!=', 0);
        $this->db->where('u.user_company_id', $company_id);
        $this->db->where('u.user_restaurant_id!=', 0);
        $this->db->order_by('u.user_role', 'ASC');
        $search = "0";

        $this->db->limit($item_per_page, $page_position);
        $query = $this->db->get();
        $data['results'] = $this->db->result($query);
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $data['action'] = "";
        $data['title'] = "User";
        $data['heading'] = "Staff";
        $data['user_id'] = "";
        $data['page_number'] = $page_number;
        if ($search == '0') {
            $data['searched'] = "";
        } else {
            $data['searched'] = $search;
        }
        $data['search'] = $search;
        $start = $page_position + 1;
        $data['start'] = $start;
        $data['end'] = ($start + count($data['results']) - 1);
        $this->db->flush_cache();

        $this->db->select('*');

        $this->db->from('tabqy_countries');

        $this->db->order_by('country_name', 'ASC');

        $query_c = $this->db->get();

        $data['all_countries'] = $this->db->result($query_c);
        if (!empty($_SESSION['selected_country'])) {
         $country_code = $_SESSION['selected_country'];
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
          $country_code = $_SESSION['userdata']['user_default_country']; 
        } 

        $lang_code = $_SESSION['lang_code'];

        $this->db->select('b.* , bl.*');
        $this->db->from('tabqy_resturantbrand as b');
        $this->db->join('tabqy_resturantbrand_language as bl', "bl.resturantbrand_language_resturantbrand_id    =b.resturantbrand_id", "inner");
        $this->db->where('b.resturantbrand_id', $restaurant_id);
        $this->db->where('bl.resturantbrand_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['brand'] = $this->db->row($query_c);
        $cuisine_id = $data['brand']['resturantbrand_cuisine'];

        // $this->db->select('co.* , cl.*');
        // $this->db->from('tabqy_company as co');
        // $this->db->join('tabqy_company_language as cl', "cl.company_language_company_id=co.company_id", "inner");
        // $this->db->where('co.company_id', $company_id);
        // $this->db->where('cl.company_language_language_code', $_SESSION['lang_code']);
        // $query_c = $this->db->get();
        // $data['company'] = $this->db->row($query_c);

        $this->db->flush_cache();
        $this->db->select('*');
        $this->db->from('tabqy_cuisine');
        $this->db->join('tabqy_cuisine_language', "tabqy_cuisine_language.cuisine_language_cuisine_id = tabqy_cuisine.cuisine_id", 'inner');
        $this->db->where('tabqy_cuisine.cuisine_id', $cuisine_id);
        $this->db->where('tabqy_cuisine_language.cuisine_language_language_code', $_SESSION['lang_code']);
        $query = $this->db->get();
        $data['cuisine_details'] = $this->db->row($query);

        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries_language as l');
        $this->db->join('tabqy_countries as co', "co.country_id=l.country_language_country_id", "inner");
        $this->db->where('co.country_code',  $country_code);
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['country'] = $this->db->row($query_c);



        view_loader('admin/user/brand_users.html', $data);
    }

    //add user with roles.
    Public function add() {
        $this->validation->validate("user_name", "Name", "required");
        $this->validation->validate("user_password", "Password", "required");
        $this->validation->validate("user_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("user_email", "Email", "required|email");
        $this->validation->validate("user_username", "Username", "required|alpha_numeric");
        $this->validation->validate("user_gender", "Gender", "required");
        $this->validation->validate("user_address", "Address", "required");
        $this->validation->validate("user_dob", "Date of birth", "required");
        $this->validation->validate("user_city", "City", "required");
        $this->validation->validate("user_zipcode", "Zipcode", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            if (!empty($_POST['user_username']) || !empty($_POST['user_email'])) {
                $username_array = $this->already_exist('user_username', $_POST['user_username']);
                if ($username_array) {
                    $data['error_msg']['user_username'] = "Username is already exist.";
                }
                $useremail_array = $this->already_exist('user_email', $_POST['user_email']);
                if ($useremail_array) {
                    $data['error_msg']['user_email'] = "Email is already exist.";
                }
            }
            if (!empty($data['error_msg'])) {
                $data['error'] = "true";
                $data['msg'] = "false";
                echo json_encode($data);
                die;
            } else {
                unset($_POST['submit']);
                unset($_POST['user_id']);

                $data['send_data'] = $_POST;
                //echo "<pre>";print_r($_POST);
                //echo "happy"; exit;
                $role_id = array();
                $pass = $this->validation->random_string(7);
                $pwd = $this->db->password_encrypt($pass);
                $username = $_POST['user_username'];
                $email = $_POST['user_email'];
                $name = $_POST['user_name'];
                $data['send_data']['user_password'] = $pwd;
                $data['send_data']['user_created'] = date('Y-m-d H:i:s');

                $this->db->select('user_role_id')->from('tabqy_user_role')->where('user_role_slug', 'brandStaff');
                $role_query = $this->db->get();
                $role_id = $this->db->row($role_query);
                $data['send_data']['user_role'] = $role_id['user_role_id'];
                $lastinsertion = $this->db->insert('tabqy_user', $data['send_data']);
                unset($data['send_data']);
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
src="' . baseurl . 'upload/logo/07.png" alt="" width="160" height="80" draggable="true" 
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
<b>' . $name . ',</b></td>
                   </tr>
             		<tr><td style="padding:15px 16px 
13px;font-family:arial;font-size:16px;color:#333">' . $msg_admin . '</td>
                   </tr></tbody></table></td></tr>
                     <tr>
                     <td style="padding:6px 16px 
8px;font-family:arial;font-size:16px;color:#333;line-height:23px"><b>Your 
Details:-</b></td>
                   </tr>
                   <tr>
                     <td style="padding:6px 16px 
8px;font-family:arial;font-size:16px;color:#333;line-height:23px"><b>Email:</b> 
<a href="mailto:' . $email . '" target="_blank">' . $email . '</a><br>
             		<b>Username:</b> ' . $username . '<br>
             		<b>Password:</b> ' . $pwd . '<br><br><br>
             	
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



                if (!$mail->send()) {
                    $msg = "Mailer Error: " . $mail->ErrorInfo . ' but your data is saved';
                    $data['error'] = "true";
                    $data['msg'] = "true";
                    $data['success_message'] = $msg;
                    echo json_encode($data);
                    die;
                }
                $data['error'] = "false";
                $data['msg'] = "true";
                $data['success_message'] = "New User added successfully";
                echo json_encode($data);
                die;
            }
        }
    }

    // Delete user Module
    Public function delete($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->delete('tabqy_user');
        die;
    }

    // Edit user Module
    Public function edit($user_id) {

        $this->validation->validate("user_name", "Name", "required");
        $this->validation->validate("user_password", "Password", "required");
        $this->validation->validate("user_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("user_email", "Email", "required|email");
        //	$this->validation->validate("user_username","Username","required|alpha_numeric");
        $this->validation->validate("user_gender", "Gender", "required");
        $this->validation->validate("user_address", "Address", "required");
        $this->validation->validate("user_city", "City", "required");
        $this->validation->validate("user_dob", "Date of birth", "required");
        $this->validation->validate("user_zipcode", "Zipcode", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['set_data']['user_updated'] = date('Y-m-d H:i:s');
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            if (!empty($_POST['user_email'])) {
                $useremail_array = $this->already_exist('user_email', $_POST['user_email'], $user_id);
                if ($useremail_array) {
                    $data['error_msg']['user_email'] = "Email is already exist.";
                }
            }
            if (!empty($data['error_msg'])) {
                $data['error'] = "true";
                $data['msg'] = "false";
                echo json_encode($data);
                die;
            } else {
                unset($_POST['submit']);
                $data['set_data'] = $_POST;
                $this->db->where('user_id', $user_id);
                $this->db->update('tabqy_user', $data['set_data']);
                $data['error'] = "false";
                $data['msg'] = "true";
                $data['success_message'] = "User updated successfully";
                echo json_encode($data);
                die;
            }
        }
    }

    function view() {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $id = $this->url->segment(4);
        $data['title'] = "User";
        $data['heading'] = "User";
        $data['action'] = 'view';
        $data['success'] = flesh('success');
        $this->db->select('tabqy_user.*,tabqy_department.department_name');
        $this->db->from('tabqy_user');
        $this->db->join('tabqy_department', 'tabqy_user.user_department_id=tabqy_department.department_id', 'LEFT');
        $this->db->where('tabqy_user.user_id', $id);
        $query = $this->db->get();
        $results = $this->db->result($query);
        $data['user'] = $results[0];
        view_loader('admin/user/view.html', $data);
    }

    public function already_exist($field, $value, $id = '') {
        $this->db->select('*');
        $this->db->from('tabqy_user');
        $this->db->where($field, $value);
        if ($id != '') {
            $this->db->where('user_id<>', $id);
        }
        $query = $this->db->get();
        $results = $this->db->result($query);
        return $results[0];
    }

    function updateStatus($id, $value) {
        $this->db->flush_cache();
        $this->db->where('user_id', $id);
        $this->db->update('tabqy_user', array('user_status' => $value));
        $this->db->flush_cache();
        $this->db->select('user_status,user_id');
        $this->db->from('tabqy_user');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        $data['cur_status'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

}
