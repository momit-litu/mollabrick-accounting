<?php
session_start();
include '../../includes/static_text.php';
include("../../dbConnect.php");
include("../../dbClass.php");
$dbClass = new dbClass;
$user_type = $_SESSION['user_type'];
if(!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") header("Location:".$activity_url."../view/login.php");
else if($dbClass->getUserGroupPermission(14) != 1 ){
?> 
	<div class="x_panel">
		<div class="alert alert-danger" align="center">You Don't Have permission of this Page.</div>
	</div>
	<?php 
}
else{
?>


<div class="x_panel">
    <div class="x_title">
        <h2>Actions</h2>
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
                <select size="1" style="width: 56px;padding: 6px;" id="action_Table_length" name="action_Table_length" aria-controls="action_Table">
                    <option value="50" selected="selected">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                 </select> 
                 Post
             </label>
         </div>
         <div class="dataTables_filter" id="action_Table_filter">         
			<div class="input-group">
                <input class="form-control" id="search_action_field" style="" type="text">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-primary has-spinner" id="search_action_button">
                     <span class="spinner"><i class="fa fa-spinner fa-spin fa-fw "></i></span>
                     <i class="fa  fa-search "></i>
                    </button> 
                </span>
            </div>
       </div>
       <div style="height:250px; width:100%; overflow-y:scroll">
        <table id="action_Table" name="table_records" class="table table-bordered  responsive-utilities jambo_table table-striped  table-scroll ">
            <thead >
                <tr class="headings">
                    <th class="column-title" width="50%">Name</th>
                    <th class="column-title" width="20%">Status</th> 
					<th class="column-title no-link last" width="10%"><span class="nobr"></span></th>	
                </tr>
            </thead>
            <tbody id="action_table_body" class="scrollable">              
                
            </tbody>
        </table>
        </div>
        <div id="action_Table_div">
            <div class="dataTables_info" id="action_Table_info">Showing <span id="from_to_limit"></span> of  <span id="total_record"></span> entries</div>
            <div class="dataTables_paginate paging_full_numbers" id="action_Table_paginate">
            </div> 
        </div>  
    </div>
</div>

<div class="x_panel activity_entry_cl">
    <div class="x_title">
        <h2>Action Entry</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
				<a class="collapse-link" id="toggle_form"><i class="fa fa-chevron-down"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" id="iniial_collapse">		
		<form method="POST"  id="action_form" name="action_form" enctype="multipart/form-data" class="form-horizontal form-label-left">   
			<div class="row">
				<div class="col-md-12">	
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Name</label>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<input type="text" id="action_name" name="action_name" class="form-control col-lg-12"/>
						</div>
					</div>				
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6" for="name">Is Active</label>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<input type="checkbox" id="is_active" name="is_active" class="form-control col-lg-12"/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-6"></label>
						<div class="col-md-3 col-sm-3 col-xs-12">
							 <input type="hidden" id="action_id" name="action_id" />    
							 <button  type="submit" id="save_action" class="btn btn-success">Save</button>                    
							 <button type="button" id="clear_button"  class="btn btn-primary">Clear</button>                         
						</div>
						 <div class="col-md-7 col-sm-7 col-xs-12">
							<div id="form_submit_error" class="text-center" style="display:none"></div>
						 </div>
					</div>
				</div>
			</div>
		</form>  
    </div>
</div>
<?php } ?>
<script src="js/customTable.js"></script> 
<script>
//------------------------------------- general & UI  --------------------------------------
/*
develped by @momit
=>load grid with paging
=>search records
*/
$(document).ready(function () {	
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
	$('#action_form').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
	});	
	$('#is_active').iCheck('check');
});
<!-- ------------------------------------------end --------------------------------------->
$(document).ready(function () {	
	var current_page_no=1;	
	load_actions = function load_actions(search_txt){
		$("#search_action_button").toggleClass('active');
		var action_Table_length = parseInt($('#action_Table_length').val());		
		$.ajax({
			url: project_url+"controller/actionController.php",
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "grid_data",
				search_txt: search_txt,
				limit:action_Table_length,
				page_no:current_page_no
			},
			success: function(data) {
				// for  showing grid's no of records from total no of records 
				show_record_no(current_page_no, action_Table_length, data.total_records )
				
				var total_pages = data.total_pages;	
				var records_array = data.records;
				$('#action_Table tbody tr').remove();
				$("#search_action_button").toggleClass('active');
				if(!jQuery.isEmptyObject(records_array)){
					// create and set grid table row
					var colums_array=["id*identifier*hidden","activity_name","status_text"];
					// first element is for view , edit condition, delete condition
					// "all" will show /"no" will show nothing
					var condition_array=["","","all", "","",""];
					// create_set_grid_table_row(records_array,colums_array,int_fields_array, condition_arraymodule_name,table/grid id, is_checkbox to select tr );
					// cauton: not posssible to use multiple grid in same page					
					create_set_grid_table_row(records_array,colums_array,condition_array,"action","action_Table", 0);
					// show the showing no of records and paging for records 
					$('#action_Table_div').show();					
					// code for dynamic pagination 				
					paging(total_pages, current_page_no, "action_Table" );					
				}
				// if the table has no records / no matching records 
				else{
					grid_has_no_result( "action_Table",8);
				}
			}
		});	
	}
	// load desire page on clik specific page no
	load_page = function load_page(page_no){
		if(page_no != 0){
			// every time current_page_no need to change if the user change page
			current_page_no=page_no;
			var search_txt = $("#search_action_field").val();
			load_actions(search_txt)
		}
	}	
	// function after click search button 
	$('#search_action_button').click(function(){
		var search_txt = $("#search_action_field").val();
		// every time current_page_no need to set to "1" if the user search from search bar
		current_page_no=1;
		load_actions(search_txt);		
	});
	//function after press "enter" to search	
	$('#search_action_field').keypress(function(event){
		var search_txt = $("#search_action_field").val();	
		if(event.keyCode == 13){
			// every time current_page_no need to set to "1" if the user search from search bar
			current_page_no=1;
			load_actions(search_txt)
		}
	})
	// load data initially on page load with paging
	load_actions("");
	//insert action
	$('#save_action').click(function(event){  
		event.preventDefault();
		var formData = new FormData($('#action_form')[0]);
		formData.append("q","insert_or_update");
		//validation 
		if($.trim($('#action_name').val()) == ""){
			success_or_error_msg('#form_submit_error','danger',not_input_insert_title_ln,"#action_name");			
		}
		else{
			$.ajax({
				url: project_url+"controller/actionController.php",
				type:'POST',
				data:formData,
				async:false,
				cache:false,
				contentType:false,processData:false,
				success: function(data){
					$('#save_action').removeAttr('disabled','disabled');
					if($.isNumeric(data)==true && data>0){
						success_or_error_msg('#form_submit_error',"success","Save Successfully");
						load_actions("");
						clear_form();
					}
				 }	
			});
			
		}	
	})
	//edit action
	edit_action = function edit_action(action_id){
		$.ajax({
			url: project_url+"controller/actionController.php",
			dataType: "json",
			type: "post",
			async:false,
			data: {
				q: "get_action_details",
				action_id: action_id
			},
			success: function(data){
				if(!jQuery.isEmptyObject(data.records)){
					$.each(data.records, function(i,data){
						//alert(data.status)
						var master_id = data.id;
						$('#action_name').val(data.activity_name);
						$('#action_id').val(data.id);
						if(data.status==0){
							$('#is_active').iCheck('check');
						}
						$('#save_action').html('Update');
						// to open submit post section
						if($.trim($('#toggle_form i').attr("class"))=="fa fa-chevron-down")
						$( "#toggle_form" ).trigger( "click" );	
					});				
				}
			}
		});	
	}
	
	clear_form = function clear_form(){			 
		$('#action_id').val('');
		$("#action_form").trigger('reset');
		$('#action_form').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});	
		$('#is_active').iCheck('check');
		$('#save_action').html('Save');
	}
	
	$('#clear_button').click(function(){
		clear_form();
	});
	
});





</script>