<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\facturasController;

	if(isset($_POST['modulo_facturas'])){

		$insAlumno  = new facturasController();

		if($_POST['modulo_facturas']=="ACTUALIZAR_REPRESENTANTE"){
			echo $insAlumno ->actualizarRepresentanteFactura();
		}	
		
		if($_POST['modulo_facturas']=="CONSULTAR_FACTURAS"){
			$alumno = $_POST['alumno'];
			$fecha_inicio = $_POST['fecha_inicio'];
			$fecha_fin = $_POST['fecha_fin'];

			// Recuperar información representante
			$datos = $insAlumno->BuscarAlumnoFactura($alumno, $fecha_inicio, $fecha_fin);

			// Listar pagos y facturas en HTML
			$pagos = $insAlumno->listarPagosFactura($alumno, $fecha_inicio, $fecha_fin);
			$facturas = $insAlumno->listarPagosFactura($alumno, $fecha_inicio, $fecha_fin);

			// Preparar representante (si la consulta trae registros)
			$representante = [];
			if(!empty($datos)){
				$fila = $datos->fetch(); 
				$representante = [
					"nombre" => $fila['representante'],
					"identificacion" => $fila['repre_identificacion'],
					"direccion" => $fila['repre_direccion'],
					"correo" => $fila['repre_correo'],
					"celular" => $fila['repre_celular'],
					"pagos" => $fila['pagos'],
					"facturas" => 0 // si quieres luego contar facturas, aquí lo llenas
				];
			}

			// Devolver como JSON
			echo json_encode([
				"pagos" => $pagos,
				"facturas" => $facturas,
				"representante" => $representante
			]);
			exit();
		}
	

	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}