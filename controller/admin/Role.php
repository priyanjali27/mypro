<?php 
class Role extends Controller{
		
	var $language; 
	var $session;
	var $name;
	public function __construct()
    {
		
        parent::__construct();
		if (is_logged_in()==FALSE)
        {
            redirect('admin/dashboard');
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
	//index function roll defned user
	function index(){
		
		//var_dump($this->validation);
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['title']	 = "User";
		$data['heading'] = "Role";
		$data['action']  ='list';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$item_per_page   = 10; //item to display per page
		$page_url        = baseurl."admin/role/index/"; //URL
		$pageno=$this->url->segment(4); // Get page number
			$this->validation->validate("search","Search","required");
		if($this->validation->validation_check()== FALSE){
			$data['error_msg']=$this->validation->error_msg;
			$data['set_data']=$_POST;
		}
		if(isset($_POST['search']) && $_POST['search'] !="")
		{
		$search = trim($_POST['search']);
		}
		elseif(!empty($this->url->segment(5))){
			$search = str_replace("%20",' ',($this->url->segment(5))?$this->url->segment(5):'0');
		}else{
			$search=0;
		}
		
		if($search!='0'){
			$this->db->flush_cache();
			$this->db->where("user_username LIKE '%$search%'");
			$this->db->or_where("user_name LIKE '%$search%'");
			$this->db->or_where("user_email LIKE '%$search%'");
		}
		
		$this->db->where('tabqy_user.user_role',1);
		$this->db->from('tabqy_user');
	$this->db->join('tabqy_userprivilege as c',"c.userprivilege_controllers_id = tabqy_user.user_id","inner");
		$get_total_rows = $this->db->count_all_results();
  
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
	$this->db->select('a.*,b.*,c.*');
	$this->db->from('tabqy_userprivilege as a');
	$this->db->join('tabqy_user as b',"b.user_id = a.userprivilege_user_id","inner");
	$this->db->join('tabqy_controllers as c',"c.controllers_id = a.userprivilege_controllers_id","inner");
	if($search!='0'){
			$this->db->where("b.user_username LIKE '%$search%'");
			$this->db->or_where("b.user_name LIKE '%$search%'");
			$this->db->or_where("b.user_email LIKE '%$search%'");
		}
	
	$this->db->group_by("a."."userprivilege_user_id");

	$this->db->limit($item_per_page,$page_position);
	$query = $this->db->get();
	$data['list_datas']=$this->db->result($query);

	$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
	$data['page_number']=$page_number;
		if($search=='0'){
			$data['searched']="";
        }else{
		$data['searched']=$search;	
		}
		$data['search']=$search;
		
		$start=$page_position+1;
		$data['start']=$start;
		$data['end']=($start+count($data['list_datas'])-1);

		$data['user_lists']  = $this->user_list();
		$data['controller_lists']  = $this->controller_list();
		
		view_loader('admin/role/index.html',$data);
	}
	
	function add(){
		
	        $user_id      = $_POST['user_username'];
			$adds         = isset($_POST['add']) ? $_POST['add']:array();
			$edits        = isset($_POST['edit']) ? $_POST['edit']:array();
			$deletes      = isset($_POST['delete']) ? $_POST['delete']:array();
			$views      = isset($_POST['view']) ? $_POST['view']:array();
			$this->validation->validate('user_username','username', 'required');            
			if($this->validation->validation_check() == true){                
				if(!empty($adds) || !empty($edits) || !empty($deletes) || !empty($views)){	
					$this->db->where('userprivilege_user_id',$user_id);
					$this->db->delete('tabqy_userprivilege');
					if(!empty($adds)){
						foreach($adds as $add){
							 $add_data = array(
							 "userprivilege_user_id"=>$user_id,
							 "userprivilege_controllers_id"=>$add,
							 "userprivilege_add"=>'1',
							 );
							 $this->db->insert("tabqy_userprivilege",$add_data);
						}
					}
					if(!empty($edits)){
						foreach($edits as $edit){
							
							$count_edit = $this->count_privilege($edit,$user_id);
							
							if($count_edit >0)
							{
								 $edit_data = array("userprivilege_edit"=>'1',);
								 $this->edit($edit_data,$edit,$user_id);
							}
							else{
								 $edit_data = array(
								 "userprivilege_user_id"=>$user_id,
								 "userprivilege_controllers_id"=>$edit,
								 "userprivilege_edit"=>'1',
							 );
							 
							  $this->db->insert("tabqy_userprivilege",$edit_data);
							}
							
						}
					}
					
					if(!empty($deletes)){
						foreach($deletes as $delete){
							
							$count_delete = $this->count_privilege($delete,$user_id);
							
							//echo $count_delete; exit;
							if($count_delete >0)
							{
								 $delete_data = array("userprivilege_delete"=>'1',);
								 $this->edit($delete_data,$delete,$user_id);
							}
							else{
								 $delete_data = array(
								 "userprivilege_user_id"=>$user_id,
								 "userprivilege_controllers_id"=>$delete,
								 "userprivilege_delete"=>'1',
							 );
							 
							  $this->db->insert("tabqy_userprivilege",$delete_data);
							}
							
						}	
					}
					
						if(!empty($views)){
						foreach($views as $view){
							
							$count_view = $this->count_privilege($view,$user_id);
							
							//echo $count_delete; exit;
							if($count_view >0)
							{
								 $view_data = array("userprivilege_delete"=>'1',);
								 $this->edit($view_data,$view,$user_id);
							}
							else{
								 $view_data = array(
								 "userprivilege_user_id"=>$user_id,
								 "userprivilege_controllers_id"=>$view,
								 "userprivilege_view"=>'1',
							 );
							 
							  $this->db->insert("tabqy_userprivilege",$view_data);
							}
							
						}	
					}

					
            $data['error']="false";
			$data['msg']="true";
			$data['success_message']="Role added successfully";
			echo json_encode($data);die;					
				}else{
			$error="Atlest one field are checked"; 
			$data['error']="true";
			$data['msg']="true";
			$data['success_message']=$error;
			echo json_encode($data);die;
						
			}				            
			
		}
		
		
		
	}
	
	
	
	function update(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$id = $this->url->segment(4);
		$data['title'] = "Role";
		$data['heading'] = "Role";
		$data['action'] = 'edit';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$data['edit_data']  = $this->ed_data($id);
		$data['user_lists']  = $this->user_list();
		$data['controller_lists']  = $this->controller_list();
		foreach($data['edit_data']['privilege'] as $checkdata){
			
			$checkdatas[$checkdata['userprivilege_controllers_id']] = $checkdata['userprivilege_user_id'];
			$checkdatas[$checkdata['userprivilege_controllers_id']."_add"] = $checkdata['userprivilege_add'];
			$checkdatas[$checkdata['userprivilege_controllers_id']."_edit"] = $checkdata['userprivilege_edit'];
			$checkdatas[$checkdata['userprivilege_controllers_id']."_delete"] = $checkdata['userprivilege_delete'];
				$checkdatas[$checkdata['userprivilege_controllers_id']."_view"] = $checkdata['userprivilege_view'];
			
		}
		
		$data['checkdata'] = $checkdatas;
		
		
		if(isset($_POST['Save']))
		{    
			//print_r($_POST); exit;
	        $user_id      = $_POST['username'];
			$adds         = isset($_POST['add']) ? $_POST['add']:array();
			$edits        = isset($_POST['edit']) ? $_POST['edit']:array();
			$deletes      = isset($_POST['delete']) ? $_POST['delete']:array();
			$views      = isset($_POST['view']) ? $_POST['view']:array();
			$this->validation->validate('username','username', 'required');            
			if($this->validation->validation_check() == true){                
				if(!empty($adds) || !empty($edits)  || !empty($deletes)|| !empty($views)){	
					$this->db->where('userprivilege_user_id',$user_id);
					$this->db->delete('tabqy_userprivilege');
					if(!empty($adds)){
						foreach($adds as $add){
							 $add_data = array(
							 "userprivilege_user_id"=>$user_id,
							 "userprivilege_controllers_id"=>$add,
							 "userprivilege_add"=>'1',
							 );
							 $this->db->insert("tabqy_userprivilege",$add_data);
						}
					}
					if(!empty($edits)){
						foreach($edits as $edit){
							
							$count_edit = $this->count_privilege($edit,$user_id);
							
							if($count_edit >0)
							{
								 $edit_data = array("userprivilege_edit"=>'1',);
								 $this->edit($edit_data,$edit,$user_id);
							}
							else{
								 $edit_data = array(
								 "userprivilege_user_id"=>$user_id,
								 "userprivilege_controllers_id"=>$edit,
								 "userprivilege_edit"=>'1',
							 );
							 
							  $this->db->insert("tabqy_userprivilege",$edit_data);
							}
							
						}
					}
					
					if(!empty($deletes)){
						foreach($deletes as $delete){
							
							$count_delete = $this->count_privilege($delete,$user_id);
							
							//echo $count_delete; exit;
							if($count_delete >0)
							{
								 $delete_data = array("userprivilege_delete"=>'1',);
								 $this->edit($delete_data,$delete,$user_id);
							}
							else{
								 $delete_data = array(
								 "userprivilege_user_id"=>$user_id,
								 "userprivilege_controllers_id"=>$delete,
								 "userprivilege_delete"=>'1',
							 );
							 
							  $this->db->insert("tabqy_userprivilege",$delete_data);
							}
							
						}	
					}
					
						if(!empty($views)){
						foreach($views as $view){
							
							$count_view = $this->count_privilege($view,$user_id);
							
							//echo $count_delete; exit;
							if($count_view >0)
							{
								 $view_data = array("userprivilege_view"=>'1',);
								 $this->edit($view_data,$view,$user_id);
							}
							else{
								 $view_data = array(
								 "userprivilege_user_id"=>$user_id,
								 "userprivilege_controllers_id"=>$view,
								 "userprivilege_view"=>'1',
							 );
							 
							  $this->db->insert("tabqy_userprivilege",$view_data);
							}
							
						}	
					}

					set_flesh('success',"Role Update Successfully");
					redirect('admin/role'); 					               
				}else{
					set_flesh("error","Atlest one field are checked"); 
				}				
			}				            
			
		}
		
		view_loader('admin/role/edit.html',$data);
		
	}
	
	
	
	function view(){
		
		$id = $this->url->segment(4);
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['title'] = "Role";
		$data['heading'] = "Role";
		$data['action'] = 'edit';
		$data['success'] = flesh('success');
		$data['error']   = flesh('error');
		$data['edit_data']  = $this->ed_data($id);
		$data['user_lists']  = $this->user_list();
		$data['controller_lists']  = $this->controller_list();
		foreach($data['edit_data']['privilege'] as $checkdata){
			
			$checkdatas[$checkdata['userprivilege_controllers_id']] = $checkdata['userprivilege_user_id'];
			$checkdatas[$checkdata['userprivilege_controllers_id']."_add"] = $checkdata['userprivilege_add'];
			$checkdatas[$checkdata['userprivilege_controllers_id']."_edit"] = $checkdata['userprivilege_edit'];
			$checkdatas[$checkdata['userprivilege_controllers_id']."_delete"] = $checkdata['userprivilege_delete'];
			$checkdatas[$checkdata['userprivilege_controllers_id']."_view"] = $checkdata['userprivilege_view'];
		}
		
		$data['checkdata'] = $checkdatas;
		
		view_loader('admin/role/view.html',$data);
	}	
	
	
	
	public function delete($id){

		$del = $this->delete_role($id);
		die;
	}
	
	
	
	
	
	function user_list(){
		
		$this->db->select('*');
		$this->db->from('tabqy_userprivilege');
		$rs = $this->db->get();
		$userprivillege = $this->db->result($rs);
		$uid = array();
		if($userprivillege){
			foreach($userprivillege as $user_data)
			{
				$uid[] = $user_data['userprivilege_user_id'];
			}
		}
		$this->db->from('tabqy_user');
		$this->db->where("user_role",'1');
		if(!empty($uid)){
			$this->db->where_not_in("user_id",$uid);
		}
		$rs1 = $this->db->get();
		$user = $this->db->result($rs1);
		return $user;
		
	}
	
	/*=======
	 function controller_list 
	 table :-- controllers 
	 working :-- fetching record of all row		 
	 return type array 
	 date 03/14/2017
	 written by pravin 
	=====*/
	
	public function controller_list(){
	 
	 $this->db->where("controllers_status",'1');
	 $rs = $this->db->get("tabqy_controllers");
	 return $this->db->result($rs);		 
	}
	
	
	/*=======
	 function count_privilege
	 parameter :-- controllerid,userid
	 table :-- userprivilege 
	 working :-- counting record of matched row		 
	 return type int 
	 date 03/15/2017
	 written by pravin 
	=====*/
	public function count_privilege($cid,$userid)
	{
		$this->db->from('tabqy_userprivilege');
		$this->db->where("userprivilege_user_id",$userid);
		$this->db->where("userprivilege_controllers_id",$cid);
		return $count = $this->db->count_all_results();
	
	}
	
	
	/*=======
	 function edit 
	 parameter :-- edit_data,controllerid,userid
	 table :-- userprivilege 
	 working :-- updating record of matched row		 
	 return type int 
	 date 03/15/2017
	 written by pravin 
	=====*/
	public function edit($edit_data,$cid,$userid){
		$this->db->where("userprivilege_user_id",$userid);
		$this->db->where("userprivilege_controllers_id",$cid);
		return $this->db->update("tabqy_userprivilege",$edit_data);
	}
	
	
	/*=======
	 function ed_data 
	 parameter :-- id 
	 table :-- userprivilege 
	 working :-- fetching record of matched row		 
	 return type array 
	 date 03/15/2017
	 written by pravin 
	=====*/
	public function ed_data($id)
	{
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$this->db->select('*');
		$this->db->from('tabqy_userprivilege');
		$this->db->where("userprivilege_user_id",$id);
		$rs = $this->db->get();
		$data['privilege'] = $this->db->result($rs);
		
		$this->db->where("user_id",$id);
		$this->db->select("user_id,user_username");
		$this->db->from('tabqy_user');
		$rs1 = $this->db->get();
		$data['user'] =  $this->db->result($rs1);
		
		return $data;
	}
	
	/*=======
	 function update 
	 parameter :--id 
	 table :-- user 
	 working :-- delete record from table	 
	 return type int
	 date 03/14/2017
	 written by pravin 
	=====*/
	public function delete_role($id){
		 $this->db->where("userprivilege_user_id",$id);
		 $del = $this->db->delete('tabqy_userprivilege');
		 return $del;
	}
	
	
	

}


?>