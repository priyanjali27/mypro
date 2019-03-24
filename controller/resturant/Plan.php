<?php

class Plan extends Controller{
    var $language; 
    var $session;
    var $name;
	 public function __construct()
	 {
	   parent::__construct();
	   date_default_timezone_set("Asia/Calcutta");
	   require "check_login.php";
           $this->privillage = $this->privillageRes();
           if ($this->privillage['brandstatus'] ==1) {
                redirect('resturant/company/brand');
            }       
       
        } 
       /*************************************
		DEVELOPER: Priyanjali Agarwal  
		DATE: 01-05-2018
		FUNCTION: index
		DESCRIPTION: list of plan model
	   *************************************/
	public function index(){ 
            $data['session'] = $_SESSION;
            $company_id      = $data['session']['resturant_userdata']['user_company_id'];
            $data['language']= $this->language;
            $data['title']   = "Resturant";
            $data['heading'] = "Plan";
            $data['action']  ='list';
            $data['success'] = flesh('success');
            $data['error']   = flesh('error');
            $item_per_page   = 10; //item to display per page
            $page_url        = baseurl."resturant/plan/index/"; //URL
            $pageno          = $this->url->segment(4); // Get page number
            
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
            $this->db->select('a.*, p.*');
            $this->db->from('tabqy_company_plan_assign a');
            $this->db->join('tabqy_plan as p', 'a.plan_id=p.plan_id', 'left');
            $this->db->where('company_id', $company_id);
            $query = $this->db->get();
            $data['plan_count'] = $query->rowCount();
            $selected_plans = $this->db->result($query);

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
               // echo "<pre>"; print_r($data['selected_plan_data']);exit;
                view_loader('resturant/plan/index.html',$data);	                
            }		
            }
}
