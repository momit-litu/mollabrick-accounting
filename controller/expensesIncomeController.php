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
		//var_dump($_REQUEST);die;	
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
				'head_id'=>$head_id,
                'project_id'=>$project_id,
				'account_no'=>$_SESSION['account_no'],
                'reference_doc'=>$attachment,
				'stakeholder_id' =>(isset($stakeholder) && $stakeholder!="")?$stakeholder:NULL,
				'agreement_id' =>(isset($agreement_id))?$agreement_id:NULL,
				'note'=>$details,
				'created_by'=>$loggedUser	
			);
			//$dbClass->print_arrays($columns_value);
			
			$return = $dbClass->insert("accounting_income_expances", $columns_value);
			
			if($return){
				if(isset($deposit) && $deposit =='on'){
					$columns_value = array(
						'date'=>$date,
						'amount'=>$amount,
						'head_id'=>1,
						'project_id'=>$project_id,
						'account_no'=>$_SESSION['account_no'],
						'reference_doc'=>$attachment,
						'note'=>$details,
						'created_by'=>$loggedUser,
						'stakeholder_id'=>$stakeholder
					);
					$return = $dbClass->insert("accounting_income_expances", $columns_value);
				}
				echo "1";
			}
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
					'head_id'=>$head_id,
                    'project_id'=>$project_id,
                    'reference_doc'=>$attachment,
					'note'=>$details,
					'created_by'=>$loggedUser
				);						
			}
			else{
				$columns_value = array(
					'date'=>$date,
					'amount'=>$amount,
					'head_id'=>$head_id,
                    'project_id'=>$project_id,
                    'note'=>$details,
					'created_by'=>$loggedUser
				);	
			}
			$condition_array = array(
				'id'=>$expense_id
			);	
			$return = $dbClass->update("accounting_income_expances", $columns_value, $condition_array);
							
			if($return) echo "2";
			else 	echo "0";
		}
	break;

    //chaki
    case "expanse_grid_data":
		$start = ($page_no*$limit)-$limit;
		$end   = $limit;
		$data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
		$entry_permission   	    = $dbClass->getUserGroupPermission(102);
		$update_permission          = $dbClass->getUserGroupPermission(103);
		$delete_permission          = $dbClass->getUserGroupPermission(104);
		$expense_grid_permission    = $dbClass->getUserGroupPermission(105);
		
		
		$condition = "";
		//# advance search for grid		
		if($search_txt == "Print" || $search_txt == "Advance_search"){		
			// for advance condition 			
		}
		// textfield search for grid
		else{
			$condition .=	" where CONCAT(e.id, e.amount, h.name, e.date, ifnull(hh.name,'')) LIKE '%$search_txt%' and e.account_no= ".$_SESSION['account_no'];			
		}
		$countsql = "SELECT count(id)  
					FROM
					(	
						SELECT e.id, e.date, e.reference_doc, round(e.amount,2) amount, e.note, h.name, ifnull(hh.name,'') parent_name, 
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
						
						$condition
						AND h.head_type = 1
						AND h.id != 2
					) A";
		$stmt = $conn->prepare($countsql);
		$stmt->execute();
		$total_records = $stmt->fetchColumn();
		$data['total_records'] = $total_records; 
		$data['entry_status'] = $entry_permission; 
		$total_pages = $total_records/$limit;		
		$data['total_pages'] = ceil($total_pages);
		if($expense_grid_permission==1){
			$sql = "SELECT  id, date, reference_doc, amount, note, head_name as headName,project_name,
					$update_permission as update_status, $delete_permission as delete_status	
					FROM
					(
						SELECT e.id, DATE_FORMAT(e.date, '%Y-%d-%d') AS date, e.reference_doc, round(e.amount,2) amount, e.note, h.name, ifnull(hh.name,'') parent_name, pi.project_name,
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
						$condition
						AND h.head_type = 1
						AND h.id != 2
						ORDER BY e.id ASC
						LIMIT $start, $end
					) A";	
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

    //chaki
    case "income_grid_data":
        $start = ($page_no*$limit)-$limit;
        $end   = $limit;
        $data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
        $entry_permission   	    = $dbClass->getUserGroupPermission(102);
        $update_permission          = $dbClass->getUserGroupPermission(103);
        $delete_permission          = $dbClass->getUserGroupPermission(104);
        $expense_grid_permission    = $dbClass->getUserGroupPermission(105);


        $condition = "";
        //# advance search for grid
        if($search_txt == "Print" || $search_txt == "Advance_search"){
            // for advance condition
        }
        // textfield search for grid
        else{
            $condition .=	" where CONCAT(e.id, e.amount, h.name, e.date, ifnull(hh.name,'')) LIKE '%$search_txt%' and e.account_no= ".$_SESSION['account_no'];
        }
        $countsql = "SELECT count(id)  
					FROM
					(	
						SELECT e.id, DATE_FORMAT(e.date, '%Y-%d-%d') AS date, e.reference_doc, round(e.amount,2) amount, e.note, h.name, ifnull(hh.name,'') parent_name, 
						CASE 	
						   WHEN h.parent is NULL THEN h.name
							WHEN h.parent is NOT NULL THEN CONCAT(hh.name,' >> ',h.name)
						END head_name,
						CASE 	
							WHEN h.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'income' WHEN 3 THEN 'Liabilities' 
						END head_type_name  
						FROM accounting_income_expances e 
						LEFT JOIN accounting_head h on h.id = e.head_id
						LEFT JOIN accounting_head hh on hh.id = h.parent
						$condition
						AND h.head_type = 2
						AND h.id != 1
					) A";

        //echo $countsql; die;
        $stmt = $conn->prepare($countsql);
        $stmt->execute();
        $total_records = $stmt->fetchColumn();
        $data['total_records'] = $total_records;
        $data['entry_status'] = $entry_permission;
        $total_pages = $total_records/$limit;
        $data['total_pages'] = ceil($total_pages);
        if($expense_grid_permission==1){
            $sql = "SELECT  id, date, reference_doc, amount, note, head_name as headName,project_name,
					$update_permission as update_status, $delete_permission as delete_status	
					FROM
					(
						SELECT e.id, DATE_FORMAT(e.date, '%Y-%d-%d') AS date, e.reference_doc, round(e.amount,2) amount, e.note, h.name, ifnull(hh.name,'') parent_name, pi.project_name,
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
						$condition
						AND h.head_type = 2
						AND h.id != 1
						ORDER BY e.id ASC
						LIMIT $start, $end
					) A";
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
		$data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
		$update_permission = $dbClass->getUserGroupPermission(103);
		if($update_permission==1){
			$sql = "SELECT id, head_id, date, reference_doc, amount, note, head_name as headName, project_code, project_name
					FROM (
						SELECT e.id, e.head_id, DATE_FORMAT(e.date, '%Y-%d-%d') AS date, e.reference_doc, e.amount, e.note, h.name, ifnull(hh.name,'') parent_name, pi.project_code, pi.project_name, 
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
						WHERE e.id = $expense_id and e.account_no= ".$_SESSION['account_no']."
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
	
	case "expenseDetailsReport":
		$condition = "WHERE ah.id !=2
                    AND ah.head_type =1 ";
		if($head_name != ''){
			$head  = "(ex.head_id = $head_id or ah.parent = $head_id)";
		}
		if($project_id!=''){
		    $project = "ex.project_id = $project_id";
        }

        if(isset($head)){

            if(isset($project)){
                $condition = $condition." AND ".' ('.$head .' AND '. $project.')';
            }
            else{
                $condition = $condition." AND ".' '.$head ;

            }
        }
        else if(isset($project)){
            $condition = $condition." AND ".$project ;
        }


		if($start_date != '' && $end_date == ''){
			$condition =$condition. " AND  ex.date >= '$start_date' ";
		}
		else if($start_date == '' && $end_date != ''){
            $condition =$condition. " AND ex.date <= '$end_date'";
		}
		else if($start_date != '' && $end_date != ''){
            $condition =$condition. " AND  ex.date between '$start_date' and '$end_date'";
		}





		//echo $condition;die;
		$data = array();	
		$sql="SELECT id, date, amount, note, project_name,head_name
                FROM(
                     SELECT ex.id, ex.date , ex.amount, ex.note, ifnull(pi.project_name,'') as project_name, 
                     CASE 	
						   WHEN ahh.name is NULL THEN ah.name
							WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name
                    FROM accounting_income_expances ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
                    LEFT JOIN project_infos pi ON pi.project_code = ex.project_id
                    $condition
                    GROUP BY ex.id
                    )A";
		//echo $sql; die;
        $details = $dbClass->getResultList($sql);
		foreach ($details as $row){
			$data['records'][] = $row;	
		}
		echo json_encode($data);
	break;


	case "incomeDetailsReport":
        $condition = "WHERE ah.id !=1  AND  ah.head_type =2 ";
        if($head_name != ''){
            $head  = "(ex.head_id = $head_id or ah.parent = $head_id)";
        }
        if($project_id!=''){
            $project = "ex.project_id = $project_id";
        }

        if(isset($head)){

            if(isset($project)){
                $condition = $condition." AND ".' ('.$head .' AND '. $project.')';
            }
            else{
                $condition = $condition." AND ".' '.$head ;

            }
        }
        else if(isset($project)){
            $condition = $condition." AND ".$project ;
        }


        if($start_date != '' && $end_date == ''){
            $condition =$condition. " AND  ex.date >= '$start_date' ";
        }
        else if($start_date == '' && $end_date != ''){
            $condition =$condition. " AND ex.date <= '$end_date'";
        }
        else if($start_date != '' && $end_date != ''){
            $condition =$condition. " AND  ex.date between '$start_date' and '$end_date'";
        }

        //echo $condition;die;
        $data = array();
        $sql="SELECT id, date, amount, note, project_name,head_name
                FROM(
                     SELECT ex.id, DATE_FORMAT(ex.date, '%Y-%d-%d') AS date, ex.amount, ex.note, ifnull(pi.project_name,'') as project_name, 
                     CASE 	
						   WHEN ahh.name is NULL THEN ah.name
							WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name
                    FROM accounting_income_expances ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
                    LEFT JOIN project_infos pi ON pi.project_code = ex.project_id
                    $condition
                    GROUP BY ex.id
                    )A";
       // echo $sql; die;
        $details = $dbClass->getResultList($sql);
        foreach ($details as $row){
            $data['records'][] = $row;
        }
        echo json_encode($data);
        break;

    case "incomeExpenseReport":
        $condition = "WHERE ah.id !=2  AND ah.id !=1 /* AND ah.parent != 5*/ ";
        if($project_id!=''){
            $condition .= " AND ex.project_id = $project_id";
        }

        if($start_date != '' && $end_date == ''){
            $condition =$condition. " AND  ex.date >= '$start_date' ";
        }
        else if($start_date == '' && $end_date != ''){
            $condition =$condition. " AND ex.date <= '$end_date'";
        }
        else if($start_date != '' && $end_date != ''){
            $condition =$condition. " AND  ex.date between '$start_date' and '$end_date'";
        }


        //echo $condition;die;
        $data = array();
        $sql="SELECT id, date, amount, note, project_name, head_type,head_name
                FROM(
                     SELECT ex.id,DATE_FORMAT(ex.date, '%Y-%d-%d') AS date , ex.amount, ex.note, ifnull(pi.project_name,'') as project_name, ah.head_type,
                     CASE 	
						   WHEN ahh.name is NULL THEN ah.name
							WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name
                    FROM accounting_income_expances ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
                    LEFT JOIN project_infos pi ON pi.project_code = ex.project_id
                    $condition
                    GROUP BY ex.id
                    )A";
       // echo $sql; die;
        $details = $dbClass->getResultList($sql);
        foreach ($details as $row){
            $data['records'][] = $row;
        }
        echo json_encode($data);
        break;




    case "summaryReport":
        $condition = "WHERE";
        if($head_id != ''){
            $head  = "(ex.head_id = $head_id or ah.parent = $head_id)";
        }
        if($project_id!=''){
            $project = "ex.project_id = $project_id";
        }

        if(isset($head)){
            if(isset($project)){
                $condition = $condition.' ('.$head .' AND '. $project.')';
            }
            else{
                $condition = $condition.' '.$head ;
            }
        }
        else if(isset($project)){
            $condition = $condition." ".$project ;
        }

        if($condition != 'WHERE'){
            $condition = $condition.' AND ';
        }


        if($start_date != '' && $end_date == ''){
            $condition =$condition. " ex.date >= '$start_date' ";
        }
        else if($start_date == '' && $end_date != ''){
            $condition =$condition. " ex.date <= '$end_date'";
        }
        else if($start_date != '' && $end_date != ''){
            $condition =$condition. " ex.date between '$start_date' and '$end_date'";
        }


        //echo $condition;die;
        $data = array();
        $sqlincomeExpense="SELECT amount, head_type
        FROM(
             SELECT  SUM(ex.amount) as amount, CASE WHEN ah.head_type = 1 THEN 'Expense' WHEN ah.head_type = 2 THEN 'Income' else 'Liability' End head_type
            FROM accounting_income_expances ex
            LEFT JOIN accounting_head ah ON ah.id=ex.head_id
            LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
            LEFT JOIN project_infos pi ON pi.project_code = ex.project_id
            $condition
            AND ex.head_id !=1 AND ex.head_id !=2 /*AND ah.parent!=1 AND ah.parent !=2*/
		    GROUP BY ah.head_type
            )A";
       // echo $sqlincomeExpense; die;
        $incomeExpense = $dbClass->getResultList($sqlincomeExpense);
        $data['incomeExpense']=$incomeExpense;

        $sqlwithdrawDeposit="SELECT amount, head_type
        FROM(
             SELECT  SUM(ex.amount) as amount, CASE WHEN ah.head_type = 1 THEN 'Withdraw' WHEN ah.head_type = 2 THEN 'Deposit' else 'Liability' End head_type
            FROM accounting_income_expances ex
            LEFT JOIN accounting_head ah ON ah.id=ex.head_id
            LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
            LEFT JOIN project_infos pi ON pi.project_code = ex.project_id
            $condition
            AND (ex.head_id =1 OR ex.head_id =2 OR ah.parent =1 OR ah.parent =2)
		    GROUP BY ah.head_type
            )A";
        //echo $sqlwithdrawDeposit; die;
        $withdrawDeposit = $dbClass->getResultList($sqlwithdrawDeposit);
        $data['withdrawDeposit']=$withdrawDeposit;

       // $condition = str_replace('ex.project_id', 'ah.project_id', $condition);


        $sqlliability="SELECT amount, head_type
        FROM(
           SELECT  SUM(ex.amount) as amount, CASE WHEN ex.types = 1 THEN 'Liability Paid' WHEN ex.types = 2 THEN 'Liability Received' WHEN ex.types = 3 THEN 'Payable' WHEN ex.types = 4 THEN 'Receivable' End head_type
            FROM liability ex
            LEFT JOIN accounting_head ah ON ah.id=ex.head_id
            LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
            LEFT JOIN project_infos pi ON pi.project_code = ah.project_id
            $condition
		    GROUP BY ex.types
            )A";
        //echo $sqlliability; die;
        $liability = $dbClass->getResultList($sqlliability);

        $data['liability']=$liability;

        echo json_encode($data);
        break;


	case "DashbordSummaryReport":
        $condition = "WHERE";
        if($project_id!=''){
            $project = "ex.project_id = $project_id";
        }

		if(isset($project)){
            $condition = $condition." ".$project ;
        }

        if($condition != 'WHERE'){
            $condition = $condition.' AND ';
        }


        if($start_date != '' && $end_date == ''){
            $condition =$condition. " ex.date >= '$start_date' ";
        }
        else if($start_date == '' && $end_date != ''){
            $condition =$condition. " ex.date <= '$end_date'";
        }
        else if($start_date != '' && $end_date != ''){
            $condition =$condition. " ex.date between '$start_date' and '$end_date'";
        }


        //echo $condition;die;
        $data = array();
        $sqlincomeExpense="SELECT amount, head_type
        FROM(
             SELECT  SUM(ex.amount) as amount, CASE WHEN ah.head_type = 1 THEN 'Expense' WHEN ah.head_type = 2 THEN 'Income' else 'Liability' End head_type
            FROM accounting_income_expances ex
            LEFT JOIN accounting_head ah ON ah.id=ex.head_id
            LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
            LEFT JOIN project_infos pi ON pi.project_code = ex.project_id
            $condition
            AND ex.head_id !=1 AND ex.head_id !=2 /*AND ah.parent!=1 AND ah.parent !=2*/
		    GROUP BY ah.head_type
            )A";
       // echo $sqlincomeExpense; die;
        $incomeExpense = $dbClass->getResultList($sqlincomeExpense);
        //$data['incomeExpense']=$incomeExpense;

        $sqlwithdrawDeposit="SELECT amount, head_type
        FROM(
             SELECT  SUM(ex.amount) as amount, CASE WHEN ah.head_type = 1 THEN 'Withdraw' WHEN ah.head_type = 2 THEN 'Deposit' else 'Liability' End head_type
            FROM accounting_income_expances ex
            LEFT JOIN accounting_head ah ON ah.id=ex.head_id
            LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
            LEFT JOIN project_infos pi ON pi.project_code = ex.project_id
            $condition
            AND (ex.head_id =1 OR ex.head_id =2 OR ah.parent =1 OR ah.parent =2)
		    GROUP BY ah.head_type
            )A";
        //echo $sqlwithdrawDeposit; die;
        $withdrawDeposit = $dbClass->getResultList($sqlwithdrawDeposit);
        //$data['withdrawDeposit']=$withdrawDeposit;

        $condition = str_replace('ex.project_id', 'ah.project_id', $condition);


        $sqlliability="SELECT amount, head_type
        FROM(
           SELECT  SUM(ex.amount) as amount, CASE WHEN ex.types = 1 THEN 'Liability Paid' WHEN ex.types = 2 THEN 'Liability Received' WHEN ex.types = 3 THEN 'Payable' WHEN ex.types = 4 THEN 'Receivable' End head_type
            FROM liability ex
            LEFT JOIN accounting_head ah ON ah.id=ex.head_id
            LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
            LEFT JOIN project_infos pi ON pi.project_code = ah.project_id
            $condition
		    GROUP BY ex.types
            )A";
        //echo $sqlliability; die;
        $liability = $dbClass->getResultList($sqlliability);

        //$data['liability']=$liability;
		
		$respponse_data = array(
			'Expense'=>0,
			'Income'=>0,
			'Deposit'=>0,
			'Withdrow'=>0,
			'lPaid'=>0,
			'lReceived'=>0,
			'Payable'=>0,
			'Receivable'=>0
		);
		
		if(isset($incomeExpense[0]['head_type']) && $incomeExpense[0]['head_type']=='Expense') $respponse_data['Expense'] = $incomeExpense[0]['amount'];
		if(isset($incomeExpense[1]['head_type']) && $incomeExpense[1]['head_type']=='Income')  $respponse_data['Income'] = $incomeExpense[1]['amount'];
		if(isset($withdrawDeposit[0]['head_type']) && $withdrawDeposit[0]['head_type']=='Deposit')  $respponse_data['Deposit'] 	 = $withdrawDeposit[0]['amount'];
		if(isset($withdrawDeposit[1]['head_type']) && $withdrawDeposit[1]['head_type']=='Withdrow')  $respponse_data['Withdrow'] = $withdrawDeposit[1]['amount'];
		if(isset($liability[0]['head_type']) && $liability[0]['head_type']=='lPaid')  $respponse_data['lPaid'] = $liability[0]['amount'];
		if(isset($liability[1]['head_type']) && $liability[1]['head_type']=='lReceived')  $respponse_data['lReceived'] = $liability[1]['amount'];
		if(isset($liability[2]['head_type']) && $liability[2]['head_type']=='Payable')  $respponse_data['Payable'] = $liability[2]['amount'];
		if(isset($liability[3]['head_type']) && $liability[3]['head_type']=='Receivable')  $respponse_data['Receivable'] = $liability[3]['amount'];

		
        echo json_encode($respponse_data);
        break;


}
?>