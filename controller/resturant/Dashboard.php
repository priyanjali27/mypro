<?php 
// Dashboard controller 
class Dashboard extends Controller{
	var $language; 
	var $session; 
	var $name;
	public function __construct()
    {
      parent::__construct();
	  require "check_login.php";
          $this->privillage = $this->privillageRes();
          if ($this->privillage['brandstatus'] == 1) {
                redirect('resturant/company/brand');
          }

    }

	/*************************************
		  DEVELOPER: Meenu  
		  DATE:  27/04/2018
		  FUNCTION:  dashboard()
		  DESCRIPTION:Dashboard company/staff,brand/Staff & Restaurant Admin/staff
	*************************************/

	function index(){
		$data['session']    = $_SESSION;                
		$data['title']      = "Dashboard";
		$data['heading']    = "Dashboard";
		$data['action']     = 'Dashboard';
		$data['success']    = flesh('success');
		$data['language']   = $this->language;
                $company_country    = $_SESSION['company_country'];                
                $company_id = $_SESSION['company_id'];
                $id = $this->url->segment(4);
                    $this->db->flush_cache();
                    $this->db->distinct();
                    $this->db->select('co.country_code,co.country_status,l.country_language_country_name,l.country_language_language_code');
                    $this->db->from('tabqy_resturantbrand as res');
                    $this->db->join('tabqy_countries as co', 'res.resturantbrand_country=co.country_code','left');
                    $this->db->join('tabqy_countries_language as l', "l.country_language_country_id=co.country_id", "inner");
                    $this->db->where('res.resturantbrand_company_id', $company_id);
                    $this->db->where('l.country_language_language_code', $_SESSION['lang_code']);
                    $country_que = $this->db->get();
                    $data['all_countries'] = $this->db->result($country_que);
                    //echo "<pre>"; print_r($data['all_countries']);exit;
             
		$this->db->flush_cache();
		$this->db->from('tabqy_user');
		$data['get_total_users'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->where('tabqy_resturantbrand.resturantbrand_type',0);
		$this->db->from('tabqy_resturantbrand');
		$data['get_total_resturants'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->where('tabqy_user.user_role',2);
		$this->db->or_where('tabqy_user.user_role',3);
		$this->db->from('tabqy_user');
		$data['get_total_tbusers'] = $this->db->count_all_results();
		$this->db->flush_cache();
		$this->db->from('tabqy_language');
		$data['get_total_languages'] = $this->db->count_all_results();
                
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
                
                $this->db->flush_cache();
                $this->db->select('*')->from('tabqy_company')->where('company_id',$company_id);
                $cr_query = $this->db->get();
                $data['company_data'] = $this->db->row($cr_query);
                $data['privillage']  = $this->privillage;
		view_loader('resturant/company/dashboard.html',$data);
	}
        
        Public function change_country(){
            if (($this->privillage['brandstatus']=='true') and ($_SESSION['resturant_userdata']['user_role']!='2')){
		$last_url = "resturant/company/brand";
            }else{
                $last_url = "resturant/dashboard";
            }
            $country_code=$this->url->segment(4);
            if($country_code)
            $_SESSION['selected_country']=$country_code;
            redirect($last_url);
	}
        Public function change_country_popup(){
		$last_url = "resturant/company/brand";
                $_SESSION['selected_country'] = $_POST['countries'];
		redirect($last_url);
	}
	}
	?>