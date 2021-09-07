<?php
session_start();
include 'includes/static_text.php';
if(isset($_SESSION['user_id']) && $_SESSION['user_id'])header("Location:".$activity_url."dashbord.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $site_title; ?></title>
        <!-- Bootstrap core CSS -->
        <link href="theme/css/bootstrap.min.css" rel="stylesheet">
        <link href="theme/fonts/css/font-awesome.min.css" rel="stylesheet">
        <link href="theme/css/animate.min.css" rel="stylesheet">
    
        <!-- Custom styling plus plugins -->
        <link href="theme/css/custom.css" rel="stylesheet">
        <link href="theme/css/icheck/flat/blue.css" rel="stylesheet">
		<link href="css/app_theme.css" rel="stylesheet">
		<link href="css/app_responsive.css" rel="stylesheet">
		<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
		<link rel="icon" href="images/favicon.png" type="image/x-icon">
    </head>
    
    <style>
    .scrollDiv {
        height:auto;
        max-height:150%;
        overflow:auto;
    }

</style>
<body style="background:#FFF;">      
        <div class=""> 
            <div id="wrapper"> 
                <div class="" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="booktable" >
					<div class="modal-dialog" role="document" style="width:94% !important; margin-top:10%;">
						<div class="" id="login_modal_responsive">
							<div class="modal-body">
								<div id="login-div" class="">
									<div id="done_login" class="">
										<div class="title text-center">
											<h3 class="text-coffee"><br>Molla Bricks<br><br></h3>
										</div>
										<form class="login-form" method="post" name="login-form" id="login-form">
											<div class="row">
												<div class="col-md-12 col-sm-12 col-xs-12">
													<div class="col-md-12 col-sm-12 col-xs-12">
														<input type="text" name="username" id="username" placeholder="Username or email address" class="input-fields" required >
													</div>
													<div class="col-md-12 col-sm-12 col-xs-12">
														<input type="password" name="password" id="password" placeholder="Password" class="input-fields" required >
													</div>
													<div class="col-md-12 col-sm-12 col-xs-12">
														<div class="row text-center">
														<br>
															<a href="javascript:void(0)" onclick="active_modal(2)"  id="send_password"><i class="fa fa-user" aria-hidden="true"></i> Lost your password?</a>
														</div>
													</div>
												</div>
												<div class="col-md-12 col-sm-12 col-xs-12 text-center">
													<div id="login_submit_error" class="text-center" style="display:none"></div>
													<input type="submit" name="submit" id="login_submit" value="LOGIN" class="button-default button-default-submit" style="width:50%; font-size:16px; height:auto">
												</div>
											</div>
										</form>
										<div class="divider-login">
											<hr>
											<span>Or</span>
										</div>
										<div class="row">
											<div class="col-md-12 col-sm-12 col-xs-12 text-center">
												<a href="javascript:void(0)" onclick="active_modal(3)" class=" btn-change button-default " id="log_reg"><i class="fa fa-user" aria-hidden="true"></i> Dont have an account?</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            
				<!-- register modal -->
				<div class="hide" id="registerModal" tabindex="-2" role="dialog" aria-labelledby="booktable">
					<div class="modal-dialog" role="document" style="width:94% !important; margin-top:5%">
						<div class=" scrollDiv" id="registration_modal_responsive">
							<div class="modal-body">
								<div id="register-div">
									<div class="title text-center">
										<h3 class="text-coffee"><br>Hishab Nikash<br><br></h3>
									</div>
									<div class="done_registration">
										<p>Please provide all valid infomation to register, Email must be unique</p>
										<form class="register-form" method="post" name="register-form" id="register-form">
											<div class="row">
												<div class="col-md-12 col-sm-12 col-xs-12">
													<input type="text" name="cust_name" id="cust_name" placeholder="Name" class="form-control" required>
												</div>
												<div class="col-md-12 col-sm-12 col-xs-12">
													<input type="text" name="cust_username" id="cust_username" placeholder="User Name" class="form-control" required>
												</div>
												<div class="col-md-12 col-sm-12 col-xs-12">
													<input type="number" name="cust_contact" id="cust_contact" pattern="[0-9]{11}" placeholder="Contact No" class="form-control" required>
												</div>
												<div class="col-md-12 col-sm-12 col-xs-12">
													<input type="email" name="cust_email" id="cust_email" placeholder="Email address" class="form-control" required>
												</div>
												<div class="col-md-12 col-sm-12 col-xs-12">
													<input type="password" name="cust_password" id="cust_password" placeholder="Password" class="form-control" required>
												</div>
												<div class="col-md-12 col-sm-12 col-xs-12">
													<input type="password" name="cust_conf_password" id="cust_conf_password"  placeholder="Confirm Password" class="form-control" required>
												</div>

												<div class="col-md-12 col-sm-12 col-xs-12">
													<input type="text" name="cust_address" id="cust_address" placeholder="Address" class="form-control" >
												</div>
						
												<div class="col-md-12 col-sm-12 col-xs-12 text-center">

													<div id="registration_submit_error" class="text-center" style="display:none"></div>
													<input type="submit" name="submit" id="register_submit" class="button-default button-default-submit" value="Register now" style="width:50%; font-size:16px; height:auto">
												</div>
											</div>
										</form>
										<!--<p>By clicking on <b>Register Now</b> button you are accepting the <a href="#">Terms &amp; Conditions</a></p>-->
										 <div class="divider-login">
											<hr>
											<span>Or</span>
										</div>
										<div class="row">
											<div class="col-md-12 col-sm-12 col-xs-12 text-center">
												<a href="javascript:void(0)" onclick="active_modal(1)" class=" btn-change button-default " id="log_reg"><i class="fa fa-user" aria-hidden="true"></i> have an account?</a>
											</div>
										</div>
								   </div>
									<div class="col-md-12 col-sm-12 col-xs-12 done_registration_msg text-center hide" >
										<div class="alert alert-success">
											<p>Your registration is completed. Please login with provided credentials</p>
										</div>
										<a href="javascript:void(0)" onclick="active_modal(1)" class="facebook-btn btn-change button-default " id="do_login"><i class="fa fa-user" aria-hidden="true"></i> Login</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- END REGISTER MODAL -->

				<!-- Start forgetr pass modal -->
				<div class="hide" id="forget_passModal" tabindex="-1" role="dialog" aria-labelledby="booktable">
					 <div class="modal-dialog" role="document" style="width:94% !important; margin-top:10%">
						<div class="" id="passwore_recovary_modal_responsive">
							<div class="modal-body">
								<div id="forget-pass-div">
									<div class="title text-center">
										<h3 class="text-coffee"><br>Burrito Brothers<br><br></h3>
									</div>
									<form class="register-form" method="post" name="forget-pass-form" id="forget-pass-form">
										<div class="row">
											<div class="sent_password">
												<div class="col-md-12 col-sm-12 col-xs-12 ">
													<input type="email" name="forget_email" id="forget_email" placeholder="Enter email address" class="input-fields">
												</div>
												<div class="col-md-12 col-sm-12 col-xs-12 text-center">
													<div id="foget_pass_submit_error" class="text-center" style="display:none"></div>
													<input type="submit" name="submit" id="foget_pass_submit"  class="button-default button-default-submit" value="Email Password" style="width:50%; font-size:16px; height:auto">
												</div>
											</div>
											<div class="col-md-12 col-sm-12 col-xs-12 sent_password_msg center hide" >
												<div class="alert alert-success">
													<p>A new password has been sent to your provided email address. please check and login</p>
												</div>
												<a href="javascript:void(0)" onclick="active_modal(1)" class="facebook-btn btn-change button-default "  id="do_login"><i class="fa fa-user" aria-hidden="true"></i> Login</a>
											</div>
										</div>
									</form>
									<div class="divider-login">
											<hr>
											<span>Or</span>
										</div>
										<div class="row">
											<div class="col-md-12 col-sm-12 col-xs-12 text-center">
												<a href="javascript:void(0)" onclick="active_modal(1)" class=" btn-change button-default " id="log_reg"><i class="fa fa-user" aria-hidden="true"></i> have an account?</a>
											</div>
										</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End login -->
				
				     <div class="text-center">
						<img width="120"  src="images/mbrothers_logo.png" alt="">
                        <p>Developed By  <a href="#">MBrothers Solution</a></p>
                    </div>
			</div>
        </div>
 

</body>

<script src="theme/js/jquery.min.js"></script>
<script src="js/static_text.js"></script>



<script>
    $('.scrollDiv').css('max-height', $(window).height());

    function active_modal(type){
        if(type==1){
			$('#loginModal').removeClass('hide');
            $('#forget_passModal').addClass('hide');
            $('#registerModal').addClass('hide');
        }
        else if(type==2){
			$('#forget_passModal').removeClass('hide');
            $('#loginModal').addClass('hide');
            $('#registerModal').addClass('hide');
        }
        else if(type==3){
			$('#loginModal').addClass('hide');
            $('#loginModal').addClass('hide');
            setTimeout(function(){
                $('#registerModal').removeClass();
            }, 400);
        }
    }

    //login validation and complete login
    $('#login_submit').click(function(event){
        event.preventDefault();
        var formData = new FormData($('#login-form')[0]);
        formData.append("q","login_customer");
        if($.trim($('#username').val()) == ""){
            success_or_error_msg('#login_submit_error','danger',"Please type user name","#emp_name");
        }
        if($.trim($('#password').val()) == ""){
            success_or_error_msg('#login_submit_error','danger',"Please type password","#password");
        }
        else{
            $.ajax({
                url: project_url+"controller/authController.php",
                type:'POST',
                data:formData,
                async:false,
                cache:false,
                contentType:false,processData:false,
                success: function(data){
                    if($.isNumeric(data)==true && data==3){
                        success_or_error_msg('#login_submit_error',"danger","Invalid username","#user_name" );
                    }
                    else if($.isNumeric(data)==true && data==2){
                        success_or_error_msg('#login_submit_error',"danger","Invalid password","#password" );
                    }
                    else if($.isNumeric(data)==true && data==1){
                        window.location=project_url+'index.php';
                    }
                }
            });
        }
    })

    // send mail if forget password


    $('#foget_pass_submit').click(function(event){
        event.preventDefault();
        var formData = new FormData($('#forget-pass-form')[0]);
        formData.append("q","forget_password");
        formData.append("is_app","1");

        if($.trim($('#forget_email').val()) == ""){
            success_or_error_msg('#foget_pass_submit_error','danger',"Please enter email address","#forget_email");
        }
        else{
            $.ajax({
                url: project_url+"controller/authController.php",
                type:'POST',
                data:formData,
                async:false,
                cache:false,
                contentType:false,processData:false,
                success: function(data){
                    if($.isNumeric(data)==true && data==2){
                        success_or_error_msg('#foget_pass_submit_error',"danger","Please provide a valid email address","#forget_email" );
                    }
                    else if($.isNumeric(data)==true && data==1){
                        $('.sent_password').addClass("hide");
                        $('.sent_password_msg').removeClass("hide");
                    }
                }
            });
        }
    })

    // send mail if forget password
    $('#register_submit').click(function(event){
        event.preventDefault();
        var formData = new FormData($('#register-form')[0]);
        formData.append("q","registration");
        if($.trim($('#cust_name').val()) == ""){
            success_or_error_msg('#registration_submit_error','danger',"Please enter name","#cust_name");
        }
        else if($.trim($('#cust_username').val()) == ""){
            success_or_error_msg('#registration_submit_error','danger',"Please enter username","#cust_username");
        }
        else if($.trim($('#cust_email').val()) == ""){
            success_or_error_msg('#registration_submit_error','danger',"Please enter email address","#cust_email");
        }
        else if($.trim($('#cust_password').val()) == ""){
            success_or_error_msg('#registration_submit_error','danger',"Please enter pasword","#cust_password");
        }
        else if($.trim($('#cust_conf_password').val()) == ""){
            success_or_error_msg('#registration_submit_error','danger',"Please confirm password ","#cust_conf_password");
        }
        else if($.trim($('#cust_password').val()) != $.trim($('#cust_conf_password').val())){
            success_or_error_msg('#registration_submit_error','danger',"Please enter same password","#cust_conf_password");
        }
        else if($.trim($('#cust_contact').val()) == ""){
            success_or_error_msg('#registration_submit_error','danger',"Please enter valid contact no","#cust_contact");
        }
        else{
			if($.trim($('#cust_email').val()) != ""){
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if(!re.test($.trim($('#cust_email').val()))){
					success_or_error_msg('#registration_submit_error','danger',"Please Insert a valid email address","#cust_email");
					return false;
				}
			}
			
            $.ajax({
                url: project_url+"controller/authController.php",
                type:'POST',
                data:formData,
                async:false,
                cache:false,
                contentType:false,processData:false,
                success: function(data){
                   // alert(data)
                    if($.isNumeric(data)==true && data==2){
                        success_or_error_msg('#registration_submit_error',"danger","Username is already exist, please try with another one","#cust_username" );
                    }
                    else if($.isNumeric(data)==true && data==3){
                        success_or_error_msg('#registration_submit_error',"danger","Email is already exist, please try with another one","#cust_email" );
                    }
                    else if($.isNumeric(data)==true && data==1){
                        $('.done_registration').addClass("hide");
                        $('.done_registration_msg').removeClass("hide");
						//active_modal(1);
                    }
                    else{
                        success_or_error_msg('#registration_submit_error',"danger","Registration is not completed. please check your information again.","#cust_email" );
                    }
                }
            });
        }
    })
	
	function success_or_error_msg(div_to_show, class_name, message, field_id){
        $(div_to_show).addClass('alert alert-custom alert-'+class_name).html(message).show("slow");
        //$(window).scrollTop(200);
        var set_interval = setInterval(function(){
            $(div_to_show).removeClass('alert alert-custom alert-'+class_name).html("").hide( "slow" );
            if(field_id!=""){ $(field_id).focus();}
            clearInterval(set_interval);
        }, 4000);
    }


	
</script>

</html>