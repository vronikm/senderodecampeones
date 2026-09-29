-- 007_facturacion_servicio_y_ruc_proveedor.sql
-- Portado de cdjg (010_facturacion_servicio_activo + 012_sri_ruc_proveedor).
--
-- 1) Interruptor para bajar y levantar el servicio de facturacion electronica.
--    Con servicio_activo = 0 el modulo queda en mantenimiento: no se generan ni
--    se envian facturas al SRI y las pantallas muestran un aviso. La pantalla de
--    configuracion sigue accesible, que es donde se vuelve a levantar.
--    mantenimiento_mensaje permite un texto propio; vacio = mensaje por defecto.
--
-- 2) RUC del proveedor del sistema de facturacion electronica.
--    Resolucion SRI NAC-DGERCGC26-00000027 (27-jul-2026, R.O. 335 Quinto
--    Suplemento del 28-jul-2026), articulo 5 "Requisito adicional en
--    comprobantes electronicos": quien emite con un sistema provisto por un
--    tercero debe incluir el RUC de ese proveedor en la informacion adicional
--    del comprobante.
--
--    Se guardan dos valores:
--      ruc_proveedor           el numero que va en el comprobante
--      etiqueta_ruc_proveedor  el nombre del campoAdicional en el XML
--
--    La etiqueta es configurable a proposito: el literal exacto lo define la
--    Ficha Tecnica de Comprobantes Electronicos vigente, y dejarlo fijo en el
--    codigo obligaria a un despliegue si el SRI lo precisa distinto.
--
--    Idempotente: se puede ejecutar varias veces.


SET @db := DATABASE();

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'facturas_electronicas_config'
        AND COLUMN_NAME = 'servicio_activo') = 0,
    'ALTER TABLE facturas_electronicas_config ADD COLUMN servicio_activo TINYINT(1) NOT NULL DEFAULT 1 AFTER config_lock',
    'SELECT ''servicio_activo ya existe'' AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'facturas_electronicas_config'
        AND COLUMN_NAME = 'mantenimiento_mensaje') = 0,
    'ALTER TABLE facturas_electronicas_config ADD COLUMN mantenimiento_mensaje VARCHAR(300) NOT NULL DEFAULT '''' AFTER servicio_activo',
    'SELECT ''mantenimiento_mensaje ya existe'' AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'facturas_electronicas_config'
        AND COLUMN_NAME = 'ruc_proveedor') = 0,
    'ALTER TABLE facturas_electronicas_config ADD COLUMN ruc_proveedor VARCHAR(13) NOT NULL DEFAULT '''' AFTER contribuyente_rimpe',
    'SELECT ''ruc_proveedor ya existe'' AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'facturas_electronicas_config'
        AND COLUMN_NAME = 'etiqueta_ruc_proveedor') = 0,
    'ALTER TABLE facturas_electronicas_config ADD COLUMN etiqueta_ruc_proveedor VARCHAR(300) NOT NULL DEFAULT ''RUC Proveedor'' AFTER ruc_proveedor',
    'SELECT ''etiqueta_ruc_proveedor ya existe'' AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
