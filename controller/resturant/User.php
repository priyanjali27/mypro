<?php

// User controller uses for superadmin,tabqy employees,customer 
class User extends Controller {

    var $language;
    var $session;
    var $name;
    var $departments;
    var $resturants;

    public function __construct() {
        parent::__construct();
        require "check_login.php";
        if (is_logged_in_resturant() == FALSE) {
            redirect('resturant/admin_login');
        }
        
        $this->privillage = $this->privillageRes();

    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27/04/2018
      FUNCTION:  restaurant_branch(restaurant_id)
      DESCRIPTION: View for all branches under brand_id
     * *********************************** */

    Public function restaurant_branch($restaurant_id) {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['departments'] = $this->departments;
        $data['resturantss'] = $this->resturants;
        $data['restaurant_id'] = $restaurant_id;
        $data['success_massage'] = flesh('success_massage');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "admin/user/restaurant_branch/$restaurant_id"; //URL
        $pageno = $this->url->segment(5); // Get page number
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
        $this->db->select('tabqy_user.*,tabqy_resturantbrand.resturantbrand_name,tabqy_resturantbrand.resturantbrand_type,dept.department_name');
        $this->db->from('tabqy_user');
        $this->db->join('tabqy_resturantbrand', 'tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id', 'LEFT');
        $this->db->join('tabqy_department as dept', 'tabqy_user.user_department_id=dept.department_id', 'left');
        $this->db->where('tabqy_user.user_restaurant_id', $restaurant_id);
        $this->db->where_in('tabqy_user.user_role', array(6, 4));
        $this->db->where('tabqy_user.user_role!=', 0);
        $this->db->where('tabqy_user.user_restaurant_id!=', 0);
        $this->db->order_by('tabqy_user.user_role', 'ASC');
        $search = "0";
        $this->db->limit($item_per_page, $page_position);
        $query = $this->db->get();
        $data['results'] = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('resturantbrand_type');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_id', $restaurant_id);
        $type_query = $this->db->get();
        $ress = $this->db->row($type_query);
        $data['brand_type'] = $ress['resturantbrand_type'];
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $data['action'] = "";
        $data['title'] = "User";
        $data['heading'] = "Staff";
        $data['user_id'] = "";
        $data['page_number'] = $page_number;
        $start = $page_position + 1;
        $data['start'] = $start;
        $data['end'] = ($start + count($data['results']) - 1);

        view_loader('resturant/user/user_restaurant_branch.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27/04/2018
      FUNCTION:  index()
      DESCRIPTION: View for all branches under brand_id which is login.
     * *********************************** */

    Public function index() {
        //echo "working on it";die;
        $restaurant_id = $_SESSION['resturant_userdata']['restaurant_id'];
        $data['restaurant_id'] = $restaurant_id;
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $this->db->flush_cache();
        $this->db->select('tabqy_resturantbrand.resturantbrand_id');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('tabqy_resturantbrand.resturantbrand_type', $restaurant_id);
        $this->db->or_where('tabqy_resturantbrand.resturantbrand_id', $restaurant_id);
        $query = $this->db->get();
        $resturantbrand = $this->db->result($query);
        if (!empty($resturantbrand)) {
            foreach ($resturantbrand as $brand) {
                $resturants[] = $brand['resturantbrand_id'];
            }
        }
        if (!empty($this->url->segment(6)) && $this->url->segment(6) != 0) {
            $data['sort'] = $this->url->segment(6);
        } else {
            $data['sort'] = 'user_id';
        }
        if ($this->url->segment(7) == 'ASC') {
            $data['sort_type'] = 'DESC';
        } elseif ($this->url->segment(7) == 'DESC') {
            $data['sort_type'] = 'ASC';
        } else {
            $data['sort_type'] = 'DESC';
        }

        $data['success_massage'] = flesh('success_massage');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "resturant/user/index/"; //URL
        $pageno = $this->url->segment(4); // Get page number
        $this->validation->validate("search", "Search", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
        }
        if (isset($_POST['search']) && $_POST['search'] != "") {
            $search = trim($_POST['search']);
        } elseif (!empty($this->url->segment(5))) {
            $search = str_replace("%20", ' ', ($this->url->segment(5)) ? $this->url->segment(5) : '0');
        } else {
            $search = 0;
        }

        if ($search != '0') {
            $this->db->flush_cache();
            $this->db->where("tabqy_user.user_username LIKE '%$search%'");
            $this->db->or_where("tabqy_user.user_name LIKE '%$search%'");
            $this->db->or_where("tabqy_user.user_email LIKE '%$search%'");
        }
        $this->db->from('tabqy_user');
        $this->db->join('tabqy_resturantbrand', 'tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id', 'LEFT');
        if ($resturants) {
            $this->db->where('tabqy_user.user_role', 4);
            $this->db->or_where('tabqy_user.user_role', 5);
            $this->db->where_in('tabqy_user.user_restaurant_id', $resturants);
        } else {
            $this->db->where('tabqy_user.user_role', 4);
            $this->db->or_where('tabqy_user.user_role', 4);
            $this->db->where_in('tabqy_user.user_restaurant_id', $restaurant_id);
        }

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
        $this->db->select('tabqy_user.*,tabqy_resturantbrand.resturantbrand_name');
        $this->db->from('tabqy_user');
        if ($search != '0') {
            $this->db->where("user_username LIKE '%$search%'");
            $this->db->or_where("user_name LIKE '%$search%'");
            $this->db->or_where("user_email LIKE '%$search%'");
        }
        $this->db->join('tabqy_resturantbrand', 'tabqy_user.user_restaurant_id=tabqy_resturantbrand.resturantbrand_id', 'LEFT');
        $this->db->order_by('tabqy_user.' . $data['sort'], $data['sort_type']);
        if ($resturants) {
            $this->db->where('tabqy_user.user_role', 4);
            $this->db->or_where('tabqy_user.user_role', 5);
            $this->db->where_in('tabqy_user.user_restaurant_id', $resturants);
        } else {
            $this->db->where('tabqy_user.user_role', 4);
            $this->db->or_where('tabqy_user.user_role', 4);
            $this->db->where_in('tabqy_user.user_restaurant_id', $restaurant_id);
        }
        $this->db->limit($item_per_page, $page_position);
        $query = $this->db->get();

        $data['results'] = $this->db->result($query);
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $data['action'] = "";
        $data['title'] = "Resturant";
        $data['heading'] = "ListUser";
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
        view_loader('resturant/user/user.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27-04-2018
      FUNCTION:  check_username(username)
      DESCRIPTION: check username already exist
     * *********************************** */

    public function check_username($username) {
        $this->db->select('tabqy_user.*');
        $this->db->from('tabqy_user');
        $this->db->where('tabqy_user.user_username', $username);
        $query = $this->db->get();
        $results = $this->db->result($query);
        return $results[0];
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27-04-2018
      FUNCTION:  check_email(email)
      DESCRIPTION: check email already exist
     * *********************************** */

    public function check_email($email) {
        $this->db->select('tabqy_user.*');
        $this->db->from('tabqy_user');
        $this->db->where('tabqy_user.user_email', $email);
        $query = $this->db->get();
        $results = $this->db->result($query);
        return $results[0];
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27-04-2018
      FUNCTION:  add()
      DESCRIPTION: add user with user_role_type
     * *********************************** */

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
                $username_array = $this->check_username($_POST['user_username']);
                if ($username_array) {
                    $data['error_msg']['user_username'] = "Username is already exist.";
                }
                $useremail_array = $this->check_email($_POST['user_email']);
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
                $pass = $this->validation->random_string(7);
                $pwd = $this->db->password_encrypt($pass);
                $username = $_POST['user_username'];
                $email = $_POST['user_email'];
                $name = $_POST['user_name'];
                $data['send_data']['user_password'] = $pwd;
                if ($_POST['user_restaurant_id']) {
                    $restaurant_id = $_POST['user_restaurant_id'];
                } else {
                    $restaurant_id = $_SESSION['resturant_userdata']['restaurant_id'];
                }
                if ($_POST['user_role']) {
                    $user_role = $_POST['user_role'];
                } else {
                    $user_role = 5;
                }
                $data['send_data']['user_role'] = $user_role;
                $data['send_data']['user_restaurant_id'] = $restaurant_id;
                $data['send_data']['user_created'] = date('Y-m-d H:i:s');
                $this->db->insert('tabqy_user', $data['send_data']);
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
		             		<b>Password:</b> ' . $pass . '<br><br><br>
		             	
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
        //	$this->validation->validate("user_email","Email","required|email");
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
        view_loader('resturant/user/view.html', $data);
    }

    public function change_password() {
        $id = $_SESSION['resturant_userdata']['user_id'];
        $data['session'] = $_SESSION;
        $company_id = $_SESSION['company_id'];
        $data['language'] = $this->language;
        $data['action'] = "Change";
        $data['user_id'] = "";
        $data['title'] = "User";
        $data['heading'] = "Change Password";
        $data['error_massage'] = flesh('error_massage');
        $data['privillage'] = $this->privillage;
        $data['success_massage'] = flesh('success_massage');
        $this->validation->validate("old_password", "Old Password", "required");
        $this->validation->validate("new_password", "New Password", "required");
        $this->validation->validate("confirm_password", "Confirm Password", "required");
        
        $this->db->flush_cache();
        $this->db->select('*')->from('tabqy_company')->where('company_id', $company_id);
        $cr_query = $this->db->get();
        $data['company_data'] = $this->db->row($cr_query);

        $this->db->flush_cache();
        $this->db->distinct();
        $this->db->select('co.country_code,co.country_status,l.country_language_country_name,l.country_language_language_code');
        $this->db->from('tabqy_resturantbrand as res');
        $this->db->join('tabqy_countries as co', 'res.resturantbrand_country=co.country_code', 'left');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('res.resturantbrand_company_id', $company_id);
        $this->db->where('res.resturantbrand_type', '0');
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $country_que = $this->db->get();
        $data['rel_countries'] = $this->db->result($country_que);
        
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            view_loader('resturant/user/change.html', $data);
        } else {
            if ($_POST) {

                if (!empty($_POST['old_password'])) {
                    $old = $this->db->password_encrypt($_POST['old_password']);
                    $this->db->select('tabqy_user.*');
                    $this->db->from('tabqy_user');
                    $this->db->where('tabqy_user.user_id', $id);
                    $this->db->where('tabqy_user.user_password', $old);
                    $query = $this->db->get();
                    $results = $this->db->result($query);

                    if (empty($results[0])) {

                        $data['error_msg']['old_password'] = "Old Password is not vaild.";
                        set_flesh('error_massage', $data['error_msg']['old_password']);
                        $data['set_data'] = $_POST;
                        //print_r($data);die;
                        view_loader('resturant/user/change.html', $data);
                    }
                }
                if ($_POST['new_password'] != $_POST['confirm_password']) {

                    $data['error_msg']['confirm_password'] = "Password does not matches.";
                    $data['set_data'] = $_POST;
                    view_loader('resturant/user/change.html', $data);
                } else {

                    $old = $this->db->password_encrypt($_POST['old_password']);
                    $new = $this->db->password_encrypt($_POST['new_password']);
                    $new_array['user_password'] = $new;
                    $this->db->where('user_id', $id);
                    $this->db->where('user_password', $old);
                    $this->db->update('tabqy_user', $new_array);
                    set_flesh('success_massage', 'Password changed successfully !!!');
                    redirect('resturant/user/change_password');
                }
            }
        }
    }

    Public function profile1() {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['action'] = "profile";
        $data['title'] = "User";
        $data['heading'] = "PROFILE";
        $data['success_massage'] = flesh('success_massage');
        //$data['success_massage']=flesh('success_message');
        $user_id = $_SESSION['resturant_userdata']['user_id'];
        $data['user_id'] = $user_id;


        $this->validation->validate("user_name", "Name", "required");
        $this->validation->validate("user_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("user_email", "Email", "required|email");
        $this->validation->validate("user_username", "Username", "required|alpha_numeric");
        $this->validation->validate("user_gender", "Gender", "required");
        $this->validation->validate("user_address", "Address", "required");
        $this->validation->validate("user_city", "City", "required");
        $this->validation->validate("user_dob", "Date of birth", "required");
        $this->validation->validate("user_zipcode", "Zipcode", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['set_data']['user_updated'] = date('Y-m-d H:i:s');
            $data['error'] = true;
            echo json_encode($data);
            die;
        } else {
            unset($_POST['submit']);
            $data['set_data'] = $_POST;
            $this->db->where('user_id', $user_id);
            $this->db->update('tabqy_user', $data['set_data']);
            $data['error'] = false;
            $data['success_message'] = 'Profile updated successfully';
            echo json_encode($data);
            die;
        }
    }

    Public function profile() {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['action'] = "profile";
        $data['title'] = "User";
        $data['heading'] = "PROFILE";
        $data['success_massage'] = flesh('success_massage');
        $user_id = $_SESSION['resturant_userdata']['user_id'];
        $data['user_id'] = $user_id;
        $company_id = $_SESSION['company_id'];
        
        $this->db->flush_cache();
        $this->db->select('*')->from('tabqy_company')->where('company_id',$company_id);
        $cr_query = $this->db->get();
        $data['company_data'] = $this->db->row($cr_query);            

        $this->db->flush_cache();
        $this->db->distinct();
        $this->db->select('co.country_code,co.country_status,l.country_language_country_name,l.country_language_language_code');
        $this->db->from('tabqy_resturantbrand as res');
        $this->db->join('tabqy_countries as co', 'res.resturantbrand_country=co.country_code','left');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('res.resturantbrand_company_id', $company_id);
        $this->db->where('res.resturantbrand_type', '0');
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $country_que = $this->db->get();
        $data['rel_countries'] = $this->db->result($country_que);
        
        $this->db->select('tabqy_user.*');
        $this->db->from('tabqy_user');
        $this->db->where('tabqy_user.user_id', $user_id);
        $query = $this->db->get();
        $results = $this->db->result($query);
        $data['set_data'] = $results[0];
        $data['privillage'] = $this->privillage;
        view_loader('resturant/user/my-profile.html', $data);
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

?>