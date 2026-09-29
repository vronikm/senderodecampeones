<?php
	/*  Interruptor de mantenimiento del modulo de facturacion electronica.
	    Devuelve false -y pinta el aviso- cuando el servicio esta abajo, para
	    que la vista no continue. La pantalla de configuracion (facturacionConfig)
	    no debe usar esta guarda: es desde donde se vuelve a levantar el servicio. */
	function facturacionServicioDisponible($insFactura, $titulo = 'Facturación'){
		try{
			$estado = $insFactura->estadoServicioFacturacion();
		}catch(Throwable $e){
			return true; // Ante un fallo leyendo el estado, no se bloquea el modulo.
		}

		if(!empty($estado['activo'])){
			return true;
		}

		facturacionRenderMantenimiento((string)($estado['mensaje'] ?? ''), $titulo);
		return false;
	}

	/*  Aviso de mantenimiento dentro de la plantilla (navbar, menu lateral y
	    footer), para que el usuario siga ubicado y pueda navegar al resto del
	    sistema. navbar/main-sidebar leen $url del ambito global (index.php). */
	function facturacionRenderMantenimiento($mensaje, $titulo = 'Facturación'){
		global $url;
		$h = static function($valor){ return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
		http_response_code(503);
		$esAdmin = in_array((int)($_SESSION['rol'] ?? 0), [1, 2], true);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo APP_NAME; ?> | <?php echo $h($titulo); ?></title>
	<link rel="icon" type="image/png" href="<?php echo APP_URL; ?>app/views/dist/img/Logos/LogoSC.png">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/adminlte.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/dist/css/sweetalert2.min.css">
	<style>
		.fe-mant-icono{
			width: 88px; height: 88px; border-radius: 50%;
			display: inline-flex; align-items: center; justify-content: center;
			background: rgba(255, 193, 7, .15); color: #d39e00; font-size: 40px;
		}
		.fe-mant-lista li{ padding: .35rem 0; }
		.fe-mant-lista i{ width: 1.5rem; text-align: center; }
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
					<div class="col-sm-6">
						<h5 class="m-0"><?php echo $h($titulo); ?></h5>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>dashboard/">Inicio</a></li>
							<li class="breadcrumb-item">Facturación</li>
							<li class="breadcrumb-item active"><?php echo $h($titulo); ?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<section class="content">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-xl-8 col-lg-10">
						<div class="card card-warning card-outline">
							<div class="card-header">
								<h3 class="card-title"><i class="fas fa-power-off mr-2"></i>Estado del servicio de facturación electrónica</h3>
								<div class="card-tools">
									<span class="badge badge-warning p-2"><i class="fas fa-tools mr-1"></i>En mantenimiento</span>
								</div>
							</div>
							<div class="card-body text-center px-md-5 py-4">
								<span class="fe-mant-icono mb-3"><i class="fas fa-tools"></i></span>
								<h4 class="mb-2">Facturación electrónica en mantenimiento</h4>
								<p class="lead text-muted mb-4"><?php echo $h($mensaje); ?></p>

								<div class="row text-left justify-content-center">
									<div class="col-md-10">
										<ul class="list-unstyled fe-mant-lista mb-0">
											<li><i class="fas fa-check-circle text-success"></i> El resto del sistema funciona con normalidad.</li>
											<li><i class="fas fa-check-circle text-success"></i> Los pagos se siguen registrando y podrán facturarse cuando el servicio vuelva.</li>
											<li><i class="fas fa-pause-circle text-warning"></i> Mientras tanto no se generan ni se envían facturas al SRI.</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="card-footer d-flex flex-wrap justify-content-center">
								<a href="<?php echo APP_URL; ?>dashboard/" class="btn btn-default btn-sm m-1">
									<i class="fas fa-arrow-left mr-1"></i>Volver al inicio
								</a>
								<a href="<?php echo APP_URL; ?>pagosList/" class="btn bg-lightblue btn-sm m-1">
									<i class="fas fa-cash-register mr-1"></i>Registrar pago
								</a>
								<?php if($esAdmin){ ?>
								<a href="<?php echo APP_URL; ?>facturacionConfig/" class="btn btn-outline-warning btn-sm m-1">
									<i class="fas fa-cog mr-1"></i>Ir a configuración para levantar el servicio
								</a>
								<?php } ?>
							</div>
						</div>
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
	<script src="<?php echo APP_URL; ?>app/views/dist/js/adminlte.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/sweetalert2.all.min.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/ajax.js"></script>
	<script src="<?php echo APP_URL; ?>app/views/dist/js/main.js"></script>
  </body>
</html>
<?php
	}
