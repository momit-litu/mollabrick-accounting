// login function
$(document).ready(function(){	
	$("#loginform").submit(function(){
		//check the username exists or not from ajax
		if($('#user_login').val() == ""){
			$("#login_error").fadeTo(200,0.1,function(){ 
				$(this).html('<div class="alert alert-danger">'+username_error+'</div>').fadeTo(900,1);
			});
		}
		else if($('#user_pass').val() == ""){
			$("#login_error").fadeTo(200,0.1,function(){ 
				$(this).html('<div class="alert alert-danger">'+password_error+'</div>').fadeTo(900,1);
			});
		}
		else{ 		
			$.post("controller/loginController.php",{ user_name:$('#user_login').val(),password:$('#user_pass').val(),user_type:$('#user_type').val(), rand:Math.random() } ,function(data){
		  		var data = data.replace(/^\s*|\s*$/g,'');
		  		if(data==1){
					$("#login_error").fadeTo(200,0.1,function(){ 
						//add message and change the class of the box and start fading
						$(this).html('<div class="alert alert-success">'+logging_in+'.....</div>').fadeTo(900,1,
						function() { 
							document.location='index.php?module=personal&view=profile';
						});			
					});
				}
				else if(data==3){
					$("#login_error").fadeTo(200,0.1,function(){ 
						$(this).html('<div class="alert alert-danger">'+not_authentic_user+'</div>').fadeTo(900,1);
					});
				}
				else if(data == 2){
					$("#login_error").fadeTo(200,0.1,function(){ 
				  		$(this).html('<div class="alert alert-danger">'+wrong_password+'</div>').fadeTo(900,1);
					});					
				}		  
        	});
		}
 		return false;
	});
	
});