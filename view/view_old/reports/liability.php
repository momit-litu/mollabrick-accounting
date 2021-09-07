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
            <h2>Liability Report</h2>
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

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-6" style="text-align:right">Liability Head</label>
                                    <div class="form-group col-md-10 col-sm-10 col-xs-6">
                                        <input type="text" id="head_name" name="head_name" class="form-control col-lg-12"/>
                                        <input type="hidden" id="head_id" name="head_id"/>
                                    </div>
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
                                <button type="button" class="btn btn-primary" onclick="load_data(2)"><i class="fa fa-lg fa-search"></i>Summary Report</button>

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
                        q: "liability_head_info",
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

            //alert(report_type)

            var head_id = $("#head_id").val();
            var head_name = $("#head_name").val();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
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

            if(head_id!='' || head_name!='' || (start_date!='' && end_date!='')){
                $.ajax({
                    url: project_url+"controller/liabilityController.php",
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "liabilityReport",
                        head_name: head_name,
                        head_id: head_id,
                        start_date: start_date,
                        end_date: end_date,
                        project_id:project_id,
                        stakeholder_id:stakeholder_id
                    },
                    success: function(data){
                        var todate = "<?php echo date("Y-m-d"); ?>";
                        var user_name =  "<?php echo $user_name; ?>";
                        var html = "";
                        var total = 0;
						var total_rec_pay = 0;
						

                        var serach_areas= "";

                        if(head_name != '')  		serach_areas += "Head Name: "+head_name+"  <br>";
                        if(start_date != '')  		serach_areas += "From: "+start_date+"  <br>";
                        if(end_date != '')  		serach_areas += "To: "+end_date+"  <br>";

                        if(!jQuery.isEmptyObject(data.records)){

                            if(report_type==1){
                                html +='<table width="100%" border="1px" style="margin-top:10px;border-collapse:collapse">' +
                                    '<thead><tr><th style="text-align:left; padding-left: 5px">Head Name</th><th style="text-align:left; padding-left: 5px">Project Name</th>'
                                html += '<th style="text-align:center">Date</th><th style="text-align:left; padding-left: 5px">Purpose</th>' +
                                    '<th style="text-align:right; padding-right: 5px"> Paid Amount</th>' +
                                    '<th style="text-align:right; padding-right: 5px">Received Amount</th></tr>' +
                                    '</thead><tbody>';
									
									
								var rec_payable_tr ="<tr><td  colspan='5' style='text-align: left; font-weight:bold; height:40px'>Payable and Receivable</td></tr>";
                                 rec_payable_tr += '<tr><th style="text-align:left; padding-left: 5px">Head Name</th><th style="text-align:left; padding-left: 5px">Project Name</th><th style="text-align:center">Date</th><th style="text-align:left; padding-left: 5px">Purpose</th>' +
                                    '<th style="text-align:right; padding-right: 5px"> Payable Amount</th>' +
                                    '<th style="text-align:right; padding-right: 5px">Receivable Amount</th></tr>';
									
								paid = 0;
                                received = 0;
								payable = 0;
								receivable = 0;
                                $.each(data.records, function(i,data){
									if(data.types=='Payable' || data.types=='Receivable'){
										if(data.types=='Payable'){
											payable +=parseFloat(data.amount);
											tem_html="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td><td></td>"
										}else{
											receivable +=parseFloat(data.amount);
											tem_html="<td></td><td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td>"
										}
										rec_payable_tr += "<tr>";
										rec_payable_tr +="<td style='text-align:left; padding-left: 5px'>"+data.head_name+"</td>";
										rec_payable_tr +="<td style='text-align:left; padding-left: 5px'>"+data.project_name+"</td>";
										rec_payable_tr +="<td style='text-align:center'>"+data.date+"</td>";
										rec_payable_tr +="<td style='text-align:left'>"+data.note+"</td>";
										//rec_payable_tr +="<td style='text-align:left'>"+data.stakeholder+"</td>";
										rec_payable_tr +=tem_html;
										rec_payable_tr += '</tr>';
									}									 
									else{
										if(data.types=='Paid'){
											paid +=parseFloat(data.amount);
											tem_html="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td><td></td>"
										}else{
											received +=parseFloat(data.amount);
											tem_html="<td></td><td style='text-align:right; padding-right: 5px'>"+(parseFloat(data.amount)).toFixed(2)+"</td>"
										}
										html += "<tr>";
										html +="<td style='text-align:left; padding-left: 5px'>"+data.head_name+"</td>";
										html +="<td style='text-align:left; padding-left: 5px'>"+data.project_name+"</td>";
										html +="<td style='text-align:center'>"+data.date+"</td>";
										html +="<td style='text-align:left'>"+data.note+"</td>";
										//html +="<td style='text-align:left'>"+data.stakeholder+"</td>";
										html +=tem_html;
										html += '</tr>';
									}
                                });
								

                                total = paid-received;
                                if(total>-1){
                                    total_html = "<tr><td colspan='4' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td style='text-align:right; padding-right: 5px'><b>"+total.toFixed(2)+"</b></td><td></td>"
                                }
                                else {
                                    total_html = "<tr><td colspan='4' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td></td><td style='text-align:right; padding-right: 5px'><b>"+Math.abs(total.toFixed(2))+"</b></td>"
                                }

							//	total_rec_pay = (parseFloat(payable)-parseFloat(receivable));
								
                           /*     if(total_rec_pay>-1){
                                    total_PR_html = "<tr><td colspan='4' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td style='text-align:right; padding-right: 5px'><b>"+total_rec_pay.toFixed(2)+"</b></td><td></td>"
                                }
                                else {
                                    total_PR_html = "<tr><td colspan='4' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td></td><td style='text-align:right; padding-right: 5px'><b>"+Math.abs(total_rec_pay.toFixed(2))+"</b></td>"
                                }
							*/	

                                html += "<tr>";
                                html +="<td colspan='4' style='text-align:right; padding-right: 5px'><b>Total</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+paid+"</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+received+"</b></td>";
                                html += '</tr>'+total_html+rec_payable_tr;
								html += "<tr>";
                                html +="<td colspan='4' style='text-align:right; padding-right: 5px'><b>Total</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+payable+"</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+receivable+"</b></td>";
                                html += '</tr>';
                                html +="</tbody></table>"
                            }
                            else {
                                html +='<table width="100%" border="1px" style="margin-top:10px;border-collapse:collapse"><thead>' +
                                    '<tr><th style="text-align:left; padding-left: 5px">Head Name</th>' +
                                    '<th style="text-align:right; padding-right: 5px">Paid Amount</th>'
                                html += '<th style="text-align:right; padding-right: 5px">Received Amount</th></tr></thead><tbody>';

                                var tem_record = {};
                                $.each(data.records, function(i,data){
                                    if ((data.head_name) in tem_record ){
                                        if(data.types=='Paid'){
                                            tem_record[data.head_name]['paid_amount']= parseFloat(tem_record[data.head_name]['paid_amount'])+parseFloat(data.amount)

                                        }else  if(data.types=='Received'){
                                            tem_record[data.head_name]['received_amount']= parseFloat(tem_record[data.head_name]['received_amount'])+parseFloat(data.amount)
                                        }
                                    } else {
										 if(data.types=='Paid' || data.types=='Received' ){
											tem_record[data.head_name]={}
											tem_record[data.head_name]['head']=data.head_name
										 }
                                        if(data.types=='Paid'){
                                            tem_record[data.head_name]['paid_amount']= parseFloat(data.amount)
                                            tem_record[data.head_name]['received_amount'] = 0;
                                        }else  if(data.types=='Received'){
                                            tem_record[data.head_name]['received_amount']= parseFloat(data.amount)
                                            tem_record[data.head_name]['paid_amount']= 0;
                                        }
                                    }
                                });
								//console.log(tem_record);
								
								var tem_record_PR = {};
                                $.each(data.records, function(i,data){
                                    if ((data.head_name) in tem_record_PR ){
                                        if(data.types=='Payable'){
                                            tem_record_PR[data.head_name]['payable_amount']= parseFloat(tem_record_PR[data.head_name]['payable_amount'])+parseFloat(data.amount)

                                        }else if(data.types=='Receivable'){
                                            tem_record_PR[data.head_name]['receivable_amount']= parseFloat(tem_record_PR[data.head_name]['receivable_amount'])+parseFloat(data.amount)
                                        }
                                    } else {
										if(data.types=='Payable' || data.types=='Receivable' ){
											tem_record_PR[data.head_name]={}
											tem_record_PR[data.head_name]['head']=data.head_name
										 }
                                        if(data.types=='Payable'){
                                            tem_record_PR[data.head_name]['paid_amount']= parseFloat(data.amount)
                                            tem_record_PR[data.head_name]['received_amount'] = 0;
                                        }else if(data.types=='Receivable'){
                                            tem_record_PR[data.head_name]['received_amount']= parseFloat(data.amount)
                                            tem_record_PR[data.head_name]['paid_amount']= 0;
                                        }
                                    }
                                });
								

                                paid = 0;
                                received = 0;
                                total = 0;
								
								payable = 0;
                                receivable = 0;
                                PR_total = 0;

                                //console.log(tem_record)
                                $.each(tem_record, function (name, amount) {
                                    //console.log(amount)
                                    if(amount['paid_amount']>0 && amount['received_amount']>0 ){
                                        paid +=parseFloat(amount['paid_amount']);
                                        received +=parseFloat(amount['received_amount']);
                                        tem_html="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(amount['paid_amount'])).toFixed(2)+"</td><td>"+(parseFloat(amount['received_amount'])).toFixed(2)+"</td>"
                                    }else if(amount['paid_amount']>0){
                                        paid +=parseFloat(amount['paid_amount']);
                                        tem_html="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(amount['paid_amount'])).toFixed(2)+"</td><td></td>"
                                    }
                                    else if(amount['received_amount']>0){
                                        received +=parseFloat(amount['received_amount']);
                                        tem_html="<td></td><td style='text-align:right; padding-right: 5px'>"+(parseFloat(amount['received_amount'])).toFixed(2)+"</td>"
                                    }

                                    html += "<tr>";
                                    html +="<td style='text-align:left; padding-left: 5px'>"+amount['head']+"</td>";
                                    html +=tem_html;
                                    html += '</tr>';
                                    total+= parseFloat(amount['amount']);
                                });
								
								html += "<tr>";
                                html +="<td colspan='1' style='text-align:right; padding-right: 5px'><b>Total</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+paid+"</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+received+"</b></td>";
                                html += '</tr>';
								
								
                                total = paid-received;
                                if(total>-1){
                                    total_html = "<tr><td colspan='1' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td style='text-align:right; padding-right: 5px'><b>"+total.toFixed(2)+"</b></td><td></td>"
                                }
                                else {
                                    total_html = "<tr><td colspan='1' style='text-align:right; padding-right: 5px'><b>Balance</b>" +
                                        "</td><td></td><td style='text-align:right; padding-right: 5px'><b>"+Math.abs(total.toFixed(2))+"</b></td>"
                                }								
                                html +=total_html;
								html += "<tr><td  colspan='3' style='text-align: left; font-weight:bold; height:40px'>Payable and Receivable</td></tr>";
								html +='<tr><th style="text-align:left; padding-left: 5px">Head Name</th>' +
                                    '<th style="text-align:right; padding-right: 5px">Payable Amount</th>'
                                html += '<th style="text-align:right; padding-right: 5px">Receivable Amount</th></tr>';

								//console.log(tem_record_PR);
								$.each(tem_record_PR, function (name, amount) {
                                    //console.log(amount)
                                    if(amount['paid_amount']>0 && amount['received_amount']>0 ){
                                        payable +=parseFloat(amount['paid_amount']);
                                        receivable +=parseFloat(amount['received_amount']);
                                        tem_html1="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(amount['paid_amount'])).toFixed(2)+"</td><td>"+(parseFloat(amount['received_amount'])).toFixed(2)+"</td>"
                                    }else if(amount['paid_amount']>0){
                                        payable +=parseFloat(amount['paid_amount']);
                                        tem_html1="<td style='text-align:right; padding-right: 5px'>"+(parseFloat(amount['paid_amount'])).toFixed(2)+"</td><td></td>"
                                    }
                                    else if(amount['received_amount']>0){
                                        receivable +=parseFloat(amount['received_amount']);
                                        tem_html1="<td></td><td style='text-align:right; padding-right: 5px'>"+(parseFloat(amount['received_amount'])).toFixed(2)+"</td>"
                                    }

                                    html += "<tr>";
                                    html +="<td style='text-align:left; padding-left: 5px'>"+amount['head']+"</td>";
                                    html +=tem_html1;
                                    html += '</tr>';
                                    //total+= parseFloat(amount['amount']);
                                });
								
								html += "<tr>";
                                html +="<td colspan='1' style='text-align:right; padding-right: 5px'><b>Total</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+payable+"</b></td>";
                                html +="<td style='text-align:right; padding-right: 5px'><b>"+receivable+"</b></td>";
                                html += '</tr>';
								
								
                                html +="</tbody></table>"
                            }

                        }
                        else{
                            html += "<table width='100%' border='1px' style='margin-top:10px;border-collapse:collapse'><tr><td><h4 style='text-align:center'>There is no data.</h4></td></tr></table>";
                        }

                        html += "</div>";
                        //alert(project_id)


                        html ='<div style="text-align:center;width:1050px;"><div style="text-align:left;">' +
                            '<input name="print" type="button" value="Print" id="printBTN" onClick="printpage();" /></div>' +
                            '<h2 style="text-align:center" id="report_head">Liability Report</h2><table width="100%">' +
                            '<tr><th width="60%" style="text-align:left"><small>'+serach_areas+'</small></th>' +
                            '<th width="40%"  style="text-align:right"><small>Printed By: '+user_name+', Date:'+todate+'</small></th></tr></table>'+html;



                        WinId = window.open("", "Expense VS Income Report","width=1100,height=800,left=50,toolbar=no,menubar=YES,status=YES,resizable=YES,location=no,directories=no, scrollbars=YES");
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