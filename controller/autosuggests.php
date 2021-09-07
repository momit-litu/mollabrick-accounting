<?php 
session_start();
include '../includes/static_text.php';
include("../dbConnect.php");
include("../dbClass.php");

$dbClass = new dbClass;
$conn       = $dbClass->getDbConn();
$loggedUser = $dbClass->getUserId();	
$user_type = $_SESSION['user_type'];
$user_id	 = $_SESSION['user_id'];

extract($_REQUEST);
switch ($q){
	case "employee_info":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}

		$sql_query = "SELECT e.emp_id, CONCAT(e.emp_id,' >> ',e.full_name,' >> ',d.department_name)empName 
					FROM emp_infos e 
					LEFT JOIN hrm_departments d on d.department_id = e.department_id where 
					ORDER BY e.emp_id";
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();
		
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["emp_id"],'label' => $row["empName"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Employee Found !!!");
		}						
		echo json_encode($json);
	break;
	
	case "project_info":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
		
		$sql_query = "SELECT project_code,project_name
					FROM project_infos
					WHERE status = 1 and account_no= ".$_SESSION['account_no']."
					ORDER BY project_code";
		//echo $sql_query;die;
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["project_code"],'label' => $row["project_name"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Project Found !!!");
		}						
		echo json_encode($json);
	break;
	
	case "employee_infos":
		$sql_query = "SELECT e.emp_id, e.full_name, d.designation_title,
					CONCAT(e.emp_id,' >> ',e.full_name,' >> ',d.designation_title) empName
					FROM emp_infos e
					LEFT JOIN hrm_designations d on d.id = e.designation_id 
					WHERE  e.account_no= ".$_SESSION['account_no']."
					ORDER BY e.emp_id";
		//echo $sql_query;die;
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();
		$json = array();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["emp_id"],'label' => $row["empName"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Employee Found !!!");
		}						
		echo json_encode($json);
	break;
	
	
	case "renter_info":
		$sql_query = "SELECT id, full_name as renter_name
					FROM renter 
					WHERE  account_no= ".$_SESSION['account_no']." and status=1
					ORDER BY full_name";
		//echo $sql_query;die;
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();
		$json = array();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["id"],'label' => $row["renter_name"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Renter Found !!!");
		}						
		echo json_encode($json);
	break;
	
	case "leave_category_name":
		$sql_query = "SELECT c.id, c.category_name
					FROM hrm_leave_categories c";
		//echo $sql_query;die;
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();
		$json = array();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["id"],'label' => $row["category_name"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Category Found !!!");
		}						
		echo json_encode($json);
	break;
	
	case "expense_head_info_old":
		$sql_query = "SELECT c.id, c.code,
					CASE 	
						WHEN c.parent is NULL THEN c.name
						WHEN c.parent is NOT NULL THEN CONCAT(ec.name,' >> ',c.name)
					END head_name 
					FROM expense_head c
					LEFT JOIN expense_head ec on c.parent = ec.id
					ORDER BY c.id";
		//echo $sql_query;die;
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();
		$json = array();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["id"],'label' => $row["head_name"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Head Found !!!");
		}						
		echo json_encode($json);
	break;
	
	case "expense_parent_info":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
		
		$sql_query = "SELECT c.id, c.code,
					CASE 	
					   WHEN c.parent is NULL THEN c.name
						WHEN c.parent is NOT NULL THEN CONCAT(ec.name,' >> ',c.name)
					END head_name 
					FROM expense_head c
					LEFT JOIN expense_head ec on c.parent = ec.id
					WHERE c.parent is not null
					and c.account_no= ".$_SESSION['account_no']."
					ORDER BY c.id";
		//echo $sql_query;die;
		$stmt = $conn->prepare($sql_query);
		$stmt->execute();
		$json = array();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		$count = $stmt->rowCount();
		if($count>0){
			foreach ($result as $row) {
				$json[] = array('id' => $row["id"],'label' => $row["head_name"]);
			}
		} else {
			$json[] = array('id' => "0",'label' => "No Parent Found !!!");
		}						
		echo json_encode($json);
	break;

    //chaki

    case "expense_head_info":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
		
        $sql_query = "SELECT id, 
        CASE WHEN parent_name is null THEN CONCAT(name,' (',head_type_name,')')
        WHEN parent_name is NOT NULL THEN CONCAT(parent_name,' >> ',name,' (',head_type_name,')')
        END headName
        FROM(
            SELECT h.*, p.name as parent_name,
            CASE 	
                WHEN h.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' WHEN 3 THEN 'Liabilities' 
                END head_type_name 
            FROM accounting_head h 
            LEFT JOIN accounting_head p on h.parent = p.id
            WHERE CONCAT(h.name,h.name) LIKE '%$term%' 
            AND h.head_type = 1  AND h.editable !=0 and h.account_no= ".$_SESSION['account_no']."
            ORDER BY h.id
            )A";
        //echo $sql_query;die;
        $stmt = $conn->prepare($sql_query);
        $stmt->execute();
        $json = array();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count>0){
            foreach ($result as $row) {
                $json[] = array('id' => $row["id"],'label' => $row["headName"]);
            }
        } else {
            $json[] = array('id' => "0",'label' => "No Head Found !!!");
        }
        echo json_encode($json);
        break;

    case "liability_head_info":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
		
        $sql_query = "SELECT id, 
        CASE WHEN parent_name is null THEN name
        WHEN parent_name is NOT NULL THEN CONCAT(parent_name,' >> ',name)
        END headName
        FROM(
            SELECT h.*, p.name as parent_name,
            CASE 	
                WHEN h.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' WHEN 3 THEN 'Liabilities' 
                END head_type_name 
            FROM accounting_head h 
            LEFT JOIN accounting_head p on h.parent = p.id
            WHERE CONCAT(h.name,h.name) LIKE '%$term%' 
            AND h.head_type = 3  AND h.editable !=0 and h.account_no= ".$_SESSION['account_no']."
            ORDER BY h.id
            )A";
        //echo $sql_query;die;
        $stmt = $conn->prepare($sql_query);
        $stmt->execute();
        $json = array();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count>0){
            foreach ($result as $row) {
                $json[] = array('id' => $row["id"],'label' => $row["headName"]);
            }
        } else {
            $json[] = array('id' => "0",'label' => "No Head Found !!!");
        }
        echo json_encode($json);
        break;

    case "income_expense_head_info":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
		
        $sql_query = "SELECT id, 
        CASE WHEN parent_name is null THEN CONCAT(name,' (',head_type_name,')')
        WHEN parent_name is NOT NULL THEN CONCAT(parent_name,' >> ',name,' (',head_type_name,')')
        END headName
        FROM(
            SELECT h.*, p.name as parent_name,
            CASE 	
                WHEN h.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' WHEN 3 THEN 'Liabilities' 
                END head_type_name 
            FROM accounting_head h 
            LEFT JOIN accounting_head p on h.parent = p.id
            WHERE CONCAT(h.name,h.name) LIKE '%$term%' 
            AND (h.head_type = 1 OR h.head_type = 2)  AND h.editable !=0 and h.account_no= ".$_SESSION['account_no']."
            ORDER BY h.id
            )A";
        //echo $sql_query;die;
        $stmt = $conn->prepare($sql_query);
        $stmt->execute();
        $json = array();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count>0){
            foreach ($result as $row) {
                $json[] = array('id' => $row["id"],'label' => $row["headName"]);
            }
        } else {
            $json[] = array('id' => "0",'label' => "No Head Found !!!");
        }
        echo json_encode($json);
        break;


    //chaki
    case "income_head_info":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
		
        $sql_query = "SELECT id, 
        CASE WHEN parent_name is null THEN CONCAT(name,' (',head_type_name,')')
        WHEN parent_name is NOT NULL THEN CONCAT(parent_name,' >> ',name,' (',head_type_name,')')
        END headName
        FROM(
            SELECT h.*, p.name as parent_name,
            CASE 	
                WHEN h.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' WHEN 3 THEN 'Liabilities' 
                END head_type_name 
            FROM accounting_head h 
            LEFT JOIN accounting_head p on h.parent = p.id
            WHERE CONCAT(h.name,h.name) LIKE '%$term%' 
            AND h.head_type = 2 AND h.editable !=0 and h.account_no= ".$_SESSION['account_no']."
            ORDER BY h.id
            )A";
        //echo $sql_query;die;
        $stmt = $conn->prepare($sql_query);
        $stmt->execute();
        $json = array();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count>0){
            foreach ($result as $row) {
                $json[] = array('id' => $row["id"],'label' => $row["headName"]);
            }
        } else {
            $json[] = array('id' => "0",'label' => "No Head Found !!!");
        }
        echo json_encode($json);
        break;


    case "project_name":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
		
        $sql_query = "SELECT project_code, project_name
	FROM
	(  SELECT  p.project_code, p.project_name, p.project_address
		FROM project_infos p
		WHERE CONCAT(p.project_code, p.project_name, p.project_address) LIKE '%$term%'   and p.account_no= ".$_SESSION['account_no']."
		ORDER BY p.project_code
	) A";
        //echo $sql_query;die;
        $stmt = $conn->prepare($sql_query);
        $stmt->execute();
        $json = array();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count>0){
            foreach ($result as $row) {
                $json[] = array('id' => $row["project_code"],'label' => $row["project_name"]);
            }
        } else {
            $json[] = array('id' => "0",'label' => "No Head Found !!!");
        }
        echo json_encode($json);
        break;

    case "stakeholder_name":
		$json = array();
		if(!isset($_SESSION)){	echo json_encode($json);}
        $sql_query = "SELECT id, name
						FROM
						(  SELECT  id, name
							FROM stakeholders p
							WHERE CONCAT(id, name) LIKE '%$term%'   and p.account_no= ".$_SESSION['account_no']."
							ORDER BY id
						) A";
        //echo $sql_query;die;
        $stmt = $conn->prepare($sql_query);
        $stmt->execute();
        $json = array();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count>0){
            foreach ($result as $row) {
                $json[] = array('id' => $row["id"],'label' => $row["name"]);
            }
        } else {
            $json[] = array('id' => "0",'label' => "No Head Found !!!");
        }
        echo json_encode($json);
        break;

}
?>