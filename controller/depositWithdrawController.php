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

        $head_id = isset($expense) ? 1 : $head_id;

        //echo $project_id;
        $project_id = $project_id!=null ? $project_id : 0;
        $stakeholder_id = ($stakeholder_id!=null && $stakeholder_id!="")? $stakeholder_id : 0;

        //echo $project_id; die;

        if(isset($expense_id) && $expense_id == ""){
            //var_dump($_REQUEST);die;
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
                'note'=>$details,
                'created_by'=>$loggedUser,
                'stakeholder_id'=>$stakeholder_id
            );
			//$dbClass->print_arrays($columns_value);
            $return = $dbClass->insert("accounting_income_expances", $columns_value);

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
                    'head_id'=>$head_id,
                    'project_id'=>$project_id,
                    'reference_doc'=>$attachment,
                    'note'=>$details,
                    'created_by'=>$loggedUser,
                    'stakeholder_id'=>$stakeholder_id

                );
            }
            else{
                $columns_value = array(
                    'date'=>$date,
                    'amount'=>$amount,
                    'head_id'=>$head_id,
                    'project_id'=>$project_id,
                    'note'=>$details,
                    'created_by'=>$loggedUser,
                    'stakeholder_id'=>$stakeholder_id

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
    case "deposit_grid_data":
        $start = ($page_no*$limit)-$limit;
        $end   = $limit;
        $data = array();

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
            $condition .=	" where CONCAT(e.id, e.amount, h.name, e.date) LIKE '%$search_txt%'  and e.account_no= ".$_SESSION['account_no'];	
        }
        $countsql = "SELECT count(id)  
					FROM
					(	
						SELECT e.id, e.date, e.reference_doc, format(e.amount,2) amount, e.note, h.name, hh.name parent_name, 
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
						AND e.head_id = 1
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
            $sql = "SELECT  id, date, reference_doc, amount, note, head_name as headName,stakeholder,project_name,
					$update_permission as update_status, $delete_permission as delete_status	
					FROM
					(
						SELECT e.id, DATE_FORMAT(e.date, '%Y-%d-%d') AS date, e.reference_doc, coalesce(s.name, '') stakeholder, coalesce(s.id, 0) stakeholder_id, coalesce(pi.project_name,'') project_name,amount, e.note, h.name, hh.name parent_name, 
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
						LEFT JOIN stakeholders s on s.id = e.stakeholder_id	
						LEFT JOIN project_infos pi on pi.project_code = e.project_id
						$condition
						AND e.head_id = 1
						ORDER BY e.id DESC
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

    case "withdraw_grid_data":
        $start = ($page_no*$limit)-$limit;
        $end   = $limit;
        $data = array();

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
            $condition .=	" where CONCAT(e.id, e.amount, h.name, e.date) LIKE '%$search_txt%' and e.account_no= ".$_SESSION['account_no'];	
        }
        $countsql = "SELECT count(id)  
					FROM
					(	
						SELECT e.id, e.date, e.reference_doc, format(e.amount,2) amount, e.note, h.name, hh.name parent_name, 
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
						AND e.head_id = 2
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
            $sql = "SELECT  id, date, reference_doc, amount, note, head_name as headName,stakeholder,project_name,
					$update_permission as update_status, $delete_permission as delete_status	
					FROM
					(
						SELECT e.id, DATE_FORMAT(e.date, '%Y-%d-%d') AS date, e.reference_doc, coalesce(s.name, '') stakeholder, coalesce(s.id, 0) stakeholder_id, coalesce(pi.project_name,'') project_name, format(e.amount,2) amount, e.note, h.name, hh.name parent_name, 
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
						LEFT JOIN stakeholders s on s.id = e.stakeholder_id	
						LEFT JOIN project_infos pi on pi.project_code = e.project_id
						$condition
						AND e.head_id = 2
						ORDER BY e.id DESC
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


    case "get_deposit_details":
        $update_permission = $dbClass->getUserGroupPermission(103);
        if($update_permission==1){
            $sql = "SELECT id, head_id, date, reference_doc, amount, note,stakeholder, stakeholder_id,project_id,project_name, CONCAT(head_name,' (',head_type_name,')') headName
					FROM (
						SELECT e.id, e.head_id, DATE_FORMAT(e.date, '%Y-%d-%d') AS date , coalesce(s.name, '') stakeholder, coalesce(s.id, 0) stakeholder_id,coalesce(e.project_id,0) project_id, coalesce(pi.project_name,'') project_name,  e.reference_doc, format(e.amount,2) amount, e.note, h.name, hh.name parent_name, 
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
						LEFT JOIN stakeholders s on s.id = e.stakeholder_id	
						LEFT JOIN project_infos pi on pi.project_code = e.project_id
						WHERE e.id = $expense_id
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
        $prev_attachment = $dbClass->getSingleRow("select reference_doc from accounting_income_expances where id=$expense_id");
        if(isset($prev_attachment['reference_doc']) && $prev_attachment['reference_doc']!=''){
            unlink("../".$prev_attachment['reference_doc']);
        }
        $condition_array = array(
            'id'=>$expense_id
        );
        if($dbClass->delete("accounting_income_expances", $condition_array)){
            echo 1;
        }
        else
            echo 0;
        break;

    case "expenseIncomeReport":
        $condition = " group by head_id ";
        if($head_name != ''){
            $condition  = " WHERE ex.head_id = $head_id group by ex.date ";
        }
        else if($start_date != '' && $end_date == ''){
            $condition  = " WHERE ex.date >= '$start_date' group by ex.head_id ";
        }
        else if($start_date == '' && $end_date != ''){
            $condition  = " WHERE ex.date <= '$end_date' group by ex.head_id ";
        }
        else if($start_date != '' && $end_date != ''){
            $condition  = " WHERE ex.date between '$start_date' and '$end_date' group by ex.head_id ";
        }

        //echo $condition;die;
        $data = array();
        $details = $dbClass->getResultList("SELECT  ifnull(ex_amount,0) ex_amount, ifnull(inc_amount,0) inc_amount, date, project_name,
											CONCAT(head_name,' (',head_type_name,')') headName, head_type_name, head_type
											FROM (
												select eh.head_type,
												sum(case when eh.head_type =1 then ex.amount end) ex_amount,
												sum(case when eh.head_type =2 then ex.amount end) inc_amount,
												CASE WHEN eh.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' WHEN 3 THEN 'Liabilities' END head_type_name,
												CASE 
													WHEN eh.parent IS NULL THEN eh.name 
													WHEN eh.parent IS NOT NULL THEN CONCAT(ehp.name,' >> ',eh.name) 
												END head_name,
												ex.date, ehp.name as parent_head_name, p.project_code, ifnull(p.project_name,'') project_name
												from accounting_income_expances ex
												left join accounting_head eh on eh.id=ex.head_id
												left join accounting_head ehp on ehp.id=eh.parent
												left join project_infos p on p.project_code=eh.project_id
												$condition
											)A");
        foreach ($details as $row){
            $data['records'][] = $row;
        }
        echo json_encode($data);
        break;

    case "depositReport":
        $condition = "WHERE ah.id =1
                    AND ah.head_type =2 ";

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
        $sql="SELECT id, date, amount, note,head_name
                FROM(
                     SELECT ex.id, ex.date , ex.amount, ex.note, 
                     CASE
                        WHEN ahh.name is NULL THEN ah.name
						WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name
                    FROM accounting_income_expances ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
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

    case "withdrawReport":
        $condition = "WHERE ah.id =2
                    AND ah.head_type =1 ";

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
        $sql="SELECT id, date, amount, note, head_name
                FROM(
                     SELECT ex.id, ex.date , ex.amount, ex.note, 
                     CASE
                        WHEN ahh.name is NULL THEN ah.name
						WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name
                    FROM accounting_income_expances ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
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

    case "depositWithdrawReport":
        if($report_type=='deposit'){
            $condition = "WHERE ah.id =1
                    AND ah.head_type =2 ";
        }elseif ($report_type=='withdraw'){
            $condition = "WHERE ah.id =2
                    AND ah.head_type =1";
        }else{
            $condition = "WHERE (ah.id =1 OR ah.id =2) 
                    AND (ah.head_type =1 OR ah.head_type =2)";
        }

        if($project_id!=''){
            $condition = $condition." AND ex.project_id = $project_id";
        }
        if($stakeholder_id!=''){
            $condition = $condition." AND ex.stakeholder_id = $stakeholder_id";
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
        $sql="SELECT id, date, amount, note,head_name,project_name,stakeholder
                FROM(
                     SELECT ex.id, ex.date , ex.amount, ex.note, ifnull(pi.project_name,'') project_name,  coalesce(s.name, '') stakeholder, 
                     CASE
                        WHEN ahh.name is NULL THEN ah.name
						WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name
                    FROM accounting_income_expances ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
                    LEFT JOIN project_infos pi ON pi.project_code=ex.project_id
                    LEFT JOIN stakeholders s on s.id = ex.stakeholder_id	
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


}
?>