<?php
	use app\controllers\dashboardController;
	$insDashboard = new dashboardController();

	$alumnosActivosSedeL=$insDashboard->obtenerAlumnosActivos(1);	
	$alumnosActivosSedeC=$insDashboard->obtenerAlumnosActivos(2);

	$alumnosInactivosSedeL=$insDashboard->obtenerAlumnosInactivos(1);	
	$alumnosInactivosSedeC=$insDashboard->obtenerAlumnosInactivos(2);

	$pagosCanceladoSedeL=$insDashboard->obtenerPagosCancelados(1);	
	$pagosCanceladoSedeC=$insDashboard->obtenerPagosCancelados(2);

	$pagosPendienteSedeL=$insDashboard->obtenerPagosPendientes(1);	
	$pagosPendienteSedeC=$insDashboard->obtenerPagosPendientes(2);
	
	$representantes=$insDashboard->obtenerRepresentantes();
	$alumnosActivos=$insDashboard->totalAlumnosActivos();
	$alumnosInactivos=$insDashboard->totalAlumnosInactivos();
	
	if($alumnosActivosSedeL->rowCount()>0){
		$alumnosActivosSedeL=$alumnosActivosSedeL->fetch();
		$totalActivosSedeL=$alumnosActivosSedeL["totalActivos"];
	}else{
		$totalActivosSedeL= 0;
	}

	if($alumnosActivosSedeC->rowCount()>0){
		$alumnosActivosSedeC=$alumnosActivosSedeC->fetch();
		$totalActivosSedeC=$alumnosActivosSedeC["totalActivos"];
	}else{
		$totalActivosSedeC= 0;
	}

	if($alumnosInactivosSedeL->rowCount()>0){
		$alumnosInactivosSedeL=$alumnosInactivosSedeL->fetch();
		$totalInactivosSedeL=$alumnosInactivosSedeL["totalInactivos"];
	}else{
		$totalInactivosSedeL= 0;
	}

	if($alumnosInactivosSedeC->rowCount()>0){
		$alumnosInactivosSedeC=$alumnosInactivosSedeC->fetch();
		$totalInactivosSedeC=$alumnosInactivosSedeC["totalInactivos"];
	}else{
		$totalInactivosSedeC= 0;
	}

	if($pagosCanceladoSedeL->rowCount()>0){
		$pagosCanceladoSedeL=$pagosCanceladoSedeL->fetch();
		$totalCanceladoSedeL=$pagosCanceladoSedeL["totalCancelados"];
	}else{
		$totalCanceladoSedeL= 0;
	}

	if($pagosCanceladoSedeC->rowCount()>0){
		$pagosCanceladoSedeC=$pagosCanceladoSedeC->fetch();
		$totalCanceladoSedeC=$pagosCanceladoSedeC["totalCancelados"];
	}else{
		$totalCanceladoSedeC= 0;
	}

	if($pagosPendienteSedeL->rowCount()>0){
		$pagosPendienteSedeL=$pagosPendienteSedeL->fetch();
		$totalPendienteSedeL=isset($pagosPendienteSedeL["totalPendientes"]) ? $pagosPendienteSedeL["totalPendientes"] : 0;
	}else{
		$totalPendienteSedeL= 0;
	}

	if($pagosPendienteSedeC->rowCount()>0){
		$pagosPendienteSedeC=$pagosPendienteSedeC->fetch();
		$totalPendienteSedeC=isset($pagosPendienteSedeC["totalPendientes"]) ? $pagosPendienteSedeC["totalPendientes"] : 0;
	}else{
		$totalPendienteSedeC= 0;
	}
		if($representantes->rowCount()>0){
		$representantes=$representantes->fetch();
		$totalRepresentantes=$representantes["totalRepresentantes"];
	}else{
		$totalRepresentantes= 0;
	}

	if($alumnosActivos->rowCount()>0){
		$alumnosActivos=$alumnosActivos->fetch();
		$totalAlumnosActivos=$alumnosActivos["totalAlumnosActivos"];
	}else{
		$totalAlumnosActivos= 0;
	}

	if($alumnosInactivos->rowCount()>0){
		$alumnosInactivos=$alumnosInactivos->fetch();
		$totalAlumnosInactivos=$alumnosInactivos["totalAlumnosInactivos"];
	}else{
		$totalAlumnosInactivos= 0;
	}

	$totalCancelados = $totalCanceladoSedeL + $totalCanceladoSedeC;
	$totalPendientes = $totalPendienteSedeL + $totalPendienteSedeC;
?>

<!DOCTYPE html>
<html lang="es">
  	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo APP_NAME; ?>| Dashboard</title>
		<link rel="icon" type="image/png" href="<?php echo APP_URL; ?>app/views/dist/img/Logos/1104523691001_2.png">
		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/fontawesome-free/css/all.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
		<!-- Tempusdominus Bootstrap 4 -->
		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
		<!-- Bootstrap 5 -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
		  integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
		  crossorigin="anonymous"
		/>
		<!-- iCheck -->
		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
		<!-- JQVMap -->
		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/jqvmap/jqvmap.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/adminlte.css">
		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/apexcharts.css">
		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/bootstrap-icons.min.css">

		<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/sweetalert2.min.css">
		<script src="<?php echo APP_URL; ?>app/views/dist/js/sweetalert2.all.min.js" ></script>
	
  	</head>
	<body class="hold-transition sidebar-mini layout-fixed">
		<div class="wrapper">

			<!-- Navbar -->
			<?php require_once "app/views/inc/navbar.php"; ?>
			<!-- /.navbar -->

			<!-- Main Sidebar Container -->
			<?php require_once "app/views/inc/main-sidebar.php"; ?>
			<!-- /.Main Sidebar Container -->  

			<!-- vista -->
			<div class="content-wrapper">

				<!-- Content Header (Page header) -->
				<div class="content-header">
					<div class="container-fluid">
					<div class="row mb-1">
						<div class="col-sm-6">
						<h1 class="m-0">Dashboard</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Inicio</a></li>
							<li class="breadcrumb-item active">Dashboard v1</li>
						</ol>
						</div><!-- /.col -->
					</div><!-- /.row -->
					</div><!-- /.container-fluid -->
				</div>
				<!-- /.content-header -->

				<!-- Main content -->
				<section class="content">
					
					<div class="container-fluid">
					<!-- Small boxes (Stat box) -->
						<div class="card card-default">
							<div class="card-header">
								<h3 class="card-title">SEDE LA SALLE</h3>
								<div class="card-tools">
									<button type="button" class="btn btn-tool" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
									</button>
								</div>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-3 col-6">
									<!-- small box -->
										<div class="small-box bg-info">
											<div class="inner">
											<h3><?php echo $totalActivosSedeL; ?></h3>
											
											<p>Alumnos activos</p>
											</div>
											<div class="icon">
											<i class="ion ion-person"></i>
											</div>
											<a href="<?php echo APP_URL;?>alumnoList/" class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
										</div>
									</div>
									<!-- ./col -->
									<div class="col-lg-3 col-6">
									<!-- small box -->
									<div class="small-box bg-success">
										<div class="inner">
										<h3><?php echo $totalCanceladoSedeL; ?></h3>

										<p>Pagos receptados mes</p>
										</div>
										<div class="icon">
										<i class="ion ion-cash"></i>
										</div>
										<a href="<?php echo APP_URL;?>reportePagos/1" class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
									</div>
									</div>
									<!-- ./col -->
									<div class="col-lg-3 col-6">
									<!-- small box -->
									<div class="small-box bg-warning">
										<div class="inner">
										<h3><?php echo $totalInactivosSedeL; ?></h3>

										<p>Alumnos inactivos</p>
										</div>
										<div class="icon">
										<i class="ion ion-android-warning"></i>
										</div>
										<a href="<?php echo APP_URL;?>pagosList/" class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
									</div>
									</div>
									<!-- ./col -->
									<div class="col-lg-3 col-6">
									<!-- small box -->
									<div class="small-box bg-danger">
										<div class="inner">
										<h3><?php echo $totalPendienteSedeL; ?></h3>

										<p>Pagos pendientes</p>
										</div>
										<div class="icon">
										<i class="ion ion-cash"></i>
										</div>
										<a href="<?php echo APP_URL;?>reportePendientes/1" class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
									
									</div>
									</div>
									<!-- ./col -->
								</div>
							</div>
						</div>
						
						<div class="card card-default">
							<div class="card-header">
								<h3 class="card-title">SEDE EL PARAISO</h3>
								<div class="card-tools">
									<button type="button" class="btn btn-tool" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
									</button>
								</div>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-3 col-6">
									<!-- small box -->
										<div class="small-box bg-info">
											<div class="inner">
											<h3><?php echo $totalActivosSedeC; ?></h3>

											<p>Alumnos activos</p>
											</div>
											<div class="icon">
											<i class="ion ion-person"></i>
											</div>
											<a href="<?php echo APP_URL;?>alumnoList/" class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
										</div>
									</div>
									<!-- ./col -->
									<div class="col-lg-3 col-6">
									<!-- small box -->
									<div class="small-box bg-success">
										<div class="inner">
										<h3><?php echo $totalCanceladoSedeC; ?></h3>

										<p>Pagos receptados mes</p>
										</div>
										<div class="icon">
										<i class="ion ion-cash"></i>
										</div>
										<a href="<?php echo APP_URL;?>reportePagos/2" class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
									</div>
									</div>
									<!-- ./col -->
									<div class="col-lg-3 col-6">
									<!-- small box -->
									<div class="small-box bg-warning">
										<div class="inner">
										<h3><?php echo $totalInactivosSedeC; ?></h3>

										<p>Alumnos inactivos</p>
										</div>
										<div class="icon">
										<i class="ion ion-android-warning"></i>
										</div>
										<a href="<?php echo APP_URL;?>alumnoList/"  class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
									</div>
									</div>
									<!-- ./col -->
									<div class="col-lg-3 col-6">
									<!-- small box -->
									<div class="small-box bg-danger">
										<div class="inner">
										<h3><?php echo $totalPendienteSedeC; ?></h3>

										<p>Pagos pendientes</p>
										</div>
										<div class="icon">
										<i class="ion ion-cash"></i>
										</div>
										<a href="<?php echo APP_URL;?>reportePendientes/2" class="small-box-footer">Ver detalle <i class="fas fa-arrow-circle-right"></i></a>
									</div>
									</div>
									<!-- ./col -->
								</div>
							</div>
						</div>
										<div class="card card-default" style="padding: 0.5rem;">
					<div class="card-header" style="padding: 0.1rem 0.5rem;">
						<h3 class="card-title">CONSOLIDADO</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							</button>
						</div>
					</div>
					<div class="card-body" style="padding: 0.5rem;">
						<div class="row">
							<!-- Representantes activos -->
							<div class="col-md-3 mb-3">
								<a href="<?php echo APP_URL; ?>representanteList/" class="text-decoration-none">
									<div class="info-box d-flex shadow-sm rounded border">
										<span class="info-box-icon bg-warning text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
										<i class="bi bi-people-fill fs-5"></i>
										</span>
										<div class="info-box-content ms-2">
											<span class="info-box-text h7 text-muted">Representantes</span>
											<span class="info-box-number h5 text-dark"><?php echo $totalRepresentantes; ?></span>
										</div>
									</div>
								</a>
							</div>

							<!-- Alumnos activos -->
							<div class="col-md-3 mb-3 align-items-center">
								<a href="#" class="text-decoration-none">
									<div class="info-box d-flex shadow-sm rounded border">
										<span class="info-box-icon bg-warning text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
										<i class="bi bi-people-fill fs-5"></i>
										</span>
										<div class="info-box-content ms-2">
											<span class="info-box-text h7 text-muted">Alumnos activos</span>
											<span class="info-box-number h5 text-dark"><?php echo $totalAlumnosActivos; ?></span>
										</div>
									</div>
								</a>
							</div>

							<!-- Alumnos inactivos -->
							<div class="col-md-3 mb-3 align-items-center">
								<a href="#" class="text-decoration-none">
									<div class="info-box d-flex shadow-sm rounded border">
										<span class="info-box-icon bg-warning text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
										<i class="bi bi-people-fill fs-5"></i>
										</span>
										<div class="info-box-content ms-2">
											<span class="info-box-text h7 text-muted">Alumnos Inactivos</span>
											<span class="info-box-number h5 text-dark"><?php echo $totalAlumnosInactivos; ?></span>
										</div>
									</div>
								</a>
							</div>
							<!-- Pendientes -->
							<div class="col-md-3 mb-3 align-items-center">
								<a href="#" class="text-decoration-none">
									<div class="info-box d-flex shadow-sm rounded border">
										<span class="info-box-icon bg-warning text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
										<i class="bi bi-people-fill fs-5"></i>
										</span>
										<div class="info-box-content ms-2">
											<span class="info-box-text h7 text-muted">Alumnos con mora</span>
											<span class="info-box-number h5 text-dark"><?php echo $totalPendientes; ?></span>
										</div>
									</div>
								</a>
							</div>
						</div>	
					</div>
				</div>
					<!-- /.row -->
					</div><!-- /.container-fluid -->
				</section>
				<!-- /.content -->
			
			</div>
			<!-- /.vista -->

			<?php require_once "app/views/inc/footer.php"; ?>

			<!-- Control Sidebar -->
			<aside class="control-sidebar control-sidebar-dark">
				<!-- Control sidebar content goes here -->
			</aside>
		<!-- /.control-sidebar -->
		</div>
		<!-- ./wrapper -->
    
		<!-- jQuery -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jquery/jquery.min.js"></script>
		<!-- jQuery UI 1.11.4 -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jquery-ui/jquery-ui.min.js"></script>
		<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

		<!-- Bootstrap 4 -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
		<!-- ChartJS -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/chart.js/Chart.min.js"></script>
		<!-- Sparkline -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/sparklines/sparkline.js"></script>
		<!-- JQVMap -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jqvmap/jquery.vmap.min.js"></script>
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
		<!-- jQuery Knob Chart -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jquery-knob/jquery.knob.min.js"></script>
		<!-- daterangepicker -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/moment/moment.min.js"></script>
		<!-- Tempusdominus Bootstrap 4 -->
		<script src="<?php echo APP_URL; ?>app/views/dist/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
		<!-- AdminLTE App -->
		<script src="<?php echo APP_URL; ?>app/views/dist/js/adminlte.js"></script>

		<script src="<?php echo APP_URL; ?>app/views/dist/js/ajax.js" ></script>
		<script src="<?php echo APP_URL; ?>app/views/dist/js/main.js" ></script>
	
	</body>
</html>