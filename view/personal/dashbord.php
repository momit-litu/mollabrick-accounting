<?php
session_start();
include '../../includes/static_text.php';
include("../../dbConnect.php");
include("../../dbClass.php");
$dbClass = new dbClass;

$user_name = $_SESSION['user_name'];
$user_type = $_SESSION['user_type'];
$stakeholders = $dbClass->getResultList("select id, name from stakeholders where status=1  and account_no=".$_SESSION['account_no']);
$projects = $dbClass->getResultList("select project_code, project_name from project_infos where status=1  and account_no=".$_SESSION['account_no']);


if(!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") header("Location:".$activity_url."../view/login.php");
else{
	$user_type_name = "Employee";
	?>
	<div class="x_panel">
        <div class="x_title">
            <h2>Dashboard</h2>
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
					 <div class="alert" style="background-color: #f4f4f4;" >
						<form id="expense_form " name="expense_form" class="form-horizontal  form-label-left">
							<div class="row col-md-3">
							
								<div class="btn-group" role="group" aria-label="First group">
									<button type="button" class="btn btn-default btn" id="dailyBtn">Today</button>
									<button type="button" class="btn btn-default btn" id="monthlyBtn"><?php echo date("F"); ?></button>
									<button type="button" class="btn btn-default btn" id="yearlyBtn"><?php echo date("Y"); ?></button>
								</div>
							

							</div>
							<div class="form-group col-md-2">
								<select  id="project_id" name="project_id"  class="form-control col-lg-12" > 
									<?php 
										foreach($projects as $project){ 
											echo "<option value='".$project['project_code']."'>".$project['project_name']."</option>";
										}
									?>
								</select>
							</div>
							<div class="form-group col-md-3">
								<label class="control-label col-md-2 col-sm-3 col-xs-3">From:</label>
								<div class="col-md-10 col-sm-9 col-xs-12">
									<input type="text" id="start_date" name="start_date" class="form-control date-picker" />
								</div>
							</div>
							<div class="form-group col-md-3">
								<label class="control-label col-md-2 col-sm-3 col-xs-3">Till:</label>
								<div class="col-md-10 col-sm-9 col-xs-12">
									<input type="text" id="end_date" name="end_date" class="form-control date-picker" />
								</div>
							</div>
							<div class="row col-md-1 text-right">
								<button type="button" class="btn btn-primary" onclick="load_data()"><i class="fa fa-lg fa-search"></i>Load Report</button>
							</div>
						</form>
						<div class="clearfix"></div>
					</div>
					
					<div id='dashboard_content'>
					</div>
				
					<!--
					<div class="row top_tiles">
						<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12 alert alert-danger">
							<div class="tile-stats  alert alert-danger">
								<div class="icon"><i class="fa fa-arrow-circle-right"></i>
								</div>
								<div class="count" id="total_exp_html" >0</div>

								<h3>Total Expense</h3>
							</div>
						</div>
						<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12   alert alert-success ">
							<div class="tile-stats alert alert-success  ">
								<div class="icon"><i class="fa fa-arrow-circle-left"></i>
								</div>
								<div class="count" id="total_inc_html" >0</div>

								<h3>Total Income</h3>
							</div>
						</div>
						<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12 alert alert-warning">
							<div class="tile-stats alert alert-warning">
								<div class="icon"><i class="fa fa-sort-amount-desc"></i>
								</div>
								<div class="count" id="total_pay_html" >0</div>

								<h3>Total Payable</h3>
							</div>
						</div>
						<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12 alert alert-info">
							<div class="tile-stats alert alert-info">
								<div class="icon"><i class="fa fa-dollar"></i>
								</div>
								<div class="count" id="total_rec_html" >0</div>

								<h3>Total Receivable</h3>
							</div>
						</div>
					</div>
				
					<div class="row top_tiles">
						<div class="col-md-3 col-xs-12 widget widget_tally_box">
							<div class="x_panel">
								<div class="x_title">
									<h4>Category wise Expense</h4>
									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									<div>
										<ul class="list-inline widget_tally">
											<li>
												<p>
													<span class="month" style="text-align:left">12 December 2014 </span>
													<span class="count" style="text-align:right">+12%</span>
												</p>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 col-xs-12 widget widget_tally_box">
							<div class="x_panel">
								<div class="x_title">
									<h4>Income List(Top 10)</h4>
									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									<div>
										<ul class="list-inline widget_tally">
											<li>
												<p>
													<span class="month" style="text-align:left">12 December 2014 </span>
													<span class="count" style="text-align:right">+12%</span>
												</p>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 col-xs-12 widget widget_tally_box">
							<div class="x_panel">
								<div class="x_title">
									<h4>Payable List(Top 10)</h4>
									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									<div>
										<ul class="list-inline widget_tally">
											<li>
												<p>
													<span class="month" style="text-align:left">12 December 2014 </span>
													<span class="count" style="text-align:right">+12%</span>
												</p>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 col-xs-12 widget widget_tally_box">
							<div class="x_panel">
								<div class="x_title">
									<h4>Receivable  List(Top 10)</h4>
									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									<div>
										<ul class="list-inline widget_tally">
											<li>
												<p>
													<span class="month" style="text-align:left">12 December 2014 </span>
													<span class="count" style="text-align:right">+12%</span>
												</p>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					-->
				</div>
            </div>
            <!-- Adnach search end -->

        </div>
    </div>

	<?php
} 
?>

<style type="text/css">
	@media print {    
		.no-print, .no-print * {
			display: none !important;
		}
	}
</style>

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


        end_date= function end_date(){
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            var date = yyyy+'-'+mm + '-' + dd;

            $("#end_date").val(date);
        }
        

        load_data = function load_data(){
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var project_id = $("#project_id").val();
            if (end_date==''){
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();
                var date = yyyy+'-'+mm + '-' + dd;

                $("#end_date").val(date);
                end_date = $("#end_date").val();
            }

            if(project_id!='' || (start_date!='' && end_date!='')){
                $.ajax({
                    url: project_url+"controller/expensesIncomeController.php",
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "summaryReport",
                        head_name: '',
                        head_id: '',
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

                        html = '<table style="border: none; width: 100%; text-align: center">\n'
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
								
                                    html+='<td width="300px" style="text-align: center; margin: 50px; border:1px; ">\n' +
										'<div class="animated flipInY col-lg-10 alert alert-info text-center"><div style="font-size:18px; color:red; font-weight:bold" >'+values['amount']+'</div><h3  >Total '+values['head_type']+'</h3></div></div>'+
                                        '   </td>'
                                    //console.log(values)
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
/*
                            if(project_id!=''){
                                html ='<div style="text-align:center;width:100%;"><div style="text-align:left;">' +
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

                            }*/



                        }
                        else{
                            html += "<table width='100%' border='1px' style='margin-top:10px;border-collapse:collapse'><tr><td><h4 style='text-align:center'>There is no data.</h4></td></tr></table>";
                        }

						$('#dashboard_content').html(html);
                        /*WinId = window.open("", "Expense VS Income Report","width=900,height=800,left=50,toolbar=no,menubar=YES,status=YES,resizable=YES,location=no,directories=no, scrollbars=YES");
                        WinId.document.open();
                        WinId.document.write(html);
                        WinId.document.close();*/
                        //clear_data();
                    }
                });
				
				
				
				/*$.ajax({
                    url: project_url+"controller/expensesIncomeController.php",
                    dataType: "json",
                    type: "post",
                    async:false,
                    data: {
                        q: "DashbordSummaryReport",
                        start_date: start_date,
                        end_date: end_date,
                        project_id:project_id
                    },
                    success: function(data){
						payable 	= ((parseFloat(data.lReceived)-parseFloat(data.lPaid))+parseFloat(data.Payable));
						receivable 	= parseFloat(data.Receivable);
						expense 	= parseFloat(data.Expense);
						income 		= data.Income;
						//alert(income)
						
						$('#total_exp_html').html(expense);
						$('#total_inc_html').html(income);
						$('#total_pay_html').html(payable);
						$('#total_rec_html').html(receivable);
					//	alert(111)
                    }
                });*/
            }
        }



        $('#dailyBtn').click(function(){
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();

            if(dd<10) dd = '0'+dd;
            if(mm<10) mm = '0'+mm;
            today = yyyy + '-' + mm + '-' + dd;

            $("#start_date").val(today);
            $("#end_date").val(today);
        });


        //current date calculation
        var date = new Date();
        var year = date.getFullYear();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
		
		//alert(date.getMonth())
		
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
    end_date();
	load_data();

</script>

