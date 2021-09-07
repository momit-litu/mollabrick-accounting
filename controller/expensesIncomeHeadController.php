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
	    //var_dump($_POST);die;
		if(isset($category_id) && $category_id == ""){
			$check_category_name_availability = $dbClass->getSingleRow("select count(name) as record_no from accounting_head where name='$category_name' and head_type=$head_type and account_no= ".$_SESSION['account_no']);
			if($check_category_name_availability['record_no']!=0) { echo 5; die;}
			
			if(isset($parent_head_id) && $parent_head_id!= ""){
				$parent = $parent_head_id;
			}else{
				$parent = NULL;
			}
			$columns_value = array(
				'name'=>$category_name,
				'code'=>$category_code,
				'parent'=>$parent,
				'account_no'=>$_SESSION['account_no'],
				'head_type'=>$head_type
			);
			if(isset($parent) && $parent!=""){
			    $columns_value['parent']=$parent;
            }
			if(isset($project_id) && $project_id!="" ){
			    $columns_value['project_id']=$project_id;
            }
			//var_dump($columns_value);die;
			$return = $dbClass->insert("accounting_head", $columns_value);
			if($return){ 
				echo "1";
			}else "0";
		}
		else if(isset($category_id) && $category_id>0){
			$check_category_name_availability = $dbClass->getSingleRow("select count(name) as record_no from accounting_head where name='$category_name' and head_type=$head_type and id!=$category_id  and account_no= ".$_SESSION['account_no']);
			if($check_category_name_availability['record_no']!=0) { echo 5; die;}
			
			if($parent_head_id != ""){
				$parent = $parent_head_id;
			}else{
				$parent = NULL;
			}
			$columns_value = array(
				'name'=>$category_name,
				'code'=>$category_code,
				'parent'=>$parent,
				'head_type'=>$head_type
			);
            if(isset($parent)  && $parent!=""){
                $columns_value['parent']=$parent;
            }
            if(isset($project_id) && $project_id!=""){
                $columns_value['project_id']=$project_id;
            }
			$condition_array = array(
				'id'=>$category_id
			);
		//	var_dump($columns_value); die;
			$return = $dbClass->update("accounting_head",$columns_value, $condition_array);
			if($return) echo "2";
			else        echo "0";			 
		}
	break;
	
	case "expanse_grid_data":
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		
		$entry_permission   	    = $dbClass->getUserGroupPermission(106);
		$delete_permission          = $dbClass->getUserGroupPermission(108);
		$update_permission          = $dbClass->getUserGroupPermission(107);
		
		$grid_permission = $dbClass->getUserGroupPermission(109);
		
		$countsql = "SELECT count(id)
					FROM(
						SELECT c.id, c.code, c.name, c.head_type, ifnull(ec.name,'') parent_name , c.account_no
						FROM accounting_head c
						LEFT JOIN accounting_head ec on c.parent = ec.id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, code, name, parent_name) LIKE '%$search_txt%'
					AND head_type=1 
					AND id !=2
					AND account_no= ".$_SESSION['account_no'];
		//echo $countsql;die;
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records;
		$data['entry_status'] = $entry_permission;	
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages); 
		if($grid_permission==1){
			$sql = 	"SELECT id, name, code, name, parent_name, head_type_name,
					$update_permission as update_status, $delete_permission as delete_status
					FROM(
						SELECT c.id, c.code, c.name, c.head_type, ifnull(ec.name,'') parent_name, c.account_no,
						CASE WHEN c.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' 
						WHEN 3 THEN 'Liabilities' END head_type_name
						FROM accounting_head c
						LEFT JOIN accounting_head ec on c.parent = ec.id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, name, code, parent_name) LIKE '%$search_txt%'
					AND head_type=1
					AND id!=2
					AND account_no= ".$_SESSION['account_no']."
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

    case "income_grid_data":
        $start = ($page_no*$limit)-$limit;
        $end   = $limit;
        $data = array();

        $entry_permission   	    = $dbClass->getUserGroupPermission(106);
        $delete_permission          = $dbClass->getUserGroupPermission(108);
        $update_permission          = $dbClass->getUserGroupPermission(107);

        $grid_permission = $dbClass->getUserGroupPermission(109);

        $countsql = "SELECT count(id)
					FROM(
						SELECT c.id, c.code, c.name, c.head_type, ifnull(ec.name,'') parent_name, c.account_no
						FROM accounting_head c
						LEFT JOIN accounting_head ec on c.parent = ec.id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, code, name, parent_name) LIKE '%$search_txt%'
					AND head_type=2
					AND id !=1 
					AND account_no= ".$_SESSION['account_no'];
        //echo $countsql;die;
        $stmt = $conn->prepare($countsql);
        $stmt->execute();
        $total_records = $stmt->fetchColumn();
        $data['total_records'] = $total_records;
        $data['entry_status'] = $entry_permission;
        $total_pages = $total_records/$limit;
        $data['total_pages'] = ceil($total_pages);
        if($grid_permission==1){
            $sql = 	"SELECT id, name, code, name, parent_name, head_type_name,
					$update_permission as update_status, $delete_permission as delete_status
					FROM(
						SELECT c.id, c.code, c.name, c.head_type, ifnull(ec.name,'') parent_name, c.account_no, 
						CASE WHEN c.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'income' 
						WHEN 3 THEN 'Liabilities' END head_type_name
						FROM accounting_head c
						LEFT JOIN accounting_head ec on c.parent = ec.id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, name, code, parent_name) LIKE '%$search_txt%'
					AND head_type=2
					AND id!=1
					AND account_no= ".$_SESSION['account_no']."
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
		$update_permission = $dbClass->getUserGroupPermission(107);
		if($update_permission==1){
			$sql = "SELECT c.id, c.code, c.name, ec.id parent_id, ec.name parent_name, c.head_type, ifnull(pi.project_name,'') project_name, c.project_id
					FROM accounting_head c
					LEFT JOIN accounting_head ec on c.parent = ec.id
					LEFT JOIN project_infos pi on pi.project_code = c.project_id
					WHERE c.id=$category_id AND c.account_no= ".$_SESSION['account_no'];
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
		$delete_permission = $dbClass->getUserGroupPermission(108);
		if($delete_permission==1){
			$condition_array = array(
				'id'=>$category_id
			);

			$return = $dbClass->delete("accounting_head", $condition_array);
		}
		if($return==1) echo "1";
		else 		   echo "0";
	break;
	
	case "expanse_parent_head_info":
		$sql_query = "SELECT id, CONCAT_WS(' >> ',name,code) parentName
					FROM accounting_head
					WHERE CONCAT_WS('-> ',code,name) LIKE '%" . $term . "%' AND parent is NULL 
					AND head_type = 1
					AND id !=2
					AND account_no= ".$_SESSION['account_no']."
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


    case "income_parent_head_info":
        $sql_query = "SELECT id, CONCAT_WS(' >> ',name,code) parentName
					FROM accounting_head
					WHERE CONCAT_WS('-> ',code,name) LIKE '%" . $term . "%' AND parent is NULL 
					AND head_type = 2
					AND id!=1
					AND account_no= ".$_SESSION['account_no']."
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
	
	 case "liability_parent_head_info":
        $sql_query = "SELECT id, CONCAT_WS(' >> ',name,code) parentName
					FROM accounting_head
					WHERE CONCAT_WS('-> ',code,name) LIKE '%" . $term . "%' AND parent is NULL 
					AND head_type = 3 AND editable !=0
					AND id!=1
					AND account_no= ".$_SESSION['account_no']."
					ORDER BY name";
       // echo $sql_query;die;
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
	    else{
	        $data['records'] ="";
	    }
		echo json_encode($data);
	break;
	
}
?>