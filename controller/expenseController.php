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
	
		if(isset($expense_id) && $expense_id == ""){
			$attachment  = "";	
			if(isset($_FILES['attached_document']) && $_FILES['attached_document']['name']!= ""){
				$desired_dir = "../images/expense";
				chmod( "../images/expense", 0777);
				$file_name = $_FILES['attached_document']['name'];
				$file_size =$_FILES['attached_document']['size'];
				$file_tmp =$_FILES['attached_document']['tmp_name'];
				$file_type=$_FILES['attached_document']['type'];	
				if($file_size < $file_max_length){
					if(file_exists("$desired_dir/".$file_name)==false){
						if(move_uploaded_file($file_tmp,"$desired_dir/".$file_name))
							$attachment = "$file_name";
					}
					else{//rename the file if another one exist
						$new_dir="$desired_dir/".time().$file_name;
						if(rename($file_tmp,$new_dir))
							$attachment =time()."$file_name";				
					}
					$attachment  = "images/expense/".$attachment;					
				}
				else {
					echo $img_error_ln;die;
				}			
			}
			else{
				$attachment  = "";	
			}
			
			$columns_value = array(
				'date'=>$date,
				'amount'=>$amount,
				'category_id'=>$head_id,
				'reference_doc'=>$attachment,
				'note'=>$details,
				'created_by'=>$loggedUser
			);	
			
			$return = $dbClass->insert("expenses", $columns_value);
			
			if($return) echo "1";
			else 	echo "0";
		}
		else{	
			if(isset($_FILES['attached_document']) && $_FILES['attached_document']['name']!= ""){
				$desired_dir = "../images/expense";
				chmod( "../images/expense", 0777);
				$file_name = $_FILES['attached_document']['name'];
				$file_size =$_FILES['attached_document']['size'];
				$file_tmp =$_FILES['attached_document']['tmp_name'];
				$file_type=$_FILES['attached_document']['type'];	
				if($file_size < $file_max_length){
					if(file_exists("$desired_dir/".$file_name)==false){
						if(move_uploaded_file($file_tmp,"$desired_dir/".$file_name))
							$attachment = "$file_name";
					}
					else{//rename the file if another one exist
						$new_dir="$desired_dir/".time().$file_name;
						if(rename($file_tmp,$new_dir))
							$attachment =time()."$file_name";				
					}
					$attachment  = "images/expense/".$attachment;					
				}
				else {
					echo $img_error_ln;die;
				}

				$columns_value = array(
					'date'=>$date,
					'amount'=>$amount,
					'category_id'=>$head_id,
					'reference_doc'=>$attachment,
					'note'=>$details,
					'created_by'=>$loggedUser
				);						
			}
			else{
				$columns_value = array(
					'date'=>$date,
					'amount'=>$amount,
					'category_id'=>$head_id,
					'note'=>$details,
					'created_by'=>$loggedUser
				);	
			}
			$condition_array = array(
				'id'=>$expense_id
			);	
			$return = $dbClass->update("expenses", $columns_value, $condition_array);
							
			if($return) echo "2";
			else 	echo "0";
		}
	break;
	
	case "grid_data":	
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		
		$entry_permission   	    = $dbClass->getUserGroupPermission(83);
		$update_permission          = $dbClass->getUserGroupPermission(84);
		$delete_permission          = $dbClass->getUserGroupPermission(85);
		$expense_grid_permission    = $dbClass->getUserGroupPermission(86);
		
		
		$condition = "";
		//# advance search for grid		
		if($search_txt == "Print" || $search_txt == "Advance_search"){		
			// for advance condition 			
		}
		// textfield search for grid
		else{
			$condition .=	" where CONCAT(e.id, e.amount, ec.name) LIKE '%$search_txt%' ";			
		}
		$countsql = "SELECT count(e.id)
					FROM expenses e 
					LEFT JOIN expense_head c on c.id = e.category_id
					LEFT JOIN expense_head ec on ec.id = c.parent
					$condition";
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records; 
		$data['entry_status'] = $entry_permission; 
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages);
		if($expense_grid_permission==1){
			$sql = "SELECT e.id, e.date, e.reference_doc, format(e.amount,2) amount, e.note, c.name, ec.name parent_name, 
					$update_permission as update_status, $delete_permission as delete_status,
					CASE 	
					   WHEN c.parent is NULL THEN c.name
						WHEN c.parent is NOT NULL THEN CONCAT(ec.name,' >> ',c.name)
					END head_name 
					FROM expenses e 
					LEFT JOIN expense_head c on c.id = e.category_id
					LEFT JOIN expense_head ec on ec.id = c.parent
					$condition
					ORDER BY e.id DESC
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
	
	case "get_expense_details":
		$update_permission = $dbClass->getUserGroupPermission(84);
		if($update_permission==1){
			$sql = "SELECT e.id, e.date, e.reference_doc, format(e.amount,2) amount, e.note, c.id head_id, 
					CASE 	
						WHEN c.parent is NULL THEN c.name
						WHEN c.parent is NOT NULL THEN CONCAT(ec.name,' >> ',c.name)
					END head_name 
					FROM expenses e 
					LEFT JOIN expense_head c on c.id = e.category_id
					LEFT JOIN expense_head ec on ec.id = c.parent
					WHERE e.id= $expense_id";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			foreach ($result as $row) {
				$data['records'][] = $row;
			}			
			echo json_encode($data);	
		}			
	break;
	
	case "delete_attached_file":
	 	$columns_value = array(
			'reference_doc'=>''
		);	 
		$condition_array = array(
			'id'=>$expense_id
		);
		if($dbClass->update("expenses",$columns_value, $condition_array)){
			unlink("../images/expense/".$file_name);
			echo 1;
		}
		else
			echo 0;			 
	break;
	
	case "delete_expense":
		$prev_attachment = $dbClass->getSingleRow("select reference_doc from expenses where id=$expense_id");
		unlink("../".$prev_attachment['reference_doc']);
		$condition_array = array(
			'id'=>$expense_id
		);
		if($dbClass->delete("expenses", $condition_array)){
			echo 1;
		}
		else
			echo 0;			 
	break;
	
	case "expenseReport":
		$condition = "";
		if($head_name != ''){
			$condition  = "  WHERE e.category_id='$head_id' OR c.parent = '$head_id' ";
		}
		else if($start_date != '' && $end_date == ''){
			$condition  = " WHERE e.date >= '$start_date' ";
		}
		else if($start_date == '' && $end_date != ''){
			$condition  = " WHERE e.date <= '$end_date' ";
		}
		else if($start_date != '' && $end_date != ''){
			$condition  = " WHERE e.date between '$start_date' and '$end_date' ";
		}
		
		$data = array();	
		$details = $dbClass->getResultList("SELECT e.id, c.id parent_id, c.code expense_code, e.date, e.amount,
											CASE 	
											   WHEN c.parent is NULL THEN c.name
												WHEN c.parent is NOT NULL THEN CONCAT(ec.name,' >> ',c.name)
											END head_name 
											FROM expenses e 
											LEFT JOIN expense_head c on c.id = e.category_id 
											LEFT JOIN expense_head ec on c.parent = ec.id
											$condition
											ORDER BY head_name");
		foreach ($details as $row){
			$data['records'][] = $row;	
		}
		echo json_encode($data);
	break;
	
	case "expenseIncomeReport":
		$condition = " group by category_id ";
		if($head_name != ''){
			$condition  = " WHERE e.category_id = $head_id group by e.date ";
		}
		else if($start_date != '' && $end_date == ''){
			$condition  = " WHERE e.date >= '$start_date' group by e.category_id ";
		}
		else if($start_date == '' && $end_date != ''){
			$condition  = " WHERE e.date <= '$end_date' group by e.category_id ";
		}
		else if($start_date != '' && $end_date != ''){
			$condition  = " WHERE e.date between '$start_date' and '$end_date' group by e.category_id ";
		}
		echo "SELECT e.id, c.id parent_id, c.code expense_code, e.date, e.amount,
											CASE 	
												bWHEN c.parent is NULL THEN c.name
												WHEN c.parent is NOT NULL THEN CONCAT(ec.name,' >> ',c.name)
											END head_name 
											FROM expenses e 
											LEFT JOIN expense_head c on c.id = e.category_id 
											LEFT JOIN expense_head ec on c.parent = ec.id
											$condition
											ORDER BY head_name";die;
		$data = array();	
		$details = $dbClass->getResultList("SELECT e.id, c.id parent_id, c.code expense_code, e.date, e.amount,
											CASE 	
												bWHEN c.parent is NULL THEN c.name
												WHEN c.parent is NOT NULL THEN CONCAT(ec.name,' >> ',c.name)
											END head_name 
											FROM expenses e 
											LEFT JOIN expense_head c on c.id = e.category_id 
											LEFT JOIN expense_head ec on c.parent = ec.id
											$condition
											ORDER BY head_name");
		foreach ($details as $row){
			$data['records'][] = $row;	
		}
		echo json_encode($data);
	break;
}
?>