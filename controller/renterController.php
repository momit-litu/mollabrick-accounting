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
		if(isset($renter_id) && $renter_id == ""){
			$check_renter_name_availability = $dbClass->getSingleRow("select count(full_name) as no_of_user from renter where full_name='$renter_name' and email='$email'");
			if($check_renter_name_availability['no_of_user']!=0) { echo 5; die;}
			
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
				'full_name'=>$renter_name,
				'email'=>$email,
				'address'=>$address,
				'age'=>$age,
				'nid_no'=>$nid_no,
				'contact_no'=>$contact_no,
				'account_no'=>$_SESSION['account_no'],
				'blood_group'=>$blood_group,
				'email'=>$email,							
				'photo'=>$photo,
				'remarks'=>$remarks,
				'status'=>(isset($is_active))?1:0,
				'created_by'=>$loggedUser
			);	
			//$dbClass->print_arrays($_REQUEST);			
			$return = $dbClass->insert("renter", $columns_value);
			
			if(is_numeric($return) && $return>0) echo "1";
			else 	echo "0";
		}
		else{
			//var_dump($_REQUEST);die;
			$check_renter_name_availability = $dbClass->getSingleRow("select count(full_name) as no_of_user from renter where full_name='$renter_name' and email='$email' and id!=$renter_id");
			if($check_renter_name_availability['no_of_user']!=0) { echo 5; die;}
							
			
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
					'full_name'=>$renter_name,
					'email'=>$email,
					'address'=>$address,
					'age'=>$age,
					'nid_no'=>$nid_no,
					'contact_no'=>$contact_no,
					'blood_group'=>$blood_group,
					'email'=>$email,							
					'photo'=>$photo,
					'remarks'=>$remarks,
					'status'=>(isset($is_active))?1:0,
					'modified_by'=>$loggedUser
				);					
			}
			else{
				$columns_value = array(
					'full_name'=>$renter_name,
					'email'=>$email,
					'address'=>$address,
					'age'=>$age,
					'nid_no'=>$nid_no,
					'contact_no'=>$contact_no,
					'blood_group'=>$blood_group,
					'email'=>$email,							
					'remarks'=>$remarks,
					'status'=>(isset($is_active))?1:0,
					'modified_by'=>$loggedUser
				);	
			}
			$condition_array = array(
				'id'=>$renter_id
			);	
			$return = $dbClass->update("renter", $columns_value, $condition_array);
			if($return) echo "2";
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

		$update_permission          = $dbClass->getUserGroupPermission(119);
		$entry_permission   	    = $dbClass->getUserGroupPermission(117);
		$delete_permission          = $dbClass->getUserGroupPermission(120);
		$grid_permission   			= $dbClass->getUserGroupPermission(118);

		
		$condition =" where  CONCAT(e.id, e.full_name, e.contact_no, e.email) LIKE '%$search_txt%' and e.account_no= ".$_SESSION['account_no'];			
		$countsql = "SELECT count(e.id) 
					FROM renter e
					$condition";
		//echo $countsql;die;			
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records; 
		$data['entry_status'] = $entry_permission; 
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages);
		if($grid_permission==1 || $permission_grid_permission==1){
			$sql = "SELECT e.id, e.full_name,e.contact_no, e.photo,  
					e.status,e.nid_no, e.email,
					(CASE e.status WHEN 1 THEN 'Active' WHEN 0 THEN 'In-active' END) active_status,
					$entry_permission as permission_status, $update_permission as update_status,	$delete_permission as delete_status
					FROM renter e
					$condition
					ORDER BY id ASC
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
	
	case "get_renter_details":
		$update_permission = $dbClass->getUserGroupPermission(119);
		if($update_permission==1){
			$renter_details = $dbClass->getResultList("SELECT e.id, e.full_name, e.nid_no, e.address,													
							e.contact_no, e.photo, e.remarks, e.email, e.blood_group,
							e.status, e.nid_no, e.age
							FROM renter e
							WHERE e.id='$renter_id'");
		//	echo $renter_details; die;
			foreach ($renter_details as $row){
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}
	break;
	

	
	case "delete_renter":
		$delete_permission = $dbClass->getUserGroupPermission(120);
		if($delete_permission==1){
			$condition_array = array(
				'id'=>$renter_id
			);
			$columns_value = array(
				'status'=>0
			);
			$return = $dbClass->update("renter", $columns_value, $condition_array);
		}
		if($return==1) echo "1";
		else 		   echo "0";
	break;


}
?>