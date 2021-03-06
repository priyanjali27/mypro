<?php

class Staffrole extends Controller {

    var $language;
    var $session;
    var $name;

    public function __construct() {
        parent::__construct();
        require "check_login.php";
        ///checking privilege and validating user

            $this->privillage = $this->privillageRes();
           //echo $this->privillage['brandstatus'];exit;
            if ($this->privillage['brandstatus'] ==1) {
                redirect('resturant/company/brand');
            }
            if ($_SESSION['resturant_userdata']['user_role'] != 2) {
                if (empty($this->privillage['add']) && empty($this->privillage['edit']) && empty($this->privillage['delete'])) {
                    redirect('resturant/dashboard');              
                }
                else {
                    if ($this->privillage['brandstatus'] == true) {
                       $_SESSION['brandlist'] = $this->privillage['brandlist'];
                    }
                }
        }
    }

    /*     * ***********************************
      DEVELOPER: praveen
      DATE:  01/05/2018
      FUNCTION:  index()
      DESCRIPTION: all users role wise
     * *********************************** */

    public function index() {
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $roleId = $this->url->segment(4);
        $data['roleId'] = $roleId;
        $company_id = $_SESSION['resturant_userdata']['user_company_id'];
        $data['company_id'] = $company_id;
        $data['country_id'] = "";

        $this->db->select('*,(select superadmin_status from tabqy_superadmin where superadmin_role_id = usertype_id and superadmin_company_id=' . $company_id . ') as supperadmin');
        $this->db->from('tabqy_usertype');
        $this->db->where('(usertype_company_id = 0 and usertype_restaurant_id = 0 ) or usertype_company_id = ' . $company_id, null, false);
        $this->db->order_by('usertype_id', 'DESC');
        $query = $this->db->get();
        $data['roles'] = $this->db->result($query);
        //echo $this->db->last_query();

        $data['controllers'] = $this->controllers();

        $privilege = $this->ed_data($roleId);
        if ($privilege) {
            foreach ($privilege as $checkdata) {

                $checkdatas[$checkdata['userprivilege_controllers_id']] = $checkdata['userprivilege_role_id'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_add"] = $checkdata['userprivilege_add'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_edit"] = $checkdata['userprivilege_edit'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_delete"] = $checkdata['userprivilege_delete'];
                //$checkdatas[$checkdata['userprivilege_controllers_id'] . "_view"] = $checkdata['userprivilege_view'];
            }
            $data['checkdata'] = $checkdatas;
        }

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

        $this->db->where_in('tabqy_user.user_role', array('2', '3'));
        $this->db->from('tabqy_user');
        if (!empty($roleId)) {
            $this->db->where('user_staffrole', $roleId);
        }
        $this->db->where('tabqy_user.user_company_id', $company_id);
        $get_total_rows = $this->db->count_all_results();

        $page_url = baseurl . "admin/staffrole/brand_staffrole/" . $roleId; //URL
        $item_per_page = 10; //item to display per page
        $pageno = $this->url->segment(7);

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

        $this->db->from('tabqy_user');
        $this->db->join('tabqy_usertype', "tabqy_usertype.usertype_id = tabqy_user.user_staffrole", "left");
        if (!empty($roleId)) {
            $this->db->where('tabqy_user.user_staffrole', $roleId);
        }
        $this->db->where('tabqy_user.user_company_id', $company_id);
        $this->db->order_by('tabqy_user.user_id', "desc");
        $this->db->limit($item_per_page, $page_position);
        $query = $this->db->get();
        $data['staffs'] = $this->db->result($query);
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, '');
        $data['action'] = "";
        $data['title'] = "User";
        $data['heading'] = "StafRole";
        $data['user_id'] = "";
        $data['page_number'] = $page_number;
        $start = $page_position + 1;
        $data['start'] = $start;
        $data['end'] = ($start + count($data['staffs']) - 1);
        $data['title'] = "Resturant";
        $data['heading'] = "ListUser";
        view_loader('resturant/staffrole/staffrole.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: praveen
      DATE:  01/05/2018
      FUNCTION:  controllers()
      DESCRIPTION: get all controllers
     * *********************************** */

    public function controllers() {
        $this->db->select('controllers_heading');
        $this->db->from('tabqy_controllersres');
        $this->db->where('controllers_sidebar_type', '1');
        $this->db->group_by('controllers_heading', 'ASC');
        $query = $this->db->get();
        $controllersHeadings = $this->db->result($query);
        foreach ($controllersHeadings as $controllersHeading) {
            $this->db->select('*');
            $this->db->from('tabqy_controllersres');
            $this->db->where('controllers_heading', $controllersHeading['controllers_heading']);
            $this->db->where('controllers_sidebar_type', '1');
            $query = $this->db->get();
            $controllers[$controllersHeading['controllers_heading']] = $this->db->result($query);
        }
        return $controllers;
    }

    /*     * ***********************************
      DEVELOPER: praveen
      DATE:  01/05/2018
      FUNCTION:  role_add()
      DESCRIPTION: add role in company module
     * *********************************** */

    function role_add() {
               
        $company_id = $_SESSION['resturant_userdata']['user_company_id'];
        $rolename = $_POST['rolename'];

        $this->validation->validate("rolename", "Name", "required");

        if ($this->validation->validation_check() == FALSE) {
            //$data['error_msg'] = $this->validation->error_msg;
            $data['success_message'] = 'This field is required';
            //print_r($data['success_message']);
            $data['error'] = true;
            echo json_encode($data);
            die;
        } else {
            //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            
            if ($this->privillage['add'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
	//privillage checking and user validating  end here.
            $data['usertype_name'] = $rolename;
            $data['usertype_company_id'] = $company_id;
            $data['usertype_created'] = date('Y-m-d H:i:s');
            $this->db->insert('tabqy_usertype', $data);
            $data['error'] = false;
            $data['success_message'] = "User Role Added Successfully";
            echo json_encode($data);
            die;
        }
    }

    /*     * ***********************************
      DEVELOPER: praveen
      DATE:  01/05/2018
      FUNCTION:  add()
      DESCRIPTION: add/edit/delete role in company module
     * *********************************** */

    function add() {
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            
            if ($this->privillage['add'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
	//privillage checking and user validating  end here.
        $company_id = $_SESSION['resturant_userdata']['user_company_id'];
        $roleId = $_POST['roleId'];
        $adds = isset($_POST['add']) ? $_POST['add'] : array();
        $edits = isset($_POST['edit']) ? $_POST['edit'] : array();
        $deletes = isset($_POST['del']) ? $_POST['del'] : array();

        $superadmin = $_POST['superadmin'];

        $this->db->from('tabqy_superadmin');
        $this->db->where('superadmin_company_id', $company_id);
        $this->db->where('superadmin_role_id', $roleId);
        $count = $this->db->count_all_results();

        if ($count > 0) {

            if ($superadmin == '1') {
                $edit_usertype_data['superadmin_status'] = '1';
                $this->db->where('superadmin_company_id', $company_id);
                $this->db->where('superadmin_role_id', $roleId);
                $edit = $this->db->update('tabqy_superadmin', $edit_usertype_data);
                if ($edit) {
                    $data['error'] = false;
                    $data['success_message'] = "Role Updated successfully";
                    echo json_encode($data);
                    die;
                } else {
                    $data['error'] = true;
                    $data['success_message'] = "Atleast one field are checked";
                    echo json_encode($data);
                    die;
                }
            }
        } else {
            $superadmin_data = array(
                "superadmin_company_id" => $company_id,
                "superadmin_role_id" => $roleId,
            );
            $this->db->insert("tabqy_superadmin", $superadmin_data);
        }

        $this->validation->validate('roleId', 'username', 'required');
        if ($this->validation->validation_check() == true) {
            if (!empty($adds) || !empty($edits) || !empty($deletes) || !empty($views)) {
                $this->db->where('userprivilege_role_id', $roleId);
                $this->db->delete('tabqy_userprivilegeres');
                if (!empty($adds)) {
                    foreach ($adds as $add) {
                        $add_data = array(
                            "userprivilege_role_id" => $roleId,
                            "userprivilege_controllers_id" => $add,
                            "userprivilege_company_id" => $company_id,
                            "userprivilege_add" => '1',
                        );
                        $this->db->insert("tabqy_userprivilegeres", $add_data);
                    }
                }
                if (!empty($edits)) {
                    foreach ($edits as $edit) {

                        $count_edit = $this->count_privilege($edit, $roleId);

                        if ($count_edit > 0) {
                            $edit_data = array("userprivilege_edit" => '1',);
                            $this->edit($edit_data, $edit, $roleId);
                        } else {
                            $edit_data = array(
                                "userprivilege_role_id" => $roleId,
                                "userprivilege_controllers_id" => $edit,
                                "userprivilege_company_id" => $company_id,
                                "userprivilege_edit" => '1',
                            );

                            $this->db->insert("tabqy_userprivilegeres", $edit_data);
                        }
                    }
                }

                if (!empty($deletes)) {
                    foreach ($deletes as $delete) {

                        $count_delete = $this->count_privilege($delete, $roleId);

                        //echo $count_delete; exit;
                        if ($count_delete > 0) {
                            $delete_data = array("userprivilege_delete" => '1',);
                            $this->edit($delete_data, $delete, $roleId);
                        } else {
                            $delete_data = array(
                                "userprivilege_role_id" => $roleId,
                                "userprivilege_controllers_id" => $delete,
                                "userprivilege_company_id" => $company_id,
                                "userprivilege_delete" => '1',
                            );

                            $this->db->insert("tabqy_userprivilegeres", $delete_data);
                        }
                    }
                }

                if (!empty($views)) {
                    foreach ($views as $view) {

                        $count_view = $this->count_privilege($view, $roleId);

                        //echo $count_delete; exit;
                        if ($count_view > 0) {
                            $view_data = array("userprivilege_delete" => '1',);
                            $this->edit($view_data, $view, $roleId);
                        } else {
                            $view_data = array(
                                "userprivilege_role_id" => $roleId,
                                "userprivilege_controllers_id" => $view,
                                "userprivilege_view" => '1',
                            );

                            $this->db->insert("tabqy_userprivilegeres", $view_data);
                        }
                    }
                }


                $data['error'] = false;
                $data['success_message'] = "Role added successfully";
                echo json_encode($data);
                die;
            } else {
                $data['error'] = true;
                $data['success_message'] = "Atleast one field are checked";
                echo json_encode($data);
                die;
            }
        }
    }

    /* =======
      function count_privilege
      parameter :-- controllerid,Roleid
      table :-- userprivilege
      working :-- counting record of matched row
      return type int
      date 03/15/2017
      written by pravin
      ===== */

    public function count_privilege($cid, $roleId) {
        $this->db->from('tabqy_userprivilegeres');
        $this->db->where("userprivilege_role_id", $roleId);
        $this->db->where("userprivilege_controllers_id", $cid);
        return $count = $this->db->count_all_results();
    }

    /* =======
      function edit
      parameter :-- edit_data,controllerid,userid
      table :-- userprivilege
      working :-- updating record of matched row
      return type int
      date 03/15/2017
      written by pravin
      ===== */

    public function edit($edit_data, $cid, $roleId) {
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            
            if ($this->privillage['edit'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
	//privillage checking and user validating  end here.
        $this->db->where("userprivilege_role_id", $roleId);
        $this->db->where("userprivilege_controllers_id", $cid);
        return $this->db->update("tabqy_userprivilegeres", $edit_data);
    }

    /*     * ***********************************
      DEVELOPER: praveen
      DATE:  01/05/2018
      FUNCTION: delete()
      DESCRIPTION: delete role in company module
     * *********************************** */

    public function delete($id) {
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            
            if ($this->privillage['delete'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
	//privillage checking and user validating  end here.

        $del = $this->delete_role($id);
        $data['error']           = "false";
        $data['msg']             = "false";
        $data['success_message'] = "User successfully deleted";
        echo json_encode($data);
        exit();
    }

    /* =======
      function ed_data
      parameter :-- id
      table :-- userprivilege
      working :-- fetching record of matched row
      return type array
      date 03/15/2017
      written by pravin
      ===== */

    public function ed_data($id) {
        $this->db->select('*');
        $this->db->from('tabqy_userprivilegeres');
        $this->db->where("userprivilege_role_id", $id);
        $rs = $this->db->get();
        $data = $this->db->result($rs);
        return $data;
    }

    /*     * ***********************************
      DEVELOPER: praveen
      DATE:  01/05/2018
      FUNCTION:  staffedit()
      DESCRIPTION: edit staff in company module
     * *********************************** */

    Public function staffedit($user_id) {
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            
            if ($this->privillage['edit'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
	//privillage checking and user validating  end here.
        $this->validation->validate("user_name", "Name", "required");
        $this->validation->validate("user_password", "Password", "required");
        $this->validation->validate("user_phoneno", "Phone number", "required|numeric");
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

    /*     * ***********************************
      DEVELOPER: praveen
      DATE:  01/05/2018
      FUNCTION:  addstaff()
      DESCRIPTION:add staff in company module
     * *********************************** */

    Public function addstaff() {
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            
            if ($this->privillage['add'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
	//privillage checking and user validating  end here.
                //echo "<pre>"; print_r($_POST);exit;
        $company_id = $_SESSION['resturant_userdata']['user_company_id'];
        $this->validation->validate("user_name", "Name", "required");
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
            $data['msg'] = "false";
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

                $data['send_data']['user_role'] = 3;
                if ($company_id != '')
                    $data['send_data']['user_company_id'] = $company_id;

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
                $mail->Body = '<html>
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
                                <tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;"><strong style="margin-right:10px;">Password:</strong>' . $pwd . '</span></td></tr>
                            
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

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27/04/2018
      FUNCTION:  check_username(username)
      DESCRIPTION: check user name
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
      DATE:  27/04/2018
      FUNCTION:  check_email(email)
      DESCRIPTION: check email
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
      DATE:  27/04/2018
      FUNCTION: staffdelete(user_id)
      DESCRIPTION: delete staff
     * *********************************** */

    Public function staffdelete($user_id) {
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            
            if ($this->privillage['delete'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
	//privillage checking and user validating  end here.
        $this->db->where('user_id', $user_id);
        $this->db->delete('tabqy_user');
        $data['error']           = "false";
        $data['msg']             = "false";
        $data['success_message'] = "Brand success fully deleted";
        echo json_encode($data);
        exit();
    }

    Public function assign_model($user_id, $company_id) {
        $data['session'] = $_SESSION;
        $data['user_id'] = $user_id;
        $data['language'] = $this->language;
        $data['company_id'] = $company_id;
        $this->db->select('*');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->where('resturantbrand_company_id', $company_id);
        $this->db->order_by('resturantbrand_name', 'ASC');
        $query = $this->db->get();
        $data['all_brands'] = $this->db->result($query);
        //echo "<pre>";print_r($data['all_brands']);die;
        
        /* Fetching Assigned brands to this user */
        $this->db->select('*');
        $this->db->from('tabqy_resuser_access');
        $this->db->where("res_user_id",$user_id);
        $sel_brands = $this->db->get();
        $data['assigned_brands'] =$this->db->row($sel_brands);
        //print_r($data['assigned_brands']);

        $company_id = $_SESSION['resturant_userdata']['user_company_id'];
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

        $data['title'] = "User";
        $data['heading'] = "StafRole";
        $data['title'] = "Resturant";
        $data['heading'] = "ListUser";

        //$this->db->select('user_name');
        //$this->db->from('tabqy_user');
        //$this->db->where('user_id',"$id");
        //$query = $this->db->get();
        //$data['user_name']=$this->db->result($query);
        //echo $this->db->last_query();


        view_loader('resturant/staffrole/assign_to_modal.html', $data);
    }

    Public function assign_brands() {
        unset($_POST['submit']);
        $data['send_data'] = $_POST;
        //echo "<pre>"; print_r($data['send_data']);exit;
        $user_id = $data['send_data']['res_user_id'];
        $data['send_data']['res_assign_date'] = date('Y-m-d H:i:s');
        unset($data['send_data']['update']);
        if ($data['send_data']['res_brand_ids'] != '') {
            
            $data['send_data']['res_brand_ids'] = implode(',', $data['send_data']['res_brand_ids']);
            $data['send_data']['res_access_status'] = 1;
            $this->db->select('*');
            $this->db->from('tabqy_resuser_access');
            $this->db->where('res_user_id', $user_id);
            $query = $this->db->get();
            $num = $query->rowCount();
            if ($num > 0) {
                $this->db->where('res_user_id', $user_id);
                unset($data['send_data']['res_user_id']);                

                $edit_data = array(
                    "res_brand_ids"    => $data['send_data']['res_brand_ids'],
                );

                $this->db->update('tabqy_resuser_access', $edit_data);
            } else { 
                //echo "<pre>"; print_r($data['send_data']);exit;
                echo $insert_id = $this->db->insert('tabqy_resuser_access', $data['send_data']);
            }

            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Brand Assigned Successfully";
            echo json_encode($data);
            die;
        }
    }

}

?>