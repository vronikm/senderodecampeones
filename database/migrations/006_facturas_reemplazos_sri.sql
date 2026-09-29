ALTER TABLE facturas_electronicas
    ADD COLUMN factura_origen_id INT NULL AFTER representante_id,
    ADD COLUMN reemplazada_por_id INT NULL AFTER factura_origen_id,
    ADD COLUMN reemplazo_motivo VARCHAR(80) NULL AFTER reemplazada_por_id,
    ADD COLUMN reemplazo_confirmado_at DATETIME NULL AFTER reemplazo_motivo,
    ADD COLUMN reemplazo_confirmado_by INT NULL AFTER reemplazo_confirmado_at,
    ADD UNIQUE KEY uk_fe_factura_origen (factura_origen_id),
    ADD KEY idx_fe_reemplazada_por (reemplazada_por_id);
