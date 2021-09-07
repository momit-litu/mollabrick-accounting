<style type="text/css">
	@media print {    
		.no-print, .no-print * {
			display: none !important;
		}
	}
</style>

<div class="x_content">
	<div class="x_panel employee_profile">
		<div class="x_title">
			<h2>Personal Information</h2>
			<div class="clearfix"></div>
		</div>
		<div class="x_content" id="iniial_collapse">
			<br />     
			<form method="emp"  id="emp_update_form" name="emp_info_form" enctype="multipart/form-data" class="form-horizontal form-label-left">   
				<div class="row">
					<div class="col-md-9">	
						<div class="form-group" id="is_active_home_page_div">
							<label class="control-label col-md-2 col-sm-2 col-xs-6">Active Home Page</label>
							<div class="col-md-4 col-sm-4 col-xs-6" id="home_checkbox">				
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2 col-sm-2 col-xs-12">Full Name</label>
							<div class="col-md-4 col-sm-4 col-xs-6">
								<input type="text" id="emp_name" name="emp_name" required class="form-control col-lg-12" readonly="readonly"/>
							</div>	
							<label class="control-label col-md-2 col-sm-2 col-xs-6">User Name</label>
							<div class="col-md-4 col-sm-4 col-xs-6">
								<input type="text" id="user_name" name="user_name" class="form-control col-lg-12" readonly="readonly"/>
							</div>
						</div>										
						<div class="form-group">
							<label class="control-label col-md-2 col-sm-2 col-xs-6">Email</label>
							<div class="col-md-4 col-sm-4 col-xs-6">
								<input type="email" id="email" name="email" class="form-control col-lg-12" readonly="readonly"/>
							</div>
							<label class="control-label col-md-2 col-sm-2 col-xs-6">Blood Group</label>
							<div class="col-md-4 col-sm-4 col-xs-6">
								<input type="text" id="blood_group" name="blood_group" class="form-control col-lg-12" readonly="readonly"/>
							</div>
						</div>
                        <div class="form-group">
							<label class="control-label col-md-2 col-sm-2 col-xs-6">NID No</label>
							<div class="col-md-4 col-sm-4 col-xs-6">
								<input type="email" id="nid_no" name="nid_no" class="form-control col-lg-12" readonly="readonly"/>
							</div>
							<label class="control-label col-md-2 col-sm-2 col-xs-6">Contact No</label>
							<div class="col-md-4 col-sm-4 col-xs-6"> 
								<input type="text" id="contact_no" name="contact_no" class="form-control col-lg-12" readonly="readonly"/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2 col-sm-2 col-xs-6"></label>
							<div class="col-md-10 col-sm-10 col-xs-12">
								<small style="color:red" >
									(If you need to change the password then choose a new password in <B>New Password</B> field.)
								</small>
							</div>
						</div>					
						<div class="form-group">
							<label class="control-label col-md-2 col-sm-2 col-xs-6">Old Password</label>
							<div class="col-md-4 col-sm-4 col-xs-6">
								<input type="password" id="old_password" name="old_password" class="form-control col-lg-12"/>
							</div>
							<label class="control-label col-md-2 col-sm-2 col-xs-6">New Password</label>
							<div class="col-md-4 col-sm-4 col-xs-6">
								<input type="password" id="new_password" name="new_password" class="form-control col-lg-12"/>
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-md-2 col-sm-2 col-xs-6"></label>
							<div class="col-md-2 col-sm-2 col-xs-12">
								<button  type="submit" id="save_emp_info" class="btn btn-success">Update</button>                                        
							</div>
							 <div class="col-md-8 col-sm-8 col-xs-12">
								<div id="form_submit_error" class="text-center" style="display:none"></div>
							 </div>
						</div>
					</div>
					<div class="col-md-3">
						<input type="hidden" id="img_url_to_copy" name="img_url_to_copy"/>
						<img src="" class="img-thumbnail" id="emp_img">
					</div>
				</div>
			</form> 
		</div>
	</div>
</div>	
	
<script src="js/customTable.js"></script> 


<script>

//------------------------------------- general & UI  --------------------------------------
	
	
$(document).ready(function () {	
	
	load_emp_profile = function load_emp_profile(){
		$('#is_active_home_page_div').hide();
		$.ajax({
			url: project_url+"controller/userController.php",
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "get_user_info"
			},
			success: function(data){
				$('#emp_id').val(data.records.emp_id);
				$('#emp_name').val(data.records.full_name);
				$('#user_name').val(data.records.user_name);
				$('#contact_no').val(data.records.contact_no);	
				$('#email').val(data.records.email);
				$('#blood_group').val(data.records.blood_group);
				$('#nid_no').val(data.records.nid_no);
				
				if(data.records.photo == ""){
					$('#emp_img').attr("src",project_url+'images/no_image.png');
				}else{
					$('#emp_img').attr("src",project_url+data.records.photo);
				}
				$('#emp_img').attr("width", "70%","height","70%");
				
				if(data.records.is_active_home_page==1){
					$('#is_active_home_page_div').show();
					$('#home_checkbox').html("<i class='fa fa-check-square fa-2x' aria-hidden='true'></i>");
				}	
			}
		});
	}
	
	load_emp_profile("");
	
	var url = project_url+"controller/userController.php";
	$('#save_emp_info').click(function(event){		
		event.preventDefault();
		var formData = new FormData($('#emp_update_form')[0]);
		formData.append("q","update_information");
		if($.trim($('#old_password').val()) == ""){
			success_or_error_msg('#form_submit_error','danger',"Please Insert Your Password!","#old_password");			
		}
		else{	
			$.ajax({
				url: url,
				type:'POST',
				data:formData,
				async:false,
				cache:false,
				contentType:false,processData:false,
				success: function(data){
					$('#save_emp_info').removeAttr('disabled','disabled');
					if(data>0){
						success_or_error_msg('#form_submit_error',"success","Updated Successfully");
						$('#old_password').val('');
						$('#new_password').val('');
						load_emp_profile("");
					}
					else{
						success_or_error_msg('#form_submit_error',"danger","Old Password Does Not Match...");
						$('#old_password').val('');
						$('#new_password').val('');
					}  
				}	
			});
		}	
	})
				
});
</script>