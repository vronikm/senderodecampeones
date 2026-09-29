-- ============================================================
--  Menú y permisos para la vista Inscripciones en línea pendientes
-- ============================================================
--  Los roles 1 y 2 reciben el ítem desde app/views/inc/menu_admin.php.
--  El resto lo toma de seguridad_menu + seguridad_permiso.
--
--  Roles habilitados: los mismos de Consentimientos LOPDP, porque es el
--  mismo público: Asistente completa los datos que el formulario público
--  no captura (horario, rubro de inscripción, contacto de emergencia) y
--  Gerente supervisa el pendiente.
--
--  Las tablas son MyISAM: no hay transacciones, por eso cada sentencia es
--  idempotente por sí sola y el script se puede volver a correr sin duplicar.
--
--  Requiere: 003_alumno_consentimiento.sql
--  (la vista identifica los alumnos por su consentimiento origen FORMULARIO)
-- ============================================================

-- ------------------------------------------------------------
-- 1) Ítem de menú (se agrega al final del primer nivel)
-- ------------------------------------------------------------
SET @orden := (SELECT COALESCE(MAX(menu_orden), 0) + 1
                 FROM seguridad_menu
                WHERE menu_padreid = 0);

INSERT INTO seguridad_menu
       (menu_nombre, menu_orden, menu_padreid, menu_hijo, menu_vista, menu_icono, menu_estado)
SELECT 'Inscripciones pendientes', @orden, 0, 'N', 'inscripcionPendientes',
       'nav-icon fas fa-user-clock text-warning', 'A'
  FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM seguridad_menu WHERE menu_vista = 'inscripcionPendientes');

-- ------------------------------------------------------------
-- 2) Permisos por rol
-- ------------------------------------------------------------
--  Se emparejan por NOMBRE de rol y no por id: los ids pueden no coincidir
--  entre local y producción. Si en producción los roles se llaman distinto,
--  este INSERT no agrega nada y la verificación del paso 3 lo deja ver.
SET @menuid := (SELECT menu_id FROM seguridad_menu
                 WHERE menu_vista = 'inscripcionPendientes' LIMIT 1);

INSERT INTO seguridad_permiso (permiso_rolid, permiso_menuid, permiso_estado)
SELECT R.rol_id, @menuid, 'A'
  FROM seguridad_rol R
 WHERE R.rol_nombre IN ('Gerente', 'Asistente')
   AND R.rol_estado = 'A'
   AND NOT EXISTS (SELECT 1 FROM seguridad_permiso P
                    WHERE P.permiso_rolid  = R.rol_id
                      AND P.permiso_menuid = @menuid);

-- ------------------------------------------------------------
-- 3) Verificación — debe devolver una fila por rol habilitado
-- ------------------------------------------------------------
SELECT M.menu_id,
       M.menu_vista,
       M.menu_orden,
       R.rol_id,
       R.rol_nombre,
       P.permiso_estado
  FROM seguridad_menu M
  INNER JOIN seguridad_permiso P ON P.permiso_menuid = M.menu_id
  INNER JOIN seguridad_rol     R ON R.rol_id        = P.permiso_rolid
 WHERE M.menu_vista = 'inscripcionPendientes'
 ORDER BY R.rol_id;
