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
		if(isset($agreement_id) && $agreement_id == ""){
			$attachment  = "";	
			if(isset($_FILES['attached_document']) && $_FILES['attached_document']['name']!= ""){
				$desired_dir = "../images/agreemenet";
				chmod( "../images/agreemenet", 0777);
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
					$attachment  = "images/agreemenet/".$attachment;					
				}
				else {
					echo $img_error_ln;die;
				}			
			}
			else{
				$attachment  = "";	
			}
			$columns_value = array(
				'start_date'=>$date,
				'end_date'=>(isset($end_date) && $end_date != "" )?$end_date:NULL,
				'current_rent'=>$rent,
				'advance'=>$advance,
                'project_id'=>$project_id,
				'account_no'=>$_SESSION['account_no'],
                'agreement'=>$attachment,
				'status'=>$status,
				'renter_id' =>$renter_id,
				'flat_id' =>$flat_id,
				'note'=>$details,
				'created_by'=>$loggedUser	
			);
			
			$return = $dbClass->insert("renter_flat_agreement", $columns_value);

			if($return){
				echo "1";
			}
			else 	echo "0";
		}
		else{	
			if(isset($_FILES['attached_document']) && $_FILES['attached_document']['name']!= ""){
				$desired_dir = "../images/agreemenet";
				chmod( "../images/agreemenet", 0777);
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
					$attachment  = "images/agreemenet/".$attachment;					
				}
				else {
					echo $img_error_ln;die;
				}

				$columns_value = array(
					'start_date'=>$date,
					'end_date'=>(isset($end_date) && $end_date != "" )?$end_date:NULL,
					'current_rent'=>$rent,
					'advance'=>$advance,
					'project_id'=>$project_id,
					'account_no'=>$_SESSION['account_no'],
					'agreement'=>$attachment,
					'renter_id' =>$renter_id,
					'flat_id' =>$flat_id,
					'status'=>$status,
					'note'=>$details	
				);						
			}
			else{
				$columns_value = array(
					'start_date'=>$date,
					'end_date'=>(isset($end_date) && $end_date != "" )?$end_date:NULL,
					'current_rent'=>$rent,
					'advance'=>$advance,
					'project_id'=>$project_id,
					'account_no'=>$_SESSION['account_no'],
					'renter_id' =>$renter_id,
					'flat_id' =>$flat_id,
					'status'=>$status,
					'note'=>$details	

				);	
			}
			$condition_array = array(
				'id'=>$agreement_id
			);	
			$return = $dbClass->update("renter_flat_agreement", $columns_value, $condition_array);
							
			if($return) echo "2";
			else 	echo "0";
		}
	break;


    case "grid_data":
        $start = ($page_no*$limit)-$limit;
        $end   = $limit;
        $data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
        $entry_permission   	    = $dbClass->getUserGroupPermission(122);
        $update_permission          = $dbClass->getUserGroupPermission(124);
        $delete_permission          = $dbClass->getUserGroupPermission(125);
        $grid_permission   			= $dbClass->getUserGroupPermission(123);


        $condition = "";
        //# advance search for grid
        if($search_txt == "Print" || $search_txt == "Advance_search"){
            // for advance condition
        }
        // textfield search for grid
        else{
            $condition .=	" where CONCAT(ifnull(r.full_name,''), ifnull(p.project_name,''), ifnull(f.flat_name,''), ifnull(ag.start_date,'')) LIKE '%$search_txt%' and ag.account_no= ".$_SESSION['account_no'];
        }
        $countsql = "SELECT count(id)  
					FROM
					(	
						SELECT ag.id, ag.renter_id, ag.project_id, ag.flat_id, ag.start_date, ag.end_date, ag.current_rent, ag.advance, ag.note,
						r.full_name AS renter_name, p.project_name, f.flat_name
						FROM renter_flat_agreement ag
						LEFT JOIN renter r ON r.id=ag.renter_id
						LEFT JOIN project_infos p ON p.project_code=ag.project_id
						LEFT JOIN flat_infos f  ON f.id=ag.flat_id
						$condition
					) A";
  
        //echo $countsql; die;
        $stmt = $conn->prepare($countsql);
        $stmt->execute();
        $total_records = $stmt->fetchColumn();
        $data['total_records'] = $total_records;
        $data['entry_status'] = $entry_permission;
        $total_pages = $total_records/$limit;
        $data['total_pages'] = ceil($total_pages);
        if($grid_permission==1){
            $sql = "SELECT  id, renter_name, project_name, flat_name, note, status_text,start_date,current_rent, advance,
					$update_permission as update_status, $delete_permission as delete_status	
					FROM
					(
						SELECT ag.id, ag.renter_id, ag.project_id, ag.flat_id, ag.start_date, ag.end_date, ag.current_rent, ag.advance, ag.note,
						r.full_name AS renter_name, p.project_name, f.flat_name, CASE ag.status WHEN 0 THEN 'Running' WHEN 1 THEN 'Closed' END status_text, ag.status, ag.created_by
						FROM renter_flat_agreement ag
						LEFT JOIN renter r ON r.id=ag.renter_id
						LEFT JOIN project_infos p ON p.project_code=ag.project_id
						LEFT JOIN flat_infos f  ON f.id=ag.flat_id
						$condition
						ORDER BY ag.id ASC
						LIMIT $start, $end
					) A";
           // echo $sql;die;

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $data['records'][] = $row;
            }
            echo json_encode($data);
        }
        break;


/*
    case "get_expense_details":
		$data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
		$update_permission = $dbClass->getUserGroupPermission(103);
		if($update_permission==1){
			$sql = "SELECT id, head_id, date, reference_doc, amount, note, head_name as headName, project_code, project_name
					FROM (
						SELECT e.id, e.head_id, e.date, e.reference_doc, e.amount, e.note, h.name, hh.name parent_name, pi.project_code, pi.project_name, 
						CASE 	
						   WHEN h.parent is NULL THEN h.name
							WHEN h.parent is NOT NULL THEN CONCAT(hh.name,' >> ',h.name)
						END head_name,
						CASE 	
							WHEN h.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' WHEN 3 THEN 'Liabilities' 
						END head_type_name  
						FROM accounting_income_expances e 
						LEFT JOIN accounting_head h on h.id = e.head_id
						LEFT JOIN accounting_head hh on hh.id = h.parent
						LEFT JOIN project_infos pi on pi.project_code = e.project_id
						WHERE e.id = $expense_id and .account_no= ".$_SESSION['account_no']."
					)A";
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
		if($dbClass->update("accounting_income_expances",$columns_value, $condition_array)){
			unlink("../images/expense/".$file_name);
			echo 1;
		}
		else
			echo 0;			 
	break;
	
	case "delete_expense":
		$prev_attachment = $dbClass->getSingleRow("select reference_doc from accounting_income_expances where id=$expense_id and e.account_no= ".$_SESSION['account_no']);
        if(isset($prev_attachment['reference_doc']) && $prev_attachment['reference_doc']!=''){
            unlink("../".$prev_attachment['reference_doc']);
        }		$condition_array = array(
			'id'=>$expense_id
		);
		if($dbClass->delete("accounting_income_expances", $condition_array)){
			echo 1;
		}
		else
			echo 0;			 
	break;
	
	*/
	case "renter_agreement":
		$data = array();
		$data['toal_records'] =0;
		$head_info = $dbClass->getSingleRow("select * from accounting_head where id=$head_id");		
		if(!is_null($head_info['flat_id']) && $head_info['flat_id']!="") {
			$data['toal_records'] =1;
			$agreement_list = $dbClass->getResultList("SELECT ag.id, p.project_name, p.project_code, f.flat_name, r.full_name, f.current_rent
							 from renter_flat_agreement ag 
							 LEFT JOIN project_infos p ON p.project_code=ag.project_id
							 LEFT JOIN renter r ON r.id=ag.renter_id
							 LEFT JOIN flat_infos f ON f.id=ag.flat_id
							 where ag.STATUS=1 AND  ag.flat_id = ".$head_info['flat_id']);
			
			foreach ($agreement_list as $row){
				$data['records'][] = $row;
			}
		}
					
		echo json_encode($data);
	break;
		
	case "get_agreement_details":
		$update_permission = $dbClass->getUserGroupPermission(10);
		if($update_permission==1){
			$agreement_details = $dbClass->getResultList("SELECT ag.id, ag.renter_id, ag.project_id, ag.flat_id, ag.start_date, ifnull(ag.end_date,'') as end_date, ag.current_rent, ag.advance, ag.note,
							r.full_name AS renter_name, p.project_name, f.flat_name, ag.status, ag.agreement, ag.note
							FROM renter_flat_agreement ag
							LEFT JOIN renter r ON r.id=ag.renter_id
							LEFT JOIN project_infos p ON p.project_code=ag.project_id
							LEFT JOIN flat_infos f  ON f.id=ag.flat_id
							WHERE ag.id='$agreement_id'  and f.account_no= ".$_SESSION['account_no']);
			//echo $project_details; die;
			foreach ($agreement_details as $row){
				$data['records'][] = $row;
			}			
			echo json_encode($data);
		}
	break;


}
?>