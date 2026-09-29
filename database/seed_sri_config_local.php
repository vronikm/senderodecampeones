<?php
/**
 * Poblado LOCAL (no versionar valores) de las tablas de configuracion SRI
 * a partir de la config efectiva actual (config/sri.php + config/sri_local.php)
 * y de los metadatos reales del certificado en storage/certificados/.
 *
 * Uso (CLI):  php database/seed_sri_config_local.php
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/server.php';
require_once __DIR__ . '/../autoload.php';

use app\controllers\facturasController;

$ctrl = new facturasController();
$cfg  = $ctrl->obtenerConfiguracionSri();
$cert = $ctrl->obtenerInfoCertificadoSri();
$em   = $cfg['emisor'] ?? [];

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

/* ---------- Configuracion del emisor ---------- */
$pdo->prepare(
    "UPDATE facturas_electronicas_config SET
        ambiente=:ambiente, tipo_emision=:tipo_emision, iva_tarifa_default=:iva,
        forma_pago_default=:fp, valores_incluyen_iva=:vii, ruc=:ruc,
        razon_social=:rs, nombre_comercial=:nc, direccion_matriz=:dm,
        direccion_establecimiento=:de, codigo_establecimiento=:ce, punto_emision=:pe,
        obligado_contabilidad=:oc, contribuyente_especial=:cesp,
        agente_retencion=:ar, contribuyente_rimpe=:rimpe
     WHERE config_lock='X'"
)->execute([
    ':ambiente'      => (string)($cfg['ambiente'] ?? '1'),
    ':tipo_emision'  => (string)($cfg['tipo_emision'] ?? '1'),
    ':iva'           => (float)($cfg['iva_tarifa_default'] ?? 0),
    ':fp'            => (string)($cfg['forma_pago_default'] ?? '20'),
    ':vii'           => !empty($cfg['valores_incluyen_iva']) ? 1 : 0,
    ':ruc'           => (string)($em['ruc'] ?? ''),
    ':rs'            => (string)($em['razon_social'] ?? ''),
    ':nc'            => (string)($em['nombre_comercial'] ?? ''),
    ':dm'            => (string)($em['direccion_matriz'] ?? ''),
    ':de'            => (string)($em['direccion_establecimiento'] ?? ''),
    ':ce'            => (string)($em['codigo_establecimiento'] ?? '001'),
    ':pe'            => (string)($em['punto_emision'] ?? '001'),
    ':oc'            => (strtoupper((string)($em['obligado_contabilidad'] ?? 'NO')) === 'SI') ? 'SI' : 'NO',
    ':cesp'          => (string)($em['contribuyente_especial'] ?? ''),
    ':ar'            => (string)($em['agente_retencion'] ?? ''),
    ':rimpe'         => (string)($em['contribuyente_rimpe'] ?? ''),
]);

/* ---------- Metadatos del certificado ---------- */
$toDateTime = static function ($s) {
    $s = trim((string)$s);
    if ($s === '') return null;
    $ts = strtotime($s);
    return $ts ? date('Y-m-d H:i:s', $ts) : null;
};

$pdo->prepare(
    "UPDATE facturas_electronicas_certificado SET
        archivo=:archivo, nombre_original=:nombre, ruc_certificado=:ruc,
        razon_social=:rs, serial=:serial, valido_desde=:vd, valido_hasta=:vh, estado=:estado
     WHERE cert_lock='X'"
)->execute([
    ':archivo' => (string)($cert['ruta'] ?? ''),
    ':nombre'  => (string)($cert['archivo'] ?? ''),
    ':ruc'     => (string)($cert['ruc'] ?? ''),
    ':rs'      => (string)($cert['titular'] ?? ''),
    ':serial'  => (string)($cert['serial'] ?? ''),
    ':vd'      => $toDateTime($cert['valido_desde'] ?? ''),
    ':vh'      => $toDateTime($cert['valido_hasta'] ?? ''),
    ':estado'  => (string)($cert['estado'] ?? 'NO_CONFIGURADO'),
]);

echo "Poblado OK\n";
