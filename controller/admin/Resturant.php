<?php

// Resturant controller 
class Resturant extends Controller {

    var $language;
    var $session;
    var $name;
    var $resturants;

    public function __construct() {
        parent::__construct();

        if (is_logged_in() == FALSE) {
            redirect('admin/dashboard');
        }

        //checking privilege and validating user
        $this->privillage = $this->privillage();
        if ($_SESSION['userdata']['user_role'] != 0) {
            if (empty($this->privillage['add']) && empty($this->privillage['edit']) && empty($this->privillage['delete'])) {
                redirect('admin/dashboard');
            } else {
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
      FUNCTION: back
      DESCRIPTION: For back screen by click
     * *********************************** */

    public function back() {
        $last_url = $_SERVER['HTTP_REFERER'];

        if ($_SERVER['REQUEST_URI'] == $last_url) {
            $last_url = baseurl . "admin/company";
        } else {
            $last_url = str_replace(baseurl, "", $last_url);
        }

        redirect($last_url);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: brands
      DESCRIPTION: show list of brands
     * *********************************** */

    Public function brands() {
        if (empty($this->url->segment(4))) {
            $last_url = "admin/company";
            redirect($last_url);
        }
        $company_id = $this->url->segment(4);
        $_SESSION['companyId']=$company_id ;




        unset($_SESSION['restaurant_id']);
        unset($_SESSION['country_id']);
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['company_id'] = $company_id;

        $data['results'] = "";
        $this->db->flush_cache();
        $this->db->select('resturantbrand_id,resturantbrand_name');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->order_by('resturantbrand_id', 'DESC');
        $query = $this->db->get();
        $data['resturants'] = $this->db->result($query);
        $data['rest_count'] = $query->rowCount();
        $data['action'] = "";
        $data['title'] = "Brand";
        $data['heading'] = "Restaurant";
        $data['user_id'] = "";

        $this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['related_country'] = $this->db->result($query_c);
        $this->db->flush_cache();
        // $sql='select *, (select count(resturantbrand_type) from tabqy_resturantbrand as branch where branch.resturantbrand_type =  tabqy_resturantbrand.resturantbrand_id) as total from tabqy_resturantbrand inner join tabqy_resturantbrand_language on resturantbrand_id = resturantbrand_language_resturantbrand_id where (resturantbrand_id in(select resturantbrand_type from tabqy_resturantbrand where resturantbrand_type !="0" and resturantbrand_country = "'.$country_code.'" group by resturantbrand_type)  ) and resturantbrand_type =0 and resturantbrand_language_language_code="'. $_SESSION['lang_code'].'"';
        // $query=$this->db->query($sql);
        $data['results'] = "";

        $this->db->select('c.country_id,c.country_code,c.country_name,c.country_file, count(r.resturantbrand_id) as Totalrest,l.country_language_country_name,l.country_language_language_code');
        $this->db->from('tabqy_countries c');
        $this->db->join('tabqy_resturantbrand as r', 'r.resturantbrand_country=c.country_code AND r.resturantbrand_type =0 AND r.resturantbrand_company_id="' . $company_id . '"', 'left');
        $this->db->join('tabqy_countries_language l', 'c.country_id=l.country_language_country_id AND l.country_language_language_code="' . $_SESSION['lang_code'] . '"', 'LEFT');
        $this->db->group_by('c.country_code');
        $this->db->order_by('c.country_id', 'DESC');
        $query = $this->db->get();
        $data['brandcountries'] = $this->db->result($query);
        $data['country_count'] = $query->rowCount();
        $data['privillage'] = $this->privillage;

         $this->db->flush_cache();
      $this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');
      $this->db->from('tabqy_cities as city');
      $this->db->join('tabqy_cities_language as lang','lang.city_language_city_id=city.city_id','inner');
      if (!empty($_SESSION['selected_country'])) {
        $this->db->where('city.country_code',  $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
           $this->db->where('city.country_code',  $_SESSION['userdata']['user_default_country']); 
        }
 


   $this->db->where('lang.city_language_language_code',$_SESSION['lang_code']);
      $this->db->order_by('city.city_name','ASC');
      $query_c = $this->db->get();
      $data['related_cities'] = $this->db->result($query_c);
      
        $this->db->flush_cache();

        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);

        $this->db->order_by('co.country_name', 'ASC');

        $query_c = $this->db->get();

        $data['all_countries'] = $this->db->result($query_c);

        $this->db->flush_cache();
        $this->db->select('rl.region_language_region_name,rl.region_language_language_code,r.*');
        $this->db->from('tabqy_regions as r');
        $this->db->join('tabqy_regions_language as rl', "rl.region_language_region_id=r.region_id", "inner");

        if (!empty($_SESSION['selected_country'])) {
        $this->db->where('r.country_code',  $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
           $this->db->where('r.country_code',  $_SESSION['userdata']['user_default_country']); 
        }
 
        $this->db->where('rl.region_language_language_code', $_SESSION['lang_code']);
        $query = $this->db->get();
        // print_r($query);
        $data['related_regions'] = $this->db->result($query);


        $this->db->flush_cache();
        $this->db->select('*');
        $this->db->from('tabqy_cuisine');
        $this->db->join('tabqy_cuisine_language', "tabqy_cuisine_language.cuisine_language_cuisine_id = tabqy_cuisine.cuisine_id", 'inner');

        $this->db->where('tabqy_cuisine_language.cuisine_language_language_code', $_SESSION['lang_code']);
        $query = $this->db->get();
        $data['all_cuisine'] = $this->db->result($query);

       $this->db->select('co.* , cl.*');
        $this->db->from('tabqy_company as co');
        $this->db->join('tabqy_company_language as cl', "cl.company_language_company_id=co.company_id", "inner");
        $this->db->where('co.company_id', $_SESSION['company_id']);
        $this->db->where('cl.company_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['company'] = $this->db->row($query_c);
        
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
        
        view_loader('admin/resturant/resturant.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: branches
      DESCRIPTION: show list of resturant
     * *********************************** */

    Public function branches() {
        if (empty($this->url->segment(4)) || empty($this->url->segment(5)) || empty($this->url->segment(6))) {
            $last_url = "admin/company";
            redirect($last_url);
        }
        $company_id = $this->url->segment(6);
        $restaurant_id = $this->url->segment(4);
        $country_id = $this->url->segment(5);
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['success_massage'] = flesh('success_message');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "admin/resturant/branches/$restaurant_id/$country_id/$company_id"; //URL
        $data['restaurant_id'] = $restaurant_id;
        $data['country_id'] = $country_id;
        $data['company_id'] = $company_id;

        $pageno = $this->url->segment(7);
        $this->validation->validate("search", "Search", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
        }
        $search = 0;
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
        $this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
        $this->db->from('tabqy_resturantbrand t2');

        $this->db->join('tabqy_resturantbrand t1', 't1.resturantbrand_type=t2.resturantbrand_id', 'RIGHT');
        $this->db->order_by('t1.resturantbrand_id', 'DESC');
        $this->db->where('t1.resturantbrand_type', $restaurant_id);
        $this->db->limit($item_per_page, $page_position);
        $query = $this->db->get();
        $data['results'] = $this->db->result($query);

        $this->db->flush_cache();
        $this->db->select('resturantbrand_id,resturantbrand_name');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->order_by('resturantbrand_id', 'DESC');
        $query = $this->db->get();
        $data['resturants'] = $this->db->result($query);

        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $data['action'] = "";
        $data['title'] = "Brand";
        $data['heading'] = "Restaurant";
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

        // $this->db->select('*');
        // $this->db->from('tabqy_countries');
        // $this->db->order_by('country_name', 'ASC');
        // $query_c = $this->db->get();
        // $data['all_countries'] = $this->db->result($query_c);
        $this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);

      		 $this->db->select('b.* , bl.*');
        $this->db->from('tabqy_resturantbrand as b');
        $this->db->join('tabqy_resturantbrand_language as bl', "bl.resturantbrand_language_resturantbrand_id    =b.resturantbrand_id", "inner");
        $this->db->where('b.resturantbrand_id', $restaurant_id);
        $this->db->where('bl.resturantbrand_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['brand'] = $this->db->row($query_c);
       $cuisine_id= $data['brand']['resturantbrand_cuisine'];
        
       $this->db->select('co.* , cl.*');
        $this->db->from('tabqy_company as co');
        $this->db->join('tabqy_company_language as cl', "cl.company_language_company_id=co.company_id", "inner");
        $this->db->where('co.company_id', $company_id);
        $this->db->where('cl.company_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['company'] = $this->db->row($query_c);
        
        $this->db->flush_cache();
        $this->db->select('*');
        $this->db->from('tabqy_cuisine');
        $this->db->join('tabqy_cuisine_language',"tabqy_cuisine_language.cuisine_language_cuisine_id = tabqy_cuisine.cuisine_id", 'inner');
        $this->db->where('tabqy_cuisine.cuisine_id', $cuisine_id);
        $this->db->where('tabqy_cuisine_language.cuisine_language_language_code', $_SESSION['lang_code']);
        $query             = $this->db->get();
        $data['cuisine_details'] = $this->db->row($query);
        
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

               $this->db->flush_cache();
      $this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');
      $this->db->from('tabqy_cities as city');
      $this->db->join('tabqy_cities_language as lang','lang.city_language_city_id=city.city_id','inner');
      if (!empty($_SESSION['selected_country'])) {
        $this->db->where('city.country_code',  $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
           $this->db->where('city.country_code',  $_SESSION['userdata']['user_default_country']); 
        }
 


      $this->db->where('lang.city_language_language_code',$_SESSION['lang_code']);
      $this->db->order_by('city.city_name','ASC');
      $query_c = $this->db->get();
      $data['related_cities'] = $this->db->result($query_c);
     // echo $this->db->last_query();
      $data['privillage'] = $this->privillage;



        view_loader('admin/resturant/branches.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: country
      DESCRIPTION: show country
     * *********************************** */

    Public function country() {
        $_SESSION['restaurant_id'] = $_POST['brand_id'];
        $_SESSION['country_id'] = $_POST['country_id'];
        $data['error_msg'] = "";
        $data['error'] = "false";
        echo json_encode($data);
        die;
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: get_role_id
      DESCRIPTION: Get role id by slug
     * *********************************** */

    function get_role_id($slug) {
        $this->db->select('user_role_id')->from('tabqy_user_role')->where('user_role_slug', $slug);
        $query = $this->db->get();
        $result = $this->db->row($query);
        return $result['user_role_id'];
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: add
      DESCRIPTION: add resturant with roles
     * *********************************** */

    Public function add() {
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
        //echo "<pre>"; print_r($_POST);exit;
        $restaurant_id = $this->url->segment(4);
        //$company_id     =   $this->url->segment(5);

        $data['session'] = $_SESSION;
        $languages = $this->language;
        $this->validation->validate("resturantbrand_name", "Name", "required");
        $this->validation->validate("resturantbrand_displayname", "Display Name", "required");
        $this->validation->validate("resturantbrand_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("resturantbrand_email", "Email", "required|email");
      //  $this->validation->validate("resturantbrand_countusers", "Total number of users", "required|numeric");
     //    $this->validation->validate("resturantbrand_address", "Address", "required");
        //$this->validation->validate("resturantbrand_countpos", "Total number of POS", "required|numeric");
        $this->validation->validate("resturantbrand_country", "Country", "required");
       // $this->validation->validate("resturantbrand_region", "Region", "required");
        $this->validation->validate("resturantbrand_city", "City", "required");
       // $this->validation->validate("resturantbrand_zone", "Zone", "required");
        $this->validation->validate("resturantbrand_location", "Location", "required");
        $this->validation->validate("resturantbrand_file", "Logo", "required");
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
            //echo "<pre>"; print_r($data['send_data']);exit;
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

            if (empty($_POST['resturantbrand_type']) || $_POST['resturantbrand_type'] == "0") {

                $data['send_data']['resturantbrand_type'] = 0;
            } else {
                $data['send_data']['resturantbrand_type'] = $restaurant_id;
            }
            $data['send_data']['resturantbrand_created'] = date('Y-m-d H:i:s');
            //$data['send_data']['resturantbrand_company_id'] = $company_id;
            //$insert_id=$this->db->lastInsertId();
            $email = $data['send_data']['resturantbrand_email'];
            $pass = $this->validation->random_string(7);
            $password = $this->db->password_encrypt($pass);

            $break_email = explode('@', $email);
            $username = $break_email[0];
            $data['error_msg'] = "";
            if (!empty($username) || !empty($email)) {
                $username_array = $this->check_username($username);
                //print_r($username_array);die;
                if ($username_array) {
                    $data['error_msg']['resturantbrand_email'] = "Email is already exist.";
                }
                $useremail_array = $this->check_email($email);
                if ($useremail_array) {
                    $data['error_msg']['resturantbrand_email'] = "Email is already exist.";
                }
            }

            if (!empty($data['error_msg'])) {
                //    $data['set_data']=$_POST;
                $data['error'] = "true";
                $data['msg'] = "false";
                echo json_encode($data);
                die;
            }
            //echo "<pre>"; print_r($data['send_data']);exit;
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
            if ($data['send_data']['resturantbrand_type'] == "") {
                $data['rest_data']['user_role'] = $this->get_role_id('brandAdmin');
            } else {
                $data['rest_data']['user_role'] = $this->get_role_id('restaurantAdmin');
            }
            $data['rest_data']['user_email'] = $email;
            $data['rest_data']['user_username'] = $username;
            $data['rest_data']['user_phoneno'] = $data['send_data']['resturantbrand_phoneno'];
            $data['rest_data']['user_address'] = $data['send_data']['resturantbrand_address'];
            $data['rest_data']['user_restaurant_id'] = $insert_id;

            $data['rest_data']['user_company_id'] = ($data['send_data']['resturantbrand_company_id']) ? $data['send_data']['resturantbrand_company_id'] : 0;

            $data['rest_data']['user_created'] = date('Y-m-d H:i:s');
            $this->db->insert('tabqy_user', $data['rest_data']);
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

                                
                                <tr><td style="color: #333;font-size: 16px;font-weight: 600;text-align:center;">Dear Customer,</td></tr>
                                <tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;">Your Details.<td> </tr>
                                <tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;"><strong style="margin-right:10px;">Email:</strong><a href="mailto:' . $email . '" target="_blank">' . $email . '</a><br></span></td></tr>
                                <tr><td style="color: #777;font-size:14px;font-weight: 600;text-align:center;"><strong style="margin-right:10px;">Login Url:</strong> ' . baseurl . 'resturant<br></span></td></tr>
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
            } else {
                $data['error'] = "false";
                $data['msg'] = "true";
                $data['success_message'] = "New Resturant added successfully,We sent login details on your Mail";
                echo json_encode($data);
                die;
            }
        }
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: delete
      DESCRIPTION: Delete resturant Module
     * *********************************** */

    Public function delete($user_id) {
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
        $this->db->where('resturantbrand_id', $user_id);
        $this->db->delete('tabqy_resturantbrand');
        $this->db->where('resturantbrand_language_resturantbrand_id', $user_id);
        $this->db->delete('tabqy_resturantbrand_language');
        $data['error'] = "false";
        $data['msg'] = "false";
        $data['success_message'] = "Brand success fully deleted";
        echo json_encode($data);
        exit();
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: edit
      DESCRIPTION: Edit resturant Module
     * *********************************** */

    Public function edit($user_id) {
        $this->validation->validate("resturantbrand_name", "Name", "required");
        $this->validation->validate("resturantbrand_displayname", "Display Name", "required");
        $this->validation->validate("resturantbrand_phoneno", "Phone number", "required|numeric");
        $this->validation->validate("resturantbrand_email", "Email", "required|email");
       // $this->validation->validate("resturantbrand_countusers", "Total number of users", "required|numeric");
      //  $this->validation->validate("resturantbrand_address", "Address", "required");
        //$this->validation->validate("resturantbrand_countpos", "Total number of POS", "required|numeric");
        $this->validation->validate("resturantbrand_country", "Country", "required");
       // $this->validation->validate("resturantbrand_region", "Region", "required");
        $this->validation->validate("resturantbrand_city", "City", "required");
       // $this->validation->validate("resturantbrand_zone", "Zone", "required");
        $this->validation->validate("resturantbrand_location", "Location", "required");
        if ($this->validation->validation_check() == FALSE) {
            $data['error_msg'] = $this->validation->error_msg;
            $data['set_data'] = $_POST;
            $data['set_data']['resturantbrand_updated'] = date('Y-m-d H:i:s');
            $data['error'] = "true";
            echo json_encode($data);
            die;
        } else {
            $language_code = $_POST['language_code'];
            $brandname = $_POST['resturantbrand_name'];
            $display_name = $_POST['resturantbrand_displayname'];
            $address = $_POST['resturantbrand_address'];

            unset($_POST['submit']);
            unset($_POST['language_code']);
            if ($_SESSION['lang_code'] != $language_code) {
                unset($_POST['resturantbrand_name']);
                unset($_POST['resturantbrand_displayname']);
                unset($_POST['resturantbrand_address']);
            }

            $data['set_data'] = $_POST;
            unset($data['set_data']['resturantbrand_id']);

            if ($_FILES) {
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
                        $data['set_data']['resturantbrand_file'] = $file_name;
                    }
                }
            }

            $this->db->where('resturantbrand_id', $user_id);
            $this->db->update('tabqy_resturantbrand', $data['set_data']);
            $edit_data_language = array(
                "resturantbrand_language_name" => $brandname,
                "resturantbrand_language_displayname" => $display_name,
                "resturantbrand_language_address" => $address,
                "resturantbrand_language_edit" => '1'
            );
            $this->db->where('resturantbrand_language_resturantbrand_id', $user_id);
            $this->db->where('resturantbrand_language_language_code', $language_code);
            $this->db->update('tabqy_resturantbrand_language', $edit_data_language);
            $data['error'] = "false";
            $data['msg'] = "true";
            $data['success_message'] = "Resturant updated successfully";
            echo json_encode($data);
            die;
        }
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: view
      DESCRIPTION: view resturant
     * *********************************** */

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
        view_loader('admin/resturant/view.html', $data);
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

    public function rest() {
        view_loader('admin/resturant/rest.html');
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: updateStatus
      DESCRIPTION: to update the status value
     * *********************************** */

    function updateStatus($id, $value) {
        $this->db->flush_cache();
        $this->db->where('resturantbrand_id', $id);
        $this->db->update('tabqy_resturantbrand', array(
            'resturantbrand_status' => $value
        ));
        $this->db->flush_cache();
        $this->db->select('resturantbrand_status,resturantbrand_id');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_id', $id);
        $query = $this->db->get();
        $data['cur_status'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: related_regions
      DESCRIPTION: List of region by country code
     * *********************************** */

    function related_regions($country_code) {
        $this->db->flush_cache();
        $this->db->select('rl.region_language_region_name,rl.region_language_language_code,r.*');
        $this->db->from('tabqy_regions as r');
        $this->db->join('tabqy_regions_language as rl', "rl.region_language_region_id=r.region_id", "inner");
        $this->db->where('r.country_code', $country_code);
        $this->db->where('rl.region_language_language_code', $_SESSION['lang_code']);
        $query = $this->db->get();
        // print_r($query);
        $data['related_regions'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

   

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: related_locations
      DESCRIPTION: List of locations by related zone
     * *********************************** */

    Public function related_locations($ci_id){

         $this->db->flush_cache();
         $this->db->select('lang.location_language_location_name, lang.location_language_language_code,loc.*');
         $this->db->from('tabqy_locations as loc');
         $this->db->join('tabqy_locations_language as lang','lang.location_language_location_id=loc.location_id','inner');
         $this->db->where('loc.city_code',$ci_id);
         //$this->db->where('loc.is_zone<>',1);
         $this->db->where('lang.location_language_language_code',$_SESSION['lang_code']);
         $this->db->order_by('loc.location_name','ASC');
         $query = $this->db->get();
         $data['related_locations'] = $this->db->result($query);
         //echo $this->db->last_query();
         echo json_encode($data);die;

         }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: edit_view
      DESCRIPTION: To edit the restaurant view
     * *********************************** */

    function edit_view($rest_id) {
        $language_code = $this->url->segment(5);
        $this->db->flush_cache();
        $this->db->from('tabqy_resturantbrand');
        $this->db->join('tabqy_resturantbrand_language', "resturantbrand_language_resturantbrand_id = resturantbrand_id", 'inner');
        $this->db->where('resturantbrand_id', $rest_id);
        $this->db->where('resturantbrand_language_language_code', $language_code);
        $query = $this->db->get();
        $data['edit_view'] = $this->db->result($query);
        // echo $this->db->last_query();
        // die();


        $this->db->flush_cache();
        $this->db->select('cl.cuisine_language_cuisine_name,cl.cuisine_language_language_code,cu.*');
        $this->db->from('tabqy_cuisine as cu');
        $this->db->join('tabqy_cuisine_language as cl', "cl.cuisine_language_cuisine_id=cu.cuisine_id", "inner");
        $this->db->where('cl.cuisine_language_language_code', $_SESSION['lang_code']);
        $query = $this->db->get();
        $data['all_cuisine'] = $this->db->result($query);
        // echo $this->db->last_query();

        //Fetching cities for selected region
        $this->db->flush_cache();
        $this->db->select('city_code');
        $this->db->from('tabqy_regions');
        $this->db->where('region_code', $data['edit_view'][0]['resturantbrand_region']);
        $query = $this->db->get();
        $city_str = $this->db->result($query);
        $selected_cities = explode(',', $city_str[0]['city_code']);
        foreach ($selected_cities as $city_code) {
            $this->db->flush_cache();
            $this->db->select('cl.city_language_name,cl.city_language_language_code,c.*');
            $this->db->from('tabqy_cities as c');
            $this->db->join('tabqy_cities_language as cl', "cl.city_language_city_id=c.city_id", "inner");
            $this->db->where('c.city_code', $city_code);
            $this->db->where('cl.city_language_language_code', $_SESSION['lang_code']);
            $que = $this->db->get();
            $data['related_cities'][] = $this->db->result($que);
        }


        //Fetching Locations from zone
        $this->db->flush_cache();
        $this->db->select('location_code');
        $this->db->from('tabqy_zone');
        $this->db->where('zone_code', $data['edit_view'][0]['resturantbrand_zone']);
        $que = $this->db->get();
        $location_str = $this->db->result($que);

        $this->db->flush_cache();
        $this->db->select('lang.location_language_location_name, lang.location_language_language_code,loc.*');
        $this->db->from('tabqy_locations as loc');
        $this->db->join('tabqy_locations_language as lang','lang.location_language_location_id=loc.location_id','inner');
        $this->db->where('loc.city_code',$data['edit_view'][0]['resturantbrand_city']);
        $this->db->where('lang.location_language_language_code',$_SESSION['lang_code']);
        $this->db->order_by('loc.location_name','ASC');
        $query = $this->db->get();
        $data['related_locations'] = $this->db->result($query);
        echo json_encode($data);
        die;
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 01-05-2018
      FUNCTION: search_index
      DESCRIPTION: To search the restaurent
     * *********************************** */

    Public function search_index() {

        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['success_massage'] = flesh('success_message');
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "admin/resturant/search_index/"; //URL
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
        $this->db->flush_cache();
        if ($search != '0') {

            $this->db->where("resturantbrand_name LIKE '%$search%'");
            $this->db->or_where("resturantbrand_displayname LIKE '%$search%'");
            $this->db->or_where("resturantbrand_email LIKE '%$search%'");
        }
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('tabqy_resturantbrand.resturantbrand_type', 0);
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
        $this->db->select('t1.*,t2.resturantbrand_name as t2resturantbrand_name');
        $this->db->from('tabqy_resturantbrand t2');
        if ($search != '0') {
            $this->db->where("t1.resturantbrand_name LIKE '%$search%'");
            $this->db->or_where("t1.resturantbrand_displayname LIKE '%$search%'");
            $this->db->or_where("t1.resturantbrand_email LIKE '%$search%'");
        }
        $this->db->join('tabqy_resturantbrand t1', 't1.resturantbrand_type=t2.resturantbrand_id', 'RIGHT');
        $this->db->order_by('t1.resturantbrand_id', 'DESC');
        $this->db->where('t1.resturantbrand_type', 0);
        $this->db->limit($item_per_page, $page_position);
        $query = $this->db->get();
        $data['results'] = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('resturantbrand_id,resturantbrand_name');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->order_by('resturantbrand_id', 'DESC');
        $query = $this->db->get();
        $data['resturants'] = $this->db->result($query);
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $data['action'] = "";
        $data['title'] = "Brand";
        $data['heading'] = "Restaurant";
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
        view_loader('admin/resturant/search_index.html', $data);
    }

    /*     * ***********************************
      DEVELOPER: Shivank Mittal
      DATE: 23-05-2018
      FUNCTION: countrywise
      DESCRIPTION: To search the countrywise restaurent
     * *********************************** */

    Public function countrywise() {
     if (!empty($_SESSION['selected_country'])) {
          $country_code = $_SESSION['selected_country'];

        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
            $country_code = $_SESSION['userdata']['user_default_country'];
 
        }
 
        $company_id = $this->url->segment(4);
        $data['session'] = $_SESSION;

        $data['language'] = $this->language;
        $item_per_page = 10; //item to display per page
        $page_url = baseurl . "admin/resturant/index/"; //URL
        $pageno = $this->url->segment(4); // Get page number
        $search = "";
        $this->db->flush_cache();
        $this->db->from('tabqy_resturantbrand t2');
        $this->db->join('tabqy_resturantbrand t1', 't1.resturantbrand_type=t2.resturantbrand_id', 'RIGHT');
        $this->db->order_by('t1.resturantbrand_id', 'DESC');
        $this->db->where('t1.resturantbrand_country', $country_code);
        $this->db->where('t1.resturantbrand_type !=', 0);
        $this->db->group_by('t2.resturantbrand_id');
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
        $sql = 'select *, (select count(resturantbrand_type) from tabqy_resturantbrand as branch where branch.resturantbrand_type =  tabqy_resturantbrand.resturantbrand_id) as total from tabqy_resturantbrand inner join tabqy_resturantbrand_language on resturantbrand_id = resturantbrand_language_resturantbrand_id where (resturantbrand_id in(select resturantbrand_type from tabqy_resturantbrand where resturantbrand_type !="0" and resturantbrand_country = "' . $country_code . '" group by resturantbrand_type) or resturantbrand_country ="' . $country_code . '" ) and resturantbrand_type =0 and resturantbrand_language_language_code="' . $lang_code . '"';

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

        $sql .= " and resturantbrand_company_id ='" . $company_id . "'";
        $query = $this->db->query($sql);

        $data['results'] = $this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('resturantbrand_id,resturantbrand_name');
        $this->db->from('tabqy_resturantbrand');
        $this->db->where('resturantbrand_type', 0);
        $this->db->order_by('resturantbrand_id', 'DESC');
        $query = $this->db->get();
        $data['resturants'] = $this->db->result($query);
        $data['rest_count'] = $query->rowCount();
        $data['links'] = $this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url, $search);
        $data['page_number'] = $page_number;
        if ($search == '0') {
            $data['searched'] = "";
        } else {
            $data['searched'] = $search;
        }
        $data['search'] = $search;
        $data['country_code'] = $country_code;
        $start = $page_position + 1;
        $data['start'] = $start;
        $data['end'] = ($start + count($data['results']) - 1);


        $this->db->flush_cache();
        $this->db->select('c.country_id,c.country_code,c.country_name,c.country_file, count(r.resturantbrand_id) as Totalrest');
        $this->db->from('tabqy_countries c');
        $this->db->join('tabqy_resturantbrand as r', 'r.resturantbrand_country=c.country_code AND r.resturantbrand_type !=0', 'left');
        $this->db->group_by('c.country_code');
        $this->db->order_by('c.country_id', 'DESC');
        $query = $this->db->get();
        $data['brandcountries'] = $this->db->result($query);
        $data['country_count'] = $query->rowCount();
        $this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);

      $this->db->flush_cache();
      $this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');
      $this->db->from('tabqy_cities as city');
      $this->db->join('tabqy_cities_language as lang','lang.city_language_city_id=city.city_id','inner');
      if (!empty($_SESSION['selected_country'])) {
        $this->db->where('city.country_code',  $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
           $this->db->where('city.country_code',  $_SESSION['userdata']['user_default_country']); 
        }
   $this->db->where('lang.city_language_language_code',$_SESSION['lang_code']);
      $this->db->order_by('city.city_name','ASC');
      $query_c = $this->db->get();
      $data['related_cities'] = $this->db->result($query_c);
       $this->db->last_query();

        
        $this->db->flush_cache();
      $this->db->select('lang.city_language_name, lang.city_language_language_code,city.*');
      $this->db->from('tabqy_cities as city');
      $this->db->join('tabqy_cities_language as lang','lang.city_language_city_id=city.city_id','inner');
          
       if (!empty($_SESSION['selected_country'])) {
        $this->db->where('city.country_code',  $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
           $this->db->where('city.country_code',  $_SESSION['userdata']['user_default_country']); 
        }
   $this->db->where('lang.city_language_language_code',$_SESSION['lang_code']);
      $this->db->order_by('city.city_name','ASC');
      $query_c = $this->db->get();
      $data['related_citiesy'] = $this->db->result($query_c);
        
        
        echo json_encode(view_data('admin/resturant/countrywiseresturant.html', $data));
        die;
        ;
    }
    
       Public function city_locations($ci_id){


	$this->db->flush_cache();
	$this->db->select('lang.location_language_location_name, lang.location_language_language_code,loc.*');
	$this->db->from('tabqy_locations as loc');
	$this->db->join('tabqy_locations_language as lang','lang.location_language_location_id=loc.location_id','inner');
	$this->db->where('loc.city_code',$ci_id);
	//$this->db->where('loc.is_zone<>',1);
	$this->db->where('lang.location_language_language_code',$_SESSION['lang_code']);
	$this->db->order_by('loc.location_name','ASC');
	$query = $this->db->get();
	$data['city_locations'] = $this->db->result($query);
        //echo $this->db->last_query();
	echo json_encode($data);die;

	}


}

?> 
