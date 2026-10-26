<?php
	use app\controllers\carnetController;
	$insCarnet = new carnetController();
	$cobrarReimpresion = $insCarnet->cobrarReimpresionCarnet();

	$valorReimpresion = $insCarnet->valorReimpresionCarnet();

	// Emision por sede (0 = todas las sedes del usuario).
	$sedeid = $insCarnet->sedeSeleccionadaCarnet();
	$sedeNombre = $insCarnet->nombreSedeCarnet($sedeid);
	$esAdminCarnet = in_array((int)($_SESSION['rol'] ?? 0), [1, 2], true);
	$alertaSinHorario = $insCarnet->alertaAlumnosSinHorario($sedeid);
?>

<html lang="es">
  <head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo APP_NAME; ?> | Carnets</title>
	<link rel="icon" type="image/png" href="<?php echo APP_URL; ?>app/views/dist/img/Logos/LogoSC.png">
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/fontawesome-free/css/all.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
	<!-- iCheck for checkboxes and radio inputs -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/adminlte.css">
	<!-- SweetAlert2 -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/sweetalert2.min.css">
	
  </head>
  
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Navbar -->
      <?php require_once "app/views/inc/navbar.php"; ?>
      
      <!-- Main Sidebar Container -->
      <?php require_once "app/views/inc/main-sidebar.php"; ?>
      
      <!-- Content Wrapper -->
      <div class="content-wrapper">
		<!-- Content Header -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h3 class="m-0">Carnets del Mes</h3>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>dashboard/">Inicio</a></li>
							<li class="breadcrumb-item active">Carnets</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<!-- Main content -->
		<section class="content">
			<div class="container-fluid">
				<!-- Sede a emitir -->
				<form action="<?php echo APP_URL; ?>carnetList/" method="POST" autocomplete="off" id="formSedeCarnet">
					<div class="card card-outline card-primary">
						<div class="card-body py-3">
							<div class="row align-items-end">
								<div class="col-md-5">
									<div class="form-group input-group-sm mb-0">
										<label for="carnet_sedeid"><i class="fas fa-map-marker-alt mr-1"></i>Emitir carnets de</label>
										<select class="form-control select2" id="carnet_sedeid" name="sedeid">
											<option value="0"<?php echo $sedeid === 0 ? ' selected' : ''; ?>><?php echo $esAdminCarnet ? 'Todas las sedes' : 'Todas mis sedes'; ?></option>
											<?php echo $insCarnet->listarSedesCarnet($sedeid); ?>
										</select>
									</div>
								</div>
								<div class="col-md-7 mt-2 mt-md-0">
									<small class="text-muted">
										La lista, el contador y <strong>Imprimir Todos</strong> se limitan a la sede elegida.
										La reimpresión usa los alumnos que marque en la lista.
									</small>
								</div>
							</div>
						</div>
					</div>
				</form>

				<!-- Card principal -->
				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-id-card"></i> 
							<?php echo htmlspecialchars($sedeNombre, ENT_QUOTES, 'UTF-8'); ?> · Alumnos con pago de pensión - <?php $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'America/Guayaquil', IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
        													echo ucfirst($formatter->format(new DateTime()));?>
						</h3>
						<div class="card-tools">							
							<!-- Botón imprimir todos con confirmación -->
							<button type="button" 
									id="btnImprimirTodos" 
									class="btn btn-success btn-sm" 
									style="margin-right: 10px;">

								<i class="fas fa-print"></i> Imprimir Todos
								<span class="badge badge-light" id="contadorCarnets">
									<i class="fas fa-spinner fa-spin"></i>
								</span>
							</button>

							<button type="button" 
									id="btn-impresion-atrasada"
									class="btn btn-info btn-sm"
									style="margin-right: 10px;">
								<i class="fas fa-calendar-alt"></i> Impresion atrasada
							</button>

							<button type="button" 
									id="btn-reimprimir-carnets"
									class="btn btn-warning btn-sm">
								<i class="fas fa-redo"></i> Reimprimir Carnets
							</button>

							<span id="contador-seleccion" 
								class="badge badge-warning" 
								style="display: none; font-size: 14px; padding: 8px 12px;">
								0 carnets seleccionados
							</span>

							<button type="button" 
									id="btn-limpiar-seleccion"
									class="btn btn-secondary btn-sm" 
									style="display: none;">
								<i class="fas fa-times"></i> Limpiar Selección
							</button>
							
							<button type="button" class="btn btn-tool" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							</button>
						</div>
					</div>
					
					<div class="card-body">						
						<div class="alert alert-info">
							<i class="fas fa-info-circle"></i>
							<strong>Información:</strong> 
							Los carnets se generan automáticamente para todos los alumnos con pago de pensión del mes actual.
							Use los checkboxes para reimprimir carnets extraviados. <?php echo $cobrarReimpresion ? 'Se generara un cobro de $' . number_format($valorReimpresion, 2, '.', '') . ' por reimpresion.' : 'No se generara cobro por reimpresion.'; ?>
						</div>

						<?php echo $alertaSinHorario; ?>

						<form id="formReimpresion" class="FormularioAjax" data-form="save">
							<table id="example1" class="table table-bordered table-striped table-sm">
								<thead>
									<tr>
										<th>Identificación</th>
										<th>Nombres</th>
										<th>Apellidos</th>	
										<th>Fecha Últ Pensión</th>
										<th>Condición</th>
										<th>Ver Carnet</th>
										<th style="text-align: center;">
											<div class="custom-control custom-checkbox">
												<input class="custom-control-input" 
													   type="checkbox" 
													   id="seleccionarTodos">
												<label for="seleccionarTodos" class="custom-control-label">
													Reimprimir
												</label>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php // Con alumnos sin horario no se lista nada (no se les puede emitir el carnet).
										echo $alertaSinHorario === '' ? $insCarnet->listarAlumnos($sedeid) : ''; ?>							
								</tbody>
							</table>
						</form>
					</div>
				</div>
			</div>
		</section>
      </div>

      <?php require_once "app/views/inc/footer.php"; ?>

      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <!-- jQuery -->
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- DataTables -->
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/select2/js/select2.full.min.js"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo APP_URL; ?>app/views/dist/js/adminlte.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/carnet_seleccion.js?v=<?php echo filemtime(__DIR__ . '/../dist/js/carnet_seleccion.js'); ?>"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/carnet_list.js?v=<?php echo filemtime(__DIR__ . '/../dist/js/carnet_list.js'); ?>"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/sweetalert2.all.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/ajax.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/main.js"></script>
	<script>
		var APP_URL = '<?php echo APP_URL; ?>';
		// Sede elegida: carnet_list.js la envia en el contador y en Imprimir Todos.
		var SEDE_CARNET_ID = <?php echo (int)$sedeid; ?>;
		var SEDE_CARNET_NOMBRE = <?php echo json_encode($sedeNombre, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>;
		var COBRAR_REIMPRESION = <?php echo $cobrarReimpresion ? 'true' : 'false'; ?>;
		var MES_ATRASADO_DEFAULT = <?php echo (int)date('n', strtotime('first day of last month')); ?>;
		var ANIO_ATRASADO_DEFAULT = <?php echo (int)date('Y', strtotime('first day of last month')); ?>;
		var MES_ACTUAL = '<?php 
        $formatter = new IntlDateFormatter(
            "es_ES", IntlDateFormatter::NONE, IntlDateFormatter::NONE, 
            "America/Guayaquil", IntlDateFormatter::GREGORIAN, "MMMM yyyy"
        );
        echo ucfirst($formatter->format(new DateTime()));
    ?>';
	</script>
  </body>
</html>
