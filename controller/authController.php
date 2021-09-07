<?php
session_start();
include '../includes/static_text.php';
include '../dbConnect.php';	
include("../dbClass.php");

$dbClass = new dbClass;	
extract($_POST);

if($q=="login_customer"){
    //echo 1; die;

	$username	= htmlspecialchars($_POST['username'],ENT_QUOTES);
    $pass	  	 = $_POST['password'];
	$query="SELECT a.user_id, a.user_name, a.user_password, e.full_name, e.photo, a.account_no, is_owner, expiry_date FROM appuser a LEFT JOIN emp_infos e ON e.emp_id=a.user_id WHERE a.user_name='".$username."' and a.is_active=1";
	

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data = array();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //$result = $dbClass->getResultList($query);
    //echo json_encode($result); die;
    foreach ($result as $row) {
			$data['records'][] = $row;
		}	
		//if username exists
		if($stmt -> rowCount()>0){
			//compare the password
			if($row['user_password'] == md5($pass)){				
				$_SESSION['user_id']=$row['user_id']; 
				$_SESSION['user_type']=1; 
				// need to get these info dynamicly later
				$_SESSION['user_pic']	= $row['photo'];
				$_SESSION['user_name']	= $row['full_name'];
				$_SESSION['account_no']	= $row['account_no'];
				$_SESSION['expiry_date']	= $row['expiry_date'];
					
				if($row['is_owner'] == 1){
					$_SESSION['user_groups'] = 14; 
				}
				else{
					$sql = "select group_concat(group_id) my_groups from user_group_member where emp_id = '".$row['user_id']."' and status = 1";		
					//echo $sql;die;
					$stmt_group = $conn->prepare($sql);
					$stmt_group->execute();
					$result_group = $stmt_group->fetch(PDO::FETCH_ASSOC);
					$_SESSION['user_groups'] = $result_group['my_groups']; 
				}
				echo 1;
			}
			else
				echo 2; 
		}
		else
			echo 3; //Invalid Login
}


if($q=="forget_password"){
	$forget_email	 = htmlspecialchars($_POST['forget_email'],ENT_QUOTES);
	$query="select email, a.c, user_name
			from emp_infos c
			LEFT JOIN appuser a ON a.user_id=c.emp_id
			WHERE  email='".$forget_email."'  and status=1";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$data = array();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);		
		foreach ($result as $row) {
			$data['records'] = $row;
		}	
		//if username exists
		if($stmt -> rowCount()>0){
			// mail a new password to customer_id
			$customer_email = $data['records']['email'];
			$username 		= $data['records']['user_name'];
			$user_id 	= $data['records']['user_id'];			
			$new_password 	= mt_rand(100000,999999);

            $original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
            $original_string = implode("", $original_string);




            if(isset($is_app)){
                $pass_key= substr(str_shuffle($original_string), 0, 8);
                $body = 'Dear '.$username.',<br><p>We are processing your password reset request, Your new password is: "'. $pass_key.'" </p><p>Please reset your password right after login.</p><p><br> Thank,</p><br>Mbrothers Hishab Nikash';
                $columns_value = array(
                    'password' => md5($pass_key)
                );
                $condition_array = array(
                    'user_id' =>$user_id
                );
                $status = 0;
            }else{
                $pass_key= substr(str_shuffle($original_string), 0, 20);
                $pass_reset_url = $project_url.'index.php?passreset='.$pass_key;
                $body = 'Dear '.$username.',<br><p>We are processing your password reset request, Please <a href="'.$pass_reset_url.'">click here</a> to login your account and update password.<b></p><p>Keep Old password field empty while reset your password through this link.</b></p><p><br> Thank,</p><br>Mbrothers Hishab Nikash';
                $status = 1;
            }



            //echo $pass_reset_url;

			$to 	 = $forget_email;
			$from 	 = $dbClass->getDescription('web_admin_email');
			$subject = "Password Reset Request from Burrito Brothers";

			
			$headers = 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $from . "\r\n" .
					'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
			
			//echo $to ."  ".$subject."   ".$body."  ". $headers;die;
			
			$sent_status = mail($to, $subject, $body, $headers);
			if($sent_status == 1 ){
			    if(isset($is_app)){
			        $result = $dbClass->update('appuser',$columns_value,$condition_array);
                }

			    $columns_value = array(
			        'user_id'=> $data['records']['user_id'],
			        'secret_key'=>$pass_key,
                    'status'=>$status
                );

			    $return = $dbClass->insert('password_reset',$columns_value);
				echo 1;
			}				
			else
				echo 2; 	
		}
		else
			echo 2; //Invalid email address
}

if($q =="password_reset_url_check"){
    $sql = "Select count(id) as id, user_id FROM password_reset WHERE  secret_key ='".$key."' AND status = 1";
    $result = $dbClass->getSingleRow($sql);
    if($result['id']==1) {
        $customer = $dbClass->getSingleRow("SELECT user_id, email FROM emp_infos WHERE emp_id = ".$result['user_id']);
        $_SESSION['user_id']=$customer['user_id'];
        $_SESSION['customer_email']=$customer['email'];
        echo 1;
    }
    else echo 0;
}


if($q=="registration"){
	$username	 = htmlspecialchars($_POST['cust_username'],ENT_QUOTES);
	$email	 = htmlspecialchars($_POST['cust_email'],ENT_QUOTES);
	
	$check_username = $dbClass->getSingleRow("select user_name from appuser WHERE  user_name='".$username."'");
	if(isset($check_username['user_name']) && $check_username['user_name'] != "") { echo 2; die;} //username is found, same username cant be taken
	
	$check_email = $dbClass->getSingleRow("select email from emp_infos WHERE  email='".$email."'");
	if(isset($check_email['email']) && $check_email['email'] != "") { echo 3; die;} //email is found, same email cant be taken
	
	$last_user_id = $dbClass->getSingleRow("select max(user_id) user_id from appuser");	
	if(empty($last_user_id)) $last_user_id['user_id'] = "1000000"; 
	$emp_id = $last_user_id['user_id'] + 1;
	
	try {
		$last_account_id = $dbClass->getSingleRow("select max(account_no) account_no from appuser");	
		$columns_value = array(
			'emp_id'=>$emp_id,
			'full_name'=>$cust_name,		
			'email'=>$cust_email,
			'address'=>$cust_address,
			'contact_no'=>$cust_contact,
			'account_no'=>isset($last_account_id['account_no'])?($last_account_id['account_no']+1):1,
			'status'=>1
		);	
		$return = $dbClass->insert("emp_infos", $columns_value);
		if(is_numeric($return) && $return>0) {
			// insert data in appuser
			if(empty($_POST['password'])){
				$password="123456";
			}		
				
			$columns_value = array(
				'user_id'=>$emp_id,
				'user_name'=>$cust_username,
				'user_password'=>md5($cust_password),
				'is_owner'=>1,
				'account_no'=>isset($last_account_id['account_no'])?($last_account_id['account_no']+1):1,
				'expiry_date'=>date("Y-m-d",strtotime("+1 month",strtotime(date("Y-m-d"))))
			);			
			$return_app = $dbClass->insert("appuser", $columns_value);
			if($return_app){
				$columns_value = array(
					'group_id'=>14, // owner group id
					'emp_id'=>$emp_id,
					'status'=>1
				);
				$return_group = $dbClass->insert("user_group_member",$columns_value);
			}
		}
		if($return_app) 	echo "1";
		else 				echo "0";
	
	}catch (Exception $e){
		echo "0";
	}

/*
        //Email
        $to 	 = $cust_email;
        $from 	 = $admin_email;
        $subject = "Registration Confirmation";
        $body 	 = 'Dear '.$cust_name.'<br><p>You have been successfully registered to Mbrothers Hishabnikash. 
            To Select Menus please visit <a href="'.$project_url.'"> Mbrothers Hishabnikash</a>.</p><p><br><br>
            Best Regards</p><p><b>Mbrothers Hishabnikash</b></p><br>Note: Please Do not reply this email.';

        try {
            $dbClass->sendMail ($to, $subject, $body);
        }catch (Exception $e){

        }
  */  

}

if($q=="contact_us_mail"){
	$to 	 = $dbClass->getDescription('web_admin_email');
	$from 	 = $email;
	$subject = "Contact us mail from $name. '$subject'";

    $headers = "From:" . $from;

    $body 	 = '<p>'.$message.'</p><p>Send By: '.$name.'</p><p>Mobile: '. $mobile;
    $return = $dbClass->contactMail($from,$to,$subject,$body);

    //echo $to ." @@@ ".$subject." @@@@  ".$body." @@@ ". $headers;die;
	if($return = 1) echo 1;
	else		 echo 2;
}


if($q=="duplicate_id_check"){
    //echo 1;
    if($type=='username'){
        $sql = "select username from customer_infos WHERE  username='$userInfo'";
    }
    else{
        $sql = "select username from customer_infos WHERE  email='$userInfo'";
    }
    $check_userInfo = $dbClass->getSingleRow($sql);
    if($check_userInfo){
        echo 0;
    }
    else
        echo 1;
}
?>