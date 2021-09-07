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
		
		if(isset($project_id) && $project_id == ""){
			$check_user_name_availability = $dbClass->getSingleRow("select count(project_name) as no_of_project from project_infos where project_name='$project_name' and account_no= ".$_SESSION['account_no']);
			if($check_user_name_availability['no_of_project']!=0) { echo 5; die;}
			$is_active=0;
			if(isset($_POST['is_active'])){
				$is_active=1;
			}
			if(isset($_FILES['project_image_upload']) && $_FILES['project_image_upload']['name']!= ""){
				$desired_dir = "../images/project";
				chmod( "../images/project", 0777);
				$file_name = $_FILES['project_image_upload']['name'];
				$file_size =$_FILES['project_image_upload']['size'];
				$file_tmp =$_FILES['project_image_upload']['tmp_name'];
				$file_type=$_FILES['project_image_upload']['type'];	
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
					$photo  = "images/project/".$photo;					
				}
				else {
					echo $img_error_ln;die;
				}			
			}
			else{
				$photo  = "";	
			}
			$columns_value = array(
				'project_name'=>$project_name,
				'project_address'=>$address,
				'phone'=>$contact_no,
				'note'=>$note,
				'status'=>$is_active,
				'logo'=>$photo,
				'note'=>$note,
				'account_no'=>$_SESSION['account_no'],
				'created_by'=>$loggedUser
			);	
			
			$return = $dbClass->insert("project_infos", $columns_value);
			if($return) echo "1";
			else 	echo "0";
		}
		else{
		//var_dump($_REQUEST);die;
			$check_user_name_availability = $dbClass->getSingleRow("select count(project_name) as no_of_project from project_infos where project_name='$project_name' and project_code!=$project_id and account_no= ".$_SESSION['account_no']);
			if($check_user_name_availability['no_of_project']!=0) { echo 5; die;}
					
			$is_active=0;
			if(isset($_POST['is_active'])){
				$is_active=1;
			}			
			
			if(isset($_FILES['project_image_upload']) && $_FILES['project_image_upload']['name']!= ""){
				$desired_dir = "../images/project";
				chmod( "../images/project", 0777);
				$file_name = $_FILES['project_image_upload']['name'];
				$file_size =$_FILES['project_image_upload']['size'];
				$file_tmp =$_FILES['project_image_upload']['tmp_name'];
				$file_type=$_FILES['project_image_upload']['type'];	
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
					$photo  = "images/project/".$photo;					
				}
				else {
					echo $img_error_ln;die;
				}

				$columns_value = array(
					'project_name'=>$project_name,
					'project_address'=>$address,
					'phone'=>$contact_no,
					'note'=>$note,
					'status'=>$is_active,
					'logo'=>$photo,
					'note'=>$note,
					'created_by'=>$loggedUser
				);					
			}
			else{
				$columns_value = array(
					'project_name'=>$project_name,
					'project_address'=>$address,
					'phone'=>$contact_no,
					'note'=>$note,
					'status'=>$is_active,
					'note'=>$note,
					'modified_by'=>$loggedUser
				);
			}
			$condition_array = array(
				'project_code'=>$project_id
			);	
			$return = $dbClass->update("project_infos", $columns_value, $condition_array);
							
			if($return) echo "2";
			else 	echo "0";
		}
	break;
	
	case "grid_data":	
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		
		$entry_permission   	    = $dbClass->getUserGroupPermission(53);
		$delete_permission          = $dbClass->getUserGroupPermission(54);
		$update_permission          = $dbClass->getUserGroupPermission(55);
		
		$project_grid_permission    = $dbClass->getUserGroupPermission(56);		
		
		$countsql = "SELECT count(project_code)
				FROM(
					SELECT project_code, project_name, project_address, phone, account_no,
					CASE status WHEN 0 THEN 'Inactive' WHEN 1 THEN 'Active' END status_text,
					note, logo, `status`, created_by, modified_by
					FROM project_infos
				)A
				WHERE CONCAT(project_code, project_name, status_text) LIKE '%$search_txt%' and account_no= ".$_SESSION['account_no'];
		//echo $countsql;die;
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records;
		$data['entry_status'] = $entry_permission; 		
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages); 
		if($project_grid_permission==1){
			$sql = 	"SELECT id, project_name, project_address, phone,status_text, note, logo, `status`, created_by, 
					modified_by, $update_permission as update_status, $delete_permission as delete_status
					FROM(
						SELECT project_code as id, project_name, project_address, ifnull(phone,'') phone, account_no, 
						CASE status WHEN 0 THEN 'Inactive' WHEN 1 THEN 'Active' END status_text,
						note, logo, `status`, created_by, modified_by
						FROM project_infos WHERE status=1
					)A
					WHERE CONCAT(id, project_name, status_text) LIKE '%$search_txt%' and account_no= ".$_SESSION['account_no']."
					ORDER BY id ASC
					LIMIT $start, $end";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);		
			foreach ($result as $row) {
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}
	break;
	
	case "get_project_details":
		$update_permission = $dbClass->getUserGroupPermission(10);
		if($update_permission==1){
			$project_details = $dbClass->getResultList("SELECT project_code, project_name, project_address, ifnull(phone,'') phone,
														status,	note, ifnull(logo,'') logo, created_by, modified_by
														FROM project_infos
														WHERE project_code='$project_id'  and account_no= ".$_SESSION['account_no']);
			//echo $project_details; die;
			foreach ($project_details as $row){
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}
	break;
	
	
	case "delete_project":
		$delete_permission = $dbClass->getUserGroupPermission(10);
		if($delete_permission==1){
			$condition_array = array(
				'project_code'	=>$project_id,
				'account_no'	=>$_SESSION['account_no']
			);
			$columns_value = array(
				'status'=>0
			);
			$return = $dbClass->update("project_infos", $columns_value, $condition_array);
		}
		if($return==1) echo "1";
		else 		   echo "0";
	break;



	case "insert_or_update_flat":	
		if(!isset($_SESSION)){	echo "0";die;}
		
		if(isset($flat_id) && $flat_id == ""){
			$check_flat_name_availability = $dbClass->getSingleRow("select count(flat_name) as no_of_flat from flat_infos where project_id='$project_id' and flat_name= '$flat_name' ");
			if($check_flat_name_availability['no_of_flat']!=0) { echo 5; die;}

			$columns_value = array(
				'project_id'=>$project_id,
				'floor_id'=>$floor_id,
				'flat_name'=>$flat_name,
				'current_rent'=>$current_rent,
				'note'=>$note,
				'status'=>(isset($_POST['is_active']))?1:0,
				'account_no'=>$_SESSION['account_no'],
				'created_by'=>$loggedUser
			);	
			
			$return = $dbClass->insert("flat_infos", $columns_value);
			if(is_numeric($return) && $return>0) {
				$project_info = $dbClass->getSingleRow("select project_name from project_infos where project_code=$project_id");
				// monthly rent head entry
				$columns_value = array(
					'name'=>"Monthly rent of $flat_name  (".$project_info['project_name'].")",
					'code'=>get_expense_code('IN', $dbClass),
					'flat_id' => $return,
					'parent'=>6, // flat rent
					'account_no'=>$_SESSION['account_no'],
					'head_type'=>2 // income
				);
				$dbClass->insert("accounting_head", $columns_value);
				// advance head entry
				$columns_value = array(
					'name'=>"Advance rent of $flat_name (".$project_info['project_name'].")",
					'code'=>get_expense_code('IN', $dbClass),
					'flat_id' => $return,
					'parent'=>6, // flat rent
					'account_no'=>$_SESSION['account_no'],
					'head_type'=>3 // liability
				);
				$dbClass->insert("accounting_head", $columns_value);
				
				echo "1";
			}
			else 	echo "0";
		}
		else{
			$check_flat_name_availability = $dbClass->getSingleRow("select count(flat_name) as no_of_flat from flat_infos where project_id='$project_id' and id!=$flat_id and flat_name= '$flat_name'");
			if($check_flat_name_availability['no_of_flat']!=0) { echo 5; die;}
		
			$columns_value = array(
				'project_id'=>$project_id,
				'floor_id'=>$floor_id,
				'flat_name'=>$flat_name,
				'current_rent'=>$current_rent,
				'note'=>$note,
				'status'=>(isset($_POST['is_active']))?1:0,
			);
		
			$condition_array = array(
				'id'=>$flat_id
			);	
			$return = $dbClass->update("flat_infos", $columns_value, $condition_array);
							
			if($return) echo "2";
			else 	echo "0";
		}
	break;
	
	case "grid_data_flat":	
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		
		$entry_permission   	    = $dbClass->getUserGroupPermission(121);
		$delete_permission          = $dbClass->getUserGroupPermission(124);
		$update_permission          = $dbClass->getUserGroupPermission(123);		
		$grid_permission    		= $dbClass->getUserGroupPermission(122);		
		
		$countsql = "SELECT count(flat_name)
				FROM(
					SELECT f.id, f.flat_name, f.account_no,  f.project_id, f.current_rent, f.note, p.project_name, ifnull(r.full_name, 'No Renter') AS renter_name, CASE f.status WHEN 0 THEN 'Inactive' WHEN 1 THEN 'Active' END status_text, f.status, f.created_by
					FROM flat_infos f
					LEFT JOIN project_infos p ON f.project_id = p.project_code
					LEFT JOIN renter_flat_agreement fr ON fr.flat_id = f.id
					LEFT JOIN renter r ON r.id=fr.renter_id
				)A
				WHERE CONCAT(flat_name, project_name,renter_name, status_text) LIKE '%$search_txt%' and account_no= ".$_SESSION['account_no'];
		//echo $countsql;die;
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records;
		$data['entry_status'] = $entry_permission; 		
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages); 
		if($grid_permission==1){
			$sql = 	"SELECT id, project_name, renter_name, flat_name, floor_no, project_id,status_text, note, current_rent, status, created_by, 
					 $update_permission as update_status, $delete_permission as delete_status
					FROM(
						SELECT f.id, f.flat_name, f.account_no,  f.project_id, f.current_rent, f.note, p.project_name, fl.floor_no,
						ifnull(r.full_name, 'No Renter') AS renter_name, CASE f.status WHEN 0 THEN 'Inactive' WHEN 1 THEN 'Active' END status_text, f.status, f.created_by
						FROM flat_infos f
						left join floors fl on fl.id=f.floor_id
						LEFT JOIN project_infos p ON f.project_id = p.project_code
						LEFT JOIN renter_flat_agreement fr ON fr.flat_id = f.id
						LEFT JOIN renter r ON r.id=fr.renter_id
					)A
					WHERE CONCAT(id, flat_name, project_name,renter_name, status_text) LIKE '%$search_txt%' and account_no= ".$_SESSION['account_no']."
					ORDER BY id ASC
					LIMIT $start, $end";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);		
			foreach ($result as $row) {
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}
	break;
	
	case "get_flat_details":
		$update_permission = $dbClass->getUserGroupPermission(10);
		if($update_permission==1){
			$flat_details = $dbClass->getResultList("	SELECT f.id, f.flat_name, f.account_no, fl.id as floor_id,  f.project_id, f.current_rent, f.note, p.project_name, ifnull(r.full_name, 'No Renter') AS renter_name, CASE f.status WHEN 0 THEN 'Inactive' WHEN 1 THEN 'Active' END status_text, f.status, f.created_by
														FROM flat_infos f
														left join floors fl on fl.id=f.floor_id
														LEFT JOIN project_infos p ON f.project_id = p.project_code
														LEFT JOIN renter_flat_agreement fr ON fr.flat_id = f.id
														LEFT JOIN renter r ON r.id=fr.renter_id
														WHERE f.id='$flat_id'  and f.account_no= ".$_SESSION['account_no']);
			//echo $project_details; die;
			foreach ($flat_details as $row){
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}
	break;
	
	
	case "delete_flat":
		$delete_permission = $dbClass->getUserGroupPermission(10);
		if($delete_permission==1){
			$condition_array = array(
				'id'	=>$flat_id,
				'account_no'	=>$_SESSION['account_no']
			);
			$columns_value = array(
				'status'=>0
			);
			$return = $dbClass->update("flat_infos", $columns_value, $condition_array);
		}
		if($return==1) echo "1";
		else 		   echo "0";
	break;
	
	case "flat_list_project_wise":		
		$flat_list = $dbClass->getResultList("SELECT * from flat_infos WHERE project_id=$project_id AND status=1");
		//echo $project_details; die;
		foreach ($flat_list as $row){
			$data['records'][] = $row;
		}			
		echo json_encode($data);
	break;
	
	case "rent_space_wise":		
		$flat_info = $dbClass->getSingleRow("SELECT current_rent from flat_infos WHERE id=$flat_id");
		if(is_numeric($flat_info['current_rent']))		echo $flat_info['current_rent'];
		else 							echo 0;
	break;
	
}



	function get_expense_code($type, $dbClass){
	    $expense_code="";
		if($type!=""){ 
    		$last_expense_code = $dbClass->getSingleRow("SELECT max(RIGHT(code,8)) as expense_code 
    											FROM accounting_head where SUBSTR(CODE,1 ,2)='$type' and account_no=".$_SESSION['account_no']);
    			
    		if($last_expense_code == null){
    			$expense_code = '00000001';
    		}else{
    			$expense_code = $last_expense_code['expense_code']+1;
    		}
    		$str_length = 8;
    		$str = substr("00000000{$expense_code}", -$str_length);
    
    		$expense_code = $type."$str";
    		$data['records'] = $expense_code;		
	    }
	    return $expense_code;
	}
	
?>