<?php
session_start();
include '../../includes/static_text.php';
include("../../dbConnect.php");
include("../../dbClass.php");
$dbClass = new dbClass;
?>

<script>
$(document).ready(function () {	
	var user_id 	= "<?php echo $_SESSION['user_id']; ?>";	
	var user_type  = "<?php echo $_SESSION['user_type']; ?>";	

	$('.date-picker').daterangepicker({
		singleDatePicker: true,
		calender_style: "picker_3",
		locale: {
			  format: 'YYYY-MM-DD',
			  separator: " - ",
		}
	});
});

</script>



<?php

if(!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") header("Location:".$activity_url."../view/login.php");
else{
	$user_type_name = "HMS Employee";
	include("emp_profile.php");	
} 
?>

<style type="text/css">
	@media print {    
		.no-print, .no-print * {
			display: none !important;
		}
	}
</style>

