<?php
session_start();
include '../../includes/static_text.php';
include("../../dbConnect.php");
include("../../dbClass.php");
$dbClass = new dbClass;
$user_type = $_SESSION['user_type'];

if(!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") header("Location:".$activity_url."../view/login.php");
else if($dbClass->getUserGroupPermission(116) != 1){
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
            <h2>Deposit & Withdraw Report</h2>
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
            <div class="x_panel">
                <div class="x_content">
                    <form id="expense_form" name="expense_form" class="form-horizontal form-label-left">
                        <div class="row">
                            <div class="ln_solid"></div>
                            <div style="text-align:center">
                                <button type="button" class="btn btn-default" id="dailyBtn">Daily</button>
                                <button type="button" class="btn btn-default" id="monthlyBtn">This Month</button>
                                <button type="button" class="btn btn-default" id="yearlyBtn">This Year</button>
                            </div>

                        </div>
                        <hr><br>
                        <div class="row">
                            <div class="col-md-2"></div>

                            <div class="col-md-8">
                                <div class="form-group" style="text-align:center">
                                    <input type="radio" checked class="-blue report_type" name="report_type" style="margin: 7px" value="all"/>Deposit & Withdraw
                                    <input type="radio" class="-blue report_type" name="report_type" style="margin: 7px" value="deposit"/>Deposit
                                    <input type="radio" class="-blue report_type" name="report_type" style="margin: 7px" value="withdraw"/>Withdraw
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-6" style="text-align:right">Project</label>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-6">
                                        <input type="text" id="project_name" name="project_name" class="form-control col-lg-12"/>
                                        <input type="hidden" id="project_id" name="project_id"/>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-6">Stakeholder</label>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-6">
                                        <input type="text" id="stakeholder" name="stakeholder"  class="form-control col-lg-12"/>
                                        <input type="hidden" id="stakeholder_id" name="stakeholder_id"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-6" style="text-align:right">Start Date</label>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-6">
                                        <input type="text" id="start_date" name="start_date" class="form-control date-picker" value="2020-01-01"/>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-6">End Date</label>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-6">
                                        <input type="text" id="end_date" name="end_date" class="form-control date-picker"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>

                        <div class="row">
                            <div class="ln_solid"></div>
                            <div style="text-align:center">
                                <button type="button" class="btn btn-primary" onclick="load_data(1)"><i class="fa fa-lg fa-search"></i>Details Report</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Adnach search end -->

        </div>
    </div>

    <?php

}
?>
<script src="js/customTable.js"></script>
<script>



    $(document).ready(function () {
        var user_type = "<?php echo $user_type; ?>";



        $('.date-picker').daterangepicker({
            singleDatePicker: true,
            calender_style: "picker_3",
            locale: {
                format: 'YYYY-MM-DD',
                separator: " - ",
            }
        });
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

        $("#head_name").autocomplete({
            search: function() {
            },
            source: function(request, response) {
                $.ajax({
                    url: project_url+'controller/autosuggests.php',
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "expense_head_info",
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                var c_id = ui.item.id;
                $('#head_id').val(c_id);
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


        clear_data = function clear_data(){
            $('#start_date').val('');
            $('#MonthYearDiv').hide();
            $('#end_date').val('');
            $("#expense_form").trigger('reset');
        }

        clear_data();

        end_date= function end_date(){
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            var date = yyyy+'-'+mm + '-' + dd;

            $("#end_date").val(date);
        }

        load_data = function load_data(report_type){

            //alert($("input[type='radio'].report_type:checked").val())

            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var report_type = $("input[type='radio'].report_type:checked").val();
            var project_id =$("#project_id").val();
            var stakeholder_id = $("#stakeholder_id").val();

            if (end_date==''){
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();
                var date = yyyy+'-'+mm + '-' + dd;

                $("#end_date").val(date);
                end_date = $("#end_date").val();
            }

            if(start_date!='' && end_date!=''){
                $.ajax({
                    url: project_url+"controller/depositWithdrawController.php",
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "depositWithdrawReport",
                        start_date: start_date,
                        end_date: end_date,
                        report_type:report_type,
                        project_id:project_id,
                        stakeholder_id:stakeholder_id
                    },
                    success: function(data){
                        var todate = "<?php echo date("Y-m-d"); ?>";
                        var user_name =  "<?php echo $user_name; ?>";
                        var html = "";
                        var total = 0;

                        var serach_areas= "";

                        if(start_date != '')  		serach_areas += "From: "+start_date+"  <br>";
                        if(end_date != '')  		serach_areas += "To: "+end_date+"  <br>";

                        var project_name = ''
                        if(!jQuery.isEmptyObject(data.records)){
                            var deposit = 0;
                            var withdraw = 0;
                            if(project_id!=''){
                                colspan = 4
                                table_head = ''
                                title = 'Deposit Reports for '+$("#project_name").val()
                            }else {
                                colspan =5
                                table_head = '<th style="text-align:left; padding-left: 5px">Project Name</th>'
                                title ='Deposit Report'
                            }


                            if(report_type=='all'){
                                report_type_table_head = '<th style="text-align:right; padding-right: 5px">Deposit Amount</th>' +
                                    '<th style="text-align:right; padding-right: 5px">Withdraw Amount</th>'
                                colspan_right = 2;

                            }else if(report_type=="deposit"){
                                report_type_table_head = '<th style="text-align:right; padding-right: 5px">Deposit Amount</th>'
                                colspan_right = 1;

                            }else {
                                report_type_table_head = '<th style="text-align:right; padding-right: 5px">Withdraw Amount</th>'
                                colspan_right = 1;
                            }


                            html +='<table width="100%" border="1px" style="margin-top:10px;border-collapse:collapse"><thead><tr>'
                            html += '<th style="text-align:left; padding-left: 5px">Head</th>'+table_head +
                                '<th style="text-align:center">Date</th><th style="text-align:left">Purpose</th><th style="text-align:left">Stakeholder</th>'+report_type_table_head+'</tr></thead><tbody>';

                            $.each(data.records, function(i,data){

                                if(project_id!='') {
                                    var project_name_display = ''
                                }else {
                                    var project_name_display = "<td style='text-align:left; padding-left: 5px'>"+data.project_name+"</td>"
                                }

                                if(report_type=='all'){

                                    if(data.head_name=='Deposit'){
                                        deposit +=parseFloat(data.amount);
                                        tem_html="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td><td></td>"
                                    }else{
                                        withdraw +=parseFloat(data.amount);
                                        tem_html="<td></td><td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td>"
                                    }

                                }else if(report_type=="deposit"){
                                    deposit +=parseFloat(data.amount);
                                    tem_html="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td>"

                                }else {
                                    withdraw +=parseFloat(data.amount);
                                    tem_html="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td>"
                                }

                                    html += "<tr>";
                                html +="<td style='text-align:left; padding-left: 5px'>"+data.head_name+"</td>";
                                html +=project_name_display;
                                html +="<td style='text-align:center'>"+data.date+"</td><td style='text-align:left'>"+data.note+"</td><td style='text-align:left'>"+data.stakeholder+"</td>"+tem_html;
                                html += '</tr>';
                                total+= parseFloat(data.amount);
                                project_name = data.project_name;
                            });

                            if(report_type=='all') {
                                total = deposit - withdraw

                                if(total>-1){
                                    total_html = "<tr><td colspan='"+colspan+"' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td style='text-align:right; padding-right: 5px'><b>"+total.toFixed(2)+"</b></td><td></td>"
                                }
                                else {
                                    total_html = "<tr><td colspan='"+colspan+"' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td></td><td style='text-align:right; padding-right: 5px'><b>"+Math.abs(total.toFixed(2))+"</b></td>"
                                }


                                html += "<tr>";
                                html += "<td colspan='" + colspan + "' style='text-align:right; padding-right: 5px'><b>Total</b></td>";
                                html += "<td style='text-align:right; padding-right: 5px'><b>" + deposit.toFixed(2) + "</b></td>";
                                html += "<td style='text-align:right; padding-right: 5px'><b>" + withdraw.toFixed(2) + "</b></td>";
                                html += '</tr>'+total_html;
                                html += "</tbody></table>"
                            }else {
                                html += "<td colspan='" + colspan + "' style='text-align:right; padding-right: 5px'><b>Total</b></td>";
                                html += "<td style='text-align:right; padding-right: 5px'><b>" + (withdraw + deposit).toFixed(2) + "</b></td>";
                                html += '</tr>';
                                html += "</tbody></table>"
                            }

                        }
                        else{
                            html += "<table width='100%' border='1px' style='margin-top:10px;border-collapse:collapse'><tr><td><h4 style='text-align:center'>There is no data.</h4></td></tr></table>";
                        }

                        html += "</div>";
                        //alert(project_id)

                        html ='<div style="text-align:center;width:850px;"><div style="text-align:left;">' +
                            '<input name="print" type="button" value="Print" id="printBTN" onClick="printpage();" /></div>' +
                            '<h2 style="text-align:center" id="report_head">'+title+'</h2><table width="100%">' +
                            '<tr><th width="60%" style="text-align:left"><small>'+serach_areas+'</small></th>' +
                            '<th width="40%"  style="text-align:right"><small>Printed By: '+user_name+', Date:'+todate+'</small></th></tr></table>'+html;



                        WinId = window.open("", "Expense VS Income Report","width=900,height=800,left=50,toolbar=no,menubar=YES,status=YES,resizable=YES,location=no,directories=no, scrollbars=YES");
                        WinId.document.open();
                        WinId.document.write(html);
                        WinId.document.close();
                        //clear_data();
                    }
                });
            }
        }


        $('#dailyBtn').click(function(){
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();

            if(dd<10) {
                dd = '0'+dd
            }

            if(mm<10) {
                mm = '0'+mm
            }

            today = yyyy + '-' + mm + '-' + dd;

            $("#start_date").val(today);
            $("#end_date").val(today);
            //load_data();
        });


        //current date calculation
        var date = new Date();
        var year = date.getFullYear();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

        lmonth = ((lastDay.getMonth() + 1) < 10)?"0"+(lastDay.getMonth() + 1):(lastDay.getMonth()+1);
        var ldays = (lastDay.getFullYear() + '-' + (lmonth) + '-' + lastDay.getDate());

        if((firstDay.getDate()) < 10){
            var lday = "0"+(firstDay.getDate());
        }
        var fdays = (lastDay.getFullYear() + '-' + (lmonth) + '-' + lday);


        $('#monthlyBtn').click(function(){
            $("#start_date").val(fdays);
            $("#end_date").val(ldays);
            //load_data();
        });

        $('#yearlyBtn').click(function(){
            $("#start_date").val(year+"-01-01");
            $("#end_date").val(year+"-12-31");
            //load_data();
        });

    });

    end_date()

</script>