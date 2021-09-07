<?php 			
class dbClass {		
	private $dbCon;	
	private $userId;
	
	public function __construct() {
		include("dbConnect.php");			
		$this->dbCon  = $conn;
		$this->userId = (isset($_SESSION['user_id']))?$_SESSION['user_id']:"";
	}
	
	function getDbConn(){
		return $this->dbCon;
	}
	
	function getUserId(){
		return $this->userId;
	}
	
	// insert function for all table
	// created ny moynul
	// parameter table name, inserted table column_name and value array
	function insert($table_name ,$columns_values){		
		try {				
			$this->dbCon->beginTransaction();	
			//var_dump($columns_values);die;
			$bind = ':'.implode(',:', array_keys($columns_values));
			$columns =  implode(',', array_keys($columns_values));
				
			$master_sql = "Insert into $table_name ($columns)  VALUES ($bind)";	
			//echo $master_sql;die;	
			$stmt = $this->dbCon->prepare($master_sql);
    		$return = $stmt->execute(array_combine(explode(',',$bind), array_values($columns_values)));
			if($return == 1){
				$just_inserted_id = $this->dbCon->lastInsertId();
				if($just_inserted_id) $original_return = $just_inserted_id;
				else 				  $original_return = 1;
			}
			else 
				$original_return = 0;
			
			$this->dbCon->commit();
			return $original_return; 
			
		} catch(PDOException $e) {
			$this->dbCon->rollback();
			echo "Insert:Error: " . $e->getMessage();
		}		
	}
	function update($table_name, $columns_values,$condition_array){		
		try {				
			$this->dbCon->beginTransaction();
			$condition_bind = ':'.implode(',:', array_keys($condition_array));			
			$bind = ':'.implode(',:', array_keys($columns_values));
			
			$set_string = "";
			foreach($columns_values as $key=>$value) {
				$set_string .= "$key =:$key, ";
			}
			
			$con_string = "";
			$count_i = 1;
			foreach($condition_array as $key=>$value) {
				if(count($condition_array) != $count_i)
					$con_string .= "$key =:$key AND ";
				else 
					$con_string .= "$key =:$key";				
				$count_i++;
			}
			
			
			$updatesql = "update $table_name set ".rtrim($set_string,", ")."  where $con_string";
			
		//	echo $updatesql;die;
			$stmt = $this->dbCon->prepare($updatesql);
		
			$condition_combined_array  = array_combine(explode(',',$condition_bind), array_values($condition_array));
			$columns_combined_array   = array_combine(explode(',',$bind), array_values($columns_values));
			$bind_array = array_merge($condition_combined_array, $columns_combined_array );  
			$return = $stmt->execute($bind_array); 
			$this->dbCon->commit();
			return $return;
			
		} catch(PDOException $e) {
			$this->dbCon->rollback();
			echo "Insert:Error: " . $e->getMessage();
		}		
	}	
	
	function delete($table_name ,$condition_array){		
		try {	
			$this->dbCon->beginTransaction();
			$condition_bind = ':'.implode(',:', array_keys($condition_array));
			$con_string = "";
			$count_i = 1;
			foreach($condition_array as $key=>$value) {
				if(count($condition_array) != $count_i)
					$con_string .= "$key =:$key AND ";
				else 
					$con_string .= "$key =:$key";				
				$count_i++;
			}
			$deletesql = "delete  from $table_name where $con_string";
			$stmt = $this->dbCon->prepare($deletesql);
			$condition_combined_array  = array_combine(explode(',',$condition_bind), array_values($condition_array));	
    		$return = $stmt->execute($condition_combined_array);
			$this->dbCon->commit();
			return $return; 
			
		} catch(PDOException $e) {
			$this->dbCon->rollback();
			echo "Insert:Error: " . $e->getMessage();
		}		
	}
	
	function getSingleRow($sql){
		$stmt = $this->dbCon->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return 	$result;	
	}
	
	function getResultList($sql){		
		$stmt = $this->dbCon->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return 	$result;	
	}
	
	function insert_school($code, $name, $type, $address){		
		$columns_value = array(
			'code'=>$code,
			'name'=>$name,
			'type'=>$type,
			'address'=>$address,
		);	
		$return = $this->insert("school", $columns_value);
		return 	$return;
	}
	
	
	//entry school and return  inserted  school id 
	function get_inserted_school_id($school_id, $school_list){	
		if($school_id == "" && $school_list != ""){
			//insert into school and get the school_id into $school_id
			$get_school_id = $this->getSingleRow("select id from school where name='$school_list' ");
			if(empty($get_school_id)){
				$school_mst_id = $this->insert_school('', $school_list, '2', '');
				if($school_mst_id>0) $school_id = $school_mst_id;
			}else{
				$school_id = $get_school_id['id']; 
			} 
		}
		return 	$school_id;		
	}

	// get school id when update
	function get_updated_school_id($school_id, $school_list){	
		$pos = strpos($school_list, '>>');
		if($pos === false) {
			$school_list = $school_list;
		}
		else{
			$str =  explode(">>",$school_list);
			$school_list = trim($str[1]);
		} 
		
		if($school_id == "" && $school_list != ""){
			//insert into school and get the school_id into $school_id
			$get_school_id = $this->getSingleRow("select id from school where name='$school_list' ");
			if(empty($get_school_id)){
				$school_mst_id = $this->insert_school('', $school_list, '2', '');
				if($school_mst_id>0) $school_id = $school_mst_id;
			}else{
				$school_id = $get_school_id['id']; 
			} 
		}
		else if($school_id != "" && $school_list != ""){
			$get_school_id = $this->getSingleRow("select id from school where name='$school_list' ");
			if(!empty($get_school_id)){
				if($school_id != $get_school_id['id']){
					$school_mst_id = $this->insert_school('', $school_list, '2', '');
					if($school_mst_id>0) $school_id = $school_mst_id;						
				}
			}
			else{
				$school_mst_id = $this->insert_school('', $school_list, '2', '');
				if($school_mst_id>0) $school_id = $school_mst_id;				
			}				
		}
		else {
				$school_id="";
		}
		return 	$school_id;
	}

	/*	
	function getUserPermission($action_id){	
		$logged_user = $this->userId;		
		$status_return = $this->getSingleRow("select status from emp_activity_permission where emp_id='$logged_user' and activity_action_id=$action_id");
		return 	$status_return['status'];
	}
	*/
	function getUserGroupPermission($action_id){	
		$logged_user = $this->userId;
		$logged_user_groups = $this->getResultList("select group_id from user_group_member where emp_id = '$logged_user' and status = 1");
		if(empty($logged_user_groups)){
			return 0;
			die;
		}
		$groups = "";
		foreach($logged_user_groups as $row){
			$groups .="'".$row['group_id']."',";
			
		}
		$groups = substr($groups,0,-1);	
		$status_return = $this->getSingleRow("select status from user_group_permission where group_id in(".$groups.") and action_id = $action_id order by status desc limit 1");
		return 	$status_return['status'];		
	}
	
	
	function getUserDetails($user_id){
		$sql = "select emp_id,full_name,,designation_name, concat(e.full_name, ' (',e.designation_name,')') employee_name,
				photo,contact_no,email,blood_group,team_leader from user_infos e where emp_id='$user_id'";
		$stmt = $this->dbCon->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return 	$result;		
	}
	
	
	function getPatientDetails($health_card_no){
		//echo $health_card_no;die;
		$health_card_details = $this->getSingleRow("select * from helth_card where card_no= '$health_card_no' and status =1 ");
		if(!empty($health_card_details)){
		//var_dump($health_card_details);die;
		// 1: ssf employee, 4:teacher,5:student, 6:vip , 7:female
		/// employee
			if($health_card_details['user_type']==1){
				$patient_details = $this->getResultList("select inf.emp_id id, inf.full_name name, ifnull(inf.health_card_no,'') health_card_no,
												inf.contact_no mobile_no, inf.photo image,nid_no as identy_no, age, address
												from user_infos inf left join  appuser u  on u.user_id=inf.emp_id 	
												where health_card_no = '$health_card_no' AND  u.is_active=1");
			}
			//teacher
			if($health_card_details['user_type']==4){
				$patient_details = $this->getResultList("select t.id, t.name, t.school_id, t.dob as age, t.address, t.mobile_no, t.image, 
												t.username, t.nid_no as identy_no, t.health_card_no, t.`status`	from teacher t
												where t.health_card_no = '$health_card_no' and status=1");
			}
			// student
			if($health_card_details['user_type']==5){
				$patient_details = $this->getResultList("select s.id, s.name, s.school_id, s.age, s.class, s.address, s.mobile_no, s.image, 
												s.username, s.identy_no, s.health_card_no, s.`status`
												from student s
												where health_card_no = '$health_card_no'  and status=1  ");
			}
			//vip
			if($health_card_details['user_type']==6){
				$patient_details = $this->getResultList("select v.id, v.name, v.dob as age, v.address, v.mobile_no,v.image, 
												v.username, v.nid_no as identy_no, v.health_card_no,v.`status`	from vip v
												where health_card_no = '$health_card_no'  and status=1  ");
			}
			//female
			if($health_card_details['user_type']==7){
				$patient_details = $this->getResultList("select f.id, f.name, f.dob as age, f.address, f.mobile_no,f.image, 
												f.username, f.nid_no as identy_no, f.health_card_no,f.`status`	from female f
												where health_card_no = '$health_card_no' and status=1");
			}
			return 	$patient_details;
		}
		else {
			return "";	
		}
	}
	
	function get_service_list(){
		$service_list_sql  = $this->getResultList("select id, service_name from female_services_list");
		foreach ($service_list_sql as $row){
			$data['service_list'][]= $row;
		}
		return $data['service_list'];
	}
	
	function get_health_card_no($user_type, $user_id){
		if($user_type==1){
			$health_card_no = $this->getSingleRow("select ifnull(health_card_no,'') health_card_no from user_infos where emp_id = '$user_id'");
		}
		//teacher
		if($user_type==4){
			$health_card_no = $this->getSingleRow("select ifnull(health_card_no,'') health_card_no from teacher where id = '$user_id'");
		}
		// student
		if($user_type==5){
			$health_card_no = $this->getSingleRow("select ifnull(health_card_no,'') health_card_no from student where id = '$user_id'");
		}
		//vip
		if($user_type==6){
			$health_card_no = $this->getSingleRow("select ifnull(health_card_no,'') health_card_no from vip where id = '$user_id'");
		}
		//female
		if($user_type==7){
			$health_card_no = $this->getSingleRow("select ifnull(health_card_no,'') health_card_no from female where id = '$user_id'");
		}
		return 	$health_card_no['health_card_no'];
	}
	
	function sendSMS($mobile_nos,$message){
		/*try{
			$soapClient = new SoapClient("http://api.onnorokomsms.com/sendsms.asmx?wsdl");
			$paramArray = array(
			'userName'=>"01980340482",
			'userPassword'=>"34178",
			'mobileNumber'=> $mobile_nos,
			'smsText'=>$message,
			'type'=>"1",
			'maskName'=> "",
			'campaignName'=>'',
			);
			//var_dump($paramArray);die;
			$value = $soapClient->__call("OneToMany", array($paramArray));
			var_dump($value);
		} 
		catch (dmException $e) {
			 echo "SMS not Send. ".$e;
		}
		*/
		
		try{
			$soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
			$paramArray = array(
				'userName'  => "01980340482",
				'userPassword'  => "34178",
				'messageText'  => "$message",
				'numberList'  => "$mobile_nos",
				'smsType' => "TEXT",
				'maskName'   => '',
				'campaignName'  => '',
			);
			//var_dump($paramArray);die;
			$value= $soapClient->__call("OneToMany", array($paramArray));
			echo 1;
		} 
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	 
	public function print_arrays()
    {
        $args = func_get_args();
        echo "<pre>";
        foreach ($args as $arg) {
            print_r($arg);
        }
        echo "</pre>";
        die();
    }

	public function print_arrays_no_die()
    {
        $args = func_get_args();
        echo "<pre>";
        foreach ($args as $arg) {
            print_r($arg);
        }
        echo "</pre>";
    }
	

    function sendMail ($to, $subject, $body){
	    $Web_email = $this->getDescription('web_admin_email');

        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );

        $from = $Web_email;
        $to =  $to;
        $subject = $subject;
        $message = $body;
        $headers = "From:" . $from . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' ;
        mail($to,$subject,$message, $headers);
        //echo "Test email sent52120";
        return 1;
    }

	
}

?>