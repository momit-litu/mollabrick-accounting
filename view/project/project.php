<?php
session_start();
include '../../includes/static_text.php';
include("../../dbConnect.php");
include("../../dbClass.php");
$dbClass = new dbClass;
$user_type = $_SESSION['user_type'];

if(!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") header("Location:".$activity_url."../view/login.php");
else if($dbClass->getUserGroupPermission(56) != 1){
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
        <h2>Project List</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
				<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
    	<div id="page_notification_div" class="text-center" style="display:none"></div>        
		<div class="dataTables_length">
        	<label>Show 
                <select size="1" style="width: 56px;padding: 6px;" id="project_Table_length" name="project_Table_length" aria-controls="project_Table">
                    <option value="50" selected="selected">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                 </select> 
                 Post
             </label>
         </div>
         <div class="dataTables_filter" id="project_Table_filter">         
			<div class="input-group">
                <input class="form-control" id="search_project_field" style="" type="text">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-primary has-spinner" id="search_project_button">
                     <span class="spinner"><i class="fa fa-spinner fa-spin fa-fw "></i></span>
                     <i class="fa  fa-search "></i>
                    </button> 
                </span>
            </div>
       </div>
       <div style="height:250px; width:100%; overflow-y:scroll">
        <table id="project_Table" name="table_records" class="table table-bordered  responsive-utilities jambo_table table-striped  table-scroll ">
            <thead >
                <tr class="headings">
					<th class="column-title" width="5%"></th>
                    <th class="column-title" width="">Name</th>
                    <th class="column-title" width="10%">Status</th>
                    <th class="column-title" width="20%">Address</th>
                    <th class="column-title" width="10%">Contact No</th>
                    <th class="column-title no-link last" width="100"><span class="nobr"></span></th>
                </tr>
            </thead>
            <tbody id="project_table_body" class="scrollable">              
                
            </tbody>
        </table>
        </div>
        <div id="project_Table_div">
            <div class="dataTables_info" id="project_Table_info">Showing <span id="from_to_limit"></span> of  <span id="total_record"></span> entries</div>
            <div class="dataTables_paginate paging_full_numbers" id="project_Table_paginate">
            </div> 
        </div>  
    </div>
</div>
<?php if($dbClass->getUserGroupPermission(53) == 1){ ?>
<div class="x_panel project_entry_cl">
    <div class="x_title">
        <h2>Project Entry</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
				<a class="collapse-link" id="toggle_form"><i class="fa fa-chevron-down"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" id="iniial_collapse">
        <br />             
		<form id="project_form" name="project_form" enctype="multipart/form-data" class="form-horizontal form-label-left">   
			<div class="row">
				<div class="col-md-9">
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6">Name<span class="required">*</span></label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="text" id="project_name" name="project_name" required class="form-control col-lg-12"/>
						</div>
						<label class="control-label col-md-2 col-sm-2 col-xs-6">Contact No</label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="text" id="contact_no" name="contact_no" required class="form-control col-lg-12"/>
						</div>
					</div>
					<div class="form-group"> 
						<label class="control-label col-md-2 col-sm-2 col-xs-6" >Address</label>
						<div class="col-md-10 col-sm-10  col-xs-6">
							<input type="text" id="address" name="address" class="form-control col-lg-12" />
						</div>
					</div>
					<div class="form-group"> 
						<label class="control-label col-md-2 col-sm-2 col-xs-6" >Note</label>
						<div class="col-md-10 col-sm-10  col-xs-6">
							<textarea rows="2" cols="100" id="note" name="note" class="form-control col-lg-12"></textarea> 
						</div>
					</div>					
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6" >Is Active</label>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<input type="checkbox" id="is_active" name="is_active" checked="checked" class="form-control col-lg-12"/>
						</div>
					</div>
					<div class="ln_solid"></div>
				</div>
				<div class="col-md-3">
					<img src="<?php echo $activity_url ?>images/no_image.png" width="70%" height="70%" class="img-thumbnail" id="project_img">
					<input type="file" name="project_image_upload" id="project_image_upload"> 
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6"></label>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<input type="hidden" id="project_id" name="project_id" />    
					<button type="submit" id="save_project_info" class="btn btn-success">Save</button>                    
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
	
	// icheck for the inputs
	$('#project_form').iCheck({
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

	load_data = function load_data(search_txt){
		$("#search_project_button").toggleClass('active');		 
		var project_Table_length =parseInt($('#project_Table_length').val());

		$.ajax({
			url: project_url+"controller/projectController.php",
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "grid_data",
				search_txt: search_txt,
				limit:project_Table_length,
				page_no:current_page_no
			},
			success: function(data){
				var todate = "<?php echo date("Y-m-d"); ?>";
				var user_name =  "<?php echo $user_name; ?>";
				
				if(data.entry_status==0){
					$('.project_entry_cl').hide();
				}
				// for  showing grid's no of records from total no of records 
				show_record_no(current_page_no, project_Table_length, data.total_records )
				
				var total_pages = data.total_pages;	
				var records_array = data.records;
				$('#project_Table tbody tr').remove();
				//$("#search_project_button").toggleClass('active');
				if(!jQuery.isEmptyObject(records_array)){
					// create and set grid table row
					var colums_array=["logo*image*"+project_url,"id*identifier*hidden", "project_name","status_text","project_address","phone*center"];
					// first element is for view , edit condition, delete condition
					// "all" will show /"no" will show nothing
					var condition_array=["","","update_status","1","delete_status","1"];
					// create_set_grid_table_row(records_array,colums_array,int_fields_array, condition_arraymodule_name,table/grid id, is_checkbox to select tr );
					// cauton: not posssible to use multiple grid in same page					
					create_set_grid_table_row(records_array,colums_array,condition_array,"project","project_Table", 0);
					// show the showing no of records and paging for records 
					$('#project_Table_div').show();					
					// code for dynamic pagination 				
					paging(total_pages, current_page_no, "project_Table" );					
				}
				// if the table has no records / no matching records 
				else{
					grid_has_no_result( "project_Table",8);
				}
				$("#search_project_button").toggleClass('active');							
			}
		});	
	}
	
	// load desire page on clik specific page no
	load_page = function load_page(page_no){
		if(page_no != 0){
			// every time current_page_no need to change if the user change page
			current_page_no=page_no;
			var search_txt = $("#search_project_field").val();
			load_data(search_txt)
		}
	}	
	// function after click search button 
	$('#search_project_button').click(function(){
		var search_txt = $("#search_project_field").val();
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
	$('#search_project_field').keypress(function(event){
		var search_txt = $("#search_project_field").val();	
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
	var url = project_url+"controller/projectController.php";	
	
	// save and update for public post/notice
	$('#save_project_info').click(function(event){		
		event.preventDefault();
		var formData = new FormData($('#project_form')[0]);
		formData.append("q","insert_or_update");
		if($.trim($('#project_name').val()) == ""){
			success_or_error_msg('#form_submit_error','danger',"Please Insert Name","#project_name");			
		}	
		else{
		//	$('#save_project_info').attr('disabled','disabled');
			
			$.ajax({
				url: url,
				type:'POST',
				data:formData,
				async:false,
				cache:false,
				contentType:false,processData:false,
				success: function(data){
					$('#save_project_info').removeAttr('disabled','disabled');
					
					if($.isNumeric(data)==true && data==5){
						success_or_error_msg('#form_submit_error',"danger","Please Insert Unique Name","#project_name" );			
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
	
	
	// clear function to clear all the form value
	clear_form = function clear_form(){			 
		$('#project_id').val('');
		$("#project_form").trigger('reset');		
		$('#project_img').attr("src",project_url+"images/no_image.png");
		$('#project_img').attr("width", "70%","height","70%");
		$('#img_url_to_copy').val("");
		$('#project_form').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});	
		$("#project_form .tableflat").iCheck('uncheck');
		$('#save_project_info').html('Save');
	}
	
	// on select clear button 
	$('#clear_button').click(function(){
		clear_form();
	});
	
	
	delete_project = function delete_project(project_id){
		if (confirm("Do you want to delete the record? ") == true) {
			$.ajax({
				url: url,
				type:'POST',
				async:false,
				data: "q=delete_project&project_id="+project_id,
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
	
	edit_project = function edit_project(project_id){
		//alert(project_id);
		$.ajax({
			url: url,
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "get_project_details",
				project_id: project_id
			},
			success: function(data){
				if(!jQuery.isEmptyObject(data.records)){
					$.each(data.records, function(i,data){ 
						clear_form();					
						$('#project_id').val(data.project_code);
						$('#project_name').val(data.project_name);
						$('#contact_no').val(data.phone);
						$('#address').val(data.project_address);
						$('#note').val(data.note);
						
						if(data.logo == ""){
							$('#project_img').attr("src",project_url+'images/no_image.png');
						}else{
							$('#project_img').attr("src",project_url+data.logo);
						}
						$('#project_img').attr("width", "70%","height","70%");
						
						if(data.is_active==1)
							$('#is_active').iCheck('check'); 
						
						//change button value 
						$('#save_project_info').html('Update project');
						
						// to open submit post section
						if($.trim($('#toggle_form i').attr("class"))=="fa fa-chevron-down")
							$( "#toggle_form" ).trigger( "click" );

					});
					
				}
			}	
		});			
	}				
	
});


<!-- ------------------------------------------end --------------------------------------->
</script>