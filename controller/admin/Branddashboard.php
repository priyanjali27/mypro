<?php

class Branddashboard extends Controller {

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
        //print_r($this->privillage);

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

    /************************************
      DEVELOPER: Shivank Mittal
      DATE:  29-05-2018
      FUNCTION:  index()
      DESCRIPTION:
    *************************************/

    public function index() {

        if(empty($this->url->segment(4)) || empty($this->url->segment(5)) || empty($this->url->segment(6)))
        {
                $last_url="admin/company";
                redirect($last_url);
        }
        $company_id=$this->url->segment(6);
        $restaurant_id=$this->url->segment(4);
        $country_id=$this->url->segment(5);
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['title'] = "Brand Dashboard";
        
        $data['restaurant_id'] = $restaurant_id; 
        $data['country_id'] = $country_id;
        $data['company_id'] = $company_id;

       if (!empty($_SESSION['selected_country'])) {
         $country_code = $_SESSION['selected_country'];
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
          $country_code = $_SESSION['userdata']['user_default_country']; 
        } 

        $lang_code = $_SESSION['lang_code'];

        $data['privillage'] = $this->privillage;





        $this->db->select('b.* , bl.*');
        $this->db->from('tabqy_resturantbrand as b');
        $this->db->join('tabqy_resturantbrand_language as bl', "bl.resturantbrand_language_resturantbrand_id    =b.resturantbrand_id", "inner");
        $this->db->where('b.resturantbrand_id', $restaurant_id);
        $this->db->where('bl.resturantbrand_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['brand'] = $this->db->row($query_c);
       $cuisine_id= $data['brand']['resturantbrand_cuisine'];
        
       


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
        $this->db->join('tabqy_cuisine_language',"tabqy_cuisine_language.cuisine_language_cuisine_id = tabqy_cuisine.cuisine_id", 'inner');
        $this->db->where('tabqy_cuisine.cuisine_id', $cuisine_id);
        $this->db->where('tabqy_cuisine_language.cuisine_language_language_code', $_SESSION['lang_code']);
        $query             = $this->db->get();
        $data['cuisine_details'] = $this->db->row($query);
        

        // $this->db->flush_cache();
        // $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        // $this->db->from('tabqy_countries as co');
        // $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        // $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        // $query_c = $this->db->get();
        // $data['all_countries'] = $this->db->result($query_c);
        
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries_language as l');
        $this->db->join('tabqy_countries as co', "co.country_id=l.country_language_country_id", "inner");
        $this->db->where('co.country_code',  $country_code);
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['country'] = $this->db->row($query_c);
        
        
  
        view_loader('admin/brands/branddashboard.html', $data);


     }




     }  
  



?>