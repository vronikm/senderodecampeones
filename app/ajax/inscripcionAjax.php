<?php

    require_once "../../config/app.php";
    require_once "../views/inc/session_start.php";
    require_once "../../autoload.php";

    use app\controllers\inscripcionController;

    if (isset($_POST['modulo_inscripcion'])) {

        $insInscripcion = new inscripcionController();

        if ($_POST['modulo_inscripcion'] == "generar_enlace") {
            echo $insInscripcion->generarEnlaceControlador();
        }

    } else {
        session_destroy();
        header("Location: " . APP_URL . "login/");
    }
