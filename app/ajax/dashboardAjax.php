<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";

	use app\controllers\dashboardController;

	if(isset($_POST['modulo_dashboard'])){

		$insDashboard = new dashboardController();

		if($_POST['modulo_dashboard']=="estadistica"){
			echo $insDashboard->ingresosLugarEntr();
		}		
	}
	else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}