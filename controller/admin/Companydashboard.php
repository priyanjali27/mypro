<?php

class Companydashboard extends Controller {

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
      DEVELOPER: Shivank Mittal
      DATE:  23-05-2018
      FUNCTION:  index()
      DESCRIPTION:
     * *********************************** */

    public function index() {
        $_SESSION['company_id'] = $this->url->segment(4);
        $data['title'] = "Company Dashboard";
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;

        // $data['company_id']=$this->url->segment(4);
        //  echo $_SESSION['company_id'];
        $this->db->flush_cache();
        $this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');
        $this->db->from('tabqy_countries as co');
        $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
        $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
        $query_c = $this->db->get();
        $data['all_countries'] = $this->db->result($query_c);
        $data['privillage'] = $this->privillage;



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
        
        view_loader('admin/company/companydashboard.html', $data);
    }

}

?>