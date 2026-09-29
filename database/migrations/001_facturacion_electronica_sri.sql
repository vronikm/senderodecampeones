CREATE TABLE IF NOT EXISTS facturas_electronicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT NOT NULL,
    representante_id INT NOT NULL,
    factura_origen_id INT DEFAULT NULL,
    reemplazada_por_id INT DEFAULT NULL,
    reemplazo_motivo VARCHAR(80) DEFAULT NULL,
    reemplazo_confirmado_at DATETIME DEFAULT NULL,
    reemplazo_confirmado_by INT DEFAULT NULL,
    clave_acceso VARCHAR(49) NOT NULL UNIQUE,
    tipo_comprobante CHAR(2) NOT NULL DEFAULT '01',
    establecimiento CHAR(3) NOT NULL,
    punto_emision CHAR(3) NOT NULL,
    secuencial CHAR(9) NOT NULL,
    fecha_emision DATE NOT NULL,
    ambiente CHAR(1) NOT NULL DEFAULT '1',
    tipo_emision CHAR(1) NOT NULL DEFAULT '1',
    cliente_tipo_identificacion CHAR(2) NOT NULL,
    cliente_identificacion VARCHAR(20) NOT NULL,
    cliente_razon_social VARCHAR(300) NOT NULL,
    cliente_direccion VARCHAR(300) DEFAULT NULL,
    cliente_email VARCHAR(200) DEFAULT NULL,
    cliente_telefono VARCHAR(50) DEFAULT NULL,
    subtotal_iva DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    subtotal_0 DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    subtotal_no_objeto DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    subtotal_exento DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    descuento DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    iva DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    estado_sri ENUM('PENDIENTE','GENERADA','FIRMADA','ENVIADA','RECIBIDA','DEVUELTA','AUTORIZADO','NO_AUTORIZADO','ERROR','ANULADA') NOT NULL DEFAULT 'GENERADA',
    xml_generado TEXT DEFAULT NULL,
    xml_firmado TEXT DEFAULT NULL,
    xml_autorizado TEXT DEFAULT NULL,
    ride_html TEXT DEFAULT NULL,
    numero_autorizacion VARCHAR(49) DEFAULT NULL,
    fecha_autorizacion DATETIME DEFAULT NULL,
    mensaje_error TEXT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_fe_alumno (alumno_id),
    INDEX idx_fe_representante (representante_id),
    INDEX idx_fe_fecha (fecha_emision),
    INDEX idx_fe_estado (estado_sri),
    INDEX idx_fe_numero (establecimiento, punto_emision, secuencial),
    UNIQUE KEY uk_fe_factura_origen (factura_origen_id),
    INDEX idx_fe_reemplazada_por (reemplazada_por_id),
    INDEX idx_fe_cliente (cliente_identificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facturas_electronicas_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_electronica_id INT NOT NULL,
    pago_id INT DEFAULT NULL,
    codigo_principal VARCHAR(25) NOT NULL,
    descripcion VARCHAR(300) NOT NULL,
    cantidad DECIMAL(14,6) NOT NULL DEFAULT 1.000000,
    precio_unitario DECIMAL(14,6) NOT NULL DEFAULT 0.000000,
    descuento DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    precio_total_sin_impuesto DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    iva_tarifa DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    iva_codigo_porcentaje CHAR(4) NOT NULL DEFAULT '0',
    iva_valor DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fed_factura (factura_electronica_id),
    INDEX idx_fed_pago (pago_id),
    CONSTRAINT fk_fed_factura FOREIGN KEY (factura_electronica_id) REFERENCES facturas_electronicas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facturas_electronicas_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_electronica_id INT NOT NULL,
    forma_pago CHAR(2) NOT NULL DEFAULT '20',
    total DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    plazo INT DEFAULT NULL,
    unidad_tiempo VARCHAR(20) DEFAULT 'dias',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fep_factura (factura_electronica_id),
    CONSTRAINT fk_fep_factura FOREIGN KEY (factura_electronica_id) REFERENCES facturas_electronicas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facturas_electronicas_secuenciales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_comprobante CHAR(2) NOT NULL DEFAULT '01',
    establecimiento CHAR(3) NOT NULL,
    punto_emision CHAR(3) NOT NULL,
    secuencial_actual INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_fe_secuencial (tipo_comprobante, establecimiento, punto_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO facturas_electronicas_secuenciales (tipo_comprobante, establecimiento, punto_emision, secuencial_actual)
VALUES ('01', '003', '004', 0)
ON DUPLICATE KEY UPDATE secuencial_actual = secuencial_actual;
