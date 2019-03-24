<?php

// Dashboard controller 
class Companytax extends Controller {

    var $language;
    var $session;
    var $name;
    var $get_data;

    public function __construct() {
        parent::__construct();
        require "check_login.php";
        $this->privillage = $this->privillageRes();
        if ($this->privillage['brandstatus'] ==1) {
                redirect('resturant/company/brand');
            }
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
            if (empty($this->privillage['add']) && empty($this->privillage['edit'])) {
                 redirect('resturant/dashboard');              
            } else {
                if ($this->privillage['brandstatus'] == true) {
                    $_SESSION['brandlist'] = $this->privillage['brandlist'];
                }
            }
        }
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
        $data['selected_country'] = $_SESSION['selected_country'];
        return $data;
    }

    /*     * *
     *     __       _             ___      
     *    /   _ __ |_) _ __  \/    |  _    
     *    \__(_)||||  (_|| | /     | (_|>< 
     */

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION:  tax()
      DESCRIPTION: tax list
     * *********************************** */

    public function tax() {
        $sessiondata = $this->get_data();
        $data['session'] = $_SESSION;
        //echo "<pre>"; print_r($sessiondata);exit;
        $data['language'] = $this->language;
        $data['success'] = flesh('success');
        $data['error'] = flesh('error');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "resturant/companytax/tax/"; //URL
        $pageno = $this->url->segment(4); // Get page number
        $sort = 'id';
        $company_id = $sessiondata['company_id'];
        $restaurant_id = $sessiondata['restaurant_id'];
        $selected_country = $sessiondata['selected_country'];
        $search = '0';

        $this->db->select('l.country_language_country_name')->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', 'co.country_id=l.country_language_country_id', 'inner');
        $this->db->where('co.country_code', $selected_country);
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $c_name = $this->db->get();
        $data['selected_country_name'] = $this->db->row($c_name);
        //print_r($selected_country_name);

        $this->db->from('tabqy_taxes');
        $this->db->where('tabqy_taxes.tax_resturant_id', $restaurant_id);
        $this->db->where('tabqy_taxes.tax_company_id', $company_id);
        $this->db->where('tabqy_taxes.tax_status', '1');
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
        $this->db->select('tabqy_taxes.*,tabqy_countries.country_name');
        $this->db->from('tabqy_taxes');
        $this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_taxes.tax_country", "inner");
        $this->db->where('tabqy_taxes.tax_resturant_id', $restaurant_id);
        $this->db->where('tabqy_taxes.tax_company_id', $company_id);
        $this->db->where('tabqy_taxes.tax_status', '1');
        $this->db->where('tabqy_taxes.tax_country', $selected_country);
        $this->db->order_by('tabqy_taxes.tax_' . $sort, 'DESC');
        $this->db->limit($item_per_page, $page_position);
        $query_r = $this->db->get();
        $data['results'] = $this->db->result($query_r);
        //echo "<pre>";print_r($query_r);exit;

        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $this->db->flush_cache();
        $this->db->select('tabqy_countries.*');
        $this->db->from('tabqy_countries');
        $this->db->where('tabqy_countries.country_status', '1');
        $query_c = $this->db->get();
        $data['related_country'] = $this->db->result($query_c);

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

        $data['title'] = "Resturant";
        $data['heading'] = "Tax";
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
        view_loader('resturant/company/tax.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION: add_tax()
      DESCRIPTION: add tax
     * *********************************** */

    public function add_tax() {
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {

            if ($this->privillage['add'] != 1) {
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        $sessiondata = $this->get_data();
        $company_id = $sessiondata['company_id'];
        $this->validation->validate("tax_percent", "Tax percent", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            $regx = '/^([+-]?[1-9]\d*|0)$/';
            if (!preg_match($regx, $_POST['tax_percent'])) {
                $data['error_msg']['tax_percent'] = "Enter integer value";
                $data['set_data'] = $_POST;
                $data['error'] = "true";
                echo json_encode($data);
                die;
            }
            $country_code = $_POST['tax_country'];
            $this->db->flush_cache();
            $this->db->select('tabqy_resturantbrand.resturantbrand_country');
            $this->db->from('tabqy_resturantbrand');
            $this->db->where('tabqy_resturantbrand.resturantbrand_company_id', $company_id);
            $query_c = $this->db->get();
            $resturantbrand = $this->db->row($query_c);
            $check_data = $this->check_countrywise_tax($country_code, $company_id);
            if (!empty($check_data)) {
                $this->update_countrywise_tax($country_code, $company_id);
            }
            $data['send_data'] = $_POST;
            $data['send_data']['tax_status'] = 1;
            $data['send_data']['tax_country'] = $country_code;
            $data['send_data']['tax_company_id'] = $company_id;
            $data['send_data']['tax_created'] = date('Y-m-d H:i:s');
            //echo "<pre>"; print_r($data['send_data']);exit;
            $this->db->insert('tabqy_taxes', $data['send_data']);
            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Tax Added Successfully";
            echo json_encode($data);
            die;
        }
    }
    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION:  view_countrywise_tax(country_code,company_id)
      DESCRIPTION: view all tax countrywise
     * *********************************** */

    public function view_countrywise_tax($id) {
        $this->db->flush_cache();
        $this->db->select('tabqy_taxes.tax_country,tabqy_taxes.tax_company_id');
        $this->db->from('tabqy_taxes');
        $this->db->where('tabqy_taxes.tax_status', '1');
        $this->db->where('tabqy_taxes.tax_id', $id);
        $query_c = $this->db->get();
        $tabqy_taxes = $this->db->row($query_c);

        if ($tabqy_taxes) {
            $company_id = $tabqy_taxes['tax_company_id'];
            $country_code = $tabqy_taxes['tax_country'];
            $this->db->flush_cache();
            $this->db->select('tabqy_taxes.*,tabqy_countries.country_name');
            $this->db->from('tabqy_taxes');
            $this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_taxes.tax_country", "inner");
            $this->db->where('tabqy_taxes.tax_company_id', $company_id);
            $this->db->where('tabqy_taxes.tax_country', $country_code);
            $this->db->where('tabqy_taxes.tax_status', '0');
            $query_d = $this->db->get();
            $commissions = $this->db->result($query_d);
            echo json_encode($commissions);
            die;
        }
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION:  check_countrywise_tax(country_code,company_id)
      DESCRIPTION: check tax countrywise
     * *********************************** */

    Public function check_countrywise_tax($country_code, $company_id) {
        $this->db->flush_cache();
        $this->db->select('tax_id');
        $this->db->from('tabqy_taxes');
        $this->db->where('tabqy_taxes.tax_country', $country_code);
        $this->db->where('tabqy_taxes.tax_company_id', $company_id);
        $query_c = $this->db->get();
        $resturantbrand = $this->db->row($query_c);
        return $resturantbrand;
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION:  update_countrywise_tax(country_code,company_id)
      DESCRIPTION: check tax countrywise
     * *********************************** */

    Public function update_countrywise_tax($country_code, $company_id) {
        $data = array('tax_status' => '0');
        $this->db->flush_cache();
        $this->db->where('tabqy_taxes.tax_country', $country_code);
        $this->db->where('tabqy_taxes.tax_company_id', $company_id);
        $this->db->where('tax_status',1);
        $this->db->update('tabqy_taxes', $data);
        return $data;
    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION:  delete_tax()
      DESCRIPTION: delete tax
     * *********************************** */
//
//    Public function delete_tax($tax_id) {
//        //privillage checking and user validating 
//        if ($_SESSION['resturant_userdata']['user_role'] != 2) {
//
//            if ($this->privillage['delete'] != 1) {
//                $data['error'] = "permission";
//                $data['msg'] = "false";
//                $data['success_message'] = "You don't have permission to perform this action";
//                echo json_encode($data);
//                exit();
//            }
//        }
//        $this->db->where('tax_id', $tax_id);
//        $this->db->delete('tabqy_taxes');
//        $data['error'] = "false";
//        $data['msg'] = "false";
//        $data['success_message'] = "Tax success fully";
//        echo json_encode($data);
//        exit();
//    }

    /*     * ***********************************
      DEVELOPER: Meenu
      DATE:  30-04-2018
      FUNCTION:  edit_tax(tax_id)
      DESCRIPTION: edit tax
     * *********************************** */

    Public function edit_tax($user_id) { 
        //privillage checking and user validating 
        if ($_SESSION['resturant_userdata']['user_role'] != 2) {  
            //echo "piyu1";exit;echo $this->privillage['edit']; exit;
            if ($this->privillage['edit']!=1) {                
                $data['error'] = "permission";
                $data['msg'] = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        $sessiondata = $this->get_data();
        $company_id = $sessiondata['company_id'];
        $this->validation->validate("tax_country", "Country name", "required");
        $this->validation->validate("tax_name", "Tax name", "required");
        $this->validation->validate("tax_percent", "Tax percent", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;            
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            $regx = '/^[-+]?[0-9]*\.?[0-9]*$/';
            if (!preg_match($regx, $_POST['tax_percent'])) {
                $data['error_msg']['tax_percent1'] = "Enter integer or decimal value";
                $data['set_data'] = $_POST;                
                $data['error'] = "true";
                echo json_encode($data);
                die;
            }
            unset($_POST['submit']);
            $data['set_data'] = $_POST;
            $country_code = $data['set_data']['tax_country'];
            $check_data = $this->check_countrywise_tax($country_code, $company_id);
            if (!empty($check_data)) {
                $this->update_countrywise_tax($country_code, $company_id);
            }
            $data['send_data'] = $_POST;
            $data['send_data']['tax_status'] = 1;
            $data['send_data']['tax_country'] = $country_code;
            $data['send_data']['tax_company_id'] = $company_id;
            $data['send_data']['tax_created'] = date('Y-m-d H:i:s');
            $this->db->insert('tabqy_taxes', $data['send_data']);
            $this->db->where('tax_id', $user_id);
            //$this->db->update('tabqy_taxes', $data['set_data']);
            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Your row Updated Successfully";
            echo json_encode($data);
            die;
        }
    }

}

?>