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
	$projects = $dbClass->getResultList("select project_code, project_name from project_infos where status=1 and account_no=".$_SESSION['account_no']);
    ?>

    <div class="x_panel">
        <div class="x_title">
            <h2>Summary Report</h2>
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
									<label class="control-label col-md-2 col-sm-2 col-xs-12">Project Name<span class="required">*</span></label>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<select  id="project_id" name="project_id" required class="form-control col-lg-12" > 
										<?php 
											foreach($projects as $project){ 
												echo "<option value='".$project['project_code']."'>".$project['project_name']."</option>";
											}
										?>
										</select>
									</div>
								</div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-6" style="text-align:right">Head</label>
                                    <div class="form-group col-md-10 col-sm-10 col-xs-6">
                                        <input type="text" id="head_name" name="head_name" class="form-control col-lg-12"/>
                                        <input type="hidden" id="head_id" name="head_id"/>
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
                                <button type="button" class="btn btn-primary" onclick="load_data(1)"><i class="fa fa-lg fa-search"></i> Report</button>

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
			showDropdowns: true,
			locale: {
				  format: 'YYYY-MM-DD',
				  separator: " - ",
			}
		});
		$('#end_date').val("");

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
                        q: "income_expense_head_info",
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

            //alert(report_type)

            var head_id = $("#head_id").val();
            var head_name = $("#head_name").val();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var project_id = $("#project_id").val();
            var project_name = $("#project_name").val();

            if (end_date==''){
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();
                var date = yyyy+'-'+mm + '-' + dd;

                $("#end_date").val(date);
                end_date = $("#end_date").val();
            }

            if(head_id!='' || head_name!='' || project_id!='' || (start_date!='' && end_date!='')){
                $.ajax({
                    url: project_url+"controller/expensesIncomeController.php",
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "summaryReport",
                        head_name: head_name,
                        head_id: head_id,
                        start_date: start_date,
                        end_date: end_date,
                        project_id:project_id
                    },
                    success: function(data){
                        var todate = "<?php echo date("Y-m-d"); ?>";
                        var user_name =  "<?php echo $user_name; ?>";
                        var html = "";
                        var income_total = 0;
                        var expense_total = 0;
                        var total = 0;

                        var serach_areas= "";

                        if(head_name != '')  		serach_areas += "Head Name: "+head_name+"  <br>";
                        if(start_date != '')  		serach_areas += "From: "+start_date;
                        if(end_date != '')  		serach_areas += " To: "+end_date+"  <br>";

                        html = '<table style="border: none; width: 900px; text-align: center">\n'
                        if(!jQuery.isEmptyObject(data)){
                            typeVsAmount = {}
                            $.each(data, function (key, datas) {
                                html+='<tr style="text-align: center; margin-bottom: 20px">\n'
                                console.log(data)
                                tem_title=''
                                tem_amount = 0
                                $.each(datas, function (i, values) {
                                    if(tem_amount!=0 && (values['head_type']== 'Income' || values['head_type']== 'Deposit' || values['head_type']== 'Liability Received')){
                                        tem_amount=values['amount']-tem_amount;
                                        if(tem_amount>0){
                                            tem_title = values['head_type'];
                                        }

                                    }else if(tem_amount!=0){
                                        tem_amount=tem_amount-values['amount'];
                                        if(tem_amount<0){
                                            tem_title = values['head_type'];
                                        }
                                    }
                                    else {
                                        tem_title = values['head_type'];
                                        tem_amount = values['amount'];
                                    }

                                    typeVsAmount[values['head_type']]=values['amount'];

                                    html+='<td width="300px" style="text-align: center; margin: 50px; border:1px; background-color:#F5F5F5  ">\n' +
                                        '     <table>\n' +
                                        '        <thead ><tr><h3 style="margin-top: 30px">'+values['head_type']+' </h3></tr></thead>\n' +
                                        '        <tbody>\n' +
                                        '            <tr style="margin-bottom: 20px"><h2>'+values['amount']+' </h2></tr></tbody>\n' +
                                        '     </table>\n' +
                                        '   </td>'
                                    console.log(values)
                                })

                               /* if(tem_title!=''){
                                    html+='<td width="300px" style="text-align: center; margin: 30px; background-color:#F5F5F5  "">\n' +
                                        '     <table>\n' +
                                        '        <thead><tr><h3 style="margin-top: 30px"> Balance</h3></tr></thead>\n' +
                                        '        <tbody>\n' +
                                        '            <tr style="margin-bottom: 20px"><h2 style="margin-bottom: 5px">'+Math.abs(tem_amount)+' </h2><small> '+tem_title+'</small></tr></tbody>\n' +
                                        '     </table>\n' +
                                        '   </td>' +
                                        '</tr>'
                                }*/

                            })
                            console.log(typeVsAmount)
                            balance = 0;
                            $.each(typeVsAmount, function (key, value) {
                                if(key == 'Income' || key == 'Deposit' || key == 'Liability Received' ) balance+=parseFloat(value);
                                if(key == 'Expense' || key == 'Withdraw' || key == 'Liability Paid') balance-=parseFloat(value);
                            })

                            //balance = parseFloat(typeVsAmount['Income']) + parseFloat(typeVsAmount['Deposit']) +parseFloat(typeVsAmount['Liability Received']) -parseFloat(typeVsAmount['Expense'])- parseFloat(typeVsAmount['Withdraw'])- parseFloat(typeVsAmount['Liability Paid'])

                            html+='</table></div>'

                            if(project_id!=''){
                                html ='<div style="text-align:center;width:850px;"><div style="text-align:left;">' +
                                    '<input name="print" type="button" value="Print" id="printBTN" onClick="printpage();" /></div>' +
                                    '<h2 style="text-align:center" id="report_head">Summary Report</h2><table width="100%">' +
                                    '<tr><th width="60%" style="text-align:left"><small>'+serach_areas+'</small></th>' +
                                    '<th width="40%"  style="text-align:right"><small>Printed By: '+user_name+', Date:'+todate+'</small></th></tr></table>'+html;

                            }else {
                                html ='<div style="text-align:center;width:850px;"><div style="text-align:left;">' +
                                    '<input name="print" type="button" value="Print" id="printBTN" onClick="printpage();" /></div>' +
                                    '<h2 style="text-align:center" id="report_head">Summary Report</h2><table width="100%">' +
                                    '<tr><th width="60%" style="text-align:left"><small>'+serach_areas+'</small></th>' +
                                    '<th width="40%"  style="text-align:right"><small>Printed By: '+user_name+', Date:'+todate+'</small></th></tr></table>'+html;

                            }



                        }
                        else{
                            html += "<table width='100%' border='1px' style='margin-top:10px;border-collapse:collapse'><tr><td><h4 style='text-align:center'>There is no data.</h4></td></tr></table>";
                        }

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