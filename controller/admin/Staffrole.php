<?php

class Staffrole extends Controller {

    var $language;
    var $session;
    var $name;
    var $resturants;

    public function __construct() {
        parent::__construct();

        if (is_logged_in() == FALSE) {
            redirect('admin/dashboard');
        }
        $this->privillage = $this->privillage();
        if ($_SESSION['userdata']['user_role'] != 0) {
            if (empty($this->privillage['add']) && empty($this->privillage['edit']) && empty($this->privillage['delete'])) {
                redirect('admin/dashboard');
            } else {
                if ($this->privillage['locationstatus'] == true) {
                    $_SESSION['locationdata'] = array(
                        'status' => true,
                        'country' => $this->privillage['country'],
                        'region' => $this->privillage['region'],
                        'city' => $this->privillage['city'],
                        'zone' => $this->privillage['zone'],
                        'location' => $this->privillage['location']
                    );
                } else {
                    $_SESSION['locationdata']['status'] = false;
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
        $this->db->flush_cache();
        $this->db->select('resturantbrand_id,resturantbrand_name');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->order_by('resturantbrand_id', 'DESC');
        $query = $this->db->get();
        $this->resturants = $this->db->result($query);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: index
      DESCRIPTION: list of staffrole
     * *********************************** */

    public function index() {
        $roleId = $this->url->segment(4);
        $data['roleId'] = $roleId;
        $this->db->select('*');
        $this->db->from('tabqy_usertype');
        $this->db->order_by('usertype_id', 'DESC');
        $query = $this->db->get();
        $data['roles'] = $this->db->result($query);
        $data['controllers'] = $this->controllers();
        $privilege = $this->ed_data($roleId);
        if ($privilege) {
            foreach ($privilege as $checkdata) {
                $checkdatas[$checkdata['userprivilege_controllers_id']] = $checkdata['userprivilege_role_id'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_add"] = $checkdata['userprivilege_add'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_edit"] = $checkdata['userprivilege_edit'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_delete"] = $checkdata['userprivilege_delete'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_view"] = $checkdata['userprivilege_view'];
            }
            $data['checkdata'] = $checkdatas;
        }
        $this->db->where('tabqy_user.user_role', 1);
        $this->db->from('tabqy_user');
        if (!empty($roleId)) {
            $this->db->where('user_staffrole', $roleId);
        }
        $get_total_rows = $this->db->count_all_results();

        $page_url = baseurl . "admin/user/index/" . $roleId; //URL
        $item_per_page = 10; //item to display per page
        $pageno = $this->url->segment(5);

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
        if (!empty($roleId)) {
            $this->db->where('user_staffrole', $roleId);
        }
        $this->db->from('tabqy_user');
        $this->db->join('tabqy_usertype', "usertype_id = user_staffrole", "left");
        $this->db->order_by('user_id', "desc");
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
        view_loader('admin/staffrole/staffrole.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: brand_staffrole
      DESCRIPTION: brandwise staff role
     * *********************************** */

    public function brand_staffrole($restaurant_id, $country_id) {
        $roleId = $this->url->segment(6);
        $data['roleId'] = $roleId;
        $data['restaurant_id'] = $restaurant_id;
        $data['country_id'] = $country_id;
        $this->db->select('*');
        $this->db->from('tabqy_usertype');
        $this->db->order_by('usertype_id', 'DESC');
        $query = $this->db->get();
        $data['roles'] = $this->db->result($query);
        $data['controllers'] = $this->controllers();
        $privilege = $this->ed_data($roleId);
        if ($privilege) {
            foreach ($privilege as $checkdata) {

                $checkdatas[$checkdata['userprivilege_controllers_id']] = $checkdata['userprivilege_role_id'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_add"] = $checkdata['userprivilege_add'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_edit"] = $checkdata['userprivilege_edit'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_delete"] = $checkdata['userprivilege_delete'];
                $checkdatas[$checkdata['userprivilege_controllers_id'] . "_view"] = $checkdata['userprivilege_view'];
            }
            $data['checkdata'] = $checkdatas;
        }
        $this->db->where('tabqy_user.user_role', 1);
        $this->db->from('tabqy_user');
        if (!empty($roleId)) {
            $this->db->where('user_staffrole', $roleId);
        }
        $this->db->where('tabqy_user.user_restaurant_id', $restaurant_id);
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
        $this->db->where('tabqy_user.user_restaurant_id', $restaurant_id);
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
        view_loader('admin/staffrole/staffrole_brandwise.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: controllers
      DESCRIPTION: To select controllers
     * *********************************** */

    public function controllers() {
        $this->db->select('controllers_heading');
        $this->db->from('tabqy_controllers');
        $this->db->group_by('controllers_heading', 'ASC');
        $query = $this->db->get();
        $controllersHeadings = $this->db->result($query);
        foreach ($controllersHeadings as $controllersHeading) {
            $this->db->select('*');
            $this->db->from('tabqy_controllers');
            $this->db->where('controllers_heading', $controllersHeading['controllers_heading']);
            $query = $this->db->get();
            $controllers[$controllersHeading['controllers_heading']] = $this->db->result($query);
        }
        return $controllers;
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 27-04-2018
      FUNCTION: role_add
      DESCRIPTION: To add a role
     * *********************************** */

    function role_add() {
        //privillage checking and user validating 
        if ($_SESSION['userdata']['user_role'] != 0) {
            if ($this->privillage['add'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        $rolename = $_POST['rolename'];
        $this->validation->validate("role_name", "Name", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['success_message'] = 'This field is required';
            $data['error'] = true;
            echo json_encode($data);
            die;
        } else {
            $data['usertype_name'] = $rolename;
            $data['usertype_created'] = date('Y-m-d H:i:s');
            $this->db->insert('tabqy_usertype', $data);
            $data['error'] = false;
            $data['success_message'] = "User Role Added Successfully";
            echo json_encode($data);
            die;
        }
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: add
      DESCRIPTION: To Add A role
     * *********************************** */

    function add() {
//privillage checking and user validating 
        if ($_SESSION['userdata']['user_role'] != 0) {
            if ($this->privillage['add'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        $roleId = $_POST['roleId'];
        $adds = isset($_POST['add']) ? $_POST['add'] : array();
        $edits = isset($_POST['edit']) ? $_POST['edit'] : array();
        $deletes = isset($_POST['del']) ? $_POST['del'] : array();
        $superadmin = $_POST['superadmin'];
        if ($superadmin == '1') {
            $edit_usertype_data['usertype_supperadmin'] = '1';
            $this->db->where('usertype_id', $roleId);
            $edit = $this->db->update('tabqy_usertype', $edit_usertype_data);
            if ($edit) {
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
        } else {
            $edit_usertype_data['usertype_supperadmin'] = '0';
            $this->db->where('usertype_id', $roleId);
            $edit = $this->db->update('tabqy_usertype', $edit_usertype_data);
        }
        $this->validation->validate('roleId', 'username', 'required');
        if ($this->validation->validation_check() == true) {
            if (!empty($adds) || !empty($edits) || !empty($deletes) || !empty($views)) {
                $this->db->where('userprivilege_role_id', $roleId);
                $this->db->delete('tabqy_userprivilege');
                if (!empty($adds)) {
                    foreach ($adds as $add) {
                        $add_data = array(
                            "userprivilege_role_id" => $roleId,
                            "userprivilege_controllers_id" => $add,
                            "userprivilege_add" => '1',
                        );
                        $this->db->insert("tabqy_userprivilege", $add_data);
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
                                "userprivilege_edit" => '1',
                            );
                            $this->db->insert("tabqy_userprivilege", $edit_data);
                        }
                    }
                }

                if (!empty($deletes)) {
                    foreach ($deletes as $delete) {
                        $count_delete = $this->count_privilege($delete, $roleId);
                        if ($count_delete > 0) {
                            $delete_data = array("userprivilege_delete" => '1',);
                            $this->edit($delete_data, $delete, $roleId);
                        } else {
                            $delete_data = array(
                                "userprivilege_role_id" => $roleId,
                                "userprivilege_controllers_id" => $delete,
                                "userprivilege_delete" => '1',
                            );
                            $this->db->insert("tabqy_userprivilege", $delete_data);
                        }
                    }
                }

                if (!empty($views)) {
                    foreach ($views as $view) {
                        $count_view = $this->count_privilege($view, $roleId);
                        if ($count_view > 0) {
                            $view_data = array("userprivilege_delete" => '1',);
                            $this->edit($view_data, $view, $roleId);
                        } else {
                            $view_data = array(
                                "userprivilege_role_id" => $roleId,
                                "userprivilege_controllers_id" => $view,
                                "userprivilege_view" => '1',
                            );
                            $this->db->insert("tabqy_userprivilege", $view_data);
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
        $this->db->from('tabqy_userprivilege');
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
        $this->db->where("userprivilege_role_id", $roleId);
        $this->db->where("userprivilege_controllers_id", $cid);
        return $this->db->update("tabqy_userprivilege", $edit_data);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: delete
      DESCRIPTION: To delete A role
     * *********************************** */

    public function delete($id) {
        $del = $this->delete_role($id);
        die;
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
        $this->db->from('tabqy_userprivilege');
        $this->db->where("userprivilege_role_id", $id);
        $rs = $this->db->get();
        $data = $this->db->result($rs);
        return $data;
    }
    
    public function staff_edit_view(){
        $staff_id = $_POST['staff_id'];
        $this->db->flush_cache();
        $this->db->select('*')->from('tabqy_user')->where('user_id',$staff_id);
        $user_query = $this->db->get();
        $data['user_result'] = $this->db->row($user_query);
        
        $this->db->flush_cache();			            
        $this->db->select("cpl.company_language_name");
        $this->db->from("tabqy_company_language as cpl");
        $this->db->join("tabqy_user_access as uac","FIND_IN_SET(cpl.company_language_company_id,uac.companyid) !=0 and uac.user_id=".$staff_id,"right");
        $this->db->where('cpl.company_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('cpl.company_language_name');
        $this->db->group_by('cpl.company_language_company_id');
        $com_que = $this->db->get();
        $data['assigned_company']= $this->db->result($com_que);
        //echo "<pre>";print_r($comp_res);exit;
        
        $this->db->flush_cache();
        $this->db->select("ll.location_language_location_name,lo.location_code");
        $this->db->from("tabqy_locations as lo");
        $this->db->join('tabqy_locations_language as ll', 'll.location_language_location_id=lo.location_id','inner');
        $this->db->join("tabqy_user_access as ac","FIND_IN_SET(lo.location_code,ac.location_code) !=0 and ac.user_id=".$staff_id,"right");
        $this->db->where('ll.location_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('ll.location_language_location_name');
        $lo_que = $this->db->get();
        $data['assigned_location']= $this->db->result($lo_que);
        //echo "<pre>";print_r($lo_res);exit;
        
        echo json_encode($data);exit;
        
    }

    /*************************************
      DEVELOPER: Shivank Mittal
      DATE: 30-04-staffedit
      FUNCTION: addstaff
      DESCRIPTION: For edit a Staff
    *************************************/

    Public function staffedit($user_id) {
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
        //privillage checking and user validating  end here
        $this->validation->validate("user_name", "Name", "required");
        $this->validation->validate("user_password", "Password", "required");
        $this->validation->validate("user_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("user_gender", "Gender", "required");
        $this->validation->validate("user_address", "Address", "required");
        $this->validation->validate("user_city", "City", "required");
        $this->validation->validate("user_dob", "Date of birth", "required");
        $this->validation->validate("user_zipcode", "Zipcode", "required|numeric");
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

            //checking super admin role
            $roleId = $_POST['user_staffrole'];
            $this->db->where("usertype_id", $roleId);
            $rs = $this->db->get("tabqy_usertype");
            $usertype_data = $this->db->row($rs);
            if ($usertype_data['usertype_supperadmin'] == '1') {
                $data['set_data']['user_role'] = 0;
            } else {
                $data['set_data']['user_role'] = 1;
            }
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
      DEVELOPER: Shivank Mittal
      DATE: 30-04-2018
      FUNCTION: addstaff
      DESCRIPTION: For Add a Staff
     * *********************************** */

    Public function addstaff($restaurant_id = '') {
        //privillage checking and user validating 
        if ($_SESSION['userdata']['user_role'] != 0) {

            if ($this->privillage['add'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }

        $this->validation->validate("user_name", "Name", "required");
        $this->validation->validate("user_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("user_email", "Email", "required|email");
        $this->validation->validate("user_username", "Username", "required|alpha_numeric");
        $this->validation->validate("user_gender", "Gender", "required");
        $this->validation->validate("user_address", "Address", "required");
        $this->validation->validate("user_dob", "Date of birth", "required");
        $this->validation->validate("user_city", "City", "required");
        $this->validation->validate("user_zipcode", "Zipcode", "required|numeric");
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
                $data['send_data']['user_password'] = $this->db->password_encrypt($_POST['user_password']);
                //checking super admin role
                $roleId = $_POST['user_staffrole'];
                $this->db->where("usertype_id", $roleId);
                $rs = $this->db->get("tabqy_usertype");
                $usertype_data = $this->db->row($rs);
                if ($usertype_data['usertype_supperadmin'] == '1') {
                    $this->db->flush_cache();
                    $this->db->select('tabqy_user_role.user_role_id');
                    $this->db->from('tabqy_user_role');
                    $this->db->where('tabqy_user_role.user_role_slug', "superadmin");
                    $query_roles = $this->db->get();
                    $role_results = $this->db->result($query_roles);
                    $data['send_data']['user_role'] = $role_results[0]['user_role_id'];
                } else {
                    $this->db->flush_cache();
                    $this->db->select('tabqy_user_role.user_role_id');
                    $this->db->from('tabqy_user_role');
                    $this->db->where('tabqy_user_role.user_role_slug', "staff");
                    $query_roles = $this->db->get();
                    $role_results = $this->db->result($query_roles);
                    $data['send_data']['user_role'] = $role_results[0]['user_role_id'];
                }
                if ($restaurant_id != '')
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
                $msg_admin = "Thank you for selecting Tabqy, you have successfully registered with us.";
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
      DEVELOPER: Shivank Mittal
      DATE: 30-04-2018
      FUNCTION: check_username
      DESCRIPTION: For check the username
      already exist or not
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
      DEVELOPER: Shivank Mittal
      DATE: 30-04-2018
      FUNCTION: check_email
      DESCRIPTION: For check the email
      already exist or not
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
      DEVELOPER: Shivank Mittal
      DATE: 30-04-2018
      FUNCTION: staffdelete
      DESCRIPTION: to delete the staff
     * *********************************** */

    Public function staffdelete($user_id) {
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
        $this->db->where('user_id', $user_id);
        $this->db->delete('tabqy_user');
        $data['error'] = "false";
        $data['msg'] = "false";
        $data['success_message'] = "Staff successfully deleted";
        echo json_encode($data);
        exit();
    }

    Public function assign_model($type, $id) {

        $data['type'] = $type;
        $data['id'] = $id;
        $data['title'] = "User";
        $data['heading'] = "StaffRole";
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;


        $this->db->select('*');
        $this->db->from('tabqy_company');
        $this->db->order_by('company_name', 'ASC');
        $query = $this->db->get();
        $data['company_count'] = $query->rowCount();
        $data['all_company'] = $this->db->result($query);

        $this->db->select('*');
        $this->db->from('tabqy_user_access');
        $this->db->where("user_id", $id);
        $this->db->where('companyid!=', NULL);
        $sel_company = $this->db->get();
        //$data['assigned_company_count'] = $sel_company->rowCount();
       $data['assigned_company'] = $this->db->row($sel_company);
       $c= $data['assigned_company']['companyid'];
       $ec=  explode(',' , $c);
       $data['assgn_company2']=$ec;
       $data['sel_company']=count($ec);
       
        
        $this->db->select('*');
        $this->db->from('tabqy_user_access');
        $this->db->where("user_id", $id);
        $this->db->where('location_code!=', NULL);
        $sel_loc = $this->db->get();
        $data['assigned_locations'] = $this->db->row($sel_loc);
        $data['as_city'] = $data['assigned_locations']['city_code'];
        $data['as_location'] = $data['assigned_locations']['location_code'];
        
        //echo "<pre>";print_r($data['as_location']);echo "<br>";
        $this->db->flush_cache();
        $this->db->select('lang.location_language_location_name, lang.location_language_language_code,loc.*');
        $this->db->from('tabqy_locations as loc');
        $this->db->join('tabqy_locations_language as lang', 'lang.location_language_location_id=loc.location_id', 'inner');
        $this->db->where('loc.city_code', $data['as_city']);
        //$this->db->where('loc.is_zone<>',1);
        $this->db->where('lang.location_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('loc.location_name', 'ASC');
        $query = $this->db->get();
        $data['related_locations'] = $this->db->result($query);
        
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('co.country_name', 'ASC');
        $query_c = $this->db->get();
        $data['related_country'] = $this->db->result($query_c);
        //echo "<pre>";print_r($data['related_country']);

        $this->db->select('user_name');
        $this->db->from('tabqy_user');
        $this->db->where('user_id', "$id");
        $query = $this->db->get();
        $data['user_name'] = $this->db->result($query);
        //echo $this->db->last_query();


        view_loader('admin/staffrole/assign_to_modal.html', $data);
    }

    Public function add_company() {

        if ($_SESSION['userdata']['user_role'] != 0) {
            if ($this->privillage['add'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }

        unset($_POST['submit']);
        $data['send_data'] = $_POST;
        $user_id = $data['send_data']['user_id'];
        $data['send_data']['assigned_date'] = date('Y-m-d H:i:s');
        if ($data['send_data']['companyid'] != '') {
            $data['send_data']['companyid'] = implode(',', $data['send_data']['companyid']);

            $this->db->select('*');
            $this->db->from('tabqy_user_access');
            $this->db->where('user_id', $user_id);
            $query = $this->db->get();
            $num = $query->rowCount();
            if ($num > 0) {
                $this->db->where('user_id', $user_id);
                unset($data['send_data']['user_id']);

                $edit_data = array(
                    "companyid" => $data['send_data']['companyid'],
                    "country_code" => NULL,
                    "city_code" => NULL,
                    "location_code" => NULL,
                );

                $this->db->update('tabqy_user_access', $edit_data);
            } else {
                $insert_id = $this->db->insert('tabqy_user_access', $data['send_data']);
            }

            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Company Added Successfully";
            echo json_encode($data);
            die;
        }
    }

    public function add_location() {

        if ($_SESSION['userdata']['user_role'] != 0) {

            if ($this->privillage['add'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        $this->validation->validate("country_code", "Country", "required");
        $this->validation->validate("city_code", "City", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {

            unset($_POST['submit']);
            $data['send_data'] = $_POST;
            $user_id = $data['send_data']['user_id'];
            $data['send_data']['assigned_date'] = date('Y-m-d H:i:s');
            if ($data['send_data']['location_code'] != '')
                $data['send_data']['location_code'] = implode(',', $data['send_data']['location_code']);


            $this->db->select('*');
            $this->db->from('tabqy_user_access');
            $this->db->where('user_id', $user_id);
            $query = $this->db->get();
            $num = $query->rowCount();
            if ($num > 0) {
                $this->db->where('user_id', $user_id);
                unset($data['send_data']['user_id']);
                $edit_data = array(
                    "companyid" => NULL,
                    "country_code" => $data['send_data']['country_code'],
                    "city_code" => $data['send_data']['city_code'],
                    "location_code" => $data['send_data']['location_code'],
                );
                $this->db->update('tabqy_user_access', $edit_data);
            } else {
                $insert_id = $this->db->insert('tabqy_user_access', $data['send_data']);
            }

            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Locations Added Successfully";
            echo json_encode($data);
            die;
        }
    }

    Public function related_cities($co_id) {
        $this->db->flush_cache();
        $this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');
        $this->db->from('tabqy_cities as city');
        $this->db->join('tabqy_cities_language as lang', 'lang.city_language_city_id=city.city_id', 'inner');
        $this->db->where('city.country_code', $co_id);
        $this->db->where('lang.city_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('city.city_name', 'ASC');
        $query_c = $this->db->get();
        $data['related_cities'] = $this->db->result($query_c);
        echo json_encode($data);
        die;
    }

    Public function related_locations($ci_id) {

        $this->db->flush_cache();
        $this->db->select('lang.location_language_location_name, lang.location_language_language_code,loc.*');
        $this->db->from('tabqy_locations as loc');
        $this->db->join('tabqy_locations_language as lang', 'lang.location_language_location_id=loc.location_id', 'inner');
        $this->db->where('loc.city_code', $ci_id);
        //$this->db->where('loc.is_zone<>',1);
        $this->db->where('lang.location_language_language_code', $_SESSION['lang_code']);
        $this->db->order_by('loc.location_name', 'ASC');
        $query = $this->db->get();
        $data['related_locations'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

}

?>