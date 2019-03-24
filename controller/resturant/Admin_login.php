<?php

// Login controller uses for company/staff,brand/Staff & Restaurant Admin/staff
class Admin_login extends Controller {

    public function __construct() {
        parent::__construct();

        if (is_logged_in_resturant() == true) {
            redirect('resturant/user');
        }        
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  27-04-2018
      FUNCTION:  index()
      DESCRIPTION:Login company/staff,brand/Staff & Restaurant Admin/staff
     * *********************************** */

    Public function index() {
        $data['cookie'] = $_COOKIE;
        $this->validation->validate("user_username", "Name", "required");
        $this->validation->validate("user_password", "Password", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            view_loader('resturant/login/login.html', $data);
        } else {

            if ($_POST) {
                $not_array = array("superadmin", "staff");
                $this->db->flush_cache();
                $this->db->select('tabqy_user_role.user_role_id');
                $this->db->from('tabqy_user_role');
                $this->db->where('tabqy_user_role.user_role_slug !=', "superadmin");
                $this->db->where('tabqy_user_role.user_role_slug !=', "staff");
                $query_roles = $this->db->get();
                $role_results = $this->db->result($query_roles);
                $username = $_POST['user_username'];
                $password = $this->db->password_encrypt($_POST['user_password']);
                if (!empty($_POST["admin_remember"])) {
                    setcookie("admin_login", $_POST["user_username"], time() + (10 * 365 * 24 * 60 * 60));
                    setcookie("admin_password", $_POST["user_password"], time() + (10 * 365 * 24 * 60 * 60));
                    setcookie("admin_remember", $_POST["admin_remember"], time() + (10 * 365 * 24 * 60 * 60));
                } else {
                    if (isset($_COOKIE["admin_login"])) {
                        setcookie("admin_login", "");
                    }
                    if (isset($_COOKIE["admin_password"])) {
                        setcookie("admin_password", "");
                    }
                    if (isset($_COOKIE["admin_remember"])) {
                        setcookie("admin_remember", "");
                    }
                }
                $this->db->flush_cache();
                $this->db->select('*');
                $this->db->from('tabqy_user');
                $this->db->where("tabqy_user.user_username", $username);
                $this->db->where("tabqy_user.user_password", $password);
                $this->db->join("tabqy_user_role", "tabqy_user_role.user_role_id=tabqy_user.user_role");

                $query = $this->db->get();
                $results = $this->db->result($query);
                $results = $results[0];
                
                $this->db->select('company_country');
                $this->db->from('tabqy_company');
                $this->db->where('company_id', $results['user_company_id']);
                $country_query = $this->db->get();
                $country_res = $this->db->result($country_query);
                $roles = "";
                foreach ($role_results as $key => $user_role_id) {
                    $roles[] = $user_role_id['user_role_id'];
                }
                $roles = array_values($roles);
                if (in_array($results['user_role'], $roles)) {
                    $userdata['user_role'] = $results['user_role'];
                    $userdata['user_role_name'] = $results['user_role_name'];
                    $userdata['user_id'] = $results['user_id'];
                    $userdata['restaurant_id'] = $results['user_restaurant_id'];
                    $userdata['user_staffrole'] = $results['user_staffrole'];
                    $userdata['user_company_id'] = $results['user_company_id'];
                    $_SESSION['resturant_userdata'] = $userdata;
                    $_SESSION['company_country'] = $country_res[0]['company_country'];
                    if($_SESSION['company_country']=='SAU'){
                        $_SESSION['selected_country'] = $_SESSION['company_country'];
                    }                    
                    $_SESSION['company_id'] = $results['user_company_id'];
                    $this->privillage = $this->privillageRes();
                    if ($this->privillage['brandstatus'] == true) {
                          $_SESSION['brandlist'] = $this->privillage['brandlist'];
                    }
                    if ($this->privillage['brandstatus'] == true && $userdata['user_role']!='2') {
                        redirect('resturant/company/brand');
                    }else{
                        redirect('resturant/dashboard');
                    }
                } else {
                    set_flesh('error_message', 'Username & password does not match.');
                    $data = array();
                    $data['error_message'] = flesh('error_message');
                    view_loader('resturant/login/login.html', $data);
                }
            } else {
                $data = array();
                view_loader('resturant/login/login.html', $data);
            }
        }
    }

}

?>