<?php
/**
 * Migra la clave de la firma (firma.key.php) a la BD, recifrada con la llave
 * endurecida, y verifica que el certificado siga siendo legible desde la BD.
 *
 * Uso (CLI):  php database/migrate_firma_clave_to_db.php
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/server.php';
require_once __DIR__ . '/../autoload.php';

use app\controllers\facturasController;

$ctrl = new facturasController();

$resultado = $ctrl->migrarClaveFirmaSriABd();
echo "Migracion de la clave: {$resultado}\n";

// Verificacion: el certificado debe seguir leyendose (usa la clave desde la BD)
$cert = $ctrl->obtenerInfoCertificadoSri();
echo "Estado certificado tras migracion: " . ($cert['estado'] ?? '?') . "\n";
echo "RUC certificado: " . ($cert['ruc'] ?? '') . " | Titular: " . ($cert['titular'] ?? '') . "\n";
