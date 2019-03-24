<?php 
// Company settings controller 
class Companysetting extends Controller{
	var $language; 
	var $session;
	var $name;
	var $get_data;
	public function __construct()
    {
      parent::__construct();
	  require "check_login.php";
          $this->privillage = $this->privillageRes();
           if ($this->privillage['brandstatus'] ==1) {
                redirect('resturant/company/brand');
            }else{                
                if($_SESSION['resturant_userdata']['user_role']!=2){
                    redirect('resturant/dashboard');
                }
            }
    }
    
     /*************************************
		  DEVELOPER: Meenu  
		  DATE:  27/04/2018
		  FUNCTION:  get_data()
		  DESCRIPTION: Get detail data in session
	*************************************/

	function get_data(){

        $data['user_id']=$_SESSION['resturant_userdata']['user_id'];
		$data['restaurant_id']=$_SESSION['resturant_userdata']['restaurant_id'];
		$data['company_id']=$_SESSION['resturant_userdata']['user_company_id'];
		$data['language_code']=$_SESSION['lang_code'];
                return $data;
    }

	

   Public function company_address(){
        $sessiondata=$this->get_data();
    	$company_id=$sessiondata['company_id'];
        $data['session'] = $_SESSION;
        $data['language'] = $this->language;
        $data['success_massage']= flesh('success_message');
        $data['action']="edit";
        $data['title']="User";
        $data['heading']="Edit Company Profile";
        $language_code = $sessiondata['language_code'];
        $user_id=$sessiondata['user_id'];
        $data['user_id']=$user_id;
        
        $this->db->flush_cache();
        $this->db->select('*')->from('tabqy_company')->where('company_id',$company_id);
        $cr_query = $this->db->get();
        $data['company_data'] = $this->db->row($cr_query);            

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
        $this->db->select('*');
        $this->db->from('tabqy_company');
        $this->db->join('tabqy_company_language','tabqy_company_language.company_language_company_id=tabqy_company.company_id','inner');
        $this->db->where('tabqy_company.company_id',$company_id);
        $this->db->where('tabqy_company_language.company_language_language_code',$language_code);
        $query_c = $this->db->get();
        $company= $this->db->row($query_c);
        
        $data['company']=$company;
        $data['language_code']=$language_code;
        view_loader('resturant/company/my-profile.html',$data);	
	}

     Public function update(){
     	$sessiondata=$this->get_data();
    	$id=$sessiondata['company_id'];
	    $this->validation->validate("company_name","Name","required");
		$this->validation->validate("company_address","Address","required");
		$this->validation->validate("company_email","Email","required");
		$this->validation->validate("company_phone","Phone","required");
		$this->validation->validate("company_logo","Logo","required");
		$this->validation->validate("company_code","Company Code","required");
		$this->validation->validate("company_vat_no","Company VAT No.","required");
		
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			$language_code = $_POST['language_code'];
            $company_name     = $_POST['company_name'];
            $address       = $_POST['company_address'];
			
			unset($_POST['submit']);
			unset($_POST['language_code']);
			
            if ($_SESSION['lang_code'] != $language_code) {
                unset($_POST['company_name']);
                unset($_POST['company_address']);
            }
			$data['send_data']= $_POST;
			unset($data['send_data']['company_id']);

			$data['error_msg'] = "";

			if(!empty($data['error_msg'])){
				$data['error']="true";
				$data['msg']="false";
				echo json_encode($data);die;
		    }
			//$data['send_data']['company_last_updated'] = date('Y-m-d H:i:s');
			
			$this->db->where('company_id',$id);
			$this->db->update('tabqy_company', $data['send_data']);
			
			$edit_data_language = array(
                "company_language_name" => $company_name,
                "company_language_address" => $address,
                "company_language_edit" => '1'
            );            
            
            $this->db->where('company_language_company_id', $id);
            $this->db->where('company_language_language_code', $language_code);
            $this->db->update('tabqy_company_language', $edit_data_language);

			$data['error']="false";
			$data['msg']="true";
			$data['success_message']="Company Details Updated successfully";
			echo json_encode($data);
			die;
		}
	}
		
}
?>