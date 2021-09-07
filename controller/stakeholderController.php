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
		
        if(isset($_POST['status'])){
            $status=1;
        } else $status=0;

        $columns_value = array(
            'name'=>$name,
            'email'=>$email,
            'mobile'=>$mobile,
            'status'=>$status,
			'account_no'=>$_SESSION['account_no'],
        );

        if(isset($id) && $id == ""){
			//$dbClass->print_arrays($_REQUEST);			
			$return = $dbClass->insert("stakeholders", $columns_value);
			if($return) echo "1";
			else 	echo "0";
		}
		else{

            $condition_array = array(
                'id'=>$id
            );
            $return = $dbClass->update("stakeholders", $columns_value,$condition_array);
            if($return) echo "1";
            else 	echo "0";
		}
	break;
	
	case "grid_data":

		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		$employee_permission	    = $dbClass->getUserGroupPermission(11);
		$update_permission          = $dbClass->getUserGroupPermission(12);
		$entry_permission   	    = $dbClass->getUserGroupPermission(10);
		$delete_permission          = 0;//$dbClass->getUserGroupPermission(13);
		$employee_grid_permission   = $dbClass->getUserGroupPermission(15);
		$permission_grid_permission = $dbClass->getUserGroupPermission(16);
		
		$condition =	" where CONCAT(s.id, s.name, s.mobile, s.email) LIKE '%$search_txt%' and account_no= ".$_SESSION['account_no'];
		$countsql = "SELECT count(id) 
					FROM stakeholders s
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
			$sql = "SELECT s.id, s.name, s.mobile, s.email, (CASE  s.status when 1 THEN 'Active' when 0 THEN 'Inactive' end) status,
					$employee_permission as permission_status, $update_permission as update_status,	$delete_permission as delete_status
					FROM stakeholders s
					$condition
					ORDER BY s.id ASC
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
            $emp_details = $dbClass->getResultList("SELECT id, name, mobile,email, status
							FROM stakeholders 
							WHERE id='$emp_id'");
            //echo $emp_details; die;
            foreach ($emp_details as $row){
                $data['records'][] = $row;
            }
            echo json_encode($data);
        }
        break;



}
?>