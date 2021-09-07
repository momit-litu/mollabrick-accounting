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
		if(isset($category_id) && $category_id == ""){
			
			if($parent_head_id != ""){
				$parent = $parent_head_id;
			}else{
				$parent = NULL;
			}
			$columns_value = array(
				'name'=>$category_name,
				'code'=>$category_code,
				'parent'=>$parent
			);
			$return = $dbClass->insert("expense_categories", $columns_value);
			if($return){ 
				echo "1";
			}else "0";
		}
		else if(isset($category_id) && $category_id>0){
			if($parent_head_id != ""){
				$parent = $parent_head_id;
			}else{
				$parent = NULL;
			}
			$columns_value = array(
				'name'=>$category_name,
				'code'=>$category_code,
				'parent'=>$parent
			);
			$condition_array = array(
				'id'=>$category_id
			);
			$return = $dbClass->update("expense_categories",$columns_value, $condition_array);
			if($return) echo "2";
			else        echo "0";			 
		}
	break;
	
	case "grid_data":
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		
		$entry_permission   	    = $dbClass->getUserGroupPermission(79);
		$delete_permission          = $dbClass->getUserGroupPermission(80);
		$update_permission          = $dbClass->getUserGroupPermission(81);
		
		$category_grid_permission = $dbClass->getUserGroupPermission(82);
		
		$countsql = "SELECT count(id)
					FROM(
						SELECT c.id, c.code, c.name, ifnull(ec.name,'') parent_name
						FROM expense_categories c
						LEFT JOIN expense_categories ec on c.parent = ec.id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, code, name) LIKE '%$search_txt%'";
		//echo $countsql;die;
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records;
		$data['entry_status'] = $entry_permission;	
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages); 
		if($category_grid_permission==1){
			$sql = 	"SELECT id, name, code, name, parent_name,
					$update_permission as update_status, $delete_permission as delete_status
					FROM(
						SELECT c.id, c.code, c.name, ifnull(ec.name,'') parent_name
						FROM expense_categories c
						LEFT JOIN expense_categories ec on c.parent = ec.id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, name, code) LIKE '%$search_txt%'
					ORDER BY id DESC
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
	
	case "get_category_details":
		$update_permission = $dbClass->getUserGroupPermission(81);
		if($update_permission==1){
			$sql = "SELECT c.id, c.code, c.name, ec.id parent_id, ec.name parent_name
					FROM expense_categories c
					LEFT JOIN expense_categories ec on c.parent = ec.id
					WHERE c.id=$category_id";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			foreach ($result as $row) {
				$data['records'][] = $row;
			}			
			echo json_encode($data);	
		}			
	break;
	
	case "delete_category":
		$delete_permission = $dbClass->getUserGroupPermission(80);
		if($delete_permission==1){
			$condition_array = array(
				'id'=>$category_id
			);

			$return = $dbClass->delete("expense_categories", $condition_array);
		}
		if($return==1) echo "1";
		else 		   echo "0";
	break;
	
	case "parent_head_info":
		$sql_query = "SELECT id, CONCAT_WS(' >> ',name,code) parentName
					FROM expense_categories
					WHERE CONCAT_WS('-> ',code,name) LIKE '%" . $term . "%' AND parent is NULL 
					ORDER BY name";
		//echo $sql_query;die;
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();
		$json = array();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["id"],'label' => $row["parentName"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Parent Found !!!");
		}						
		echo json_encode($json);
	break;
	
	case "get_expense_code":
		$last_expense_code = $dbClass->getSingleRow("SELECT max(RIGHT(code,8)) as expense_code 
											FROM expense_categories");
			
		if($last_expense_code == null){
			$expense_code = '00000001';
		}else{
			$expense_code = $last_expense_code['expense_code']+1;
		}
		$str_length = 8;
		$str = substr("00000000{$expense_code}", -$str_length);

		$expense_code = "EX_$str";
		$data['records'] = $expense_code;		
		
		echo json_encode($data);
	break;
	
}
?>