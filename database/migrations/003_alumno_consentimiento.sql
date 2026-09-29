-- ============================================================
--  Registro de consentimientos LOPDP
-- ============================================================
--  Antes de esto la única huella era `alumno_representante.repre_firmado`,
--  un char(1) que en este sistema significa "firmó el formulario LOPDP en
--  papel" (ver formularioLPPDF + estadofirmado). No servía para el
--  consentimiento en línea: no distingue qué se autorizó, ni cuándo, ni
--  sobre qué texto, ni de qué menor.
--
--  Una fila por consentimiento otorgado o revocado. Nunca se actualiza:
--  una revocatoria es una fila nueva con consent_otorgado = 'N', de modo
--  que el historial completo queda demostrable.
--
--  Origen de las filas:
--    FORMULARIO -> senderodecampeones_form (inscripción en línea)
--    ADMIN      -> panel, vista consentimientoList
-- ============================================================

CREATE TABLE IF NOT EXISTS `alumno_consentimiento` (
  `consent_id`         int          NOT NULL AUTO_INCREMENT,
  `consent_alumnoid`   int          NOT NULL COMMENT 'Menor sobre el que recae la autorización',
  `consent_repreid`    int          NOT NULL COMMENT 'Representante legal que la otorga',
  `consent_tipo`       varchar(10)  NOT NULL COMMENT 'DATOS | IMAGEN',
  `consent_otorgado`   char(1)      NOT NULL DEFAULT 'S' COMMENT 'S=otorga  N=revoca',
  `consent_origen`     varchar(12)  NOT NULL COMMENT 'FORMULARIO=inscripción en línea  ADMIN=registrado en el panel',
  `consent_usuario`    varchar(50)  DEFAULT NULL COMMENT 'Usuario del panel cuando el origen es ADMIN',
  `consent_fecha`      datetime     NOT NULL,
  `consent_ip`         varchar(45)  DEFAULT NULL COMMENT 'IP desde la que se aceptó (solo origen FORMULARIO)',
  `consent_textohash`  varchar(64)  DEFAULT NULL COMMENT 'SHA-256 del texto exacto aceptado',
  `consent_observacion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`consent_id`),
  KEY `idx_consent_alumno` (`consent_alumnoid`),
  KEY `idx_consent_repre` (`consent_repreid`),
  KEY `idx_consent_tipo` (`consent_tipo`, `consent_otorgado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
