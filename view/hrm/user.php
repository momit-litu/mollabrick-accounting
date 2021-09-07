<?php
session_start();
include '../../includes/static_text.php';
include("../../dbConnect.php");
include("../../dbClass.php");
$dbClass = new dbClass;
$user_type = $_SESSION['user_type'];

if(!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") header("Location:".$activity_url."../view/login.php");
else if($dbClass->getUserGroupPermission(15) != 1){
?> 
	<div class="x_panel">
		<div class="alert alert-danger" align="center">You Don't Have permission of this Page.</div>
	</div>
	<?php 
} 
else{
	$user_name = $_SESSION['user_name'];
	?>
	
<div class="x_panel">
    <div class="x_title">
        <h2>User List</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
				<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
    	<div id="page_notification_div" class="text-center" style="display:none"></div>        
		
		<!-- Advance Search Div-->
		<div class="x_panel hide">
			<div class="row">
				<ul class="nav navbar-right panel_toolbox">
					<li>
						<a class="collapse-link-adv" id="toggle_form_ad"><b><small class="text-primary">Advance Search & Report</small></b><i class="fa fa-chevron-down"></i></a>
					</li>
				</ul>
			</div>
			<div class="x_content adv_cl" id="iniial_collapse_adv">
				<div class="row advance_search_div alert alert-warning">
					<div class="row">
						<label class="control-label col-md-1 col-sm-2 col-xs-4" style="text-align:right">Active</label>
						<div class="col-md-3 col-sm-4 col-xs-8">
							<input type="radio" class="flat_radio" name="is_active_status" id="is_active_status" value="1"/> Yes
							<input type="radio" class="flat_radio" name="is_active_status" id="is_active_status" value="0" /> No
							<input type="radio" class="flat_radio" name="is_active_status" id="is_active_status" value="2" checked="CHECKED"/> All
						</div>
						<div class="col-md-3" style="text-align:center">					
							<button type="button" class="btn btn-info" id="adv_search_button"><i class="fa fa-lg fa-search"></i></button>
							<button type="button" class="btn btn-warning" id="adv_search_print"><i class="fa fa-lg fa-print"></i></button>
						</div>
					</div>
				</div> 
			</div>
		</div> 
		<!-- Adnach search end -->
		
		<div class="dataTables_length">
        	<label>Show 
                <select size="1" style="width: 56px;padding: 6px;" id="emp_Table_length" name="emp_Table_length" aria-controls="emp_Table">
                    <option value="50" selected="selected">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                 </select> 
                 Post
             </label>
         </div>
         <div class="dataTables_filter" id="emp_Table_filter">         
			<div class="input-group">
                <input class="form-control" id="search_emp_field" style="" type="text">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-primary has-spinner" id="search_emp_button">
                     <span class="spinner"><i class="fa fa-spinner fa-spin fa-fw "></i></span>
                     <i class="fa  fa-search "></i>
                    </button> 
                </span>
            </div>
       </div>
       <div style="height:250px; width:100%; overflow-y:scroll">
        <table id="emp_Table" name="table_records" class="table table-bordered  responsive-utilities jambo_table table-striped  table-scroll ">
            <thead >
                <tr class="headings">
					<th class="column-title" width="5%"></th>
					<th class="column-title" width="10%">ID</th>
                    <th class="column-title" width="28%">Name</th>
                    <th class="column-title" width="15%">User Name</th>
                    <th class="column-title" width="20%">Email</th>
                    <th class="column-title" width="15%">Contact No</th>
                    <th class="column-title no-link last"  width="100"><span class="nobr"></span></th>
                </tr>
            </thead>
            <tbody id="emp_table_body" class="scrollable">              
                
            </tbody>
        </table>
        </div>
        <div id="emp_Table_div">
            <div class="dataTables_info" id="emp_Table_info">Showing <span id="from_to_limit"></span> of  <span id="total_record"></span> entries</div>
            <div class="dataTables_paginate paging_full_numbers" id="emp_Table_paginate">
            </div> 
        </div>  
    </div>
</div>
<?php if($dbClass->getUserGroupPermission(10) == 1){ ?>
<div class="x_panel employee_entry_cl">
    <div class="x_title">
        <h2>User Entry</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
				<a class="collapse-link" id="toggle_form"><i class="fa fa-chevron-down"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" id="iniial_collapse">
        <br />             
		<form id="emp_form" name="emp_form" enctype="multipart/form-data" class="form-horizontal form-label-left">   
			<div class="row">
				<div class="col-md-9">
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6">Full Name<span class="required">*</span></label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="text" id="emp_name" name="emp_name" required class="form-control col-lg-12"/>
						</div>
						<label class="control-label col-md-2 col-sm-2 col-xs-6" >NID No<span class="required">*</span></label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="text" id="nid_no" name="nid_no" class="form-control col-lg-12" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6" for="name">Blood Group</label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="text" id="blood_group" name="blood_group" required class="form-control col-lg-12"/>
						</div>
						<label class="control-label col-md-2 col-sm-2 col-xs-6" for="name">Age</label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="text" id="age" name="age" required class="form-control col-lg-12"/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6">Contact No<span class="required">*</span></label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="text" id="contact_no" name="contact_no" required class="form-control col-lg-12"/>
						</div>
						<label class="control-label col-md-2 col-sm-2 col-xs-6">Email</label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="email" id="email" name="email" required class="form-control col-lg-12"/>
						</div>
					</div>
					<div class="form-group"> 
						<label class="control-label col-md-2 col-sm-2 col-xs-6" >Address</label>
						<div class="col-md-10 col-sm-10  col-xs-6">
							<input type="text" id="address" name="address" class="form-control col-lg-12" />
						</div>
					</div>	
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6">User Name<span class="required">*</span></label>
						<div class="col-md-4 col-sm-4 col-xs-12">
							<input type="text" id="user_name" name="user_name"  class="form-control col-lg-12"/>
						</div>
						<label class="control-label col-md-2 col-sm-2 col-xs-6">Password</label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="password" id="password" name="password"  class="form-control col-lg-12"/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6" >Is Active</label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="checkbox" id="is_active" name="is_active" checked="checked" class="form-control col-lg-12"/>
						</div>
					</div>
					<br/>					
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6">Remarks</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<textarea rows="2" cols="100" id="remarks" name="remarks" class="form-control col-lg-12"></textarea> 
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6" >User Group</label>
						<div id="group_select" class="col-md-10 col-sm-10 col-xs-12"></div>
					</div>
					<div class="ln_solid"></div>
				</div>
				<div class="col-md-3">
					<img src="<?php echo $activity_url ?>images/no_image.png" width="70%" height="70%" class="img-thumbnail" id="emp_img">
					<input type="file" name="emp_image_upload" id="emp_image_upload"> 
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6"></label>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<input type="hidden" id="emp_id" name="emp_id" />    
					<button type="submit" id="save_emp_info" class="btn btn-success">Save</button>                    
					<button type="button" id="clear_button" class="btn btn-primary">Clear</button>                         
				</div>
				 <div class="col-md-7 col-sm-7 col-xs-12">
					<div id="form_submit_error" class="text-center" style="display:none"></div>
				 </div>
			</div>
		</form>  
    </div>
</div>
	
<?php
		}
	} 
?>
<script src="js/customTable.js"></script> 
<script>
//------------------------------------- general & UI  --------------------------------------
/*
develped by @momit
=>load grid with paging
=>search records
*/
$(document).ready(function () {	
	var user_type = "<?php echo $user_type; ?>";
	// close form submit section onload page
	var x_panel = $('#iniial_collapse').closest('div.x_panel');
	var button = $('#iniial_collapse').find('i');
	var content = x_panel.find('div.x_content');
	content.slideToggle(200);
	(x_panel.hasClass('fixed_height_390') ? x_panel.toggleClass('').toggleClass('fixed_height_390') : '');
	(x_panel.hasClass('fixed_height_320') ? x_panel.toggleClass('').toggleClass('fixed_height_320') : '');
	button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
	setTimeout(function () {
		x_panel.resize();
	}, 50);


	// collaps button function
	$('.collapse-link').click(function () {
		var x_panel = $(this).closest('div.x_panel');
		var button = $(this).find('i');
		var content = x_panel.find('div.x_content');
		content.slideToggle(200);
		(x_panel.hasClass('fixed_height_390') ? x_panel.toggleClass('').toggleClass('fixed_height_390') : '');
		(x_panel.hasClass('fixed_height_320') ? x_panel.toggleClass('').toggleClass('fixed_height_320') : '');
		button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
		setTimeout(function () {
			x_panel.resize();
		}, 50);
	})	
	
	// close form submit section onload page
	var x_panel = $('#iniial_collapse_adv').closest('div.x_panel');
	var button = $('#iniial_collapse').find('i');
	var content = x_panel.find('div.x_content');
	content.slideToggle(200);
	(x_panel.hasClass('fixed_height_390') ? x_panel.toggleClass('').toggleClass('fixed_height_390') : '');
	(x_panel.hasClass('fixed_height_320') ? x_panel.toggleClass('').toggleClass('fixed_height_320') : '');
	button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
	setTimeout(function () {
		x_panel.resize();
	}, 50); 
	
	// collaps button function
	$('.collapse-link-adv').click(function (){
		var x_panel = $(this).closest('div.x_panel');
		var button = $(this).find('i');
		var content = x_panel.find('div.x_content');
		content.slideToggle(200);
		(x_panel.hasClass('fixed_height_390') ? x_panel.toggleClass('').toggleClass('fixed_height_390') : '');
		(x_panel.hasClass('fixed_height_320') ? x_panel.toggleClass('').toggleClass('fixed_height_320') : '');
		button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
		setTimeout(function () {
			x_panel.resize();
		}, 50);
	}) 
	
	// icheck for the inputs
	$('#emp_form').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
	});	
	
	$('.flat_radio').iCheck({
		//checkboxClass: 'icheckbox_flat-green'
		radioClass: 'iradio_flat-green'
	});

	
});
<!-- ------------------------------------------end --------------------------------------->


//------------------------------------- grid table codes --------------------------------------
/*
develped by @momit
=>load grid with paging
=>search records
*/
$(document).ready(function (){	
	// initialize page no to "1" for paging
	var current_page_no=1;	
	$('.adv_cl').hide();
	load_data = function load_data(search_txt){
		$("#search_emp_button").toggleClass('active');		 
		var emp_Table_length =parseInt($('#emp_Table_length').val());		
		$.ajax({
			url: project_url+"controller/userController.php",
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "grid_data",
				search_txt: search_txt,
				limit:emp_Table_length,
				page_no:current_page_no
			},
			success: function(data){
				var todate = "<?php echo date("Y-m-d"); ?>";
				var user_name =  "<?php echo $user_name; ?>";
				var html = "";
				if(data.entry_status==0){
					$('.employee_entry_cl').hide();
				}
				// for  showing grid's no of records from total no of records 
				show_record_no(current_page_no, emp_Table_length, data.total_records )
				
				var total_pages = data.total_pages;	
				var records_array = data.records;
				$('#emp_Table tbody tr').remove();
				//$("#search_emp_button").toggleClass('active');
				if(!jQuery.isEmptyObject(records_array)){
					// create and set grid table row
					var colums_array=["photo*image*"+project_url,"emp_id*identifier", "full_name","user_name","email","contact_no*center"];
					// first element is for view , edit condition, delete condition
					// "all" will show /"no" will show nothing
					var condition_array=["","","update_status", "1","delete_status","1"];
					// create_set_grid_table_row(records_array,colums_array,int_fields_array, condition_arraymodule_name,table/grid id, is_checkbox to select tr );
					// cauton: not posssible to use multiple grid in same page					
					create_set_grid_table_row(records_array,colums_array,condition_array,"user","emp_Table", 0);
					// show the showing no of records and paging for records 
					$('#emp_Table_div').show();					
					// code for dynamic pagination 				
					paging(total_pages, current_page_no, "emp_Table" );					
				}
				// if the table has no records / no matching records 
				else{
					grid_has_no_result( "emp_Table",8);
				}
				$("#search_emp_button").toggleClass('active');								
			}
		});	
	}
	
	load_user_groups = function load_user_groups(){
		$.ajax({
			url: project_url+"controller/userController.php",
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "get_user_groups"
			},
			success: function(data) {
				//var option_html = '';	
				if(!jQuery.isEmptyObject(data.records)){
					var html = '<table class="table table-bordered jambo_table"><thead><tr class="headings"><th class="column-title text-center" class="col-md-8 col-sm-8 col-xs-8" >User Groups</th><th class="col-md-2 col-sm-2 col-xs-12"><input type="checkbox" id="check-all" class="tableflat">Select All</th></tr></thead>';
						$.each(data.records, function(i,datas){ 			
							 html += '<tr><td colspan="2">';
							 $.each(datas.module_group, function(i,module_group){ 
								module_group_arr = module_group.split("*");	
								html += '<div class="col-md-3" ><input type="checkbox" name="group[]"  class="tableflat"  value="'+module_group_arr[0]+'"/> '+module_group_arr[1]+'</div>';								
							 });
							html += '</td></tr>';
							
						});
					html +='</table>';	
				}
				$('#group_select').html(html);
				$('#emp_form').iCheck({
						checkboxClass: 'icheckbox_flat-green',
						radioClass: 'iradio_flat-green'
				});									
				
				$('#emp_form input#check-all').on('ifChecked', function () {
					//alert('check');
					$("#emp_form .tableflat").iCheck('check');
				});
				$('#emp_form input#check-all').on('ifUnchecked', function () {
					//alert('ucheck');
					$("#emp_form .tableflat").iCheck('uncheck');
				});
			}
		});
	}
	
	// load desire page on clik specific page no
	load_page = function load_page(page_no){
		if(page_no != 0){
			// every time current_page_no need to change if the user change page
			current_page_no=page_no;
			var search_txt = $("#search_emp_field").val();
			load_data(search_txt)
		}
	}	
	// function after click search button 
	$('#search_emp_button').click(function(){
		var search_txt = $("#search_emp_field").val();
		// every time current_page_no need to set to "1" if the user search from search bar
		current_page_no=1;
		load_data(search_txt)
		// if there is lot of data and it tooks lot of time please add the below condition
		/*
		if(search_txt.length>3){
			load_data(search_txt)	
		}
		*/
	});
	//function after press "enter" to search	
	$('#search_emp_field').keypress(function(event){
		var search_txt = $("#search_emp_field").val();	
		if(event.keyCode == 13){
			// every time current_page_no need to set to "1" if the user search from search bar
			current_page_no=1;
			load_data(search_txt)
			// if there is lot of data and it tooks lot of time please add the below condition
			/*
			if(search_txt.length>3){
				load_data(search_txt,1)	
			}*/
		}
	})
	
	// load data initially on page load with paging
	load_data("");
	load_user_groups("");
});


<!-- ------------------------------------------end --------------------------------------->


<!-- -------------------------------Form related functions ------------------------------->

/*
develped by @momit
=>form submition for add/edit
=>clear form
=>load data to edit
=>delete record
=>view 
*/
$(document).ready(function () {		
	var url = project_url+"controller/userController.php";	
	
	// save and update for public post/notice
	$('#save_emp_info').click(function(event){		
		event.preventDefault();
		var formData = new FormData($('#emp_form')[0]);
		formData.append("q","insert_or_update");
		if($.trim($('#emp_name').val()) == ""){
			success_or_error_msg('#form_submit_error','danger',"Please Insert Name","#emp_name");			
		}		
		else if($.trim($('#contact_no').val()) == ""){
			success_or_error_msg('#form_submit_error','danger',"Please Insert contact no","#contact_no");			
		}
		else if($.trim($('#nid_no').val()) == ""){
			success_or_error_msg('#form_submit_error','danger',"Please Insert NID no","#nid_no");			
		}		
		else if($.trim($('#user_name').val()) == ""){
			success_or_error_msg('#form_submit_error','danger',"Please Insert User Name","#user_name");			
		}			
		else{
		//	$('#save_emp_info').attr('disabled','disabled');			
			$.ajax({
				url: url,
				type:'POST',
				data:formData,
				async:false,
				cache:false,
				contentType:false,processData:false,
				success: function(data){
					$('#save_emp_info').removeAttr('disabled','disabled');
					
					if($.isNumeric(data)==true && data==5){
						success_or_error_msg('#form_submit_error',"danger","Please Insert Unique Username","#user_name" );			
					}
					else if($.isNumeric(data)==true && data>0){
						success_or_error_msg('#form_submit_error',"success","Save Successfully");
						load_data("");
						clear_form();
					}
					else{
						if(data == "img_error")
							success_or_error_msg('#form_submit_error',"danger",not_saved_msg_for_img_ln);
						else	
							success_or_error_msg('#form_submit_error',"danger","Not Saved...");												
					}
				 }	
			});
		}	
	})
	
	//advance search
	$('#adv_search_button').click(function(){
		load_data("Advance_search");
	});
	
	//print advance search data
	$('#adv_search_print').click(function(){
		load_data("Print");
	});
	
	// clear function to clear all the form value
	clear_form = function clear_form(){			 
		$('#emp_id').val('');
		$("#emp_form").trigger('reset');		
		$('#emp_img').attr("width", "0%");
		$('#emp_img').attr("src",project_url+"images/no_image.png");
		$('#emp_img').attr("width", "70%","height","70%");
		$('#img_url_to_copy').val("");
		$('#emp_form').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});	
		$("#emp_form .tableflat").iCheck('uncheck');
		$('#save_emp_info').html('Save');		
	}
	
	// on select clear button 
	$('#clear_button').click(function(){
		clear_form();
	});
	
	
	delete_user = function delete_user(emp_id){
		if (confirm("Do you want to delete the record? ") == true) {
			$.ajax({
				url: url,
				type:'POST',
				async:false,
				data: "q=delete_employee&emp_id="+emp_id,
				success: function(data){
					if($.trim(data) == 1){
						success_or_error_msg('#page_notification_div',"success","Deleted Successfully");
						load_data("");
					}
					else{
						success_or_error_msg('#page_notification_div',"danger","Not Deleted...");						
					}
				 }	
			});
		} 	
	}
	
	edit_user = function edit_user(emp_id){
		//alert(emp_id);
		$.ajax({
			url: url,
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "get_emp_details",
				emp_id: emp_id
			},
			success: function(data){
				if(!jQuery.isEmptyObject(data.records)){
					$.each(data.records, function(i,data){ 
						clear_form();					
						$('#emp_id').val(data.emp_id);
						$('#emp_name').val(data.full_name);
						$('#user_name').val(data.user_name);
						$('#nid_no').val(data.nid_no);
						$('#contact_no').val(data.contact_no);						
						$('#email').val(data.email);
						$('#age').val(data.age);						
						$('#address').val(data.address);
						$('#remarks').val(data.remarks);
						$('#blood_group').val(data.blood_group);
						if(data.photo == ""){
							$('#emp_img').attr("src",project_url+'images/no_image.png');
						}else{
							$('#emp_img').attr("src",project_url+data.photo);
						}
						$('#emp_img').attr("width", "70%","height","70%");

						//change button value 
						$('#save_emp_info').html('Update User');
						
						// to open submit post section
						if($.trim($('#toggle_form i').attr("class"))=="fa fa-chevron-down")
							$( "#toggle_form" ).trigger( "click" );
						$.ajax({
							url: project_url+"controller/userController.php",
							dataType: "json",
							type: "post",
							async:false,
							data: {
								q: "get_emp_user_groups",
								emp_id: emp_id
							},
							success: function(data) {
								//alert(data);
								//var option_html = '';	
								if(!jQuery.isEmptyObject(data.records)){
									var html = '<table class="table table-bordered jambo_table"><thead><tr class="headings"><th class="column-title text-center" class="col-md-8 col-sm-8 col-xs-8" >User Groups</th><th class="col-md-2 col-sm-2 col-xs-12"><input type="checkbox" id="check-all" class="tableflat">Select All</th></tr></thead>';
										$.each(data.records, function(i,datas){ 			
											 html += '<tr><td colspan="2">';
											 $.each(datas.module_group, function(i,module_group){ 
											// alert(module_group)
												module_group_arr = module_group.split("*");	
												if(module_group_arr[2]==1) 	
													html += '<div class="col-md-3" ><input type="checkbox" name="group[]"  class="tableflat" checked="checked"  value="'+module_group_arr[0]+'"/> '+module_group_arr[1]+'</div>';	
												else
													html += '<div class="col-md-3" ><input type="checkbox" name="group[]"  class="tableflat"  value="'+module_group_arr[0]+'"/> '+module_group_arr[1]+'</div>';	
											 });
											html += '</td></tr>';
											
										});
									html +='</table>';	
								}
								$('#group_select').html(html);
								$('#emp_form').iCheck({
										checkboxClass: 'icheckbox_flat-green',
										radioClass: 'iradio_flat-green'
								});									
								
								$('#emp_form input#check-all').on('ifChecked', function () {
									//alert('check');
									$("#emp_form .tableflat").iCheck('check');
								});
								$('#emp_form input#check-all').on('ifUnchecked', function () {
									//alert('ucheck');
									$("#emp_form .tableflat").iCheck('uncheck');
								});
							}
						});
						
					});
					
				}
			}	
		});			
	}				
	
});


<!-- ------------------------------------------end --------------------------------------->
</script>