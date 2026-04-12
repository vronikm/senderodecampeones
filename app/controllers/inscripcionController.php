<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\TokenHelper;

class inscripcionController extends mainModel
{
    /**
     * Genera el enlace de inscripción para una sede.
     */
    public function generarEnlaceControlador()
    {
        $sede_id    = intval($this->limpiarCadena($_POST['sede_id'] ?? '0'));
        $horas      = intval($this->limpiarCadena($_POST['horas_vigencia'] ?? '72'));

        if ($sede_id <= 0) {
            return json_encode([
                'tipo'   => 'simple',
                'titulo' => 'Error',
                'texto'  => 'Debe seleccionar una sede.',
                'icono'  => 'error'
            ]);
        }

        if ($horas < 1 || $horas > 720) {
            $horas = 72;
        }

        $expiraEnSegundos = $horas * 3600;

        // Obtener nombre de la sede
        $datosSede = $this->seleccionarDatos("Unico", "general_sede", "sede_id", $sede_id);
        if ($datosSede->rowCount() === 0) {
            return json_encode([
                'tipo'   => 'simple',
                'titulo' => 'Error',
                'texto'  => 'La sede seleccionada no existe.',
                'icono'  => 'error'
            ]);
        }

        $sede       = $datosSede->fetch(\PDO::FETCH_ASSOC);
        $sedeNombre = $sede['sede_nombre'];

        $urlFormulario   = TokenHelper::generarURL($sede_id, $expiraEnSegundos);
        $enlaceWhatsApp  = TokenHelper::generarEnlaceWhatsApp($sede_id, $sedeNombre, $expiraEnSegundos);

        return json_encode([
            'tipo'            => 'enlace',
            'titulo'          => 'Enlace generado',
            'texto'           => 'El enlace de inscripción para la sede ' . $sedeNombre . ' ha sido generado.',
            'icono'           => 'success',
            'url_formulario'  => $urlFormulario,
            'enlace_whatsapp' => $enlaceWhatsApp,
            'sede_nombre'     => $sedeNombre,
            'vigencia_horas'  => $horas
        ]);
    }

    /**
     * Lista las sedes activas para el select.
     */
    public function listarSedesActivas()
    {
        $consulta = $this->ejecutarConsulta("SELECT sede_id, sede_nombre FROM general_sede ORDER BY sede_nombre");
        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }
}
