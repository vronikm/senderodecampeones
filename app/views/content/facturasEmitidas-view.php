<?php
	use app\controllers\facturasController;

	$insFactura = new facturasController();
	$limpiar = static function($valor) use ($insFactura){ return $insFactura->limpiarCadena($valor); };
	$fechaInicio = isset($_POST['factura_fecha_inicio']) ? $limpiar($_POST['factura_fecha_inicio']) : '';
	$fechaFin = isset($_POST['factura_fecha_fin']) ? $limpiar($_POST['factura_fecha_fin']) : '';
	$numero = isset($_POST['factura_numero']) ? $limpiar($_POST['factura_numero']) : '';
	$cliente = isset($_POST['factura_cliente']) ? $limpiar($_POST['factura_cliente']) : '';
	$estado = isset($_POST['factura_estado']) ? $limpiar($_POST['factura_estado']) : '';
	$sedeid = isset($_POST['factura_sedeid']) ? $limpiar($_POST['factura_sedeid']) : '';
	$rolid = (int)($_SESSION['rol'] ?? 0);
	$usuario = $_SESSION['usuario'] ?? '';
	$h = static function($valor){ return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
	$estados = ['PENDIENTE','GENERADA','FIRMADA','ENVIADA','RECIBIDA','DEVUELTA','AUTORIZADO','NO_AUTORIZADO','ERROR','ANULADA'];
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo APP_NAME; ?> | Facturas emitidas</title>
	<link rel="icon" type="image/png" href="<?php echo APP_URL; ?>app/views/dist/img/Logos/LogoSC.png">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/adminlte.css">
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require_once "app/views/inc/navbar.php"; ?>
      <?php require_once "app/views/inc/main-sidebar.php"; ?>

      <div class="content-wrapper">
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2 align-items-center">
					<div class="col-sm-6"><h5 class="m-0">Facturas emitidas</h5></div>
					<div class="col-sm-6">
						<a href="<?php echo APP_URL; ?>facturasList/" class="btn btn-sm bg-lightblue float-sm-right">
							<i class="fas fa-plus mr-1"></i> Registrar factura
						</a>
					</div>
				</div>
			</div>
		</div>

		<section class="content">
			<div class="container-fluid">
				<div class="card card-default">
					<div class="card-header"><h3 class="card-title">Filtros de búsqueda</h3></div>
					<form action="<?php echo APP_URL; ?>facturasEmitidas/" method="POST" autocomplete="off">
						<div class="card-body">
							<div class="row align-items-end">
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="factura_fecha_inicio">Fecha inicio</label>
										<input type="date" class="form-control" id="factura_fecha_inicio" name="factura_fecha_inicio" value="<?php echo $h($fechaInicio); ?>">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="factura_fecha_fin">Fecha fin</label>
										<input type="date" class="form-control" id="factura_fecha_fin" name="factura_fecha_fin" value="<?php echo $h($fechaFin); ?>">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="factura_numero">Nro. factura</label>
										<input type="text" class="form-control" id="factura_numero" name="factura_numero" value="<?php echo $h($numero); ?>" placeholder="001-001-000000001">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="factura_cliente">Cliente o identificación</label>
										<input type="text" class="form-control" id="factura_cliente" name="factura_cliente" value="<?php echo $h($cliente); ?>" placeholder="Cliente o cédula">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="factura_estado">Estado SRI</label>
										<select class="form-control" id="factura_estado" name="factura_estado">
											<option value="">Todos</option>
											<?php foreach($estados as $opcion){ ?>
												<option value="<?php echo $opcion; ?>" <?php echo $estado === $opcion ? 'selected' : ''; ?>><?php echo $opcion; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="factura_sedeid">Sede</label>
										<select class="form-control" id="factura_sedeid" name="factura_sedeid">
											<?php if(in_array($rolid, [1, 2], true)){ ?><option value="">Todas</option><?php } ?>
											<?php echo $insFactura->listarSedeFacturas($sedeid, $rolid, $usuario); ?>
										</select>
									</div>
								</div>
								<div class="col-md-2"><button type="submit" class="btn btn-sm bg-lightblue btn-block">Buscar</button></div>
							</div>
						</div>
					</form>
				</div>

				<div class="card card-default">
					<div class="card-header"><h3 class="card-title">Resultado de la búsqueda</h3></div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="facturas-emitidas" class="table table-bordered table-striped table-sm">
								<thead>
									<tr>
										<th>Fecha</th>
										<th>Nro. factura</th>
										<th>Sede</th>
										<th>Identificación</th>
										<th>Cliente</th>
										<th>Pagos/Rubros</th>
										<th>Total</th>
										<th>Estado SRI</th>
										<th>Opciones</th>
									</tr>
								</thead>
								<tbody>
									<?php echo $insFactura->listarFacturasEmitidas([
										'fecha_inicio' => $fechaInicio,
										'fecha_fin' => $fechaFin,
										'numero' => $numero,
										'cliente' => $cliente,
										'estado' => $estado,
										'sedeid' => $sedeid
									], $rolid, $usuario); ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</section>
      </div>

		<?php require_once "app/views/inc/footer.php"; ?>
    </div>

	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jquery/jquery.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/adminlte.min.js"></script>
	<script>
		$(function () {
			$('#facturas-emitidas').DataTable({
				responsive: true,
				lengthChange: false,
				autoWidth: false,
				order: [[0, 'desc']],
				language: {
					emptyTable: 'No hay facturas emitidas para los filtros seleccionados',
					info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
					infoEmpty: 'Mostrando 0 a 0 de 0 registros',
					infoFiltered: '(filtrado de _MAX_ registros totales)',
					search: 'Buscar:',
					zeroRecords: 'No se encontraron facturas coincidentes',
					paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' }
				}
			});
		});
	</script>
  </body>
</html>
