<?php session_start();
include 'includes/static_text.php';
if(!isset($_SESSION['user_id'])){ ob_start(); header("Location:".$activity_url."login.php"); exit();}
else if($_SESSION['user_id'] == ""){ ob_start(); header("Location:".$activity_url."login.php"); exit();}
else if(!isset($_REQUEST['view'])){ob_start(); header("Location:".$activity_url."index.php?module=personal&view=dashbord"); exit();}
else if($_REQUEST['view'] == "" ){ ob_start(); header("Location:".$activity_url."index.php?module=personal&view=dashbord"); exit();}
else{
	include("dbConnect.php");
	include("dbClass.php");
	$dbClass = new dbClass;		
	$user_id = $_SESSION['user_id'];
	$user_type = $_SESSION['user_type'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $site_title; ?></title>    
    <!-- Bootstrap core CSS -->
    <link href="theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="theme/fonts/css/font-awesome.min.css" rel="stylesheet">
    <link href="theme/css/animate.min.css" rel="stylesheet">
    <!-- Custom styling plus plugins -->
    <link href="theme/css/custom.css" rel="stylesheet">    
    <!--calender-->
    <link href="theme/css/calendar/fullcalendar.css" rel="stylesheet">
    <link href="theme/css/calendar/fullcalendar.print.css" rel="stylesheet" media="print"> 
    <link href="theme/css/jquery-ui.css" rel="stylesheet">       
    <!--data table-->
    <link href="theme/css/datatables/tools/css/dataTables.tableTools.css" rel="stylesheet">     
    <!-- select2 -->
    <link href="theme/css/select/select2.min.css" rel="stylesheet">
    <!-- switchery -->
    <link href="theme/css/switchery/switchery.min.css"  rel="stylesheet" />  
         
    <link href="theme/css/icheck/flat/green.css" rel="stylesheet">
    
	<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
	<link rel="icon" href="images/favicon.png" type="image/x-icon">
	<script src="theme/js/jquery.min.js"></script>
    <script src="js/static_text.js"></script>
    <script src="js/common.js"></script> 
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;"> 
                       <a href="" class="site_title" style="font-size:15px !important; font-weight:bold">Molla Bricks</a>
                    </div>
                    <div class="clearfix"></div>
                    <!-- menu prile quick info -->
                    <div class="profile">
                        <div class="profile_pic">
                            <img src="<?php echo $_SESSION['user_pic']; ?>" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">
                            <span><h2><?php echo $_SESSION['user_name']; ?></h2></span>
                        </div>
                    </div>
                    <!-- /menu prile quick info -->
                    <br />
                    <!-- sidebar menu -->
                     <?php include("view/common_view/left_menu.php"); ?>
                    <!-- /sidebar menu -->
                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        <a data-toggle="tooltip" data-placement="top" title="Logout" href="logout.php">
                            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                        </a>
                    </div>
                    <!-- /menu footer buttons -->
                </div>
            </div>
	
            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav class="" role="navigation">
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo $_SESSION['user_pic'] ; ?>" alt="">
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                                    <li><a href="index.php?module=personal&view=profile">  Profile</a></li>
                                    <li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                </ul>
                            </li>
						<!--
						   <li role="presentation" class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa  fa-bell-slash"></i>
                                    <span class="badge bg-red" id="unread_notifications"></span>
                                </a>
                                <ul id="notification_ul" class="dropdown-menu list-unstyled msg_list animated fadeInDown" role="menu">
                                    <li>
                                    	<div class="text-left col-md-6">
											<button class="btn btn-primary btn-xs has-spinner" id="load_more_not_button"><span class="spinner"><i class="fa fa-spinner fa-spin fa-fw"></i></span>Load More Notificatons?</button>
   										</div>
                                        <div class="text-right col-md-6">
                                            <a href="index.php?module=personal&view=notification">
                                                <strong>All Notifications</strong>
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </li> -->
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- /top navigation -->
            <!-- page content -->
            <div class="right_col" role="main">
                <div class="">
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div id="main_container" class="" style="min-height:500px;">
      							<!--
                                
                                All the pages will load here 
                                
                                -->
                            </div>                             
                        </div>
                    </div>
                </div>
                <!-- footer content -->
                <footer>
                    <div class="">
                        <p class="pull-right">
                            <span class="lead"><span class="lead">&copy;  2020 Hishab Nikash</span></span>
                        </p>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
            </div>
            <!-- /page content -->
        </div>

    </div>

    <div id="custom_notifications" class="custom-notifications dsp_none">
        <ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
        </ul>
        <div class="clearfix"></div>
        <div id="notif-group" class="tabbed_notifications"></div>
    </div>


	<div id="wait" style="display:none;width:69px;height:89px;position:absolute;top:50%;left:50%;padding:2px;">	
		 <button class="btn btn-primary btn-lg has-spinner active" disabled >
			<span class="spinner"><i class="fa fa-spinner fa-spin fa-fw "></i></span> <?php echo 'Loading.............'; ?></span>
		</button>
	</div>

  	<!--  <script src="js/post.js"></script>-->
    <script src="theme/js/bootstrap.min.js"></script>
    <script src="theme/js/custom.js"></script> 
    <script src="theme/js/jquery-ui.js"></script> 

    <!-- chart js  -->
    <script src="theme/js/chartjs/chart.min.js"></script>      
    
    <!-- bootstrap progress js -->
	<script src="theme/js/progressbar/bootstrap-progressbar.min.js" type="text/javascript"></script>
    <script src="theme/js/nicescroll/jquery.nicescroll.min.js" type="text/javascript"></script>
    
    <!-- icheck -->
	<script src="theme/js/icheck/icheck.min.js" type="text/javascript"></script> 
 	
    <!-- Datatables -->
    <script src="theme/js/datatables/js/jquery.dataTables.js"></script>
    <script src="theme/js/datatables/tools/js/dataTables.tableTools.js"></script>
    
    <!-- daterangepicker     -->
    <script type="text/javascript" src="theme/js/moment/moment.min.js"></script>
    <script type="text/javascript" src="theme/js/datepicker/daterangepicker.js"></script>
   
     <!-- tags -->
    <script src="theme/js/tags/jquery.tagsinput.min.js"></script>
    
    <!-- switchery -->
    <script src="theme/js/switchery/switchery.min.js"></script>
    
    <!-- select2 -->
    <script src="theme/js/select/select2.full.js"></script>
    
    <!-- form validation -->
   <!-- <script type="text/javascript" src="theme/js/parsley/parsley.min.js"></script>-->
    
    <!-- textarea resize -->
    <script src="theme/js/textarea/autosize.min.js"></script>    
    <script> autosize($('.resizable_textarea'));    </script>
 
    <!-- pace -->
    <script src="theme/js/pace/pace.min.js"></script>
   
    <!-- ckeditor -->
    <script src="theme/ckeditor-ckfinder-integration-master/ckeditor/ckeditor.js" type="text/javascript" ></script>
  
<?php
}
?>
</body>
</html>
<script>
$(document).ready(function () {
	var user_id = "<?php echo $_SESSION['user_id']; ?>";
	$('body').on("click", ".dropdown-menu", function (e) {
		$(this).parent().is(".open") && e.stopPropagation();
	});	
	$('.menu-link').on("click", function(){
		if($( window ).width() <911){
			$('#menu_toggle').trigger("click");
		}
	})
});
</script>