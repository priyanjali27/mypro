<?php

class Company extends Controller {

    var $language;
    var $session;
    var $name;

    public function __construct() {
        parent::__construct();

        if (is_logged_in() == FALSE) {
            redirect('admin/dashboard');
        }
        ///checking privilege and validating user
        $this->privillage = $this->privillage();
        ///print_r($this->privillage);

        if ($_SESSION['userdata']['user_role'] != 0) {
            if (empty($this->privillage['add']) && empty($this->privillage['edit']) && empty($this->privillage['delete'])) {
                redirect('admin/dashboard');
            } else {

                if ($this->privillage['companystatus'] == true) {
                    $_SESSION['companyid'] = $this->privillage['companyid'];
                }
                  if ($this->privillage['locationstatus'] == true) {
                    $_SESSION['locationcode'] = $this->privillage['locationcode'];
                }
            }
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
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      FUNCTION:  index()
      DESCRIPTION: Shows the list of all companies
     * *********************************** */

    public function index() {
        $data['session'] = $_SESSION;


        $data['language'] = $this->language;
        $data['success_massage'] = flesh('success_message');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "admin/company/index/"; //URL
        $pageno = $this->url->segment(4); // Get page number

        $this->validation->validate("search", "Search", "required");

        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
        }
        $this->db->flush_cache();

        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);

        $this->db->order_by('co.country_name', 'ASC');

        $query_c = $this->db->get();

        $data['related_country'] = $this->db->result($query_c);

        $this->db->flush_cache();
        $this->db->from('tabqy_company');
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
        $this->db->select('co.* , cl.*, a.no_of_days,p.plan_name,p.plan_price');
        $this->db->from('tabqy_company as co');
        $this->db->join('tabqy_company_plan_assign as a', 'co.company_id=a.company_id', 'left');
        $this->db->join('tabqy_plan as p', 'a.plan_id=p.plan_id', 'left');
        $this->db->join('tabqy_company_language as cl', "cl.company_language_company_id=co.company_id", "inner");
         $this->db->where('cl.company_language_language_code', $_SESSION['lang_code']);

        if (!empty($_SESSION['selected_country'])) {
            $this->db->where('co.company_country', $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
            $this->db->where('co.company_country', $_SESSION['userdata']['user_default_country']);
        }


        ///$this->db->join('tabqy_user_access as ua', "FIND_IN_SET(co.company_id, ua.companyid)" ,'left');
        //$this->db->where('ua.user_id', $_SESSION['userdata']['user_id']);
        if ($_SESSION['userdata']['user_role'] != 0 && $this->privillage['companystatus'] == true) {
            $this->db->where_in('co.company_id', $_SESSION['companyid']);
        }
        $this->db->order_by('co.company_id', 'ASC');
        $query = $this->db->get();
        $data['results'] = $this->db->result($query);
        //echo "<pre>";print_r($data['results']);exit;
       // echo $this->db->last_query();


        $search = '0';
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);

        $data['action'] = "";

        $data['title'] = "Company";

        $data['heading'] = "Company";

        $data['user_id'] = "";

        $data['page_number'] = $page_number;
        $start = $page_position + 1;
        $data['start'] = $start;
        $data['end'] = ($start + count($data['results']) - 1);

        // $this->db->flush_cache();
        // $this->db->select('*');
        // $this->db->from('tabqy_countries');
        // $que = $this->db->get();
        // $data['all_countries'] =$this->db->result($que);

        $this->db->flush_cache();

        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);

        $this->db->order_by('co.country_name', 'ASC');

        $query_c = $this->db->get();

        $data['all_countries'] = $this->db->result($query_c);
         $data['privillage'] = $this->privillage;
      if ($this->privillage['locationstatus'] == true) {
          redirect('admin/branchesaccess');
      }         
        view_loader('admin/company/index.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      FUNCTION:  check_existence()
      PARAMETER: 	field name, value to be checked
      DESCRIPTION: Used to check the value of particular field already exists
     * *********************************** */

    public function check_existence($field, $value) {
        $this->db->select('*');
        $this->db->from('tabqy_company');
        $this->db->where($field, $value);
        $query = $this->db->get();
        $results = $this->db->result($query);
        return $results[0];
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      FUNCTION:  add()
      DESCRIPTION: Used to add company
     * *********************************** */

    public function add() {
        //privillage checking and user validating 
        //print_r($_POST);
        if ($_SESSION['userdata']['user_role'] != 0) {

            if ($this->privillage['add'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        //privillage checking and user validating  end here.

        $data['session'] = $_SESSION;
        $languages = $this->language;

        $this->validation->validate("company_name", "Name", "required");
        // $this->validation->validate("company_address", "Address", "required");
        $this->validation->validate("company_email", "Email", "required");
        $this->validation->validate("company_phone", "Phone", "required");
        $this->validation->validate("company_code", "Company Code", "required");
        $this->validation->validate("company_vat_no", "Company VAT No.", "required");
        //$this->validation->validate("company_cr_no","Company CR No.","required");

        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            if (!empty($_POST['company_code']) || !empty($_POST['company_vat_no'])) {
                $duplicate_code = $this->check_existence('company_code', $_POST['company_code']);
                if ($duplicate_code) {
                    $data['error_msg']['company_code'] = "Company Code already exists.";
                }
                $duplicate_vat = $this->check_existence('company_vat_no', $_POST['company_vat_no']);
                if ($duplicate_vat) {
                    $data['error_msg']['company_vat_no'] = "VAT No. already exists.";
                }
            }
            if (!empty($data['error_msg'])) {
                $data['error'] = "true";
                $data['msg'] = "false";
                echo json_encode($data);
                die;
            } else {
                unset($_POST['submit']);
                unset($_POST['company_id']);
                $data['send_data'] = $_POST;

                $email = $data['send_data']['company_email'];
                $pass = $_POST['company_password'];
                $password = $this->db->password_encrypt($pass);
                $data['send_data']['company_password'] = $password;
                $break_email = explode('@', $email);
                $username = $break_email[0];
                $data['error_msg'] = "";
                if (!empty($email)) {
                    $useremail_array = $this->check_email($email);
                    if ($useremail_array) {
                        $data['error_msg']['company_email'] = "Email is already exist.";
                    }
                }
                if (!empty($data['error_msg'])) {
                    $data['error'] = "true";
                    $data['msg'] = "false";
                    echo json_encode($data);
                    die;
                }
                $data['send_data']['company_created'] = date('Y-m-d H:i:s');
                $data['send_data']['company_status'] = 1;

                $insert_id = $this->db->insert('tabqy_company', $data['send_data']);

                foreach ($languages as $language) {
                    $edit = ($language['language_code'] == $_SESSION['lang_code']) ? '1' : '0';
                    $add_data_language = array(
                        "company_language_company_id" => $insert_id,
                        "company_language_language_code" => $language['language_code'],
                        "company_language_name" => $_POST['company_name'],
                        "company_language_address" => $_POST['company_address'],
                        "company_language_edit" => $edit,
                        "company_language_status" => '1'
                    );

                    $this->db->insert('tabqy_company_language', $add_data_language);
                }

                $data['user_data']['user_company_id'] = $insert_id;
                $data['user_data']['user_password'] = $password;
                $data['user_data']['user_role'] = 2;
                $data['user_data']['user_email'] = $email;
                $data['user_data']['user_username'] = $username;
                $data['user_data']['user_phoneno'] = $data['send_data']['company_phone'];
                $data['user_data']['user_created'] = date('Y-m-d H:i:s');
                $this->db->insert('tabqy_user', $data['user_data']);
                require "core/lib/phpmailer/PHPMailerAutoload.php";

                $mail = new PHPMailer;
                $mail->setFrom('tabqy@askonlinetraning.com', 'TABQY');
                $mail->addAddress($email, 'Recepient Name');
                $mail->isHTML(true);
                $mail->Subject = 'Welcome To Tabqy';
                $msg_admin = "Thank you for selecting Tabqy, you have successfully registered with us.";
                $name = '';
                $mail->Body = ' <html>
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


                if (!$mail->send()) {
                    $msg = "Mailer Error: " . $mail->ErrorInfo . ' but your data is saved';
                    $data['error'] = "true";
                    $data['msg'] = "true";
                    //$data['plan_detail'] = $this->fetch_plan_details();
                    $data['last_inserted_company_id'] = $insert_id;
                    $data['success_message'] = $msg;
                    echo json_encode($data);
                    die;
                } else {
                    $data['error'] = "false";
                    $data['msg'] = "true";
                    $data['last_inserted_company_id'] = $insert_id;
                    $data['success_message'] = "New Company added successfully,We sent login details on your Mail";
                    echo json_encode($data);
                    die;
                }
            }
        }
    }

    /************************************
      DEVELOPER: Shivank
      DATE:  25-05-2018
      FUNCTION:  fetch_plan_details()
      DESCRIPTION: Used to get all plans with their details
     * *********************************** */

    function fetch_plan_details() {
        $company_id = $this->url->segment(4);
        $_SESSION['company_id'] = $company_id;
        $data['session'] = $_SESSION;
        $data['title'] = "Plan";
        $data['language'] = $this->language;

        $this->db->flush_cache();
        $this->db->select('plan_id,plan_name,plan_price')->from('tabqy_plan')->order_by('plan_name', 'ASC');
        $query = $this->db->get();
        $all_plans = $this->db->result($query);
        //echo"<pre>"; print_r($all_plans);die();

        foreach ($all_plans as $key => $value) {
            $this->db->select('pd.*,pm.plan_model_name');
            $this->db->from('tabqy_plan_detail as pd');
            $this->db->join('tabqy_plan_model as pm', 'pd.plan_detail_model_id=pm.plan_model_id', 'left');
            $this->db->where('pd.plan_detail_plan_id', $value['plan_id']);
            $data1 = $query1 = $this->db->get();
            $all_plan_data[] = array(
                'plan_id' => $value['plan_id'],
                "plan" => $value,
                "plandetail" => $this->db->result($query1),
            );
        }
        $data['all_plan_data'] = $all_plan_data;
        $data['company_id'] = $company_id;
        //echo "<pre>"; print_r($data);exit;	
        $this->db->flush_cache();
        $this->db->select('a.*, p.*');
        $this->db->from('tabqy_company_plan_assign a');
        $this->db->join('tabqy_plan as p', 'a.plan_id=p.plan_id', 'left');
        $this->db->where('company_id', $company_id);
        $query = $this->db->get();
        $data['plan_count'] = $query->rowCount();
       // print_r($data['plan_count']);die();
        $selected_plans = $this->db->result($query);
          // print_r($selected_plans);die();
        if (!empty($selected_plans)) {

            foreach ($selected_plans as $key => $value) {
                $this->db->select('pd.*,pm.plan_model_name');
                $this->db->from('tabqy_plan_detail as pd');
                $this->db->join('tabqy_plan_model as pm', 'pd.plan_detail_model_id=pm.plan_model_id', 'left');
                $this->db->where('pd.plan_detail_plan_id', $value['plan_id']);
                $data1 = $query1 = $this->db->get();
                $selected_plan_data[] = array(
                    'plan_id' => $value['plan_id'],
                    "plan" => $value,
                    "plandetail" => $this->db->result($query1),
                );
            }

            $data['selected_plan_data'] = $selected_plan_data;
            foreach ($selected_plan_data as $key => $plan_data){
              $plan_model_id = $plan_data['plandetail'];
          }
            foreach ($plan_model_id as $key => $plan_mod){
              $plan_model_d_id[] = $plan_mod['plan_detail_model_id'];
          }

        
        //echo "<pre>"; print_r($plan_model_d_id);exit;
        $this->db->flush_cache();
        $this->db->select('plan_model_id');
        $this->db->from('tabqy_plan_model');
        $this->db->where('plan_model_status','1');

        $query_c = $this->db->get();
        $all_models = $this->db->result($query_c);
          //echo "<pre>"; print_r($all_models);exit;
        foreach($all_models as $all){
            $new_all[] = $all['plan_model_id'];
        }

        $plan_diff =  array_diff($new_all,$plan_model_d_id);
    
        foreach($plan_diff as $key=>$value){
                $this->db->select('*');
                $this->db->from('tabqy_plan_model');
                $this->db->where('plan_model_id',$value);
                $diff_query = $this->db->get();
                $data['diff_result'][] = $this->db->result($diff_query);
            }
      }
        // echo "<pre>"; print_r($data['$diff_result']);die();
        $this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('co.country_name', 'ASC');
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);

        $this->db->select('co.* , cl.*');
        $this->db->from('tabqy_company as co');
        $this->db->join('tabqy_company_language as cl', "cl.company_language_company_id=co.company_id", "inner");
        $this->db->where('co.company_id', $_SESSION['company_id']);
        $this->db->where('cl.company_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['company'] = $this->db->row($query_c);
        // echo"<pre>"; print_r($data['company']);die();
        
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries_language as l');
        $this->db->join('tabqy_countries as co', "co.country_id=l.country_language_country_id", "inner");
         
       if (!empty($_SESSION['selected_country'])) {
        $this->db->where('co.country_code',  $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
           $this->db->where('co.country_code',  $_SESSION['userdata']['user_default_country']); 
        }
 
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['country'] = $this->db->row($query_c);

        view_loader('admin/company/plan_assign.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      PARAMETER: Company id
      FUNCTION:  update()
      DESCRIPTION: Used to edit/update company details
     * *********************************** */

    public function update($id) {
        //privillage checking and user validating 
        if ($_SESSION['userdata']['user_role'] != 0) {

            if ($this->privillage['edit'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        //privillage checking and user validating  end here.

        $this->validation->validate("company_name", "Name", "required");
        //$this->validation->validate("company_address", "Address", "required");
        $this->validation->validate("company_email", "Email", "required");
        $this->validation->validate("company_phone", "Phone", "required");
        $this->validation->validate("company_logo", "Logo", "required");
        $this->validation->validate("company_code", "Company Code", "required");
        //$this->validation->validate("company_vat_no", "Company VAT No.", "required");

        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            $language_code = $_POST['language_code'];
            $company_name = $_POST['company_name'];
            $address = $_POST['company_address'];

            unset($_POST['submit']);
            unset($_POST['language_code']);

            if ($_SESSION['lang_code'] != $language_code) {
                unset($_POST['company_name']);
                unset($_POST['company_address']);
            }
            $data['send_data'] = $_POST;
            unset($data['send_data']['company_id']);

            $data['error_msg'] = "";

            if (!empty($data['error_msg'])) {
                $data['error'] = "true";
                $data['msg'] = "false";
                echo json_encode($data);
                die;
            }
            //$data['send_data']['company_last_updated'] = date('Y-m-d H:i:s');

            $this->db->where('company_id', $id);
            $this->db->update('tabqy_company', $data['send_data']);

            $edit_data_language = array(
                "company_language_name" => $company_name,
                "company_language_address" => $address,
                "company_language_edit" => '1'
            );

            $this->db->where('company_language_company_id', $id);
            $this->db->where('company_language_language_code', $language_code);
            $this->db->update('tabqy_company_language', $edit_data_language);

            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Company Details Updated successfully";
            echo json_encode($data);
            die;
        }
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      PARAMETER: company id
      FUNCTION:  view()
      DESCRIPTION: Used to get data for view action
     * *********************************** */

    public function view($company_id) {
        $language_code = $this->url->segment(5);
        $this->db->flush_cache();
        $this->db->select('co.*,la.*');
        $this->db->from('tabqy_company as co');
        $this->db->join('tabqy_company_language as la', 'co.company_id=la.company_language_company_id', 'inner');
        $this->db->where('co.company_id', $company_id);
        $this->db->where('la.company_language_language_code', $language_code);
        $query = $this->db->get();
        $data['view_result'] = $this->db->result($query);
        //echo "<pre>"; print_r($query);die;
        echo json_encode($data);
        die;
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      PARAMETER: company id
      FUNCTION:  delete()
      DESCRIPTION: Used to delete company
     * *********************************** */

    public function delete($company_id) {
        //privillage checking and user validating 
        if ($_SESSION['userdata']['user_role'] != 0) {

            if ($this->privillage['delete'] != 1) {
                $data['error'] = "true";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        //privillage checking and user validating  end here.
        $this->db->where('company_id', $company_id);
        $this->db->delete('tabqy_company');

        $this->db->where('company_language_company_id', $company_id);
        $this->db->delete('tabqy_company_language');
        $data['error'] = "false";
        $data['msg'] = "false";
        $data['success_message'] = "Company successfully deleted";
        echo json_encode($data);
        exit();
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      PARAMETER: email
      FUNCTION:  check_mail()
      DESCRIPTION: Used to check if email already exist
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
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      FUNCTION:  updateStatus()
      DESCRIPTION: Used to change status of company
     * *********************************** */

    function updateStatus($id, $value) {
        $this->db->flush_cache();
        $this->db->where('company_id', $id);
        $this->db->update('tabqy_company', array('company_status' => $value));
        $this->db->flush_cache();
        $this->db->select('company_status,company_id');
        $this->db->from('tabqy_company');
        $this->db->where('company_id', $id);
        $query = $this->db->get();
        $data['cur_status'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      FUNCTION:  plan_assign()
      DESCRIPTION: Used to assign plan to company
     * *********************************** */

    function plan_assign() {
        //$this->validation->validate("plan_id","Plan","required");
        $this->validation->validate("no_of_days", "No. of days", "required|number");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            unset($_POST['submit']);
            $data['send_data'] = $_POST;
            //echo "<pre>"; print_r($_POST);exit;
            $last_insertion = $this->db->insert('tabqy_company_plan_assign', $data['send_data']);
            //print_r($last_insertion);exit;
            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Plan Assigned to Company successfully";

            echo json_encode($data);
            die;
        }
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      FUNCTION:  custom_plan()
      DESCRIPTION: Used to get all models and redirect to custom plan page
     * *********************************** */

    function custom_plan() {
        $data['title'] = "Company";
        $data['heading'] = "Company";
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['action'] = 'edit_detail';
        $sort = 'plan_detail_id';
        $plan_id = '';
        $data['plan_id'] = $plan_id;
        $data['company_id'] = $this->url->segment(4);

        /* Fetching Plan Details */
        $this->db->select('*');
        $this->db->from('tabqy_plan_detail');
        $this->db->where("plan_detail_plan_id", $plan_id);
        $query = $this->db->get();
        $data['plandetaildatas'] = $this->db->result($query);
        $planmodelID = array();
        $planmodelqunatity = array();
        if (!empty($data['plandetaildatas'])) {
            foreach ($data['plandetaildatas'] as $plandetaildata) {

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
        if (isset($_POST['update']) && !empty($_POST)) {
            $this->db->where("plan_detail_plan_id", $plan_id);
            $this->db->delete('tabqy_plan_detail');

            $quantity = $_POST['quantity'];
            $plan = $_POST['plan'];
            $plan_model = $_POST['plan_model'];

            foreach ($plan_model as $key => $value) {
                $add_data = array(
                    'plan_detail_plan_id' => $plan,
                    'plan_detail_model_id' => $value,
                    'plan_detail_quantity' => $quantity[$value],
                );

                $this->db->where("plan_detail_plan_id", $plan_id);
                $edit = $this->db->insert('tabqy_plan_detail', $add_data);
            }
            if ($edit) {
                redirect('admin/company');
            }
        } else {
            view_loader('admin/company/custom_plan.html', $data);
        }
    }

    /*     * ***********************************
      DEVELOPER: Priyanjali
      DATE:  01-05-2018
      FUNCTION:  add_custom_plan()
      DESCRIPTION: Used to add custom plan and assign to company
     * *********************************** */

    function add_custom_plan() {
        $company_id = $this->url->segment(4);
        $this->validation->validate("plan_name", " Plan name", "required");
        $this->validation->validate("plan_price", " Plan price", "required");

        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            unset($_POST['update']);
            unset($_POST['company_id']);
            $data['send_data'] = $_POST;
            //echo "<pre>";print_r($_POST);exit;
            //if(isset($_POST['update']) && !empty($_POST)){
            $add_data = array(
                'plan_name' => $_POST['plan_name'],
                'plan_price' => $_POST['plan_price'],
                'plan_description' => $_POST['plan_description'],
                'plan_status' => '1',
            );
            $data['add_plan_data'] = $add_data;
            //echo "<pre>";print_r($data['add_plan_data']);exit;
            $last_insertid = $this->db->insert("tabqy_plan", $data['add_plan_data']);

            $quantity = $_POST['quantity'];
            $plan = $last_insertid;
            $plan_model = $_POST['plan_model'];

            foreach ($plan_model as $key => $value) {
                $add_data = array(
                    'plan_detail_plan_id' => $plan,
                    'plan_detail_model_id' => $value,
                    'plan_detail_quantity' => $quantity[$value],
                );

                $this->db->where("plan_detail_plan_id", $last_insertid);
                $edit = $this->db->insert('tabqy_plan_detail', $add_data);
            }
            $plan_assigndata = array(
                'company_id' => $company_id,
                'plan_id' => $last_insertid,
                'no_of_days' => $_POST['no_of_days'],
            );
            $ca = $this->db->insert('tabqy_company_plan_assign', $plan_assigndata);
            if ($ca) {
                redirect('admin/company');
            }
            //}	
        }
    }

    Public function change_country() {
        $last_url = "admin/company";
        $country_code = $this->url->segment(4);
        unset($_SESSION['userdata']['user_default_country']);

        if ($country_code)
            $_SESSION['selected_country'] = $country_code;
        redirect($last_url);
    }

    Public function change_country_popup() {
        $user_id = $_SESSION['userdata']['user_id'];
        $last_url = "admin/company/index";
        $_SESSION['selected_country'] = $_POST['countries'];

       if (isset($_POST['user_default_country'])) {
    
         $edit_data = array(
                "user_default_country" => $_SESSION['selected_country']
                
            );

        $this->db->where('user_id',  $user_id);
        $this->db->update('tabqy_user', $edit_data);
       // echo $this->db->last_query();
  // $_SESSION['userdata']['user_default_country'];



       }
      redirect($last_url);
    }

}

?>
