<?php 
session_start();
include '../includes/static_text.php';
include("../dbConnect.php");
include("../dbClass.php");


$dbClass = new dbClass;
$conn       = $dbClass->getDbConn();
$loggedUser = $dbClass->getUserId();	



extract($_REQUEST);

switch ($q){	
	case "insert_or_update":
		if(!isset($_SESSION)){	echo "0";die;}
		//echo "STOP";die;
		if(isset($emp_id) && $emp_id == ""){
			$check_user_name_availability = $dbClass->getSingleRow("select count(user_name) as no_of_user from appuser where user_name='$user_name'");
			if($check_user_name_availability['no_of_user']!=0) { echo 5; die;}
			
			$last_user_id = $dbClass->getSingleRow("select max(user_id) user_id from appuser");	
			if(empty($last_user_id)) $last_user_id['user_id'] = "1000000"; 
			$emp_id = $last_user_id['user_id'] + 1;

			$photo  = "";	
			if(isset($_FILES['emp_image_upload']) && $_FILES['emp_image_upload']['name']!= ""){
				$desired_dir = "../images/employee";
				chmod( "../images/employee", 0777);
				$file_name = $_FILES['emp_image_upload']['name'];
				$file_size =$_FILES['emp_image_upload']['size'];
				$file_tmp =$_FILES['emp_image_upload']['tmp_name'];
				$file_type=$_FILES['emp_image_upload']['type'];	
				if($file_size < $file_max_length){
					if(file_exists("$desired_dir/".$file_name)==false){
						if(move_uploaded_file($file_tmp,"$desired_dir/".$file_name))
							$photo = "$file_name";
					}
					else{//rename the file if another one exist
						$new_dir="$desired_dir/".time().$file_name;
						if(rename($file_tmp,$new_dir))
							$photo =time()."$file_name";				
					}
					$photo  = "images/employee/".$photo;					
				}
				else {
					echo $img_error_ln;die;
				}			
			}

			$columns_value = array(
				'emp_id'=>$emp_id,
				'full_name'=>$emp_name,
				'email'=>$email,
				'address'=>$address,
				'account_no'=>$_SESSION['account_no'],
				'age'=>$age,
				'nid_no'=>$nid_no,
				'contact_no'=>$contact_no,
				'blood_group'=>$blood_group,
				'email'=>$email,							
				'photo'=>$photo,
				'remarks'=>$remarks,
				'created_by'=>$loggedUser
			);	
			//$dbClass->print_arrays($_REQUEST);			
			$return = $dbClass->insert("emp_infos", $columns_value);
			if(is_numeric($return) && $return>0) {
				// insert data in appuser
				$is_active=0;
				if(isset($_POST['is_active'])){
					$is_active=1;
				}
				if(empty($_POST['password'])){
					$password="123456";
				}
				$columns_value = array(
					'user_id'=>$emp_id,
					'user_name'=>$user_name,
					'user_password'=>md5($password),
					'account_no'=>$_SESSION['account_no'],
					'expiry_date'=>$_SESSION['expiry_date'],
					'is_active'=>$is_active,
					'is_owner'=>0,
					'created_by'=>$loggedUser
				);			
				$return_app = $dbClass->insert("appuser", $columns_value);
				if($return_app){
					if(isset($_POST['group'])){
						$group_result = $dbClass->getResultList("select id from user_group where status=0");
						foreach($group_result as $row){
							$columns_value = array(
								'group_id'=>$row['id'],
								'emp_id'=>$emp_id,
								'status'=>0
							);
							$return_group = $dbClass->insert("user_group_member",$columns_value);
						}
						if($return_group){
							foreach($group as $key=>$module_group_id){
								$columns_value = array('status'=>1);
								$condition_array = array(
									'group_id'=>$module_group_id,
									'emp_id'=>$emp_id,
								);
								$return_succes = $dbClass->update("user_group_member", $columns_value, $condition_array);
								if(!$return_succes) break;	
							}
						}
					}
					else{
						$group_result = $dbClass->getResultList("select id from user_group where status=0");
						foreach($group_result as $row){
							$columns_value = array(
								'group_id'=>$row['id'],
								'emp_id'=>$emp_id,
								'status'=>0
							);
							$return_succes = $dbClass->insert("user_group_member",$columns_value);
							//echo $return_succes."--";
						}
					}
				}
			}
			if($return_succes) echo "1";
			else 	echo "0";
		}
		else{
			//var_dump($_REQUEST);die;
			$check_user_name_availability = $dbClass->getSingleRow("select count(user_name) as no_of_user from appuser where user_name='$user_name' and user_id!=$emp_id");
			if($check_user_name_availability['no_of_user']!=0) { echo 5; die;}
							
			
			if(isset($_FILES['emp_image_upload']) && $_FILES['emp_image_upload']['name']!= ""){
				$desired_dir = "../images/employee";
				chmod( "../images/employee", 0777);
				$file_name = $_FILES['emp_image_upload']['name'];
				$file_size =$_FILES['emp_image_upload']['size'];
				$file_tmp =$_FILES['emp_image_upload']['tmp_name'];
				$file_type=$_FILES['emp_image_upload']['type'];	
				if($file_size < $file_max_length){
					if(file_exists("$desired_dir/".$file_name)==false){
						if(move_uploaded_file($file_tmp,"$desired_dir/".$file_name))
							$photo = "$file_name";
					}
					else{//rename the file if another one exist
						$new_dir="$desired_dir/".time().$file_name;
						if(rename($file_tmp,$new_dir))
							$photo =time()."$file_name";				
					}
					$photo  = "images/employee/".$photo;					
				}
				else {
					echo $img_error_ln;die;
				}

				$columns_value = array(
					'emp_id'=>$emp_id,
					'full_name'=>$emp_name,
					'email'=>$email,
					'address'=>$address,
					'age'=>$age,
					'nid_no'=>$nid_no,
					'contact_no'=>$contact_no,
					'blood_group'=>$blood_group,
					'email'=>$email,							
					'photo'=>$photo,
					'remarks'=>$remarks,
					'modified_by'=>$loggedUser
				);					
			}
			else{
				$columns_value = array(
					'emp_id'=>$emp_id,
					'full_name'=>$emp_name,
					'email'=>$email,
					'address'=>$address,
					'age'=>$age,
					'nid_no'=>$nid_no,
					'contact_no'=>$contact_no,
					'blood_group'=>$blood_group,
					'email'=>$email,							
					'remarks'=>$remarks,
					'modified_by'=>$loggedUser
				);	
			}
			$condition_array = array(
				'emp_id'=>$emp_id
			);	
			$return = $dbClass->update("emp_infos", $columns_value, $condition_array);
							
			
			//Update data in appuser
			$is_active=0;
			if(isset($_POST['is_active'])){
				$is_active=1;
			}
			if(empty($_POST['password'])){
				$columns_value = array(
					'is_active'=>$is_active
				);
			}
			else{
				$columns_value = array(
					'user_password'=>md5($password),
					'is_active'=>$is_active
				);
			}			
			$condition_array = array(
				'user_id'=>$emp_id
			);	
			$return_app = $dbClass->update("appuser", $columns_value, $condition_array);
			if($return_app){
				$columns_value = array('status'=>0);
				$condition_array = array('emp_id'=>$emp_id);
				$return_group = $dbClass->update("user_group_member",$columns_value, $condition_array);
				if($return_group){
					foreach($group as $key=>$module_group_id){
						$columns_value = array('status'=>1);
						$condition_array = array(
							'group_id'=>$module_group_id,
							'emp_id'=>$emp_id,
						);
						//var_dump($condition_array);die;
						$return_succes = $dbClass->update("user_group_member", $columns_value, $condition_array);
						if(!$return_succes) break;	
					}
				}
			}
			if($return_succes) echo "2";
			else 	echo "0";
		}
	break;
	
	case "grid_data":
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		if(!isset($_SESSION)){
			echo json_encode($data);
		}
		$employee_permission	    = $dbClass->getUserGroupPermission(11);
		$update_permission          = $dbClass->getUserGroupPermission(12);
		$entry_permission   	    = $dbClass->getUserGroupPermission(10);
		$delete_permission          = $dbClass->getUserGroupPermission(13);
		$employee_grid_permission   = $dbClass->getUserGroupPermission(15);
		$permission_grid_permission = $dbClass->getUserGroupPermission(16);
		
		$condition =" where a.is_active =1 and CONCAT(e.emp_id, e.full_name, e.contact_no, e.email) LIKE '%$search_txt%' and e.account_no= ".$_SESSION['account_no'];			
		$countsql = "SELECT count(e.emp_id) 
					FROM emp_infos e
					join appuser a on a.user_id=e.emp_id 
					$condition";
		//echo $countsql;die;			
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records; 
		$data['entry_status'] = $entry_permission; 
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages);
		if($employee_grid_permission==1 || $permission_grid_permission==1){
			$sql = "SELECT e.emp_id, e.full_name,e.contact_no, e.photo,  
					a.is_active, a.user_name,e.nid_no, e.email,
					(CASE a.is_active WHEN 1 THEN 'Active' WHEN 0 THEN 'Blocked' END) active_status,
					$employee_permission as permission_status, $update_permission as update_status,	$delete_permission as delete_status
					FROM emp_infos e
					JOIN appuser a ON a.user_id=e.emp_id
					$condition
					ORDER BY emp_id ASC
					LIMIT $start, $end";	
			//echo $sql;die;				
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);		
			foreach ($result as $row) {
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}			 
	break;
	
	case "get_emp_details":
		$update_permission = $dbClass->getUserGroupPermission(10);
		if($update_permission==1){
			$emp_details = $dbClass->getResultList("SELECT e.emp_id, e.full_name, e.nid_no, e.address,													
							e.contact_no, e.photo, e.remarks, e.email, e.blood_group,
							a.is_active, a.user_name, e.nid_no, e.age
							FROM emp_infos e
							JOIN appuser a ON a.user_id=e.emp_id
							WHERE emp_id='$emp_id'");
			//echo $emp_details; die;
			foreach ($emp_details as $row){
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}
	break;
	
	case "get_user_info":
		$emp_info = "SELECT e.emp_id, e.full_name, e.nid_no, e.address,	e.contact_no, e.photo,  e.remarks, e.email, e.blood_group,					a.is_active, a.user_name, e.nid_no, e.age
					FROM emp_infos e
					JOIN appuser a ON a.user_id=e.emp_id
					WHERE emp_id='$loggedUser'";
		$stmt = $conn->prepare($emp_info);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row){
			$data['records'] = $row;
		}			
		echo json_encode($data);
	break;
	
	case "update_information":
		$password = $dbClass->getSingleRow("select user_password from appuser where user_id=$loggedUser");
		if(isset($new_password) && $new_password != ""){
			if($password['user_password'] == md5($old_password)){
				$columns_value = array(
					'user_password'=>md5($new_password),
					'user_name'=>$user_name
				);
				$condition_array = array(
					'user_id'=>$loggedUser
				);
				$return_pass = $dbClass->update("appuser", $columns_value, $condition_array);
				if($return_pass){
					$columns_value = array(
						'full_name'=>$emp_name,
						'email'=>$email,
						'blood_group'=>$blood_group,
						'nid'=>$nid,
						'email'=>$email,
						'contact_no'=>$contact_no
					);
					$condition_array = array(
						'emp_id'=>$loggedUser
					);
					$return = $dbClass->update("emp_infos", $columns_value, $condition_array);
					if($return==1) echo "1";
				}
			}
			else echo "0";
		}
		else{
			if($password['user_password'] == md5($old_password)){
				$columns_value = array(
					'user_name'=>$user_name
				);
				$condition_array = array(
					'user_id'=>$loggedUser
				);
				$return_pass = $dbClass->update("appuser", $columns_value, $condition_array);
				if($return_pass){
					$columns_value = array(
						'full_name'=>$emp_name,
						'email'=>$email,
						'blood_group'=>$blood_group,
						'nid_no'=>$nid_no,
						'email'=>$email,
						'contact_no'=>$contact_no
					);
					$condition_array = array(
						'emp_id'=>$loggedUser
					);
					$return = $dbClass->update("emp_infos", $columns_value, $condition_array);
					if($return==1) echo "1";	
				}
			}
			else echo "0";
		}
	break;
	

	case "get_user_groups":
		$user_groups = $dbClass->getResultList("select group_concat(ug.id,'*', ug.group_name) module_group_ids
												from 
												user_group ug where ug.status=0");
		foreach ($user_groups as $row) {
			$module_group_ids_arr = explode(',',$row['module_group_ids']);
			$arr['module_group']=$module_group_ids_arr;			
			$data['records'][] = $arr;
		}			
		echo json_encode($data);
	break;
	
	case "get_user_groups_emp":
		$data = array();
		// if an user is already in another group, then need to avoid the user
		// will be done by momit
		$user_group_emps = $dbClass->getResultList("select distinct(um.emp_id),CONCAT_WS(' >> ',e.emp_id,e.full_name,e.designation_name) empName from user_group_member um 
												left join user_infos e on e.emp_id=um.emp_id
												where um.group_id in ($group) and `status`= 1");
		foreach ($user_group_emps as $row){
			$data['records'][] = $row;
		}		
		echo json_encode($data);
		
	break;
	
	case "get_emp_user_groups":
		$user_groups = $dbClass->getResultList("select group_concat(ug.id,'*', ug.group_name,'*',ugm.`status`) module_group_ids
												from 
												user_group_member ugm 
												left join user_group ug on ug.id=ugm.group_id
												where ugm.emp_id=$emp_id and ug.status=0");
												
		foreach ($user_groups as $row) {
			$module_group_ids_arr = explode(',',$row['module_group_ids']);
			$arr['module_group']=$module_group_ids_arr;			
			$data['records'][] = $arr;
		}		
		echo json_encode($data);
	break;
	
	case "get_post_user_groups":
		$user_groups = $dbClass->getResultList("select group_concat(ug.id,'*', ug.group_name,'*',ap.`status`) module_group_ids
												from 
												activity_public_post_group ap 
												left join user_group ug on ug.id=ap.group_id
												where ap.post_id=$post_id and ug.status=0");
		foreach ($user_groups as $row) {
			$module_group_ids_arr = explode(',',$row['module_group_ids']);
			$arr['module_group']=$module_group_ids_arr;			
			$data['records'][] = $arr;
		}			
		echo json_encode($data);	
	break;
	
	case "get_permission_details":	
		$permission_details = $dbClass->getResultList("select group_concat(aa.id,'*', aa.activity_name ,'*',ep.`status`) module_activity_ids_actions
									from 
									user_web_permission ep
									left join web_actions aa on aa.id=ep.activity_action_id
									where ep.emp_id= $emp_id and aa.status=0");
		foreach ($permission_details as $row) {
			$module_activity_ids_actions_arr = explode(',',$row['module_activity_ids_actions']);
			$arr['module_activity']=$module_activity_ids_actions_arr;			
			$data['records'][] = $arr;
		}	
		echo json_encode($data);	
	break;
	
	case "delete_employee":
		$delete_permission = $dbClass->getUserGroupPermission(10);
		if($delete_permission==1){
			$condition_array = array(
				'user_id'=>$emp_id
			);
			$columns_value = array(
				'is_active'=>0
			);
			$return = $dbClass->update("appuser", $columns_value, $condition_array);
		}
		if($return==1) echo "1";
		else 		   echo "0";
	break;
	
	case "get_department_members":
		$loggedUser_dept_id_result = $dbClass->getSingleRow("select dept_id from user_infos e where e.emp_id=$loggedUser");	 
		$loggedUser_dept_id        = $loggedUser_dept_id_result['dept_id'];
		$department_members_list   = $dbClass->getResultList("select concat(e.full_name,'(',e.designation_name,')') full_name, e.photo 
									 from user_infos e where e.dept_id = '$loggedUser_dept_id' and e.emp_id != '$loggedUser'"); 
		foreach ($department_members_list as $row){
				$data['records'][] = $row;
			}			
		echo json_encode($data); 
	break;	
	
	case "get_current_date":
		$current_date = date('d');
		if($current_date <= 25){
			$start_date  = mktime(0, 0, 0, date('n') - 1, 26);
			$start_date  = date('Y-m-d', $start_date); 
			$end_date	= date('Y-m-d', strtotime(date('Y-m-d') .' -1 day')); 	
		}
		else{
			$start_date  = mktime(0, 0, 0, date('n'), 26);
			$start_date  = date('Y-m-d', $start_date); 
			$end_date	= date('Y-m-d', strtotime(date('Y-m-d') .' -1 day'));  	
		}	
		$data['start_date'] = $start_date;			
		$data['end_date'] = $end_date;			
		echo json_encode($data);		
	break;
	
	case "view_departments":
		//var_dump($_REQUEST);die;
		$dept_details = $dbClass->getResultList("SELECT d.department_id, d.department_name FROM hrm_departments d order by d.department_id");
		foreach ($dept_details as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);
	break;
	
	case "view_designations":
		//var_dump($_REQUEST);die;
		$dept_details = $dbClass->getResultList("SELECT d.id, d.designation_title FROM hrm_designations d order by d.id");
		foreach ($dept_details as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);
	break;
	
	case "view_projects":
		//var_dump($_REQUEST);die;
		$dept_details = $dbClass->getResultList("SELECT p.project_code, p.project_name FROM project_infos p order by p.project_code");
		foreach ($dept_details as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);
	break;
	
	case "empReport":
		//var_dump($_REQUEST);die;
		$condition = "";
		if($emp_active_status != 2 && $project_name != ''){
			$condition  = " WHERE a.is_active = $emp_active_status and p.project_name = '$project_name'";
		}
		else if($emp_active_status != 2 && $project_name == ''){
			$condition  = " WHERE a.is_active = $emp_active_status ";
		}
		else if($emp_active_status == 2 && $project_name != ''){
			$condition  = " WHERE p.project_name = '$project_name' ";
		}
		else{
			$condition  = "";
		}	
		$data = array();
		$details = $dbClass->getResultList("SELECT e.emp_id, e.full_name, dep.department_name, des.designation_title, p.project_name, e.contact_no,
											CASE WHEN a.is_active = 0 THEN 'In-Active' WHEN 1 THEN 'Active' END active_status
											FROM emp_infos e
											LEFT JOIN appuser a on a.user_id = e.emp_id
											LEFT JOIN hrm_departments dep on dep.department_id = e.department_id
											LEFT JOIN hrm_designations des on des.id = e.designation_id
											LEFT JOIN project_infos p on p.project_code = e.project_code $condition");
											
		foreach ($details as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);
	break;

}
?>