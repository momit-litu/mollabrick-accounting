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
        if(isset($expense_id) && $expense_id == ""){
            //var_dump($_REQUEST);die;
            $attachment  = "";
            if(isset($_FILES['attached_document']) && $_FILES['attached_document']['name']!= ""){
                $desired_dir = "../images/liability";
                chmod( "../images/liability", 0777);
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
                    $attachment  = "images/liability/".$attachment;
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
                'reference_doc'=>$attachment,
                'note'=>$details,
                'types'=>$liability_type,
                'created_by'=>$loggedUser,
                'stakeholder_id'=>$stakeholder_id,
				'account_no'=>$_SESSION['account_no'],
                'project_id'=>$project_id
            );

            $return = $dbClass->insert("liability", $columns_value);

            if($return) echo "1";
            else 	echo "0";
        }
        else{
            if(isset($_FILES['attached_document']) && $_FILES['attached_document']['name']!= ""){
                $desired_dir = "../images/liability";
                chmod( "../images/liability", 0777);
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
                    $attachment  = "images/liability/".$attachment;
                }
                else {
                    echo $img_error_ln;die;
                }

                $columns_value = array(
                    'date'=>$date,
                    'amount'=>$amount,
                    'head_id'=>$head_id,
                    'reference_doc'=>$attachment,
                    'note'=>$details,
                    'types'=>$liability_type,
                    'created_by'=>$loggedUser,
                    'stakeholder_id'=>$stakeholder_id,
                    'project_id'=>$project_id
                );
            }
            else{
                $columns_value = array(
                    'date'=>$date,
                    'amount'=>$amount,
                    'head_id'=>$head_id,
                    'note'=>$details,
                    'types'=>$liability_type,
                    'created_by'=>$loggedUser,
                    'stakeholder_id'=>$stakeholder_id,
                    'project_id'=>$project_id
                );
            }
            $condition_array = array(
                'id'=>$expense_id
            );
            $return = $dbClass->update("liability", $columns_value, $condition_array);

            if($return) echo "2";
            else 	echo "0";
        }
        break;

    
    case "liability_grid_data":
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
            $condition .=	" where CONCAT(l.id, l.amount, h.name, l.date) LIKE '%$search_txt%' and l.account_no= ".$_SESSION['account_no'];
        }
        $countsql = "SELECT count(id)  
					FROM
					(	SELECT h.name as head_type_name, l.id, l.date, l.amount, l.note, l.reference_doc, l.types
                        FROM liability l
                        LEFT JOIN accounting_head h on h.id = l.head_id
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
        if($expense_grid_permission==1){
            $sql = "SELECT  id, date, reference_doc, format(amount,2) amount, note, concat(parent_name,'>>',head_type_name) as headName, types,stakeholder, stakeholder_id,project_id,project_name,
					$update_permission as update_status, $delete_permission as delete_status	
					FROM
					(
						SELECT h.name as head_type_name, l.id, DATE_FORMAT(l.date, '%Y-%d-%d') AS date, l.amount, l.note, l.reference_doc, ifnull(hh.name, '') as parent_name, coalesce(s.name, '') stakeholder, coalesce(s.id, 0) stakeholder_id,coalesce(l.project_id,0) project_id, coalesce(pi.project_name,'') project_name, 
						CASE l.types WHEN 1 THEN 'Paid' WHEN 2 THEN 'Received' WHEN 3 THEN 'Payable' WHEN 4 THEN 'receivable' ELSE 'Not Define' END types
                        FROM liability l
                        LEFT JOIN accounting_head h on h.id = l.head_id
                        LEFT JOIN accounting_head hh on hh.id = h.parent
						LEFT JOIN stakeholders s on s.id = l.stakeholder_id	
						LEFT JOIN project_infos pi on pi.project_code = l.project_id
						$condition
						ORDER BY l.id ASC
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

    case  "liability_head_grid_data":
        $start = ($page_no*$limit)-$limit;
        $end   = $limit;
        $data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
        $entry_permission   	    = $dbClass->getUserGroupPermission(106);
        $delete_permission          = $dbClass->getUserGroupPermission(108);
        $update_permission          = $dbClass->getUserGroupPermission(107);

        $grid_permission = $dbClass->getUserGroupPermission(109);

        $countsql = "SELECT count(id)
					FROM(
						SELECT c.id, c.code, c.name, c.head_type, c.editable, ifnull(ec.name,'') parent_name, ifnull(pi.project_name,'') as project_name, c.account_no
						FROM accounting_head c
						LEFT JOIN accounting_head ec on c.parent = ec.id
						LEFT JOIN project_infos pi on pi.project_code = c.project_id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, code, name) LIKE '%$search_txt%' 
					AND head_type=3 AND editable !=0
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
            $sql = 	"SELECT id, name, code, name, parent_name, head_type_name,project_name,project_id,
					$update_permission as update_status, $delete_permission as delete_status
					FROM(
						SELECT c.id, c.code, c.name, c.head_type, c.editable, ifnull(ec.name,'') parent_name, ifnull(pi.project_name,'') project_name, c.project_id, c.account_no, 
						CASE WHEN c.head_type = 1 THEN 'Expenses' WHEN 2 THEN 'Income' 
						WHEN 3 THEN 'Liabilities' END head_type_name
						FROM accounting_head c
						LEFT JOIN accounting_head ec on c.parent = ec.id
						LEFT JOIN project_infos pi on pi.project_code = c.project_id
						ORDER BY c.id
					)A
					WHERE CONCAT(id, name, code) LIKE '%$search_txt%'
					AND head_type=3 AND editable !=0 AND account_no= ".$_SESSION['account_no']."
					ORDER BY id desc
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



    case "get_liability_details":
		$data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
        $update_permission = $dbClass->getUserGroupPermission(103);
        if($update_permission==1){
            $sql = "SELECT  id, date, reference_doc, types, amount, note, head_type_name as headName, head_id,stakeholder, stakeholder_id,project_id,project_name
					FROM (
						SELECT h.name as head_type_name, types,  l.id, DATE_FORMAT(l.date, '%Y-%d-%d') AS date, l.amount, l.note, l.reference_doc,l.head_id, coalesce(s.name, '') stakeholder, coalesce(s.id, 0) stakeholder_id,coalesce(l.project_id,0) project_id, coalesce(pi.project_name,'') project_name
                        FROM liability l
                        LEFT JOIN accounting_head h on h.id = l.head_id
                        LEFT JOIN stakeholders s on s.id = l.stakeholder_id	
						LEFT JOIN project_infos pi on pi.project_code = l.project_id
                        WHERE l.id = $id AND l.account_no= ".$_SESSION['account_no']."
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
            'id'=>$id
        );
        if($dbClass->update("liability",$columns_value, $condition_array)){
            unlink("../images/expense/".$file_name);
            echo 1;
        }
        else
            echo 0;
        break;

    case "delete_liability":
        $prev_attachment = $dbClass->getSingleRow("select reference_doc from liability where id=$id");
        if(isset($prev_attachment['reference_doc']) && $prev_attachment['reference_doc']!=''){
            unlink("../".$prev_attachment['reference_doc']);
        }
        $condition_array = array(
            'id'=>$id,
			'account_no' => $_SESSION['account_no']
        );
        if($dbClass->delete("liability", $condition_array)){
            echo 1;
        }
        else
            echo 0;
        break;

    case "liabilityReport":
		$data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
        $condition = "WHERE ah.head_type =3 AND ex.account_no= ".$_SESSION['account_no'];
		
        if($head_name != ''){
            $head  = "(ex.head_id = $head_id or ah.parent = $head_id)";
        }

        if(isset($head)){
            $condition = $condition." AND ".' '.$head ;
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
        $sql="SELECT id, date, amount, note,head_name, types, project_name, stakeholder
                FROM(
                     SELECT ex.id, DATE_FORMAT(ex.date,'%d-%m-%Y') as date , ex.amount, ex.note, ifnull(pi.project_name,'') project_name,  coalesce(s.name, '') stakeholder,
                     CASE
                         WHEN ahh.name is NULL THEN ah.name
						WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name,
					CASE when ex.types = 1 THEN 'Paid' when ex.types = 2 THEN 'Received' when ex.types = 3 THEN 'Payable' when ex.types = 4 THEN 'Receivable'  else 'Not Defined' END types
                    FROM liability ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
                    LEFT JOIN project_infos pi ON pi.project_code = ex.project_id 
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

    case "liabilityReceivedReport":
		$data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
				
        $condition = "WHERE ex.types = 2 AND ah.head_type =3 AND ex.account_no= ".$_SESSION['account_no'];
        if($head_name != ''){
            $head  = "(ex.head_id = $head_id or ah.parent = $head_id)";
        }

        if(isset($head)){
            $condition = $condition." AND ".' '.$head ;
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
        $sql="SELECT id, date, amount, note,head_name, types, project_name
                FROM(
                     SELECT ex.id, ex.date , ex.amount, ex.note, ifnull(pi.project_name, '') project_name,
                     CASE
                         WHEN ahh.name is NULL THEN ah.name
						WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name,
					CASE when ex.types = 1 THEN 'Paid' else 'Received' END types
                    FROM liability ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
                    LEFT JOIN project_infos pi ON pi.project_code = ah.project_id
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

    case "liabilityPaidReport":
		$data = array();
		if(!isset($_SESSION)){	echo json_encode($data);}
		
        $condition = "WHERE ex.types = 1 AND ah.head_type =3 AND ex.account_no= ".$_SESSION['account_no'];
        if($head_name != ''){
            $head  = "(ex.head_id = $head_id or ah.parent = $head_id)";
        }

        if(isset($head)){
            $condition = $condition." AND ".' '.$head ;
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
        $sql="SELECT id, date, amount, note,head_name, types, project_name
                FROM(
                     SELECT ex.id, ex.date , ex.amount, ex.note, ifnull(pi.project_name, '') project_name,
                     CASE
                         WHEN ahh.name is NULL THEN ah.name
						WHEN ahh.name is NOT NULL THEN CONCAT(ahh.name,' >> ',ah.name)
						END head_name,
					CASE when ex.types = 1 THEN 'Paid' else 'Received' END types
                    FROM liability ex
                    LEFT JOIN accounting_head ah ON ah.id=ex.head_id
                    LEFT JOIN accounting_head ahh ON ahh.id=ah.parent
                    LEFT JOIN project_infos pi ON pi.project_code = ah.project_id
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