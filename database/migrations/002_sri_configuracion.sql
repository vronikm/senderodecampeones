-- =====================================================================
-- 002_sri_configuracion.sql
-- Tablas para administrar la vista "Configuracion SRI" (facturacionConfig)
-- Reemplazan el almacenamiento en archivos:
--   facturas_electronicas_config       <- config/sri_local.php
--   facturas_electronicas_certificado  <- metadatos de storage/certificados/firma.p12
--   facturas_electronicas_forma_pago   <- catalogo 'formas_pago' de config/sri.php
-- El binario .p12 y la clave cifrada permanecen en storage/ (fuera de la BD).
-- =====================================================================

-- ---------------------------------------------------------------------
-- Configuracion del emisor y parametros SRI (fila unica)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS facturas_electronicas_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_lock CHAR(1) NOT NULL DEFAULT 'X',
    ambiente CHAR(1) NOT NULL DEFAULT '1',
    tipo_emision CHAR(1) NOT NULL DEFAULT '1',
    iva_tarifa_default DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    forma_pago_default CHAR(2) NOT NULL DEFAULT '20',
    valores_incluyen_iva TINYINT(1) NOT NULL DEFAULT 1,
    secuencial_inicio INT NOT NULL DEFAULT 1,
    ruc VARCHAR(13) NOT NULL DEFAULT '',
    razon_social VARCHAR(300) NOT NULL DEFAULT '',
    nombre_comercial VARCHAR(300) NOT NULL DEFAULT '',
    direccion_matriz VARCHAR(300) NOT NULL DEFAULT '',
    direccion_establecimiento VARCHAR(300) NOT NULL DEFAULT '',
    codigo_establecimiento CHAR(3) NOT NULL DEFAULT '001',
    punto_emision CHAR(3) NOT NULL DEFAULT '001',
    obligado_contabilidad ENUM('SI','NO') NOT NULL DEFAULT 'NO',
    contribuyente_especial VARCHAR(13) NOT NULL DEFAULT '',
    agente_retencion VARCHAR(8) NOT NULL DEFAULT '',
    contribuyente_rimpe VARCHAR(300) NOT NULL DEFAULT '',
    updated_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_config_lock (config_lock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Garantiza la existencia de la fila unica (sin pisar datos si ya existe)
INSERT INTO facturas_electronicas_config (config_lock) VALUES ('X')
ON DUPLICATE KEY UPDATE config_lock = config_lock;

-- ---------------------------------------------------------------------
-- Metadatos de la firma electronica (.p12). El archivo y la clave
-- cifrada siguen viviendo en storage/certificados/ (no en la BD).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS facturas_electronicas_certificado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cert_lock CHAR(1) NOT NULL DEFAULT 'X',
    archivo VARCHAR(300) NOT NULL DEFAULT '',
    nombre_original VARCHAR(255) NOT NULL DEFAULT '',
    ruc_certificado VARCHAR(13) NOT NULL DEFAULT '',
    razon_social VARCHAR(300) NOT NULL DEFAULT '',
    serial VARCHAR(100) NOT NULL DEFAULT '',
    valido_desde DATETIME DEFAULT NULL,
    valido_hasta DATETIME DEFAULT NULL,
    -- Estados que produce obtenerInfoCertificadoSri(): VALIDO, CADUCADO,
    -- NO_CONFIGURADO, SIN_CLAVE, CLAVE_INVALIDA, NO_LEIBLE, RUC_NO_COINCIDE,
    -- OPENSSL_NO_DISPONIBLE, ERROR
    estado VARCHAR(30) NOT NULL DEFAULT 'NO_CONFIGURADO',
    -- Clave del .p12 cifrada (AES-256-CBC, base64 de JSON{method,iv,data}).
    -- La llave de cifrado NO vive aqui ni en el repo: ver claveCriptoSri().
    clave_cifrada TEXT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_cert_lock (cert_lock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO facturas_electronicas_certificado (cert_lock) VALUES ('X')
ON DUPLICATE KEY UPDATE cert_lock = cert_lock;

-- ---------------------------------------------------------------------
-- Catalogo SRI de formas de pago (codigos oficiales del SRI)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS facturas_electronicas_forma_pago (
    codigo CHAR(2) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    orden INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO facturas_electronicas_forma_pago (codigo, nombre, orden) VALUES
    ('01', 'SIN UTILIZACION DEL SISTEMA FINANCIERO', 1),
    ('15', 'COMPENSACION DE DEUDAS', 2),
    ('16', 'TARJETA DE DEBITO', 3),
    ('17', 'DINERO ELECTRONICO', 4),
    ('18', 'TARJETA PREPAGO', 5),
    ('19', 'TARJETA DE CREDITO', 6),
    ('20', 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO', 7),
    ('21', 'ENDOSO DE TITULOS', 8)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), orden = VALUES(orden);
