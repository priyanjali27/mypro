<?php 

// Category controller uses for superadmin,tabqy employees,customer 
class Menus extends Controller{
	var $language; 
	var $session;
	var $name;
	public function __construct()
    {
         parent::__construct();
		if (is_logged_in_resturant()== FALSE)
        {
		
            redirect('resturant/user');
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
		$userid=$_SESSION['resturant_userdata']['user_id'];
		$this->db->where('tabqy_user.user_id', $userid);
		$user = $this->db->get();
		$name=$this->db->result($user);
		$_SESSION['resturant_userdata']['user_name']=$name[0]['user_name'];
		if(empty($_SESSION['lang_code'])) {
			$_SESSION['lang_code']="en";
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		} else{
			include_once "core/lang/lang_".$_SESSION['lang_code'].".php";
		}
    }
	//show list of users .
	Public function items(){
		$data['title']="Menu";
		$data['heading']="Menu item";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$this->db->flush_cache();
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		$this->db->select('tabqy_categories.category_id,tabqy_categories.category_name');
		$this->db->from('tabqy_categories');
			$this->db->where("tabqy_categories.category_brand_id",$restaurant_id);
		$this->db->order_by('tabqy_categories.category_created','DESC');
		$this->db->limit(9,0);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('tabqy_item.*');
		$this->db->from('tabqy_item');
		$this->db->where("tabqy_item.item_brand_id",$restaurant_id);
		$this->db->where("tabqy_item.item_status",'1');
		$this->db->order_by('tabqy_item.item_created','DESC');
		$this->db->limit(3,0);
		$query = $this->db->get();
		//print_r($query);die;
		$data['items']=$this->db->result($query);
		view_loader('resturant/items/item-list.html',$data);
	}
	//show list of users .
	Public function additems(){
		//echo "<pre>";
		unset($_SESSION['combo_array']);
		$data['title']="Menu";
		$data['heading']="Menu combo item";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		// List of categories
		$this->db->select('tabqy_categories.category_id,tabqy_categories.category_name');
		$this->db->from('tabqy_categories');
		$this->db->where("tabqy_categories.category_brand_id",$restaurant_id);
		$this->db->order_by('tabqy_categories.category_id','DESC');
		$query = $this->db->get();
		$data['categories']=$this->db->result($query);
		// List of resturants
		$this->db->flush_cache();
		$this->db->select('t1.*');
		$this->db->from('tabqy_resturantbrand t1');
		$this->db->where('t1.resturantbrand_type',$restaurant_id);
		$this->db->order_by('t1.resturantbrand_id','DESC');
 	    $query = $this->db->get();
		$data['resturants']=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('t1.*');
		$this->db->from(' tabqy_quantity t1');
		$this->db->where('t1.quantity_status',1);
		$this->db->order_by('t1.quantity_id','DESC');
 	    $query = $this->db->get();
		$data['quantities']=$this->db->result($query);
		if(isset($_SESSION['associative_array'])&& !empty($_SESSION['associative_array'])){
		$data['associative_array']=$_SESSION['associative_array'];
		}else{
		$data['associative_array']="";	
		}
		if(isset($_SESSION['portion_array'])&& !empty($_SESSION['portion_array'])){
		$data['portion_array']=$_SESSION['portion_array'];
		}else{
		$data['portion_array']="";	
		}
		
		
	
		if(isset($_POST['individual'])){

		$data['action']="";
		$data['title']="Menu";
		$data['heading']="List Menu";
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		$item=array();
		$item=$_POST;
		$item_table['item_name']=$item['item_name'];
		$item_table['item_code']=$item['item_code'];
		$item_table['item_category_id']=$item['item_category_id'];
		$item_table['item_brand_id']=$restaurant_id;
		$item_table['item_description']=$item['item_description'];
		$name=$_FILES['item_file']['name'];
		$type=$_FILES['item_file']['type'];
		$tmp_name=$_FILES['item_file']['tmp_name'];
		$size=$_FILES['item_file']['size'];
	    $filename  = basename($_FILES['item_file']['name']);
         $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name       = time()."_".$_FILES['item_file']['name'];
	   $allowedExts = array("gif", "jpeg", "jpg", "png");
		
		if ((($_FILES["item_file"]["type"] == "image/gif")
		|| ($_FILES["item_file"]["type"] == "image/jpeg")
		|| ($_FILES["item_file"]["type"] == "image/jpg")
		|| ($_FILES["item_file"]["type"] == "image/png"))
		&& in_array($extension, $allowedExts)){
				if (move_uploaded_file($_FILES['item_file']['tmp_name'], "upload/images/".$file_name)) 
				{
				  $item_table['item_image']=$file_name;
				}
		}
		$item_table['item_defaultprice']=$item['item_default_price'];
		 $item_table['item_type']='0';
		if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
	       $item_table['item_branchprice']='1';
		}
		if(isset($item['item_hide_pos'])&& isset($item['item_hide_web']))
		{
			 $item_table['item_visiblity']='3';
		}elseif(isset($item['item_hide_pos'])){
			 $item_table['item_visiblity']='2';
		}else{
			 $item_table['item_visiblity']='1';
		}
		
		$item_table['item_available_from']=date('H:i:s',strtotime($item['sliderstarttime']));
		$item_table['item_available_to']=date('H:i:s',strtotime($item['sliderendtime']));
        $item_table['item_created']=date('Y-m-d H:i:s');
		$item_table['item_updated']=date('Y-m-d H:i:s');
		if($item['item_status'] == '1'){
			$item_table['item_status']  = '1';
		}else{
			$item_table['item_status']  = '0';
		}	
		
		$item_id=$this->db->insert('tabqy_item', $item_table);
        // save data in tabqy_itempriceofbranch table
		if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
			$item_resturant_prices=array();
			foreach($item['item_resturant_price'] as $key=>$item_resturant_price){
				 		$item_resturant_prices['itempriceofbranch_branch_id']= $key	;
						$item_resturant_prices['itempriceofbranch_item_id']= $item_id;
						$item_resturant_prices['itempriceofbranch_price']=$item_resturant_price;
						$item_resturant_prices['itempriceofbranch_status']='1';
			$this->db->insert('tabqy_itempriceofbranch', $item_resturant_prices);
			}
		}
		// save data in tabqy_discount table
		if(isset($item['discount_price']) && !empty($item['discount_price'])){
            $tabqy_discount=array();
			
			$tabqy_discount['discount_item_id']=$item_id;
			$tabqy_discount['discount_from']= !empty($item['datepicker_from_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
			$tabqy_discount['discount_to']= !empty($item['datepicker_to_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
			$tabqy_discount['discount_price']=$item['item_discount_price'];
			$tabqy_discount['discount_status']='1';
            $this->db->insert('tabqy_discount', $tabqy_discount);
		}
		// save data in tabqy_item_displayonbranch table
		if(isset($item['locations']) && !empty($item['locations'])){
			$locations=array();
			foreach($item['locations'] as $key=>$location){
				 		$locations['displayonbranch_item_id']= $item_id;
						 $locations['displayonbranch_branch_id']=$location;
						 $locations['displayonbranch_status']='1';
			$this->db->insert('tabqy_item_displayonbranch', $locations);
			}
		}
		// save data in tabqy_item_associated table
		if(isset($_SESSION['associative_array']) && !empty($_SESSION['associative_array'])){
			$associativearray=array();
			foreach($_SESSION['associative_array'] as $associative_array){
				 	$associativearray['associated_name']=$associative_array['name']; 
					$associativearray['associated_value']= $associative_array['price']; 
					$associativearray['associated_preadded']=$associative_array['checked']; 
					$associativearray['associated_status']= '1'; 
					$associativearray['associated_item_id']=$item_id;
					$this->db->insert('tabqy_item_associated', $associativearray);
			}
		}
		// save data in tabqy_itemportion table
		if(isset($_SESSION['portion_array']) && !empty($_SESSION['portion_array'])){
			$portionsarray=array();
			foreach($_SESSION['portion_array'] as $portions_array){
				 	$portionsarray['itemportion_portion_id']=$portions_array['name']; 
					$portionsarray['itemportion_price']= $portions_array['price']; 
					$portionsarray['itemportion_status']= '1'; 
					$portionsarray['itemportion_item_id']=$item_id;
					$this->db->insert('tabqy_itemportion', $portionsarray);
			}
		}
		// save data in tabqy_itemavlableday table
		if(isset($item['item_days']) && !empty($item['item_days'])){
			$itemdays=array();
			foreach($item['item_days'] as $item_days){
				 	$itemdays['itemavlableday_day']=$item_days; 
					$itemdays['itemavlableday_item_id']=$item_id;
					$itemdays['itemavlableday_status']='1';
					$this->db->insert('tabqy_itemavlableday', $itemdays);
			}
		}
		unset($_SESSION['portion_array']);
		 unset($_SESSION['associative_array']);
		 set_flesh('success_massage', 'New item added successfully');
		
			redirect('resturant/menus/');
		}
		if(isset($_POST['combo'])){
		$combin_a=array_merge($_POST['select_portion'],$_POST['quantities']);
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		$item=array();
		$item=$_POST;
		$item_table['item_name']=$item['item_name'];
		$item_table['item_code']=$item['item_code'];
		$item_table['item_category_id']=$item['item_category_id'];
		$item_table['item_brand_id']=$restaurant_id;
		$item_table['item_description']=$item['item_description'];
		$name=$_FILES['item_file']['name'];
		$type=$_FILES['item_file']['type'];
		$tmp_name=$_FILES['item_file']['tmp_name'];
		$size=$_FILES['item_file']['size'];
	    $filename  = basename($_FILES['item_file']['name']);
         $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name       = time()."_".$_FILES['item_file']['name'];
	   $allowedExts = array("gif", "jpeg", "jpg", "png");
		
		if ((($_FILES["item_file"]["type"] == "image/gif")
		|| ($_FILES["item_file"]["type"] == "image/jpeg")
		|| ($_FILES["item_file"]["type"] == "image/jpg")
		|| ($_FILES["item_file"]["type"] == "image/png"))
		&& in_array($extension, $allowedExts)){
				if (move_uploaded_file($_FILES['item_file']['tmp_name'], "upload/images/".$file_name)) 
				{
				  $item_table['item_image']=$file_name;
				}
		}
		$item_table['item_defaultprice']=$item['item_default_price'];
		 $item_table['item_type']='1';
		if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
	       $item_table['item_branchprice']='1';
		}
		if(isset($item['item_hide_pos'])&& isset($item['item_hide_web']))
		{
			 $item_table['item_visiblity']='3';
		}elseif(isset($item['item_hide_pos'])){
			 $item_table['item_visiblity']='2';
		}else{
			 $item_table['item_visiblity']='1';
		}
		
		$item_table['item_available_from']=date('H:i:s',strtotime($item['sliderstarttime']));
		$item_table['item_available_to']=date('H:i:s',strtotime($item['sliderendtime']));
        $item_table['item_created']=date('Y-m-d H:i:s');
		$item_table['item_updated']=date('Y-m-d H:i:s');
		if($item['item_status'] == '1'){
			$item_table['item_status']  = '1';
		}else{
			$item_table['item_status']  = '0';
		}	
		
		$item_id=$this->db->insert('tabqy_item', $item_table);
        // save data in tabqy_itempriceofbranch table
		if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
			$item_resturant_prices=array();
			foreach($item['item_resturant_price'] as $key=>$item_resturant_price){
				 		$item_resturant_prices['itempriceofbranch_branch_id']= $key	;
						$item_resturant_prices['itempriceofbranch_item_id']= $item_id;
						$item_resturant_prices['itempriceofbranch_price']=$item_resturant_price;
						$item_resturant_prices['itempriceofbranch_status']='1';
			$this->db->insert('tabqy_itempriceofbranch', $item_resturant_prices);
			}
		}
		// save data in tabqy_discount table
		if(isset($item['discount_price']) && !empty($item['discount_price'])){
            $tabqy_discount=array();
			
			$tabqy_discount['discount_item_id']=$item_id;
			$tabqy_discount['discount_from']= !empty($item['datepicker_from_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
			$tabqy_discount['discount_to']= !empty($item['datepicker_to_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
			$tabqy_discount['discount_price']=$item['item_discount_price'];
			$tabqy_discount['discount_status']='1';
            $this->db->insert('tabqy_discount', $tabqy_discount);
		}
		// save data in tabqy_item_displayonbranch table
		if(isset($item['locations']) && !empty($item['locations'])){
			$locations=array();
			foreach($item['locations'] as $key=>$location){
				 		$locations['displayonbranch_item_id']= $item_id;
						 $locations['displayonbranch_branch_id']=$location;
						 $locations['displayonbranch_status']='1';
			$this->db->insert('tabqy_item_displayonbranch', $locations);
			}
		}
		// save data in tabqy_item_associated table
		if(isset($_POST['select_portion']) && !empty($_POST['select_portion'])){
			$selectportion=array();
			foreach($_POST['select_portion'] as $key=>$combo_array){
				$selectportion['comboitem_comboitem_id']=$key; 
					$selectportion['comboitem_portion_id']=$combo_array;
					$selectportion['comboitem_quantity']=$_POST['quantities'][$key];					
					$selectportion['comboitem_status ']= '1'; 
					$selectportion['comboitem_item_id']=$item_id;
					$this->db->insert('tabqy_comboitem', $selectportion);
				
			}
		}
		
		// save data in tabqy_itemavlableday table
		if(isset($item['item_days']) && !empty($item['item_days'])){
			$itemdays=array();
			foreach($item['item_days'] as $item_days){
				 	$itemdays['itemavlableday_day']=$item_days; 
					$itemdays['itemavlableday_item_id']=$item_id;
					$itemdays['itemavlableday_status']='1';
					$this->db->insert('tabqy_itemavlableday', $itemdays);
			}
		}
		unset($_SESSION['combo_array']);
		set_flesh('success_massage', 'New item added successfully');
		redirect('resturant/menus/');
		}
		
		
		view_loader('resturant/items/add-item-combo.html',$data);
	}
	Public function session_associate(){
		
		//unset($_SESSION['associative_array']);die;
		$associative_array=$_POST;
		if(isset($_SESSION['associative_array']) && !empty($_SESSION['associative_array'])){
			array_push($_SESSION['associative_array'],$associative_array);
		}else{
			$_SESSION['associative_array'][0]=$associative_array;
		}
		//print_r($_SESSION['associative_array']);die;
		echo json_encode($_SESSION['associative_array']);die;
	}
	Public function session_remove(){
		//print_r($_SESSION['associative_array']);die;
		$id=$_POST['data_id'];
		$newdata=array();
		if(isset($_SESSION['associative_array']) && !empty($_SESSION['associative_array'])){
		
		foreach($_SESSION['associative_array'] as $k=>$associate){
		if($k==$id){
			
		}else
			$newdata[]=$associate;
		}
		
		unset($_SESSION['associative_array']);
		}
		if(!empty($newdata)){
	    $_SESSION['associative_array']=$newdata;
		//print_r($_SESSION['associative_array']);die;
		echo json_encode($_SESSION['associative_array']);die;
		}else{
		echo "0";die;
		}
	}
	
	Public function session_portion(){
		
		//unset($_SESSION['portion_array']);die;
		$portion_array=$_POST;
		$arraitems=array();
		if(isset($_SESSION['portion_array']) && !empty($_SESSION['portion_array'])){
		foreach($_SESSION['portion_array'] as $k=>$portions){
			$arraitems[]=$portions['name'];
		}
		
			
				if(in_array($portion_array['name'],$arraitems)){
				}else{
			   array_push($_SESSION['portion_array'],$portion_array);
				
				}
		}else{
			$_SESSION['portion_array'][0]=$portion_array;
		}
		//print_r($_SESSION['portion_array']);die;
		echo json_encode($_SESSION['portion_array']);die;
	}
	Public function session_portion_remove(){
		//print_r($_POST);
		//print_r($_SESSION['portion_array']);die;
		$id=$_POST['data_id'];
		$newdata=array();
		if(isset($_SESSION['portion_array']) && !empty($_SESSION['portion_array'])){
		
		foreach($_SESSION['portion_array'] as $k=>$associate){
		if($k==$id){
			
		}else
			$newdata[]=$associate;
		}
		
		unset($_SESSION['portion_array']);
		}
		if(!empty($newdata)){
	    $_SESSION['portion_array']=$newdata;//print_r($_SESSION['portion_array']);die;
		echo json_encode($_SESSION['portion_array']);die;
		}else{
		echo "0";die;
		}
	}
	
	Public function sessionreset(){
		unset($_SESSION['portion_array']);
		unset($_SESSION['associative_array']);
		die;
	}


		Public function index(){
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$data['success_massage']= flesh('success_message');
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
	 	$item_per_page      = 10; //item to display per page
		$page_url           = baseurl."resturant/menus/index/"; //URL
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
			$this->db->where("tabqy_item.item_name LIKE '%$search%'");
		}
		$this->db->where("tabqy_item.item_brand_id",$restaurant_id);
		$this->db->from('tabqy_item');
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
		$this->db->select('tabqy_item.*,tabqy_categories.category_name');
		$this->db->from('tabqy_item');
		if($search!='0'){
			$this->db->where("tabqy_item.item_name LIKE '%$search%'");
		}
		$this->db->join('tabqy_categories','tabqy_item.item_category_id=tabqy_categories.category_id','inner');
			
		$this->db->where("tabqy_item.item_brand_id",$restaurant_id);
		
		$this->db->order_by('tabqy_item.item_id','DESC');
		$this->db->limit($item_per_page,$page_position);
		$query = $this->db->get();
		$data['results']=$this->db->result($query);
		$data['links']=$this->pagination->paginate($item_per_page, $page_number, $get_total_rows, $total_pages, $page_url,$search);
		$data['action']="";
		$data['title']="Menu";
		$data['heading']="List Menu";
		$data['user_id']="";
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
		view_loader('resturant/items/list.html',$data);
	}
	Public function delete($id){
		$this->db->where('item_id', $id);
        $this->db->delete('tabqy_item');
        $this->db->where('itempriceofbranch_item_id', $id);
        $this->db->delete('tabqy_itempriceofbranch'); 
        $this->db->where('discount_item_id', $id);
        $this->db->delete('tabqy_discount'); 	
         $this->db->where('displayonbranch_item_id', $id);
        $this->db->delete('tabqy_item_displayonbranch'); 
		$this->db->where('associated_item_id', $id);
		$this->db->delete('tabqy_item_associated'); 
		$this->db->where('itemportion_item_id', $id);
		$this->db->delete('tabqy_itemportion'); 		
		$this->db->where('itemavlableday_item_id', $id);
		$this->db->delete('tabqy_itemavlableday'); 
		die;
	}
	
   
	
	Public function delete_edit($id){
        $this->db->where('itempriceofbranch_item_id', $id);
        $this->db->delete('tabqy_itempriceofbranch'); 
        $this->db->where('discount_item_id', $id);
        $this->db->delete('tabqy_discount'); 	
         $this->db->where('displayonbranch_item_id', $id);
        $this->db->delete('tabqy_item_displayonbranch'); 
		$this->db->where('associated_item_id', $id);
		$this->db->delete('tabqy_item_associated'); 
		$this->db->where('itemportion_item_id', $id);
		$this->db->delete('tabqy_itemportion'); 		
		$this->db->where('itemavlableday_item_id', $id);
		$this->db->delete('tabqy_itemavlableday'); 
	}
  	Public function delete_combo_edit($id){
        $this->db->where('itempriceofbranch_item_id', $id);
        $this->db->delete('tabqy_itempriceofbranch'); 
        $this->db->where('discount_item_id', $id);
        $this->db->delete('tabqy_discount'); 	
         $this->db->where('displayonbranch_item_id', $id);
        $this->db->delete('tabqy_item_displayonbranch'); 
		$this->db->where('comboitem_item_id', $id);
		$this->db->delete('tabqy_comboitem'); 
		
		$this->db->where('itemavlableday_item_id', $id);
		$this->db->delete('tabqy_itemavlableday'); 
	}
  
  
	Public function edit(){
		$data['action']  =$this->url->segment(3);
		$item_id =$this->url->segment(4);
		$editdata = $this->item_edit_data($item_id);
		$data['action_data']=1;
		$displayonbranch = array();
		$branchprice = array();
		if(!empty($editdata['itemportion_data'] )){
			foreach($editdata['itemportion_data'] as $portion){
				$this->db->select('quantity_name');
				$this->db->where('quantity_id',$portion['itemportion_portion_id']);
				$this->db->from('tabqy_quantity');
				$rs_qty = $this->db->get();
				$data_qty = $this->db->row($rs_qty);
			    //echo $this->db->last_query();
				//print_r($data_qty);
				$_SESSION['portion_array'][] = array("name"=>$portion['itemportion_portion_id'],"text"=>$data_qty['quantity_name'],"price"=>$portion['itemportion_price']);
			}
		}
		if(!empty($editdata['associated_data'] )){
			foreach($editdata['associated_data'] as $associated){
			
				$_SESSION['associative_array'][] = array("name"=>$associated['associated_name'],"price"=>$associated['associated_value'],"checked"=>$associated['associated_preadded']);
			}
		}
		$branchprice = array();
		if(!empty($editdata['priceofbranch_data'])){
			foreach($editdata['priceofbranch_data'] as $branchprices){
				
				$branchprice[$branchprices['itempriceofbranch_branch_id']] = $branchprices['itempriceofbranch_price'];
			}
			
			
		}
		if(!empty($editdata['displayonbranch_data'])){
		foreach( $editdata['displayonbranch_data'] as $displayonbranchs){
				
				$displayonbranch[] = $displayonbranchs['displayonbranch_branch_id'];
			}
		}
		$itemavlableday_data = array();
		if(!empty($editdata['itemavlableday_data'])){
			foreach($editdata['itemavlableday_data'] as $itemavlableday_datas){
				$itemavlableday_data[] = $itemavlableday_datas['itemavlableday_day'];
			}
		}
		$data['displayonbranch_data'] 	= $displayonbranch;
		$data['priceofbranch_data']     = $branchprice;
		$data['item_data'] 				= $editdata['item_data'];
		$data['discount_datas'] 		= $editdata['discount_data'][0];
		$data['itemavlableday_data'] 	= $itemavlableday_data;
	    
		//print_r($data['itemavlableday_data']);
		$data['item_name'] = isset($_POST['item_name']) ? $_POST['item_name'] : $editdata['item_data']['item_name'];
		$data['item_code'] = isset($_POST['item_code']) ? $_POST['item_code'] : $editdata['item_data']['item_code'];
		$data['item_category_id'] = isset($_POST['item_category_id']) ? $_POST['item_category_id'] : $editdata['item_data']['item_category_id'];
		
		$data['item_image'] = $editdata['item_data']['item_image'];
		$data['item_description'] = isset($_POST['item_description']) ? $_POST['item_description'] : $editdata['item_data']['item_description'];
		
		$data['item_default_price'] = isset($_POST['item_default_price']) ? $_POST['item_default_price'] : $editdata['item_data']['item_defaultprice'];
		
		$data['item_check_locprices'] = isset($_POST['item_check_locprices']) ? $_POST['item_check_locprices'] : $editdata['item_data']['item_branchprice'];
		
		
		
		
		$data['title']="Menu";
		$data['heading']="Menu combo item";
		$data['session'] = $_SESSION;
		$data['language'] = $this->language;
		$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
		// List of categories
		$this->db->select('tabqy_categories.category_id,tabqy_categories.category_name');
		$this->db->from('tabqy_categories');
		$this->db->where("tabqy_categories.category_brand_id",$restaurant_id);
		$this->db->order_by('tabqy_categories.category_id','DESC');
		$query = $this->db->get();
		$data['categories']=$this->db->result($query);
		// List of resturants
		$this->db->flush_cache();
		$this->db->select('t1.*');
		$this->db->from('tabqy_resturantbrand t1');
		$this->db->where('t1.resturantbrand_type',$restaurant_id);
		$this->db->order_by('t1.resturantbrand_id','DESC');
 	    $query = $this->db->get();
		$data['resturants']=$this->db->result($query);
		$this->db->flush_cache();
		$this->db->select('t1.*');
		$this->db->from(' tabqy_quantity t1');
		$this->db->where('t1.quantity_status',1);
		$this->db->order_by('t1.quantity_id','DESC');
 	    $query = $this->db->get();
		$data['quantities']=$this->db->result($query);
		
				
		if(isset($_SESSION['associative_array'])&& !empty($_SESSION['associative_array'])){
		$data['associative_array']=$_SESSION['associative_array'];
		}else{
		$data['associative_array']="";	
		}
		if(isset($_SESSION['portion_array'])&& !empty($_SESSION['portion_array'])){
		$data['portion_array']=$_SESSION['portion_array'];
		}else{
		$data['portion_array']="";	
		}

		
		
		
		
		if($_POST){
			
			$data['action']="";
			$data['title']="Menu";
			$data['heading']="List Menu";
			$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
			
			$item=array();
			$item=$_POST;
			$item_table['item_name']=$item['item_name'];
			//$item_table['item_code']=$item['item_code'];
			$item_table['item_category_id']=$item['item_category_id'];
			$item_table['item_brand_id']=$restaurant_id;
			$item_table['item_description']=$item['item_description'];
			
			$name=$_FILES['item_file']['name'];
			$type=$_FILES['item_file']['type'];
			$tmp_name=$_FILES['item_file']['tmp_name'];
			$size=$_FILES['item_file']['size'];
			$filename  = basename($_FILES['item_file']['name']);
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			$file_name       = time()."_".$_FILES['item_file']['name'];
		    $allowedExts = array("gif", "jpeg", "jpg", "png");
			
			if ((($_FILES["item_file"]["type"] == "image/gif")
			|| ($_FILES["item_file"]["type"] == "image/jpeg")
			|| ($_FILES["item_file"]["type"] == "image/jpg")
			|| ($_FILES["item_file"]["type"] == "image/png"))
			&& in_array($extension, $allowedExts)){
					if (move_uploaded_file($_FILES['item_file']['tmp_name'], "upload/images/".$file_name)) 
					{
					  unlink("upload/images/".$editdata['item_data']['item_image']);
					  $item_table['item_image']=$file_name;
					}
			}
			
			
			$item_table['item_defaultprice']=$item['item_default_price'];
			 $item_table['item_type']='0';
			if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
			   $item_table['item_branchprice']='1';
			}
			if(isset($item['item_hide_pos'])&& isset($item['item_hide_web']))
			{
				 $item_table['item_visiblity']='3';
			}elseif(isset($item['item_hide_pos'])){
				 $item_table['item_visiblity']='2';
			}else{
				 $item_table['item_visiblity']='1';
			}
			
			$item_table['item_available_from']=date('H:i:s',strtotime($item['sliderstarttime']));
			$item_table['item_available_to']=date('H:i:s',strtotime($item['sliderendtime']));
			$item_table['item_created']=date('Y-m-d H:i:s');
			$item_table['item_updated']=date('Y-m-d H:i:s');
			if($item['item_status'] == '1'){
				$item_table['item_status']  = '1';
			}else{
				$item_table['item_status']  = '0';
			}	
			
			$this->db->where('item_id',$item_id);
			$item_updated =$this->db->update('tabqy_item', $item_table);
			$this->delete_edit($item_id); 
			
			// save data in tabqy_itempriceofbranch table
			if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
				$item_resturant_prices=array();
				foreach($item['item_resturant_price'] as $key=>$item_resturant_price){
							$item_resturant_prices['itempriceofbranch_branch_id']= $key	;
							$item_resturant_prices['itempriceofbranch_item_id']= $item_id;
							$item_resturant_prices['itempriceofbranch_price']=$item_resturant_price;
							$item_resturant_prices['itempriceofbranch_status']='1';
				$this->db->insert('tabqy_itempriceofbranch', $item_resturant_prices);
				}
			}
			// save data in tabqy_discount table
			if(isset($item['item_discount_price']) && !empty($item['item_discount_price'])){
				$tabqy_discount=array();
				
				$tabqy_discount['discount_item_id']=$item_id;
				$tabqy_discount['discount_from']= !empty($item['datepicker_from_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
				$tabqy_discount['discount_to']= !empty($item['datepicker_to_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
				$tabqy_discount['discount_price']=$item['item_discount_price'];
				$tabqy_discount['discount_status']='1';
				$this->db->insert('tabqy_discount', $tabqy_discount);
			}
			// save data in tabqy_item_displayonbranch table
			if(isset($item['locations']) && !empty($item['locations'])){
				$locations=array();
				foreach($item['locations'] as $key=>$location){
							$locations['displayonbranch_item_id']= $item_id;
							 $locations['displayonbranch_branch_id']=$location;
							 $locations['displayonbranch_status']='1';
				$this->db->insert('tabqy_item_displayonbranch', $locations);
				}
			}
			// save data in tabqy_item_associated table
			if(isset($_SESSION['associative_array']) && !empty($_SESSION['associative_array'])){
				$associativearray=array();
				foreach($_SESSION['associative_array'] as $associative_array){
						$associativearray['associated_name']=$associative_array['name']; 
						$associativearray['associated_value']= $associative_array['price']; 
						$associativearray['associated_preadded']=$associative_array['checked']; 
						$associativearray['associated_status']= '1'; 
						$associativearray['associated_item_id']=$item_id;
						$this->db->insert('tabqy_item_associated', $associativearray);
				}
			}
			// save data in tabqy_itemportion table
			if(isset($_SESSION['portion_array']) && !empty($_SESSION['portion_array'])){
				$portionsarray=array();
				foreach($_SESSION['portion_array'] as $portions_array){
						$portionsarray['itemportion_portion_id']=$portions_array['name']; 
						$portionsarray['itemportion_price']= $portions_array['price']; 
						$portionsarray['itemportion_status']= '1'; 
						$portionsarray['itemportion_item_id']=$item_id;
						$this->db->insert('tabqy_itemportion', $portionsarray);
				}
			}
			// save data in tabqy_itemavlableday table
			if(isset($item['item_days']) && !empty($item['item_days'])){
				$itemdays=array();
				foreach($item['item_days'] as $item_days){
						$itemdays['itemavlableday_day']=$item_days; 
						$itemdays['itemavlableday_item_id']=$item_id;
						$itemdays['itemavlableday_status']='1';
						$this->db->insert('tabqy_itemavlableday', $itemdays);
				}
			}
			unset($_SESSION['portion_array']);
			unset($_SESSION['associative_array']);
			set_flesh('success_massage', 'New item added successfully');
			redirect('resturant/menus/');
		}
		
		view_loader('resturant/items/add-item-combo.html',$data);
		
	}
  
  
  
	Public function edit_data(){
			$data['action']  =$this->url->segment(3);
			$item_id =$this->url->segment(4);
			$id =$this->url->segment(4);
			$editdata = $this->item_edit_data($item_id);
			if($_POST){
			unset($_SESSION['combo_array']);
			}
			$data['title']="Menu";
			$data['action']="edit";
			$data['heading']="Menu combo item";
			$data['action_data']=2;
			$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
			$this->db->where("tabqy_item.item_id",$id);
			$this->db->from("tabqy_item");
			$this->db->join('tabqy_categories','tabqy_item.item_category_id=tabqy_categories.category_id','inner');
			$rs_item = $this->db->get();
			$data['item_data'] = $this->db->result($rs_item);

			$this->db->where("itempriceofbranch_item_id",$id);
			$this->db->from("tabqy_itempriceofbranch");
			$rs_price_branch = $this->db->get();
			$data['priceofbranch_data'] = $this->db->result($rs_price_branch);

			$this->db->where("discount_item_id",$id);
			$this->db->from("tabqy_discount");
			$rs_discount_price = $this->db->get();
			$data['discount_data'] = $this->db->result($rs_discount_price);
			$this->db->where("displayonbranch_item_id",$id);
			$this->db->from("tabqy_item_displayonbranch");
			$this->db->join('tabqy_resturantbrand','tabqy_resturantbrand.  resturantbrand_id=tabqy_item_displayonbranch.displayonbranch_branch_id');
			$rs_displayonbranch = $this->db->get();
			$displayonbranch_data = $this->db->result($rs_displayonbranch);
			$data['displayonbranch_data']=$displayonbranch_data;
			if(!empty($displayonbranch_data)){
			$d_array=array();
			foreach($displayonbranch_data as $d_data){
			$d_array[]=$d_data['resturantbrand_id'];
			}
			}
			$data['d_array']=$d_array;
			$this->db->where("itemavlableday_item_id",$id);
			$this->db->from("tabqy_itemavlableday");
			$rs_itemavlableday = $this->db->get();
			$data['itemavlableday_data'] = $this->db->result($rs_itemavlableday);
			if(!empty($data['itemavlableday_data'])){
			$d1_array=array();
			foreach($data['itemavlableday_data'] as $d1_data){
			$d1_array[]=$d1_data['itemavlableday_day'];
			}

			}

			$data['d1_array']=$d1_array;
			$this->db->where("tabqy_comboitem.comboitem_item_id",$id);
			$this->db->from("tabqy_comboitem");
			$this->db->join('tabqy_item','tabqy_item.  item_id=tabqy_comboitem.comboitem_comboitem_id');

			$this->db->join('tabqy_quantity','tabqy_quantity.  quantity_id=tabqy_comboitem.comboitem_portion_id');
		//	$this->db->join('tabqy_itemportion','tabqy_itemportion.  itemportion_id=tabqy_quantity.quantity_id');
			$rs_combo = $this->db->get();
			$combo_items = $this->db->result($rs_combo);
			//unset($_SESSION['combo_array']);die;
			$s_value="";
			$c_value=count($combo_items);
			/*if(isset($_SESSION['combo_array'])){
			$s_value=count($_SESSION['combo_array']);
			}
			if($c_value!=$s_value){
			unset($_SESSION['combo_array']);
			}*/

			foreach($combo_items as $k=>$combo_item){
			$_SESSION['combo_array'][$k]['check_portion']=$combo_item['comboitem_portion_id'];
			$_SESSION['combo_array'][$k]['item_id']=$combo_item['comboitem_comboitem_id'];
			$comboitem_comboitem_id=$combo_item['comboitem_comboitem_id'];
			$this->db->where("tabqy_item.item_id",$comboitem_comboitem_id);
			$this->db->from("tabqy_item");
			$rs_itemportion = $this->db->get();
			$itemportion_data= $this->db->result($rs_itemportion);
			foreach($itemportion_data as $key=>$portion){
			$this->db->flush_cache();
			$this->db->select("tabqy_itemportion.itemportion_portion_id,tabqy_quantity.quantity_name");
			$this->db->from("tabqy_itemportion");
			$this->db->join('tabqy_quantity','tabqy_quantity.quantity_id=tabqy_itemportion. 	itemportion_portion_id');
			$this->db->where("tabqy_itemportion.itemportion_item_id",$portion['item_id']);
			$rs_itemportion1 = $this->db->get();
			$itemportion_data= $this->db->result($rs_itemportion1);
			$itemportion_data1=$itemportion_data;
			}
			$combo_items[$k]['portions']=$itemportion_data1;
			}
			$data['combo_items']=$combo_items;
			//print_r($_SESSION['combo_array']);

			$this->db->select('tabqy_categories.category_id,tabqy_categories.category_name');
			$this->db->from('tabqy_categories');
			$this->db->where("tabqy_categories.category_brand_id",$restaurant_id);
			$this->db->order_by('tabqy_categories.category_id','DESC');
			$query = $this->db->get();
			$data['categories']=$this->db->result($query);
			// List of resturants
			$this->db->flush_cache();
			$this->db->select('t1.*');
			$this->db->from('tabqy_resturantbrand t1');
			$this->db->where('t1.resturantbrand_type',$restaurant_id);
			$this->db->order_by('t1.resturantbrand_id','DESC');
			$query = $this->db->get();
			$data['resturants']=$this->db->result($query);			

			//	print_r($_SESSION['combo_array']);die;


			if($_POST){

			$data['action']="";
			$data['title']="Menu";
			$data['heading']="List Menu";


			$item=array();
			$item=$_POST;
			$item_table['item_name']=$item['item_name'];
			//$item_table['item_code']=$item['item_code'];
			$item_table['item_category_id']=$item['item_category_id'];
			$item_table['item_brand_id']=$restaurant_id;
			$item_table['item_description']=$item['item_description'];

			$name=$_FILES['item_file']['name'];
			$type=$_FILES['item_file']['type'];
			$tmp_name=$_FILES['item_file']['tmp_name'];
			$size=$_FILES['item_file']['size'];
			$filename  = basename($_FILES['item_file']['name']);
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			$file_name       = time()."_".$_FILES['item_file']['name'];
			$allowedExts = array("gif", "jpeg", "jpg", "png");

			if ((($_FILES["item_file"]["type"] == "image/gif")
			|| ($_FILES["item_file"]["type"] == "image/jpeg")
			|| ($_FILES["item_file"]["type"] == "image/jpg")
			|| ($_FILES["item_file"]["type"] == "image/png"))
			&& in_array($extension, $allowedExts)){
			if (move_uploaded_file($_FILES['item_file']['tmp_name'], "upload/images/".$file_name)) 
			{
			unlink("upload/images/".$editdata['item_data']['item_image']);
			$item_table['item_image']=$file_name;
			}
			}


			$item_table['item_defaultprice']=$item['item_default_price'];
			// $item_table['item_type']='1';
			if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
			$item_table['item_branchprice']='1';
			}
			if(isset($item['item_hide_pos'])&& isset($item['item_hide_web']))
			{
			$item_table['item_visiblity']='3';
			}elseif(isset($item['item_hide_pos'])){
			$item_table['item_visiblity']='2';
			}else{
			$item_table['item_visiblity']='1';
			}

			$item_table['item_available_from']=date('H:i:s',strtotime($item['sliderstarttime']));
			$item_table['item_available_to']=date('H:i:s',strtotime($item['sliderendtime']));
			$item_table['item_created']=date('Y-m-d H:i:s');
			$item_table['item_updated']=date('Y-m-d H:i:s');
			if($item['item_status'] == '1'){
			$item_table['item_status']  = '1';
			}else{
			$item_table['item_status']  = '0';
			}	

			$this->db->where('item_id',$item_id);
			$item_updated =$this->db->update('tabqy_item', $item_table);
			$this->delete_combo_edit($item_id); 

			// save data in tabqy_itempriceofbranch table
			if(isset($item['item_resturant_price']) && !empty($item['item_resturant_price'])){
			$item_resturant_prices=array();
			foreach($item['item_resturant_price'] as $key=>$item_resturant_price){
			$item_resturant_prices['itempriceofbranch_branch_id']= $key	;
			$item_resturant_prices['itempriceofbranch_item_id']= $item_id;
			$item_resturant_prices['itempriceofbranch_price']=$item_resturant_price;
			$item_resturant_prices['itempriceofbranch_status']='1';
			$this->db->insert('tabqy_itempriceofbranch', $item_resturant_prices);
			}
			}
			// save data in tabqy_discount table
			if(isset($item['onoffswitch']) && !empty($item['onoffswitch'])){
			$tabqy_discount=array();

			$tabqy_discount['discount_item_id']=$item_id;
			$tabqy_discount['discount_from']= !empty($item['datepicker_from_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
			$tabqy_discount['discount_to']= !empty($item['datepicker_to_discount'])?date("Y-m-d",strtotime($item['datepicker_from_discount'])) : '';
			$tabqy_discount['discount_price']=$item['item_discount_price'];
			$tabqy_discount['discount_status']='1';
			$this->db->insert('tabqy_discount', $tabqy_discount);
			}
			// save data in tabqy_item_displayonbranch table
			if(isset($item['locations']) && !empty($item['locations'])){
			$locations=array();
			foreach($item['locations'] as $key=>$location){
			$locations['displayonbranch_item_id']= $item_id;
			$locations['displayonbranch_branch_id']=$location;
			$locations['displayonbranch_status']='1';
			$this->db->insert('tabqy_item_displayonbranch', $locations);
			}
			}
			if(isset($_POST['select_portion']) && !empty($_POST['select_portion'])){
			$selectportion=array();
			foreach($_POST['select_portion'] as $key=>$combo_array){
			$selectportion['comboitem_comboitem_id']=$key; 
			$selectportion['comboitem_portion_id']=$combo_array;
			$selectportion['comboitem_quantity']=$_POST['quantities'][$key];					
			$selectportion['comboitem_status ']= '1'; 
			$selectportion['comboitem_item_id']=$item_id;
			$this->db->insert('tabqy_comboitem', $selectportion);

			}
			}

			// save data in tabqy_itemportion table

			// save data in tabqy_itemavlableday table
			if(isset($item['item_days']) && !empty($item['item_days'])){
			$itemdays=array();
			foreach($item['item_days'] as $item_days){
			$itemdays['itemavlableday_day']=$item_days; 
			$itemdays['itemavlableday_item_id']=$item_id;
			$itemdays['itemavlableday_status']='1';
			$this->db->insert('tabqy_itemavlableday', $itemdays);
			}
			}
			unset($_SESSION['combo_array']);
			set_flesh('success_massage', 'New item updated successfully');
			redirect('resturant/menus/');
			}

			view_loader('resturant/items/edit-combo.html',$data);
				
	}
  
	/*================
	function : item_edit_data
	table 	 : items and all related
	parameter: item id 
	return   : array
	written  : Pravin kumar 
	date 	 : 23/03/2018
	===============*/
	public function item_edit_data($id){
		$data  = array();
		$this->db->where("item_id",$id);
		$this->db->from("tabqy_item");
		$rs_item = $this->db->get();
		$data['item_data'] = $this->db->row($rs_item);
		
		$this->db->where("itempriceofbranch_item_id",$id);
		$this->db->from("tabqy_itempriceofbranch");
		$rs_price_branch = $this->db->get();
		$data['priceofbranch_data'] = $this->db->result($rs_price_branch);
		
		$this->db->where("discount_item_id",$id);
		$this->db->from("tabqy_discount");
		$rs_discount_price = $this->db->get();
		$data['discount_data'] = $this->db->result($rs_discount_price);
		
		$this->db->where("associated_item_id",$id);
		$this->db->from("tabqy_item_associated");
		$rs_associated = $this->db->get();
		$data['associated_data'] = $this->db->result($rs_associated);
		
		$this->db->where("displayonbranch_item_id",$id);
		$this->db->from("tabqy_item_displayonbranch");
		$rs_displayonbranch = $this->db->get();
		$data['displayonbranch_data'] = $this->db->result($rs_displayonbranch);
		
		$this->db->where("itemavlableday_item_id",$id);
		$this->db->from("tabqy_itemavlableday");
		$rs_itemavlableday = $this->db->get();
		$data['itemavlableday_data'] = $this->db->result($rs_itemavlableday);
		
		$this->db->where("itemportion_item_id",$id);
		$this->db->from("tabqy_itemportion");

		
		$rs_itemportion = $this->db->get();
		$data['itemportion_data'] = $this->db->result($rs_itemportion);
		
		return $data;
	}
  
	Public function view($id){
			$data['session'] = $_SESSION;
			$data['language'] = $this->language;
			$data['success_massage']= flesh('success_message');
			$restaurant_id=$_SESSION['resturant_userdata']['restaurant_id'];
			
			$data['action']="";
			$data['title']="Menu";
			$data['heading']="View Menu";
			$this->db->where("tabqy_item.item_id",$id);
			$this->db->from("tabqy_item");
             $this->db->join('tabqy_categories','tabqy_item.item_category_id=tabqy_categories.category_id','inner');
			$rs_item = $this->db->get();
			$data['item_data'] = $this->db->result($rs_item);

			$this->db->where("itempriceofbranch_item_id",$id);
			$this->db->from("tabqy_itempriceofbranch");
			$rs_price_branch = $this->db->get();
			$data['priceofbranch_data'] = $this->db->result($rs_price_branch);

			$this->db->where("discount_item_id",$id);
			$this->db->from("tabqy_discount");
			$rs_discount_price = $this->db->get();
			$data['discount_data'] = $this->db->result($rs_discount_price);
            if($data['item_data'][0]['item_type']=="0"){
			$this->db->where("associated_item_id",$id);
			$this->db->from("tabqy_item_associated");
			$rs_associated = $this->db->get();
			$data['associated_data'] = $this->db->result($rs_associated);
			$this->db->where("itemportion_item_id",$id);
			$this->db->from("tabqy_itemportion");
			$this->db->join('tabqy_quantity','tabqy_quantity.quantity_id=tabqy_itemportion.itemportion_portion_id');
			$rs_itemportion = $this->db->get();
			$data['itemportion_data'] = $this->db->result($rs_itemportion);
            }else{
				$this->db->where("tabqy_comboitem.comboitem_item_id",$id);
			$this->db->from("tabqy_comboitem");
				$this->db->join('tabqy_item','tabqy_item.  item_id=tabqy_comboitem.comboitem_comboitem_id');
			
				$this->db->join('tabqy_quantity','tabqy_quantity.  quantity_id=tabqy_comboitem.comboitem_portion_id');
				$this->db->join('tabqy_itemportion','tabqy_itemportion.  itemportion_id=tabqy_quantity.quantity_id');
			$rs_combo = $this->db->get();
			$data['combo_items'] = $this->db->result($rs_combo);

			}

			$this->db->where("displayonbranch_item_id",$id);
			$this->db->from("tabqy_item_displayonbranch");
			$this->db->join('tabqy_resturantbrand','tabqy_resturantbrand.  resturantbrand_id=tabqy_item_displayonbranch.displayonbranch_branch_id');
			$rs_displayonbranch = $this->db->get();
			$data['displayonbranch_data'] = $this->db->result($rs_displayonbranch);

			$this->db->where("itemavlableday_item_id",$id);
			$this->db->from("tabqy_itemavlableday");
			$rs_itemavlableday = $this->db->get();
			$data['itemavlableday_data'] = $this->db->result($rs_itemavlableday);

			

			view_loader('resturant/items/view-item.html',$data);
		}
	
	public function check_unique_itemcode(){
			$item_code=$_POST['item_code'];
			$this->db->select('item_id');
			$this->db->from('tabqy_item');
			$this->db->where('tabqy_item.item_code',$item_code);
			$query=$this->db->get();
			$row=$this->db->result($query);
			if(!empty($row)){
				$data['error_code']='true';
				echo json_encode($data);
			}else{
				$data['error_code']='false';
				echo json_encode($data);
			}
			die;
			
		}
		
	
		Public function check_search(){
		    $item_data="";
			$cat_id=$_POST['cat_id'];
			$search=$_POST['search'];
		
			if(!empty($cat_id)){
			$this->db->where("item_category_id",$cat_id);
			}
			if(!empty($search)){
			$this->db->like('item_name',$search);
			}
			$this->db->from("tabqy_item");
			$rs_item = $this->db->get();
			$item_data = $this->db->result($rs_item);
			$arr_items = array();
		//print_r($_SESSION['combo_array']);die;
			if(isset($_SESSION['combo_array']) && !empty($_SESSION['combo_array'])){
				foreach($_SESSION['combo_array'] as $k=>$c){
		    $arr_items[] = $c['item_id'];
				}
			if(!empty($item_data)){
			foreach($item_data as $k=>$idata){
				$item_data[$k]['item_checked']="";
				
				if(in_array($idata['item_id'],$arr_items)){
					$item_data[$k]['item_checked']="checked";
				}
				
			}
			}
			}
			echo json_encode($item_data);die;
			
		}
		
		
	Public function checked_combo(){
		$item_id=$_POST['item_id'];
		$newdata=array();
		$arr_items=array();
		if(isset($_SESSION['combo_array']) && !empty($_SESSION['combo_array'])){
			foreach($_SESSION['combo_array'] as $k=>$c){
		    $arr_items[$k] = $c['item_id'];
			}
			if(in_array($item_id,$arr_items)==FALSE){
				$data['item_id']=$item_id;
				$data['check_portion']="";
				array_push($_SESSION['combo_array'],$data);
				}
			
			   
			
				
		}
		else{
			
			$_SESSION['combo_array'][0]['item_id']=$item_id;
            $_SESSION['combo_array'][0]['check_portion']="";				
		}
		
		
         if(!empty($_SESSION['combo_array'])){
			$combos_array=$_SESSION['combo_array'];
			foreach($combos_array as $key=>$combo_array){
			$this->db->where("tabqy_item.item_id",$combo_array['item_id']);
			$this->db->from("tabqy_item");
			$rs_itemportion = $this->db->get();
			$data['itemportion_data'] = $this->db->result($rs_itemportion);
			foreach($data['itemportion_data'] as $k=>$portion){
			$this->db->flush_cache();
			$this->db->select("tabqy_itemportion.itemportion_portion_id,tabqy_quantity.quantity_name");
			$this->db->from("tabqy_itemportion");
			$this->db->join('tabqy_quantity','tabqy_quantity.quantity_id=tabqy_itemportion. 	itemportion_portion_id');
			$this->db->where("tabqy_itemportion.itemportion_item_id",$portion['item_id']);
			$rs_itemportion1 = $this->db->get();
			$itemportion_data= $this->db->result($rs_itemportion1);
			$data['itemportion_data']=$portion;
			$data['itemportion_data']['portions']=$itemportion_data;
			}
			
			$combos_array[$key]=$data['itemportion_data'];
			$combos_array[$key]['check_portion']=$combo_array['check_portion'];
			}
		   
		    echo json_encode($combos_array);die;
			
		}else{
		echo "0";die;
		}
		}
		
		Public function checked_combos(){
		$item_ids=$_POST['item_id'];
		if(!empty($item_ids)){
			$this->db->where_in("tabqy_itemportion.itemportion_item_id",$item_ids);
			$this->db->from("tabqy_item");
			$this->db->join('tabqy_itemportion','tabqy_itemportion.itemportion_item_id=tabqy_item.item_id');
			$this->db->join('tabqy_quantity','tabqy_quantity.quantity_id=tabqy_itemportion. 	itemportion_portion_id');
			$rs_itemportion = $this->db->get();
			$data['itemportion_data'] = $this->db->result($rs_itemportion);
			echo json_encode($data['itemportion_data']);die;
			
		}else{
		echo "0";die;
		}
		}
		
		
	Public function remove_items_combo(){
		$item_id=$_POST['item_id'];
		$newdata=array();
		//print_r($_SESSION['combo_array']);die;
		if(isset($_SESSION['combo_array']) && !empty($_SESSION['combo_array'])){
			foreach($_SESSION['combo_array'] as $k=>$c){
		      if($item_id==$c['item_id']){
			   unset($_SESSION['combo_array'][$k]);
				}
			}
				
		}
		else{
			
		$_SESSION['combo_array'][0]['item_id']=$item_id;
            $_SESSION['combo_array'][0]['check_portion']="";	
		}
		 if(!empty($_SESSION['combo_array'])){
			$combos_array=$_SESSION['combo_array'];
			foreach($combos_array as $key=>$combo_array){
			$this->db->where("tabqy_item.item_id",$combo_array['item_id']);
			$this->db->from("tabqy_item");
			$rs_itemportion = $this->db->get();
			$data['itemportion_data'] = $this->db->result($rs_itemportion);
			foreach($data['itemportion_data'] as $k=>$portion){
			$this->db->flush_cache();
			$this->db->select("tabqy_itemportion.itemportion_portion_id,tabqy_quantity.quantity_name");
			$this->db->from("tabqy_itemportion");
			$this->db->join('tabqy_quantity','tabqy_quantity.quantity_id=tabqy_itemportion. 	itemportion_portion_id');
			$this->db->where("tabqy_itemportion.itemportion_item_id",$portion['item_id']);
			$rs_itemportion1 = $this->db->get();
			$itemportion_data= $this->db->result($rs_itemportion1);
			$data['itemportion_data']=$portion;
			$data['itemportion_data']['portions']=$itemportion_data;
			}
			
			$combos_array[$key]=$data['itemportion_data'];
			$combos_array[$key]['check_portion']=$combo_array['check_portion'];
			}
		
		    echo json_encode($combos_array);die;
			
		}else{
		echo "0";die;
		}
		}
		Public function set_portions(){
		$item_id=$_POST['item_id'];
        $portion_id=$_POST['portion_id'];
			if(!empty($_SESSION['combo_array'])){
			
			foreach($_SESSION['combo_array'] as $k=>$c){
		    $arr_items[$k] = $c['item_id'];
			}
			if(in_array($item_id,$arr_items)){
				$_SESSION['combo_array'][$k]['check_portion']=$portion_id;
				}else{
					$_SESSION['combo_array'][$k]['check_portion']="";	
				}			
			}
			echo "<pre>";
			print_r($_SESSION['combo_array']);
		die;
		}
	function updateStatus($id, $value){
		  $this->db->flush_cache();
		  $this->db->where('item_id', $id);
		  $this->db->update('tabqy_item',array('item_status'=>$value));
		  $this->db->flush_cache();
		  $this->db->select('item_status,item_id');
		  $this->db->from('tabqy_item');
		  $this->db->where('item_id', $id);
		  $query = $this->db->get();
		  $data['cur_status'] = $this->db->result($query);
		  echo json_encode($data);die;
	 }
}
?>