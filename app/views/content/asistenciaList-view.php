<?php
	use app\controllers\asistenciaController;
	$insAsistencia = new asistenciaController();

	$h = static function($valor){ return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
	$rolid = (int)($_SESSION['rol'] ?? 0);
	$usuario = (string)($_SESSION['usuario'] ?? '');
	$esAdmin = in_array($rolid, [1, 2], true);

	/* ---------- Filtros (por defecto, el mes en curso) ---------- */
	$anio = (int)($_POST['asistencia_anio'] ?? date('Y'));
	$mes = (int)($_POST['asistencia_mes'] ?? date('n'));
	if($anio < 2000 || $anio > 2100){ $anio = (int)date('Y'); }
	if($mes < 1 || $mes > 12){ $mes = (int)date('n'); }
	$sedeid = (int)($_POST['alumno_sedeid'] ?? 0);
	$horarioid = (int)($_POST['horario_id'] ?? 0);

	$meses = [1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
	$diasSemana = ['', 'L', 'M', 'X', 'J', 'V', 'S', 'D'];
	$diasMes = (int)date('t', mktime(0, 0, 0, $mes, 1, $anio));
	$hoy = ($anio === (int)date('Y') && $mes === (int)date('n')) ? (int)date('j') : 0;

	$anios = $insAsistencia->aniosConAsistencia();
	$noLaborables = $insAsistencia->diasNoLaborablesAsistencia($anio, $mes);
	$consulta = $insAsistencia->consultarAsistenciaMensual($anio, $mes, $sedeid, $horarioid, $rolid, $usuario);
	$filas = $consulta['filas'];
	$resumen = $consulta['resumen'];

	$clasePorcentaje = static function($p){
		if($p === null){ return 'secondary'; }
		return $p >= 90 ? 'success' : ($p >= 75 ? 'warning' : 'danger');
	};
	$periodo = $meses[$mes].' '.$anio;
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo APP_NAME; ?> | Ver asistencias</title>
	<link rel="icon" type="image/png" href="<?php echo APP_URL; ?>app/views/dist/img/Logos/LogoSC.png">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/adminlte.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/sweetalert2.min.css">
	<style>
		/* Tarjetas de resumen a la misma altura */
		.resumen-asistencia > [class*="col-"] { display: flex; }
		.resumen-asistencia .small-box { width: 100%; display: flex; flex-direction: column; }
		.resumen-asistencia .small-box .inner { flex: 1 1 auto; }
		.resumen-asistencia .small-box p { margin-bottom: 0; }

		/* Grilla mensual: 31 dias no caben en pantalla, se desplaza en
		   horizontal con la columna del alumno fija a la izquierda. */
		.grilla-wrap { overflow-x: auto; }
		.grilla { font-size: 12px; white-space: nowrap; }
		.grilla th, .grilla td { vertical-align: middle; }
		.grilla .col-dia { width: 26px; min-width: 26px; padding: 2px !important; text-align: center; }
		.grilla .col-alumno { position: sticky; left: 0; z-index: 2; background: #fff; min-width: 230px; box-shadow: 1px 0 0 #dee2e6; }
		.grilla thead .col-alumno { z-index: 3; background: #f4f6f9; }
		.grilla tbody tr:nth-of-type(odd) .col-alumno { background: #f9f9f9; }
		.grilla thead th { background: #f4f6f9; }
		.grilla .no-lab, .leyenda .no-lab { background: repeating-linear-gradient(45deg, #f1f1f1, #f1f1f1 3px, #e6e6e6 3px, #e6e6e6 6px) !important; color: #adb5bd; }
		.grilla .hoy { outline: 2px solid #3c8dbc; outline-offset: -2px; }
		.m-P { background: #28a745 !important; color: #fff; font-weight: 600; }
		.m-F { background: #dc3545 !important; color: #fff; font-weight: 600; }
		.m-J { background: #6c757d !important; color: #fff; font-weight: 600; }
		.leyenda span { display: inline-block; width: 18px; height: 18px; line-height: 18px; text-align: center; border-radius: 3px; font-size: 11px; margin-right: 4px; vertical-align: middle; }
		.pct-bar { height: 4px; margin-top: 3px; }
	</style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require_once "app/views/inc/navbar.php"; ?>
      <?php require_once "app/views/inc/main-sidebar.php"; ?>

      <div class="content-wrapper">
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2 align-items-center">
					<div class="col-sm-6"><h5 class="m-0"><i class="fas fa-calendar-check mr-2"></i>Ver asistencias</h5></div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>dashboard/">Inicio</a></li>
							<li class="breadcrumb-item">Asistencia</li>
							<li class="breadcrumb-item active">Ver asistencias</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<section class="content">
			<div class="container-fluid">

				<!-- Filtros -->
				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Criterios de búsqueda</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<form action="<?php echo APP_URL; ?>asistenciaList/" method="POST" autocomplete="off">
						<div class="card-body">
							<div class="row align-items-end">
								<div class="col-md-3">
									<div class="form-group input-group-sm">
										<label for="alumno_sedeid">Sede</label>
										<select class="form-control select2" id="alumno_sedeid" name="alumno_sedeid">
											<option value="0"<?php echo $sedeid === 0 ? ' selected' : ''; ?>><?php echo $esAdmin ? 'Todas' : 'Todas mis sedes'; ?></option>
											<?php echo $insAsistencia->listarSedeAsistencia($sedeid, $rolid, $usuario); ?>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="asistencia_anio">Año</label>
										<select class="form-control" id="asistencia_anio" name="asistencia_anio">
											<?php foreach($anios as $a){ ?>
												<option value="<?php echo (int)$a; ?>"<?php echo (int)$a === $anio ? ' selected' : ''; ?>><?php echo (int)$a; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<label for="asistencia_mes">Mes</label>
										<select class="form-control" id="asistencia_mes" name="asistencia_mes">
											<?php foreach($meses as $num => $nombre){ ?>
												<option value="<?php echo $num; ?>"<?php echo $num === $mes ? ' selected' : ''; ?>><?php echo $nombre; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group input-group-sm">
										<label for="horario_id">Horario</label>
										<select class="form-control select2" id="horario_id" name="horario_id">
											<option value="0"<?php echo $horarioid === 0 ? ' selected' : ''; ?>>Todos</option>
											<?php echo $insAsistencia->listarOptionHorarioAsistencia($horarioid, $sedeid); ?>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group-sm">
										<button type="submit" class="form-control btn btn-sm bg-lightblue"><i class="fas fa-search mr-1"></i>Buscar</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<!-- Resumen del mes -->
				<div class="row resumen-asistencia">
					<div class="col-lg-3 col-6">
						<div class="small-box bg-info">
							<div class="inner">
								<h3><?php echo (int)$resumen['alumnos']; ?></h3>
								<p>Alumnos con registro<br><small><?php echo $h($periodo); ?></small></p>
							</div>
							<div class="icon"><i class="fas fa-users"></i></div>
						</div>
					</div>
					<div class="col-lg-3 col-6">
						<div class="small-box bg-<?php echo $clasePorcentaje($resumen['porcentaje']); ?>">
							<div class="inner">
								<h3><?php echo $resumen['porcentaje'] === null ? '—' : $h($resumen['porcentaje']).'<sup style="font-size: 20px">%</sup>'; ?></h3>
								<p>Asistencia general<br><small><?php echo (int)$resumen['P']; ?> presentes de <?php echo (int)($resumen['P'] + $resumen['F']); ?> clases</small></p>
							</div>
							<div class="icon"><i class="fas fa-chart-pie"></i></div>
						</div>
					</div>
					<div class="col-lg-3 col-6">
						<div class="small-box bg-<?php echo $resumen['F'] > 0 ? 'danger' : 'olive'; ?>">
							<div class="inner">
								<h3><?php echo (int)$resumen['F']; ?></h3>
								<p>Faltas<br><small><?php echo (int)$resumen['J']; ?> justificada(s) aparte</small></p>
							</div>
							<div class="icon"><i class="fas fa-user-times"></i></div>
						</div>
					</div>
					<div class="col-lg-3 col-6">
						<div class="small-box bg-<?php echo $resumen['riesgo'] > 0 ? 'warning' : 'secondary'; ?>">
							<div class="inner">
								<h3><?php echo (int)$resumen['riesgo']; ?></h3>
								<p>Bajo 75% de asistencia<br><small>alumnos a dar seguimiento</small></p>
							</div>
							<div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
						</div>
					</div>
				</div>

				<!-- Grilla mensual -->
				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Asistencia de <?php echo $h($periodo); ?></h3>
						<div class="card-tools leyenda small text-muted">
							<span class="m-P">P</span>Presente
							<span class="m-F ml-2">F</span>Falta
							<span class="m-J ml-2">J</span>Justificado
							<span class="no-lab ml-2">&nbsp;</span>Sin entrenamiento
						</div>
					</div>
					<div class="card-body">
						<div class="grilla-wrap">
							<table id="tabla-asistencia" class="table table-bordered table-striped table-sm grilla">
								<thead>
									<tr>
										<th class="col-alumno" rowspan="2">Alumno</th>
										<th rowspan="2">Edad</th>
										<?php for($d = 1; $d <= $diasMes; $d++){
											$clases = 'col-dia'.(isset($noLaborables[$d]) ? ' no-lab' : '').($d === $hoy ? ' hoy' : '');
											$titulo = $noLaborables[$d] ?? '';
										?>
											<th class="<?php echo $clases; ?>" title="<?php echo $h($titulo); ?>"><?php echo $d; ?></th>
										<?php } ?>
										<th rowspan="2" class="text-center">P</th>
										<th rowspan="2" class="text-center">F</th>
										<th rowspan="2" class="text-center">J</th>
										<th rowspan="2" class="text-center" style="min-width: 90px;">% Asistencia</th>
									</tr>
									<tr>
										<?php for($d = 1; $d <= $diasMes; $d++){
											$dow = (int)date('N', mktime(0, 0, 0, $mes, $d, $anio));
										?>
											<th class="col-dia<?php echo isset($noLaborables[$d]) ? ' no-lab' : ''; ?>"><?php echo $diasSemana[$dow]; ?></th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php foreach($filas as $fila){ ?>
									<tr>
										<td class="col-alumno">
											<?php echo $h($fila['nombre']); ?>
											<?php if($sedeid === 0){ ?><br><small class="text-muted"><?php echo $h($fila['sede']); ?></small><?php } ?>
										</td>
										<td class="text-center"><?php echo $h($fila['edad']); ?></td>
										<?php for($d = 1; $d <= $diasMes; $d++){
											$m = $fila['marcas'][$d];
											$clases = 'col-dia'.($m !== '' ? ' m-'.$m : (isset($noLaborables[$d]) ? ' no-lab' : '')).($d === $hoy ? ' hoy' : '');
										?>
											<td class="<?php echo $clases; ?>"><?php echo $m; ?></td>
										<?php } ?>
										<td class="text-center text-success font-weight-bold"><?php echo (int)$fila['conteo']['P']; ?></td>
										<td class="text-center text-danger font-weight-bold"><?php echo (int)$fila['conteo']['F']; ?></td>
										<td class="text-center text-secondary"><?php echo (int)$fila['conteo']['J']; ?></td>
										<td class="text-center" data-order="<?php echo $fila['porcentaje'] === null ? -1 : $h($fila['porcentaje']); ?>">
											<?php if($fila['porcentaje'] === null){ ?>
												<span class="text-muted">—</span>
											<?php }else{ $c = $clasePorcentaje($fila['porcentaje']); ?>
												<strong class="text-<?php echo $c; ?>"><?php echo $h($fila['porcentaje']); ?>%</strong>
												<div class="progress pct-bar"><div class="progress-bar bg-<?php echo $c; ?>" style="width: <?php echo $h($fila['porcentaje']); ?>%"></div></div>
											<?php } ?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<small class="text-muted d-block mt-2">
							<i class="fas fa-info-circle mr-1"></i>El porcentaje se calcula sobre las clases registradas de cada alumno
							(presentes ÷ presentes + faltas). Las faltas justificadas no restan.
						</small>
					</div>
				</div>
			</div>
		</section>
      </div>

      <?php require_once "app/views/inc/footer.php"; ?>
      <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jquery/jquery.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/select2/js/select2.full.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/jszip/jszip.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/plugins/datatables-buttons/js/buttons.print.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/adminlte.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/sweetalert2.all.min.js"></script>
	<script>
		$(function () {
			$('.select2').select2({theme: 'bootstrap4', width: '100%'});

			const sede = $('#alumno_sedeid option:selected').text().trim();
			const horario = $('#horario_id').val() !== '0'
				? $('#horario_id option:selected').text().split('|')[0].trim()
				: 'Todos';
			const titulo = 'Asistencias <?php echo $h($periodo); ?> | Sede: ' + sede + ' | Horario: ' + horario;

			$('#tabla-asistencia').DataTable({
				paging: true,
				pageLength: 50,
				lengthChange: false,
				autoWidth: false,
				order: [[0, 'asc']],
				orderCellsTop: true,
				// Columnas de dias (2 .. 2+diasMes-1): no se ordenan.
				columnDefs: [{ orderable: false, targets: Array.from({length: <?php echo $diasMes; ?>}, (_, i) => i + 2) }],
				language: {
					emptyTable: 'No hay asistencias registradas para <?php echo $h($periodo); ?> con estos filtros',
					info: 'Mostrando _START_ a _END_ de _TOTAL_ alumnos',
					infoEmpty: 'Mostrando 0 a 0 de 0 alumnos',
					infoFiltered: '(filtrado de _MAX_ alumnos)',
					search: 'Buscar alumno:',
					zeroRecords: 'No se encontraron alumnos coincidentes',
					paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' }
				},
				buttons: [
					{ extend: 'excel', text: '<i class="fas fa-file-excel mr-1"></i>Excel', title: titulo },
					{ extend: 'print', text: '<i class="fas fa-print mr-1"></i>Imprimir', title: titulo,
					  exportOptions: { modifier: { page: 'all', search: 'applied', order: 'applied' } } }
				]
			}).buttons().container().appendTo('#tabla-asistencia_wrapper .col-md-6:eq(0)');

			// Cambiar de sede recarga los horarios de esa sede.
			$('#alumno_sedeid').on('change', function(){ $('#horario_id').val('0'); this.form.submit(); });
		});
	</script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/ajax.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/main.js"></script>
  </body>
</html>
