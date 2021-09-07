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
		if(isset($action_id) && $action_id == ""){
			$is_active = 1;
			if(isset($_POST['is_active'])){
				$is_active = 0;
			}
			$columns_value = array(
				'activity_name'=>$action_name,
				'status'=>$is_active
			);
			$return = $dbClass->insert("web_actions", $columns_value);
			$activity_action_id = $dbClass->getSingleRow("select a.id from web_actions as a where a.activity_name = '$action_name'");
			if($return){
				/* $employee_result = $dbClass->getResultList("select emp_id from emp_infos");
					foreach($employee_result as $row){
					$columns_value = array(
						'activity_action_id'=>$activity_action_id['id'],
						'emp_id'=>$row['emp_id'],
						'status'=>0
					);
					$return_emp_permission = $dbClass->insert("emp_activity_permission",$columns_value); 
				*/	
				$group_result = $dbClass->getResultList("select id from user_group");
				foreach($group_result as $row){
					$columns_value = array(
						'action_id'=>$activity_action_id['id'],
						'group_id'=>$row['id'],
						'status'=>0
					);
					$return_group_permission = $dbClass->insert("user_group_permission",$columns_value);
					if($return_group_permission) echo "1";	
				}
			}
			else "0";
		}
		else if(isset($action_id) && $action_id>0){
			$is_active = 1;
			if(isset($_POST['is_active'])){
				$is_active = 0;
			}
			$columns_value = array(
				'activity_name'=>$action_name,
				'status'=>$is_active
			);
			$condition_array = array(
				'id'=>$action_id
			);
			$return = $dbClass->update("web_actions",$columns_value, $condition_array);
			if($return) echo "2";
			else        echo "0";			 
		}
	break;
	
	case "grid_data":
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		
		$countsql = "SELECT count(id)
					from(
						select a.id, a.activity_name,
						a.`status`,case status when 1 then 'Inactive' when 0 then 'Active' end status_text
						from web_actions a 
					)A
					WHERE CONCAT(id, activity_name, status_text) LIKE '%$search_txt%'";
		//echo $countsql;die;
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records;  
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages); 
		$sql = 	"SELECT id, activity_name, status_text
				from(
					select a.id, a.activity_name,
					a.`status`,case status when 1 then 'Inactive' when 0 then 'Active' end status_text
					from web_actions a 
				)A
				WHERE CONCAT(id, activity_name, status_text) LIKE '%$search_txt%'
				ORDER BY id ASC LIMIT $start, $end";	
		//echo $sql; die;		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);		
		foreach ($result as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);	
	
	break;
	case "get_action_details":
		//var_dump($_POST);die;
		$sql = "select * from web_actions where id=$action_id";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
		foreach ($result as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);
					
	break;

}
?>