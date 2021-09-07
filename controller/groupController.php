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
		//var_dump($_POST);die;
		//insert 
		if(isset($group_id) && $group_id == ""){
			$is_active = 1;
			if(isset($_POST['is_active'])){
				$is_active = 0;
			}
			$columns_value = array(
				'group_name'=>$group_name,
				'status'=>$is_active
			);
			$return = $dbClass->insert("user_group", $columns_value);
			$last_insert_group_id = $dbClass->getSingleRow("select id from user_group where group_name = '$group_name'");
			if($return){
				$action_result = $dbClass->getResultList("select id from web_actions where status=0");
				foreach($action_result as $row){
					$columns_value = array(
						'group_id'=>$last_insert_group_id['id'],
						'action_id'=>$row['id'],
						'status'=>0
					);
					$return_group_permission = $dbClass->insert("user_group_permission",$columns_value);
				}
			}
			if($return_group_permission){
				$employee_result = $dbClass->getResultList("select emp_id from emp_infos");
				foreach($employee_result as $row){
					$columns_value = array(
						'group_id'=>$last_insert_group_id['id'],
						'emp_id'=>$row['emp_id'],
						'status'=>0
					);
					$return_user_group_member = $dbClass->insert("user_group_member",$columns_value);
				}
			}
			if($return_user_group_member) echo "1";	
			else "0";
		}
		//update
		else if(isset($group_id) && $group_id>0){
			//var_dump($_POST);die;
			$is_active = 1;
			if(isset($_POST['is_active'])){
				$is_active = 0;
			}
			$columns_value = array(
				'group_name'=>$group_name,
				'status'=>$is_active
			);
			$condition_array = array(
				'id'=>$group_id
			);
			$return = $dbClass->update("user_group",$columns_value, $condition_array);
			if($return){
				$columns_value = array('status'=>0);
				$condition_array = array('group_id'=>$group_id);
				$return_group_permission = $dbClass->update("user_group_permission",$columns_value, $condition_array);
				if($return_group_permission){
					foreach($permision as $key=>$module_action_id){
						$columns_value = array('status'=>1);
						$condition_array = array(
							'group_id'=>$group_id,
							'action_id'=>$module_action_id
						);
						$return_succes = $dbClass->update("user_group_permission",$columns_value, $condition_array);
						if(!$return_succes) break;	
					}
				}
			}
			if($return_succes) echo "2";
			else        echo "0";
			 
		}
	
	break;
	
	
	case "grid_data":
	//var_dump($_POST);die;	
		$sql = 	"SELECT id, group_name, status_text
				from(
					select u.id, u.group_name,
					u.`status`,case status when 1 then 'Inactive' when 0 then 'Active' end status_text
					from user_group u
				)U
				WHERE CONCAT(id, group_name, status_text) LIKE '%$search_txt%'
				ORDER BY id ASC";	
		//echo $sql; die;		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);		
		foreach ($result as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);	
	
	break;
	case "get_group_details":
		//var_dump($_POST);die;
		$sql = "select * from user_group where id=$group_id";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
		foreach ($result as $row) {
			$data['records'][] = $row;
		}			
		echo json_encode($data);
					
	break;
	
	case "get_permission_details":	
		$permission_details = $dbClass->getResultList("SELECT aa.id, aa.activity_name,up.`status`, up.group_id, m.module_name
									FROM user_group_permission up
									LEFT JOIN web_actions aa ON aa.id=up.action_id
									left join web_module m on m.id=aa.module_id
									where up.group_id= $group_id and aa.status=0 order by module_name asc ");
				
		$permission_string = "";
		foreach ($permission_details as $row) {
			$permission_string .= $row['id']."*". $row['activity_name']."*". $row['status']."*". $row['module_name'].",";
		}
		$permission_string = rtrim($permission_string,',');
		$module_activity_ids_actions_arr = explode(',',$permission_string);
		$arr['module_activity']=$module_activity_ids_actions_arr;			
		$data['records'][] = $arr;
			
		echo json_encode($data);	
	break;

}
?>