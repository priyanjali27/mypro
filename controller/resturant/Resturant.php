<?php

// Resturant controller 
class Resturant extends Controller {

    var $language;
    var $session;
    var $name;

    public function __construct() {
        parent::__construct();
        require "check_login.php";
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27/04/2018
      FUNCTION:  get_data()
      DESCRIPTION: Get detail data in session
     * *********************************** */

    function get_data() {

        $data['user_id'] = $_SESSION['resturant_userdata']['user_id'];
        $data['restaurant_id'] = $_SESSION['resturant_userdata']['restaurant_id'];
        $data['company_id'] = $_SESSION['resturant_userdata']['user_company_id'];
        $data['language_code'] = $_SESSION['lang_code'];
        return $data;
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27/04/2018
      FUNCTION:  index()
      DESCRIPTION: list of branches
     * *********************************** */

    Public function index($restaurant_id) {
        $sessiondata = $this->get_data();
        $company_id = $sessiondata['company_id'];
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['success_massage'] = flesh('success_message');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "resturant/resturant/index/" . $restaurant_id; //URL
        $pageno = $this->url->segment(4); // Get page number
        $this->validation->validate("search", "Search", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
        }
        $data['restaurant_id'] = $restaurant_id;
        $search = 0;
        
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
        
        $this->db->flush_cache();

        $this->db->from('tabqy_resturantbrand');
        $this->db->where('tabqy_resturantbrand.resturantbrand_type', $restaurant_id);
        $get_total_rows = $this->db->count_all_results(); //Total no. of values

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
        $lang_code = $_SESSION['lang_code'];
        $sql = 'select *, (select count(resturantbrand_type) from tabqy_resturantbrand as branch where branch.resturantbrand_type =  tabqy_resturantbrand.resturantbrand_id) as total from tabqy_resturantbrand inner join tabqy_resturantbrand_language on resturantbrand_id = resturantbrand_language_resturantbrand_id where resturantbrand_company_id ="' . $company_id . '"  and resturantbrand_type ="' . $restaurant_id . '" and resturantbrand_language_language_code="' . $lang_code . '"';

        if (!empty($_SESSION['locationdata']['location'])) {
            $sql .= " and resturantbrand_location ='" . $_SESSION['locationdata']['location'] . "'";
        } else {
            if (!empty($_SESSION['locationdata']['zone'])) {
                $sql .= " and resturantbrand_zone ='" . $_SESSION['locationdata']['zone'] . "'";
            } else {
                if (!empty($_SESSION['locationdata']['city'])) {
                    $sql .= " and resturantbrand_city ='" . $_SESSION['locationdata']['city'] . "'";
                } else {
                    if (!empty($_SESSION['locationdata']['region'])) {
                        $sql .= " and resturantbrand_region ='" . $_SESSION['locationdata']['region'] . "'";
                    } else {
                        if (!empty($_SESSION['locationdata']['country'])) {
                            $sql .= " and resturantbrand_country ='" . $_SESSION['locationdata']['country'] . "'";
                        }
                    }
                }
            }
        }
        $sql .= "order by tabqy_resturantbrand.resturantbrand_id DESC";
        $query = $this->db->query($sql);
        $data['results'] = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('resturantbrand_id,resturantbrand_name');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->order_by('resturantbrand_id', 'DESC');
        $query = $this->db->get();
        $data['resturants'] = $this->db->result($query);    /* Fetching All Countries */ $this->db->select('*');
        $this->db->from('tabqy_countries');
        $this->db->order_by('country_name', 'ASC');
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);    /* Fetching All Countries Ends... */
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $data['action'] = "";
        $data['title'] = "Resturant";
        $data['heading'] = "Resturant List";
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

        view_loader('resturant/resturant/resturant.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27/04/2018
      FUNCTION:  add()
      DESCRIPTION: add branch
     * *********************************** */

    Public function add() {
        $sessiondata = $this->get_data();
        $company_id = $sessiondata['company_id'];
        $restaurant_id = $this->url->segment(4);
        $sessiondata = $this->get_data();
        $data['session'] = $_SESSION;
        $languages = $this->language;
        $this->validation->validate("resturantbrand_name", "Name", "required");
        $this->validation->validate("resturantbrand_displayname", "Display Name", "required");
        $this->validation->validate("resturantbrand_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("resturantbrand_email", "Email", "required|email");
        $this->validation->validate("resturantbrand_countusers", "Total number of users", "required|numeric");
        $this->validation->validate("resturantbrand_address", "Address", "required");
        $this->validation->validate("resturantbrand_countpos", "Total number of POS", "required|numeric");
        $this->validation->validate("resturantbrand_country", "Country", "required");
        $this->validation->validate("resturantbrand_region", "Region", "required");
        $this->validation->validate("resturantbrand_city", "City", "required");
        $this->validation->validate("resturantbrand_zone", "Zone", "required");
        $this->validation->validate("resturantbrand_location", "Location", "required");
        $this->validation->validate("resturantbrand_file", "Logo", "required");
        $company_id = $sessiondata['company_id'];
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            unset($_POST['submit']);
            unset($_POST['resturantbrand_id']);
            $data['send_data'] = $_POST;
            $name = $_FILES['image']['name'];
            $type = $_FILES['image']['type'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $size = $_FILES['image']['size'];
            $filename = basename($_FILES['image']['name']);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $file_name = time() . "_" . $_FILES['image']['name'];
            $allowedExts = array(
                "gif",
                "jpeg",
                "jpg",
                "png"
            );

            if ((($_FILES["image"]["type"] == "image/gif") || ($_FILES["image"]["type"] == "image/jpeg") || ($_FILES["image"]["type"] == "image/jpg") || ($_FILES["image"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], "upload/logoimages/" . $file_name)) {
                    $data['send_data']['resturantbrand_file'] = $file_name;
                }
            }

            if (empty($_POST['resturantbrand_type']) && $_POST['resturantbrand_type'] == "0") {
                $data['send_data']['resturantbrand_type'] = 0;
            }
            $data['send_data']['resturantbrand_created'] = date('Y-m-d H:i:s');
            $email = $data['send_data']['resturantbrand_email'];
            $pass = $this->validation->random_string(7);
            $password = $this->db->password_encrypt($pass);
            $break_email = explode('@', $email);
            $username = $break_email[0];
            $data['error_msg'] = "";
            if (!empty($username) || !empty($email)) {
                $username_array = $this->check_username($username);
                if ($username_array) {
                    $data['error_msg']['resturantbrand_email'] = "Email is already exist.";
                }
                $useremail_array = $this->check_email($email);
                if ($useremail_array) {
                    $data['error_msg']['resturantbrand_email'] = "Email is already exist.";
                }
            }

            if (!empty($data['error_msg'])) {
                $data['error'] = "true";
                $data['msg'] = "false";
                echo json_encode($data);
                die;
            }
            $data['send_data']['resturantbrand_type'] = $restaurant_id;
            $data['send_data']['resturantbrand_company_id'] = $company_id;
            $insert_id = $this->db->insert('tabqy_resturantbrand', $data['send_data']);
            foreach ($languages as $language) {
                $edit = ($language['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                $add_data_language = array(
                    "resturantbrand_language_resturantbrand_id" => $insert_id,
                    "resturantbrand_language_language_code" => $language['language_code'],
                    "resturantbrand_language_name" => $_POST['resturantbrand_name'],
                    "resturantbrand_language_displayname" => $_POST['resturantbrand_displayname'],
                    "resturantbrand_language_address" => $_POST['resturantbrand_address'],
                    "resturantbrand_language_edit" => $edit,
                    "resturantbrand_language_status" => '1'
                );

                $this->db->insert('tabqy_resturantbrand_language', $add_data_language);
            }
            $data['rest_data']['user_password'] = $password;
            $data['rest_data']['user_role'] = "4";
            $data['rest_data']['user_email'] = $email;
            $data['rest_data']['user_username'] = $username;
            $data['rest_data']['user_phoneno'] = $data['send_data']['resturantbrand_phoneno'];
            $data['rest_data']['user_address'] = $data['send_data']['resturantbrand_address'];
            $data['rest_data']['user_restaurant_id'] = $insert_id;
            $data['rest_data']['user_company_id'] = $company_id;
            $data['rest_data']['user_created'] = date('Y-m-d H:i:s');
            $this->db->insert('tabqy_user', $data['rest_data']);
            require "core/lib/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer;
            $mail->setFrom('tabqy@askonlinetraning.com', 'TABQY');
            $mail->addAddress($email, 'Recepient Name');
            $mail->isHTML(true);
            $mail->Subject = 'Welcome To Tabqy';
            $mail->Body = $this->email_function($pass, $username, $email, $name);
            if (!$mail->send()) {
                $msg = "Mailer Error: " . $mail->ErrorInfo . ' but your data is saved';
                $data['error'] = "true";
                $data['msg'] = "true";
                $data['success_message'] = $msg;
                echo json_encode($data);
                die;
            } else {
                $data['error'] = "false";
                $data['msg'] = "true";
                $data['success_message'] = "New Resturant added successfully,We sent login details on your Mail";
                echo json_encode($data);
                die;
            }
        }
    }

    // Delete resturant Module
    Public function delete($user_id) {

        $this->db->where('resturantbrand_id', $user_id);
        $this->db->delete('tabqy_resturantbrand');
        die;
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION:  email_function(pass,username,email,name)
      DESCRIPTION: Email sent to user
     * *********************************** */

    public function email_function($pass, $username, $email, $name) {
        $data = '
			<html>
			<head>
			<meta charset="utf-8">
			<title>Test</title>
			<style>          
			.invoice-box{      
			background-color: #FFFFFF;         max-width:800px;         margin:30px 0;         padding:30px;         border:1px solid #eee;       
			box-shadow:0 0 10px rgba(0, 0, 0, .15);         font-size:16px;         line-height:24px;       
			font-family:"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;         color:#555;     }        
			.invoice-boxx{        background-image: url("http://images.alphacoders.com/458/458169.jpg");       
			max-width:800px;         margin:auto;         padding:30px;         border:1px solid #eee;        
			box-shadow:0 0 10px rgba(0, 0, 0, .15);         font-size:16px;         line-height:24px;        
			font-family:"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;         color:#f7f7f7;     }
			.btn {   background: #3cb0fd;   background-image: -webkit-linear-gradient(top, #3cb0fd, #3cb0fd); 
			background-image: -moz-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -ms-linear-gradient(top, #3cb0fd, #3cb0fd);   
			background-image: -o-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: linear-gradient(to bottom, #3cb0fd, #3cb0fd); 
			-webkit-border-radius: 4;   -moz-border-radius: 4;   border-radius: 4px;   font-family: Arial;   color: #ffffff;   font-size: 35px;   
			padding: 6px 16px 10px 20px;   text-decoration: none; } 
			.btn:hover {   background: #3cb0fd;   background-image: -webkit-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -moz-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -ms-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: -o-linear-gradient(top, #3cb0fd, #3cb0fd);   background-image: linear-gradient(to bottom, #3cb0fd, #3cb0fd);   text-decoration: none; }           .invoice-box table{         width:100%;         line-height:inherit;         text-align:left;     }          .invoice-box table td{         padding:5px;         vertical-align:top;     }          .invoice-box table tr td:nth-child(2){         text-align:;     }          .invoice-box table tr.top table td{         padding-bottom:20px;     }          .invoice-box table tr.top table td.title{         font-size:45px;         line-height:45px;         color:#333;     }          .invoice-box table tr.information table td{         padding-bottom:40px;     }          .invoice-box table tr.heading td{         background:#EEEEE0;         border-bottom:1px solid #ddd;         font-weight:bold;     }          .invoice-box table tr.details td{         padding-bottom:20px;     }          .invoice-box table tr.item td{         border-bottom:1px solid #eee;     }          .invoice-box table tr.item.last td{         border-bottom:none;     }          .invoice-box table tr.total td:nth-child(2){         border-top:2px solid #eee;         font-weight:bold;     }          @media only screen and (max-width: 600px) {         .invoice-box table tr.top table td{             width:100%;             display:block;             text-align:center;         }                  .invoice-box table tr.information table td{             width:100%;             display:block;             text-align:center;         }     }    
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


			<tr><td style="color: #333;font-size: 16px;font-weight: 600;text-align:center;">Dear ' . $name . ',</td></tr>
			<tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;">Your Details.<td> </tr>
			<tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;"><strong style="margin-right:10px;">Email:</strong><a href="mailto:' . $email . '" target="_blank">' . $email . '</a><br></span></td></tr>

			<tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;"><strong style="margin-right:10px;">Username:</strong>' . $username . '</span></td></tr>
			<tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;"><strong style="margin-right:10px;">Password:</strong>' . $pass . '</span></td></tr>

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
			</body>
			</html>';
        return $data;
    }

    // Edit resturant Module
    Public function edit($user_id) {

        $this->validation->validate("resturantbrand_name", "Name", "required");
        $this->validation->validate("resturantbrand_displayname", "Display Name", "required");
        $this->validation->validate("resturantbrand_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("resturantbrand_email", "Email", "required|email");
        $this->validation->validate("resturantbrand_countusers", "Total number of users", "required");
        $this->validation->validate("resturantbrand_address", "Address", "required");
        $this->validation->validate("resturantbrand_countpos", "Total number of POS", "required");
        $this->validation->validate("resturantbrand_country", "Country", "required");
        $this->validation->validate("resturantbrand_region", "Region", "required");
        $this->validation->validate("resturantbrand_city", "City", "required");
        $this->validation->validate("resturantbrand_zone", "Zone", "required");
        $this->validation->validate("resturantbrand_location", "Location", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['set_data']['resturantbrand_updated'] = date('Y-m-d H:i:s');
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            unset($_POST['submit']);
            $data['set_data'] = $_POST;
            unset($data['set_data']['resturantbrand_id']);
            $this->db->where('resturantbrand_id', $user_id);
            $this->db->update('tabqy_resturantbrand', $data['set_data']);
            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Resturant updated successfully";
            echo json_encode($data);
            die;
        }
    }

    function view() {
        $id = $this->url->segment(4);
        $data['title'] = "Resturant";
        $data['heading'] = "Resturant";
        $data['action'] = 'view';
        $data['success'] = flesh('success');
        $this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
        $this->db->from('tabqy_resturantbrand t2');
        $this->db->join('tabqy_resturantbrand t1', 't1.resturantbrand_type=t2.resturantbrand_id', 'RIGHT');
        $this->db->where('t1.resturantbrand_id', $id);
        $query = $this->db->get();
        $results = $this->db->result($query);
        $data['resturant'] = $results[0];

        view_loader('resturant/resturant/view.html', $data);
    }

    public function check_username($username) {
        $this->db->select('tabqy_user.*');
        $this->db->from('tabqy_user');
        $this->db->where('tabqy_user.user_username', $username);
        $query = $this->db->get();
        $results = $this->db->result($query);
        return $results[0];
    }

    public function check_email($email) {
        $this->db->select('tabqy_user.*');
        $this->db->from('tabqy_user');
        $this->db->where('tabqy_user.user_email', $email);
        $query = $this->db->get();
        $results = $this->db->result($query);
        return $results[0];
    }

    // Edit resturant Module
    Public function profile1() {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['success_massage'] = flesh('success_message');
        $data['action'] = "edit";
        $data['title'] = "User";
        $data['heading'] = "Edit Resturant Profile";
        $user_id = $_SESSION['resturant_userdata']['user_id'];
        $restaurant_id = $_SESSION['resturant_userdata']['restaurant_id'];
        $data['user_id'] = $user_id;
        $data['restaurant_id'] = $restaurant_id;
        $this->validation->validate("resturantbrand_name", "Name", "required");
        $this->validation->validate("resturantbrand_displayname", "Display Name", "required");
        $this->validation->validate("resturantbrand_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("resturantbrand_email", "Email", "required|email");
        $this->validation->validate("resturantbrand_countusers", "Total number of users", "required");
        $this->validation->validate("resturantbrand_country", "Country", "required");
        $this->validation->validate("resturantbrand_region", "Region", "required");
        $this->validation->validate("resturantbrand_city", "City", "required");
        $this->validation->validate("resturantbrand_zone", "Zone", "required");
        $this->validation->validate("resturantbrand_location", "Location", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['set_data']['resturantbrand_updated'] = date('Y-m-d H:i:s');
            $data['error'] = true;
            echo json_encode($data);
            die;
        } else {
            unset($_POST['submit']);
            $data['set_data'] = $_POST;
            unset($data['set_data']['resturantbrand_id']);
            $this->db->where('resturantbrand_id', $restaurant_id);
            $this->db->update('tabqy_resturantbrand', $data['set_data']);
            $data['error'] = false;
            $data['success_message'] = 'Resturant updated successfully';
            echo json_encode($data);
            die;
        }
    }

    Public function profile() {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['success_massage'] = flesh('success_message');
        $data['action'] = "edit";
        $data['title'] = "User";
        $data['heading'] = "Edit Resturant Profile";
        $user_id = $_SESSION['resturant_userdata']['user_id'];
        $restaurant_id = $_SESSION['resturant_userdata']['restaurant_id'];
        $data['user_id'] = $user_id;
        $data['restaurant_id'] = $restaurant_id;
        $this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
        $this->db->from('tabqy_resturantbrand as t2');
        $this->db->join('tabqy_resturantbrand t1', 't1.resturantbrand_type=t2.resturantbrand_id', 'RIGHT');

        $this->db->where('t1.resturantbrand_id', $restaurant_id);

        $query = $this->db->get();
        $results = $this->db->result($query);
        $data['set_data'] = $results[0];
        $this->db->flush_cache();      /* Fetching all countries */ $this->db->select('*');
        $this->db->from('tabqy_countries');
        $this->db->order_by('country_name', 'ASC');
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);    /* Fetching Regions of selected country */ $this->db->flush_cache();
        $this->db->select('region_name,region_code');
        $this->db->from('tabqy_regions');
        $this->db->where('country_code', $data['set_data']['resturantbrand_country']);
        $query = $this->db->get();
        $data['related_regions'] = $this->db->result($query);      /*  Fetching cities for selected region */ $this->db->flush_cache();
        $this->db->select('city_code');
        $this->db->from('tabqy_regions');
        $this->db->where('region_code', $data['set_data']['resturantbrand_region']);
        $query = $this->db->get();
        $city_str = $this->db->result($query);
        $selected_cities = explode(',', $city_str[0]['city_code']);
        foreach ($selected_cities as $city_code) {
            $this->db->flush_cache();
            $this->db->select('city_name,city_code');
            $this->db->from('tabqy_cities');
            $this->db->where('city_code', $city_code);
            $que = $this->db->get();
            $city_result = $this->db->result($que);
            $data['related_cities'][] = $city_result[0];
        } /* Fetching zone from city */ $this->db->flush_cache();
        $this->db->select('zone_name,zone_code,location_code');
        $this->db->from('tabqy_zone');
        $this->db->where('city_code', $data['set_data']['resturantbrand_city']);
        $query = $this->db->get();
        $data['related_zone'] = $this->db->result($query);      /* Fetching Locations from zone */ $this->db->flush_cache();
        $this->db->select('location_code');
        $this->db->from('tabqy_zone');
        $this->db->where('zone_code', $data['set_data']['resturantbrand_zone']);
        $que = $this->db->get();
        $location_str = $this->db->result($que);
        $explode_loc = explode(',', $location_str[0]['location_code']);
        foreach ($explode_loc as $loc_code) {
            $this->db->flush_cache();
            $this->db->select('location_name,location_code');
            $this->db->from('tabqy_locations');
            $this->db->where('location_code', $loc_code);
            $que = $this->db->get();
            $loc_result = $this->db->result($que);
            $data['related_locations'][] = $loc_result[0];
        }
        view_loader('resturant/resturant/my-profile.html', $data);
    }

    function updateStatus($id, $value) {
        $this->db->flush_cache();
        $this->db->where('resturantbrand_id', $id);
        $this->db->update('tabqy_resturantbrand', array('resturantbrand_status' => $value));
        $this->db->flush_cache();
        $this->db->select('resturantbrand_status,resturantbrand_id');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_id', $id);
        $query = $this->db->get();
        $data['cur_status'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

    function related_regions($country_code) {
        $this->db->flush_cache();
        $this->db->select('re.region_code,re.region_status,rl.region_language_region_name,rl.region_language_language_code');
        $this->db->from('tabqy_regions as re');
        $this->db->join('tabqy_regions_language as rl', "rl.region_language_region_id=re.region_id", "inner");
        $this->db->where('rl.region_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('rl.region_language_region_name', 'ASC');        
        $this->db->where('re.country_code', $country_code);
        $query = $this->db->get();
        $data['related_regions'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

    function related_cities($region_code) {
        $this->db->flush_cache();
        $this->db->select('city_code');
        $this->db->from('tabqy_regions');
        $this->db->where('region_code', $region_code);
        $query = $this->db->get();
        $city_str = $this->db->result($query);
        $selected_cities = explode(',', $city_str[0]['city_code']);
        foreach ($selected_cities as $city_code) {
            $this->db->flush_cache();            
            $this->db->select('ci.city_code,ci.city_status,cl.city_language_name,cl.city_language_language_code');
            $this->db->from('tabqy_cities as ci');
            $this->db->join('tabqy_cities_language as cl', "cl.city_language_city_id=ci.city_id", "inner");
            $this->db->where('cl.city_language_language_code', $_SESSION['lang_code']);
            $this->db->where('ci.city_code', $city_code);                   
            $this->db->order_by('cl.city_language_name', 'ASC'); 
            $que = $this->db->get();
            $data['related_cities'][] = $this->db->result($que);
        } echo json_encode($data);
        die;
    }

    function related_zone($city) {
        $this->db->flush_cache();
        $this->db->select('zo.zone_status,zo.zone_code,zo.location_code,zl.zone_language_zone_name,zl.zone_language_code');
        $this->db->from('tabqy_zone as zo');
        $this->db->join('tabqy_zone_language as zl', "zl.zone_language_zone_id=zo.zone_id", "inner");
        $this->db->where('zl.zone_language_code', $_SESSION['lang_code']);
        $this->db->where('zo.city_code', $city);
        $this->db->order_by('zl.zone_language_zone_name', 'ASC'); 
        $query = $this->db->get();
        $data['related_zone'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

    function related_locations($rel_zone) {
        $this->db->flush_cache();
        $this->db->select('location_code');
        $this->db->from('tabqy_zone');
        $this->db->where('zone_code', $rel_zone);
        $que = $this->db->get();
        $location_str = $this->db->result($que);
        $explode_loc = explode(',', $location_str[0]['location_code']);
        foreach ($explode_loc as $loc_code) {
            $this->db->flush_cache();
            $this->db->select('lo.location_status,lo.location_code,ll.location_language_location_name,ll.location_language_language_code');
            $this->db->from('tabqy_locations as lo');
            $this->db->join('tabqy_locations_language as ll', "ll.location_language_location_id=lo.location_id", "inner");
            $this->db->where('ll.location_language_language_code', $_SESSION['lang_code']);
            $this->db->where('lo.location_code', $loc_code);
            $this->db->order_by('ll.location_language_location_name', 'ASC'); 
            $que = $this->db->get();
            $data['related_locations'][] = $this->db->result($que);
        } echo json_encode($data);
        die;
    }

    function edit_view($rest_id) {
        $this->db->flush_cache();
        $this->db->select('*');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_id', $rest_id);
        $query = $this->db->get();
        $data['edit_view'] = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('region_name,region_code');
        $this->db->from('tabqy_regions');
        $this->db->where('country_code', $data['edit_view'][0]['resturantbrand_country']);
        $query = $this->db->get();
        $data['related_regions'] = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('city_code');
        $this->db->from('tabqy_regions');
        $this->db->where('region_code', $data['edit_view'][0]['resturantbrand_region']);
        $query = $this->db->get();
        $city_str = $this->db->result($query);
        $selected_cities = explode(',', $city_str[0]['city_code']);
        foreach ($selected_cities as $city_code) {
            $this->db->flush_cache();
            $this->db->select('city_name,city_code');
            $this->db->from('tabqy_cities');
            $this->db->where('city_code', $city_code);
            $que = $this->db->get();
            $data['related_cities'][] = $this->db->result($que);
        } $this->db->flush_cache();
        $this->db->select('zone_name,zone_code,location_code');
        $this->db->from('tabqy_zone');
        $this->db->where('city_code', $data['edit_view'][0]['resturantbrand_city']);
        $query = $this->db->get();
        $data['related_zone'] = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('location_code');
        $this->db->from('tabqy_zone');
        $this->db->where('zone_code', $data['edit_view'][0]['resturantbrand_zone']);
        $que = $this->db->get();
        $location_str = $this->db->result($que);
        $explode_loc = explode(',', $location_str[0]['location_code']);
        foreach ($explode_loc as $loc_code) {
            $this->db->flush_cache();
            $this->db->select('location_name,location_code');
            $this->db->from('tabqy_locations');
            $this->db->where('location_code', $loc_code);
            $que = $this->db->get();
            $data['related_locations'][] = $this->db->result($que);
        } echo json_encode($data);
        die;
    }

}

?>
		