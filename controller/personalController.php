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
		if(isset($master_id)  && $master_id == ""){
			$columns_value = array(
				'name'=>$name,
				'organization'=>$organization,
				'designation'=>$designation,
				'email'=>$email,
				'mobile_no'=>$phone_no,
				'created_by'=>$loggedUser
			);	
			$return = $dbClass->insert("external_contact", $columns_value);
			echo $return;
		}
		else if(isset($master_id) && $master_id>0){
			$columns_value = array(
				'name'=>$name,
				'organization'=>$organization,
				'designation'=>$designation,
				'email'=>$email,
				'mobile_no'=>$phone_no,
				'created_by'=>$loggedUser
			);			
			$condition_array = array(
				'id'=>$master_id
			);
			$return = $dbClass->update("external_contact",$columns_value, $condition_array);
			if($return==1) echo "2";
			else 		   echo "0";	
		}	
	break;
	
	case "load_contacts":		
		if($contact_type == "employee"){
			$con_sql = $dbClass->getResultList("select emp_id, full_name,  designation_name, photo, contact_no,  email, blood_group, is_active_home_page from user_infos");		
			foreach ($con_sql as $row) {
				$data['records'][] = $row;
			}
			echo json_encode($data);
		}
		else if($contact_type == "external"){
			$con_sql = $dbClass->getResultList("select * from external_contact where created_by = '$loggedUser'");		
			foreach ($con_sql as $row) {
				$data['records'][] = $row;
			}
			echo json_encode($data);
		}			
	break;
	
	case "get_external_details":
		$external_details = $dbClass->getResultList("select * from external_contact where id='$id'");
		foreach ($external_details as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);			
	break;
	
	case "delete":	 
		//var_dump($_POST);die;
		$condition_array = array(
			'id'=>$id
		);
		$return = $dbClass->delete("external_contact", $condition_array);
		if($return==1) echo "1";
		else 		   echo "0";		 
	break;
}
?>