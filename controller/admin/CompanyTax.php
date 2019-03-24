
<?php
class CompanyTax extends Controller{
	var $language; 
	var $session;
	var $name;
	var $get_data;
	public function __construct()
    {
      parent::__construct();
	  if (is_logged_in()==FALSE)
        {
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
        $this->db->order_by('tabqy_language.language_id','DESC');
        $query = $this->db->get();
        $this->language=$this->db->result($query);
        $this->db->flush_cache();
        $this->db->select('tabqy_user.user_name');
        $this->db->from('tabqy_user');
        $userid=$_SESSION['userdata']['user_id'];
        $this->db->where('tabqy_user.user_id', $userid);
        $user = $this->db->get();
        $name = $this->db->result($user);
        $_SESSION['userdata']['user_name']=$name[0]['user_name'];
        if(empty($_SESSION['lang_code'])) {
                $_SESSION['lang_code']="en";
                include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
        } else{
                include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
        }

    }

/*************************************
 DEVELOPER: Priyanjali  
 DATE:  02-05-2018
 FUNCTION:  index()
 DESCRIPTION: tax list
*************************************/
    
    public function index(){ 
    	$company_id=$this->url->segment(4);
		 $_SESSION['company_id'] = $company_id;
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/companyTax/index/"; //URL
                $pageno		 = $this->url->segment(5); // Get page number
                $sort 		 = 'id';
		
                $data['company_id'] = $company_id;
		$search = '0';
		$this->db->from('tabqy_taxes');
		$this->db->where('tabqy_taxes.tax_company_id',$company_id);
		$this->db->where('tabqy_taxes.tax_status','1');
		$get_total_rows  = $this->db->count_all_results(); //Total no. of values
		if(isset($pageno)){ //Get page number 
			$page_number = filter_var($pageno, FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
		if(!is_numeric($page_number)){
			die('Invalid page number!');} //incase of invalid page number
		}else{
			$page_number = 1; //if there's no page number, set it to 1
		}
		$total_pages = ceil($get_total_rows/$item_per_page);
		$page_position = (($page_number-1) * $item_per_page); //get starting position to fetch the records
		
		$this->db->flush_cache();
		$this->db->select('tx.*,co.country_name');
		$this->db->from('tabqy_taxes as tx');
		$this->db->join('tabqy_countries as co', "co.country_code= tx.tax_country", "inner");
		$this->db->where('tx.tax_company_id',$company_id);
		$this->db->where('tx.tax_status','1');
		


       if (!empty($_SESSION['selected_country'])) {
        $this->db->where('tx.tax_country',  $_SESSION['selected_country']);
        }
        if (!empty($_SESSION['userdata']['user_default_country'])) {
           $this->db->where('tx.tax_country',  $_SESSION['userdata']['user_default_country']); 
        }
 
		$this->db->order_by('tx.tax_'.$sort,'DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		//echo"<pre>";print_r($data['results']);die;
		
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$this->db->flush_cache();
		$this->db->select('tabqy_countries.*');
		$this->db->from('tabqy_countries');
		$this->db->where('tabqy_countries.country_status','1');
		$query_c = $this->db->get();
		$data['related_country'] = $this->db->result($query_c);
		
		$data['title']="Tax";
		$data['heading']="Tax";
		$data['page_number']=$page_number;
		if($search=='0'){
			$data['searched']="";
		}else{
		$data['searched']=$search;	
		}
		$data['search']=$search;
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['results'])-1);
		
				$this->db->flush_cache();

		$this->db->select('l.country_language_country_name,l.country_language_language_code,co.*');

		$this->db->from('tabqy_countries as co');
                $this->db->join('tabqy_countries_language as l',"l.country_language_country_id=co.country_id","inner");
		$this->db->where('l.country_language_language_code',$_SESSION['lang_code']);

		$this->db->order_by('co.country_name','ASC');

		$query_c = $this->db->get();

		$data['all_countries'] = $this->db->result($query_c);

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

         $data['privillage'] = $this->privillage;
		view_loader('admin/company/company_tax.html',$data);		

	}
    
     /*************************************
	  DEVELOPER: Priyanjali  
	  DATE:  02-05-2018
	  FUNCTION: add_tax()
	  DESCRIPTION: add tax
	*************************************/

    public function add_tax(){
        //privillage checking and user validating 
        if ($_SESSION['userdata']['user_role'] != 0) {
            
            if ($this->privillage['add'] != 1) {
                $data['error']           = "permission";
                $data['msg']             = "false";
                $data['success_message'] = "You don't have permission to perform this action";
                echo json_encode($data);
                exit();
            }
        }
        //privillage checking and user validating  end here.
    	$company_id= $_POST['tax_company_id'];
        $this->validation->validate("tax_percent","Tax percent","required");	
            if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
			$data['error']="true";
			echo json_encode($data);die;
		}else{
			$regx = '/^([+-]?[1-9]\d*|0)$/';
            if(!preg_match($regx, $_POST['tax_percent'])){
                    $data['error_msg']['tax_percent']="Enter integer value";
                    $data['set_data']=$_POST;
                    $data['error']="true";
                    echo json_encode($data);die;
            }
        $country_code=$_POST['tax_country'];
        $check_data = $this->check_countrywise_tax($country_code,$company_id);
        if(!empty($check_data)){
            $this->update_countrywise_tax($country_code,$company_id);
        }
        $data['send_data']=$_POST;			
     	$data['send_data']['tax_status'] = 1;
        $data['send_data']['tax_country'] = $_POST['tax_country'];
        $data['send_data']['tax_company_id'] = $company_id;
        $data['send_data']['tax_created'] = date('Y-m-d H:i:s');
        $this->db->insert('tabqy_taxes', $data['send_data']);
        $data['error']="false";
		$data['msg']="true";
		$data['success_message']="Tax Added Successfully";
		echo json_encode($data);die;
		}
    }
    
    /*************************************
	  DEVELOPER: Priyanjali  
	  DATE:  02-05-2018
	  FUNCTION:  view_countrywise_tax(country_code,company_id)
	  DESCRIPTION: view all tax countrywise
	*************************************/

    public function view_countrywise_tax($id){
		$this->db->flush_cache();
		$this->db->select('tabqy_taxes.tax_country,tabqy_taxes.tax_company_id');
		$this->db->from('tabqy_taxes');
		$this->db->where('tabqy_taxes.tax_status','1');
		$this->db->where('tabqy_taxes.tax_id',$id);
		$query_c = $this->db->get();
		$tabqy_taxes = $this->db->row($query_c);
	
		if($tabqy_taxes){
		$company_id=$tabqy_taxes['tax_company_id'];
		$country_code=$tabqy_taxes['tax_country'];
		$this->db->flush_cache();
		$this->db->select('tabqy_taxes.*,tabqy_countries.country_name');
		$this->db->from('tabqy_taxes');
		$this->db->join('tabqy_countries', "tabqy_countries.country_code=tabqy_taxes.tax_country", "inner");
		$this->db->where('tabqy_taxes.tax_company_id',$company_id);
		$this->db->where('tabqy_taxes.tax_country',$country_code);
		$this->db->where('tabqy_taxes.tax_status','0');
		$query_d = $this->db->get();
		$commissions = $this->db->result($query_d);
		echo json_encode($commissions);die;
		}
		
		 
	}

    /*************************************
	  DEVELOPER: Priyanjali  
	  DATE:  02-05-2018
	  FUNCTION:  check_countrywise_tax(country_code,company_id)
	  DESCRIPTION: check tax countrywise
	*************************************/

	Public function check_countrywise_tax($country_code,$company_id){
            $this->db->flush_cache();
            $this->db->select('tax_id');
            $this->db->from('tabqy_taxes');
            $this->db->where('tabqy_taxes.tax_country',$country_code);
            $this->db->where('tabqy_taxes.tax_company_id',$company_id);
            $query_c = $this->db->get();
            $resturantbrand= $this->db->row($query_c);
            return $resturantbrand;
        }

    /*************************************
	  DEVELOPER: Priyanjali  
	  DATE:  02-05-2018
	  FUNCTION:  update_countrywise_tax(country_code,company_id)
	  DESCRIPTION: check tax countrywise
    *************************************/

	Public function update_countrywise_tax($country_code,$company_id){
		$data=array('tax_status'=>'0');
		$this->db->flush_cache();
		$this->db->where('tabqy_taxes.tax_country',$country_code);
		$this->db->where('tabqy_taxes.tax_company_id',$company_id);
		$this->db->update('tabqy_taxes', $data);
		//echo $this->db->last_query();
		return $data;
    }


	 /*************************************
	  DEVELOPER: Priyanjali  
	  DATE:  02-05-2018
	  FUNCTION:  delete_tax()
	  DESCRIPTION: delete tax
	*************************************/

	Public function delete_tax($tax_id){
            	//privillage checking and user validating 
                if ($_SESSION['userdata']['user_role'] != 0) {

                    if ($this->privillage['delete'] != 1) {
                        $data['error']           = "true";
                        $data['msg']             = "false";
                        $data['success_message'] = "You don't have permission to perform this action";
                        echo json_encode($data);
                        exit();
                    }
                }
                //privillage checking and user validating  end here.
 		$this->db->where('tax_id', $tax_id);
                $this->db->delete('tabqy_taxes'); 
                die;
    }

	/*************************************
	  DEVELOPER: Priyanjali  
	  DATE:  02-05-2018
	  FUNCTION:  edit_tax(tax_id)
	  DESCRIPTION: edit tax
	*************************************/

	Public function edit_tax($tax_id, $company_id){
            //privillage checking and user validating 
                    if($_SESSION['userdata']['user_role'] != 0 ){ 

                    if($this->privillage['edit'] != 1 ){
                    $data['error']="permission";
                    $data['msg']="false";
                    $data['success_message']="You don't have permission to perform this action";
                    echo json_encode($data);
                    exit();
                    }
                    } 
            //privillage checking and user validating  end here.
		$this->validation->validate("tax_country","Country name","required");
		$this->validation->validate("tax_name","Tax name","required");
		$this->validation->validate("tax_percent","Tax percent","required");
		$this->validation->validate("tax_price","Tax price","required");
		if($this->validation->validation_check()== FALSE){
			    $data['error_msg']=$this->validation->error_msg;
			    $data['set_data']= $_POST;
			    $data['error']="true";
			    echo json_encode($data);die;
		}else{
			$regx = '/^[-+]?[0-9]*\.?[0-9]*$/';
		    if(!preg_match($regx, $_POST['tax_percent'])){
		        $data['error_msg']['tax_percent1']="Enter integer or decimal value";
				$data['set_data']=$_POST;
				$data['error']="true";
				echo json_encode($data);die;
				}
		    	unset($_POST['submit']);
				$data['set_data']= $_POST;
				$country_code=$data['set_data']['tax_country'];
				$check_data=$this->check_countrywise_tax($country_code,$company_id);
		        if(!empty($check_data)){
		          $this->update_countrywise_tax($country_code,$company_id);
		        }
		        $data['send_data']=$_POST;			
		     	$data['send_data']['tax_status'] = 1;
				$data['send_data']['tax_country'] =$country_code;
				$data['send_data']['tax_company_id'] =$company_id;
		        $data['send_data']['tax_created'] = date('Y-m-d H:i:s');
		        $this->db->insert('tabqy_taxes', $data['send_data']);
						$this->db->where('tax_id', $tax_id);
						//$this->db->update('tabqy_taxes', $data['set_data']);
				        $data['error']="false";
				    	$data['msg']="true";
						$data['success_message']="Your row Updated Successfully";
						echo json_encode($data);die;
		}		
    }
}
