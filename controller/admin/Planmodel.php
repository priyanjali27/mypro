<?php
/* Class For Model Assign */

class Planmodel extends Controller{
    var $language; 
	var $session;
	var $name;
		public function __construct()
			{
			parent::__construct();
			date_default_timezone_set("Asia/Calcutta");
			if (is_logged_in()==FALSE)
			 {
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
		$this->db->order_by('tabqy_language.language_id','DESC');
		$query = $this->db->get();
		$this->language=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('tabqy_user.user_name');
		$this->db->from('tabqy_user');
		$userid=$_SESSION['userdata']['user_id'];
		$this->db->where('tabqy_user.user_id', $userid);
		$user = $this->db->get();
		$name=$this->db->result($user);
		$_SESSION['userdata']['user_name']=$name[0]['user_name'];
		if(empty($_SESSION['lang_code'])) {
		$_SESSION['lang_code']="en";
		include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		} else{
		include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		} 
		}
	  /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 01-05-2018
		FUNCTION: index
		DESCRIPTION: list of plan model
	   *************************************/		
	public function index(){ 
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['title']	 = "Plan Management";
		$data['heading'] = "Point";
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/plan/index/"; //URL
		$pageno			 = $this->url->segment(4); // Get page number
		$sort 			 = 'plan_model_id';
		$this->db->flush_cache();
		$this->db->select('*');
		$this->db->from('tabqy_plan_model');
		$this->db->order_by($sort,'DESC');
		$query = $this->db->get();	
		$data['results']=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('*');
		$this->db->from('tabqy_plan');
		$this->db->order_by('plan_id','DESC');
		$query = $this->db->get();
		$data['resultss']=$this->db->result($query);
		view_loader('admin/plan/index.html',$data);		
			}
	  /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 30-04-2018
		FUNCTION: assign_model
		DESCRIPTION: For assign a plan model
	    *************************************/
	Public function assign_model($plan_id){	
	 if ($_SESSION['userdata']['user_role'] != 0) {
		 if ($this->privillage['add'] != 1) {
		 $data['error']           = "permission";
		 $data['msg']             = "false";
		 $data['success_message'] = "You don't have permission to perform this action";
		 echo json_encode($data);
		 exit();
		  }
		}
		$data['title']="Plan Management";
		$data['heading']="Plan";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action'] = 'edit_detail';
		$sort 			 = 'plan_detail_id';
		$data['plan_id'] = $plan_id;	
		$this->db->select('*');
		$this->db->from('tabqy_plan');
		$this->db->order_by('plan_name','ASC');
		$query = $this->db->get();
		$data['all_plans'] =$this->db->result($query);		
        /* Fetching Plan Details */
        $this->db->select('*');
		$this->db->from('tabqy_plan_detail');
		$this->db->where("plan_detail_plan_id",$plan_id);
		$query = $this->db->get();
		$data['plandetaildatas'] =$this->db->result($query);
		$planmodelID = array();
		$planmodelqunatity = array();
		if(!empty($data['plandetaildatas']))
		{
			foreach($data['plandetaildatas'] as $plandetaildata){
				$planmodelID[] = $plandetaildata['plan_detail_model_id'];
				$planmodelqunatity[] = $plandetaildata['plan_detail_quantity'];
			}
		}
		$data['planmodelID'] = $planmodelID;	
		$data['planmodelqunatity'] = $planmodelqunatity;
		
		/* Fetching All Models */
		$this->db->select('m.*, d.plan_detail_quantity as pmq,d.plan_detail_plan_id');
		$this->db->from('tabqy_plan_model as m');
		$this->db->join('tabqy_plan_detail as d','d.plan_detail_model_id = m.plan_model_id AND d.plan_detail_plan_id='.$plan_id,'left');
		$this->db->order_by('m.plan_model_id','asc');
		$query = $this->db->get();
		$data['model'] = $this->db->result($query);
		//print_r($data['model']);die();
		
		
        if(isset($_POST['update']) && !empty($_POST)){		
			$this->db->where("plan_detail_plan_id",$plan_id);
			$this->db->delete('tabqy_plan_detail');
			$quantity = $_POST['quantity'];
			$plan      = $_POST['plan'];
			$plan_model  =$_POST['plan_model'];			 
          foreach ($plan_model as $key => $value) {
				 $add_data = array(
						'plan_detail_plan_id'       =>$plan,
						'plan_detail_model_id'      =>$value,
						'plan_detail_quantity'      =>$quantity[$value],
				 );
				
				$this->db->where("plan_detail_plan_id",$plan_id);
				$edit = $this->db->insert('tabqy_plan_detail',$add_data);
			}
		if($edit){					
				redirect('admin/plan');					  
				}
				else{
					 $data['set_data']  = $_POST;
					set_flesh('error', 'You do not have permission to perform this action');
					$data['error']= flesh('error');
					view_loader('admin/plan/edit.html',$data);
				}
		}else{
			view_loader('admin/plan/assign_model_to_plan.html',$data);
		}
    }
	    /*************************************
		DEVELOPER: Shivank Mittal  
		DATE: 30-04-2018
		FUNCTION: edit_model_details
		DESCRIPTION: For edit the model details
	    *************************************/
 	Public function edit_model_details(){	
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
		$data['title']="Plan Management";
		$data['heading']="cms";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['action'] = 'edit';
		$sort 			 = 'plan_model_id';
		
        $this->db->flush_cache();
        $this->db->select('tabqy_plan_model.*');
		$this->db->from('tabqy_plan_model');
		$this->db->order_by('tabqy_plan_model.'.$sort);
		$query = $this->db->get();
		$data['results'] =$this->db->result($query);
		if(isset($_POST['update']) && !empty($_POST)){
			$quantity = $_POST['quantity'];
			$price    =$_POST['price'];
            $plan_ids  =$_POST['plan_id'];
         foreach ($plan_ids as $key => $value) {
	          $new_data = array(
			      'plan_model_quantity' =>$quantity[$key],
				  'plan_model_price'   =>$price[$key],
				);
			$this->db->where("plan_model_id",$value);
			$edit = $this->db->update('tabqy_plan_model',$new_data);
			//die();
		 }
		  if($edit){
					redirect('admin/plan');					  
					  }
					  else{
                     $data['set_data']  = $_POST;
					set_flesh('error', 'You do not have permission to perform this action');
					$data['error']= flesh('error');
					view_loader('admin/plan/edit.html',$data);
				}
		  }else{
			view_loader('admin/plan/edit.html',$data);
		}	
	} 
}