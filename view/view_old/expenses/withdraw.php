<?php
session_start();
include '../../includes/static_text.php';
include("../../dbConnect.php");
include("../../dbClass.php");
$dbClass = new dbClass;
$user_type = $_SESSION['user_type'];

if(!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") header("Location:".$activity_url."../view/login.php");
else if($dbClass->getUserGroupPermission(105) != 1){
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
            <h2>Withdraw List</h2>
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
            <!-- Adnach search end -->

            <div class="dataTables_length">
                <label>Show
                    <select size="1" style="width: 56px;padding: 6px;" id="expense_Table_length" name="expense_Table_length" aria-controls="expense_Table">
                        <option value="50" selected="selected">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                    </select>
                    Post
                </label>
            </div>
            <div class="dataTables_filter" id="expense_Table_filter">
                <div class="input-group">
                    <input class="form-control" id="search_expense_field" style="" type="text">
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-primary has-spinner" id="search_expense_button">
                     <span class="spinner"><i class="fa fa-spinner fa-spin fa-fw "></i></span>
                     <i class="fa  fa-search "></i>
                    </button>
                </span>
                </div>
            </div>
            <div style="height:250px; width:100%; overflow-y:scroll">
                <table id="expense_Table" name="table_records" class="table table-bordered  responsive-utilities jambo_table table-striped  table-scroll ">
                    <thead>
                    <tr class="headings">
                        <th class="column-title" width="15%">Head Name</th>
                        <th class="column-title" width="15%">Project Name</th>
                        <th class="column-title" width="10%">Date</th>
                        <th class="column-title" width="">Note</th>
                        <th class="column-title" width="10%">Amount</th>
                        <th class="column-title" width="15%">Stakeholder</th>
                        <th class="column-title no-link last" width="100"><span class="nobr"></span></th>
                    </tr>
                    </thead>
                    <tbody id="expense_table_body" class="scrollable">

                    </tbody>
                </table>
            </div>
            <div id="expense_Table_div">
                <div class="dataTables_info" id="expense_Table_info">Showing <span id="from_to_limit"></span> of  <span id="total_record"></span> entries</div>
                <div class="dataTables_paginate paging_full_numbers" id="expense_Table_paginate">
                </div>
            </div>
        </div>
    </div>
    <?php if($dbClass->getUserGroupPermission(102) == 1){ ?>
        <div class="x_panel expense_entry_cl">
            <div class="x_title">
                <h2>Withdraw Entry</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link" id="toggle_form"><i class="fa fa-chevron-down"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" id="iniial_collapse">
                <br />
                <form id="expense_form" name="expense_form" enctype="multipart/form-data" class="form-horizontal form-label-left">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <input type="hidden" id="head_id" name="head_id" value="2"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-6">Amount<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <input type="text" id="amount" name="amount"  class="form-control col-lg-12"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-6">Date</label>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <input type="text" id="date" name="date" class="form-control col-lg-12 date-picker"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-6">Stakeholder</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="stakeholder" name="stakeholder"  class="form-control col-lg-12"/>
                                <input type="hidden" id="stakeholder_id" name="stakeholder_id"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-6">Project Name</label>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <input type="text" id="project_name" name="project_name"  class="form-control col-lg-12"/>
                                <input type="hidden" id="project_id" name="project_id"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-6">Document</label>
                            <div class="col-md-6 col-sm-6 col-xs-12" id="first_section">
                                <input name="attached_document" id="attached_document" class="form-control input-sm col-md-6 col-xs-12"  type="file" />
                            </div>
                            <input type="text" class="tags form-control col-lg-12 hide" name="uploded_files" id="uploded_files" value="" />
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-6">Details</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <textarea rows="2" cols="100" id="details" name="details" class="form-control col-lg-12"></textarea>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-6"></label>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <input type="hidden" id="expense_id" name="expense_id" />
                                <button type="submit" id="save_expense_info" class="btn btn-success">Save</button>
                                <button type="button" id="clear_button" class="btn btn-primary">Clear</button>
                            </div>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div id="form_submit_error" class="text-center" style="display:none"></div>
                            </div>
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

        $("#stakeholder").autocomplete({
            search: function() {
            },
            source: function(request, response) {
                $.ajax({
                    url: project_url+'controller/autosuggests.php',
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "stakeholder_name",
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                var head_id = ui.item.id;
                $(this).next().val(head_id);
            }
        });

        $("#project_name").autocomplete({
            search: function() {
            },
            source: function(request, response) {
                $.ajax({
                    url: project_url+'controller/autosuggests.php',
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "project_name",
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                var head_id = ui.item.id;
                $(this).next().val(head_id);
            }
        });

        $('.date-picker').daterangepicker({
            singleDatePicker: true,
            calender_style: "picker_3",
            locale: {
                format: 'YYYY-MM-DD',
                separator: " - ",
            }
        });

        //auto suggest head name with parrent name

    });


    $(document).ready(function (){
        // initialize page no to "1" for paging
        var current_page_no=1;

        $('.adv_cl').hide();

        load_data = function load_data(search_txt){
            $("#search_expense_button").toggleClass('active');
            var expense_Table_length =parseInt($('#expense_Table_length').val());

            var expense_active_status = $("input[name=is_active_status]:checked").val();

            $.ajax({
                url: project_url+"controller/depositWithdrawController.php",
                dataType: "json",
                type: "post",
                async:false,
                data: {
                    q: "withdraw_grid_data",
                    search_txt: search_txt,
                    expense_active_status: expense_active_status,
                    limit:expense_Table_length,
                    page_no:current_page_no
                },
                success: function(data){
                    var todate = "<?php echo date("Y-m-d"); ?>";
                    var user_name =  "<?php echo $user_name; ?>";
                    var html = "";
                    if($.trim(search_txt) == "Print"){
                        var serach_areas= "";
                        if(expense_active_status == 1)  serach_areas += "Active <br>";
                        if(expense_active_status == 0)  serach_areas += "In-Active <br>";
                        /*<input name="print" type="button" value="Print" id="printBTN" onClick="printpage();" />*/

                        html +='<div width="100%"  style="text-align:center"><img src="'+employee_import_url+'/images/logo.png" width="80"/></div><h2 style="text-align:center">Hotel Management System</h2><h4 style="text-align:center">Customer Information Report</h4><table width="100%"><tr><th width="60%" style="text-align:left"><small>'+serach_areas+'</small></th><th width="40%"  style="text-align:right"><small>Printed By: '+user_name+', Date:'+todate+'</small></th></tr></table>';

                        if(!jQuery.isEmptyObject(data.records)){
                            html +='<table width="100%" border="1px" style="margin-top:10px;border-collapse:collapse"><thead><tr><th style="text-align:center">Room Name</th><th style="text-align:center">Room Type</th><th style="text-align:center">Ac</th><th style="text-align:center">Availability</th><th style="text-align:center">Ret Per Night</th></tr></thead><tbody>';
                            $.each(data.records, function(i,data){
                                html += "<tr>";
                                html +="<td style='text-align:left'>"+data.expense_name+"</td>";
                                html +="<td style='text-align:left'>"+data.expense_type_status+"</td>";
                                html +="<td style='text-align:left'>"+data.ac_status+"</td>";
                                html +="<td style='text-align:left'>"+data.availability_status+"</td>";
                                html +="<td style='text-align:right'>"+data.rate_per_night+"</td>";
                                html += '</tr>';
                            });
                            html +="</tbody></table>"
                        }
                        else{
                            html += "<table width='100%' border='1px' style='margin-top:10px;border-collapse:collapse'><tr><td><h4 style='text-align:center'>There is no data.</h4></td></tr></table>";
                        }
                        WinId = window.open("", "Expense Report","width=950,height=800,left=50,toolbar=no,menubar=YES,status=YES,resizable=YES,location=no,directories=no, scrollbars=YES");
                        WinId.document.open();
                        WinId.document.write(html);
                        WinId.document.close();
                    }
                    else{
                        if(data.entry_status==0){
                            $('.expense_entry_cl').hide();
                        }
                        // for  showing grid's no of records from total no of records
                        show_record_no(current_page_no, expense_Table_length, data.total_records )

                        var total_pages = data.total_pages;
                        var records_array = data.records;
                        $('#expense_Table tbody tr').remove();
                        //$("#search_expense_button").toggleClass('active');
                        if(!jQuery.isEmptyObject(records_array)){
                            // create and set grid table row
                            var colums_array=["id*identifier*hidden","headName","project_name","date","note","amount","stakeholder"];
                            // first element is for view , edit condition, delete condition
                            // "all" will show /"no" will show nothing
                            var condition_array=["","","update_status", "1","delete_status","1"];
                            // create_set_grid_table_row(records_array,colums_array,int_fields_array, condition_arraymodule_name,table/grid id, is_checkbox to select tr );
                            // cauton: not posssible to use multiple grid in same page
                            create_set_grid_table_row(records_array,colums_array,condition_array,"expense","expense_Table", 0);
                            // show the showing no of records and paging for records
                            $('#expense_Table_div').show();
                            // code for dynamic pagination
                            paging(total_pages, current_page_no, "expense_Table" );
                        }
                        // if the table has no records / no matching records
                        else{
                            grid_has_no_result( "expense_Table",5);
                        }
                        $("#search_expense_button").toggleClass('active');
                    }
                }
            });
        }

        // load desire page on clik specific page no
        load_page = function load_page(page_no){
            if(page_no != 0){
                // every time current_page_no need to change if the user change page
                current_page_no=page_no;
                var search_txt = $("#search_expense_field").val();
                load_data(search_txt)
            }
        }
        // function after click search button
        $('#search_expense_button').click(function(){
            var search_txt = $("#search_expense_field").val();
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
        $('#search_expense_field').keypress(function(event){
            var search_txt = $("#search_expense_field").val();
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


    /*
    develped by @momit
    =>form submition for add/edit
    =>clear form
    =>load data to edit
    =>delete record
    =>view
    */
    $(document).ready(function () {
        var url = project_url+"controller/depositWithdrawController.php";

        // save and update for public post/notice
        $('#save_expense_info').click(function(event){
            event.preventDefault();
            var formData = new FormData($('#expense_form')[0]);
            formData.append("q","insert_or_update");
            if($.trim($('#amount').val()) == ""){
                success_or_error_msg('#form_submit_error','danger',"Please Insert Amount","#amount");
            }
            else{
                //	$('#save_expense_info').attr('disabled','disabled');

                $.ajax({
                    url: url,
                    type:'POST',
                    data:formData,
                    async:false,
                    cache:false,
                    contentType:false,processData:false,
                    success: function(data){
                        $('#save_expense_info').removeAttr('disabled','disabled');

                        if($.isNumeric(data)==true && data>0){
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
            $('#expense_id').val('');
            $("#expense_form").trigger('reset');

            $('#uploded_files_tagsinput').remove();
            $('#save_expense_info').html('Save');
        }

        // on select clear button
        $('#clear_button').click(function(){
            clear_form();
        });


        delete_expense = function delete_expense(expense_id){
            if (confirm("Do you want to delete the record? ") == true) {
                $.ajax({
                    url: url,
                    type:'POST',
                    async:false,
                    data: "q=delete_expense&expense_id="+expense_id,
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


        edit_expense = function edit_expense(expense_id){
            $.ajax({
                url: url,
                dataType: "json",
                type: "post",
                async:false,
                data: {
                    q: "get_deposit_details",
                    expense_id: expense_id
                },
                success: function(data){
                    if(!jQuery.isEmptyObject(data.records)){
                        $.each(data.records, function(i,data){
                            clear_form();

                            amount=data.amount.split(",").join("");

                            $('#expense_id').val(data.id);
                            $('#head_id').val(data.head_id);
                            $('#amount').val(parseFloat(amount));
                            $('#details').val(data.note);
                            $('#date').val(data.date);
                            $('#stakeholder').val(data.stakeholder);
                            $('#stakeholder_id').val(data.stakeholder_id);
                            $('#project_id').val(data.project_id);
                            $('#project_name').val(data.project_name);

                            //change button value
                            $('#save_expense_info').html('Update');

                            // to open submit post section
                            if($.trim($('#toggle_form i').attr("class"))=="fa fa-chevron-down")
                                $( "#toggle_form" ).trigger( "click" );

                            var attach_str = data.reference_doc;
                            var attach = attach_str.split("/");

                            if($.trim(data.reference_doc) != ""){
                                $('#uploded_files').val(attach[2]);
                                $('#uploded_files').tagsInput({
                                    width: 'auto',
                                    onRemoveTag:function(fie_name){
                                        if (confirm("Do you want to delete the attached file"+ fie_name +"? ") == true) {
                                            $.ajax({
                                                url: url,
                                                type:'POST',
                                                async:false,
                                                data: "q=delete_attached_file&expense_id="+expense_id+"&file_name="+fie_name,
                                                success: function(data){
                                                    if($.trim(data) == 1){
                                                        success_or_error_msg('#form_submit_error',"success",delete_msg_ln);
                                                    }
                                                    else{
                                                        success_or_error_msg('#form_submit_error',"danger",not_delete_msg_ln);
                                                        return false;
                                                    }
                                                }
                                            });
                                        }
                                    }
                                });
                                $('#uploded_files_tag').css("display","none");
                                $('#uploded_files_tagsinput').remove();
                            }

                        });

                    }
                }
            });
        }

    });


</script>