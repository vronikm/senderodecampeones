-- ============================================================
--  Menú y permisos para la vista Consentimientos LOPDP
-- ============================================================
--  Los roles 1 y 2 (Super Administrador / Administrador) reciben el ítem
--  desde app/views/inc/menu_admin.php, que es HTML fijo. El resto de roles
--  arma el sidebar desde seguridad_menu + seguridad_permiso
--  (menuController::ObtenerMenu), así que sin estas filas la vista existe
--  y responde, pero no aparece en el menú de esos usuarios.
--
--  Roles habilitados aquí: Gerente (supervisión) y Asistente (es quien
--  recibe el formulario LOPDP firmado en papel y registra el consentimiento
--  con origen ADMIN).
--
--  Las tablas son MyISAM: no hay transacciones, por eso cada sentencia es
--  idempotente por sí sola y el script se puede volver a correr sin duplicar.
--
--  Requiere: 003_alumno_consentimiento.sql
-- ============================================================

-- ------------------------------------------------------------
-- 1) Ítem de menú (se agrega al final del primer nivel)
-- ------------------------------------------------------------
SET @orden := (SELECT COALESCE(MAX(menu_orden), 0) + 1
                 FROM seguridad_menu
                WHERE menu_padreid = 0);

INSERT INTO seguridad_menu
       (menu_nombre, menu_orden, menu_padreid, menu_hijo, menu_vista, menu_icono, menu_estado)
SELECT 'Consentimientos LOPDP', @orden, 0, 'N', 'consentimientoList',
       'nav-icon fas fa-user-shield text-warning', 'A'
  FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM seguridad_menu WHERE menu_vista = 'consentimientoList');

-- ------------------------------------------------------------
-- 2) Permisos por rol
-- ------------------------------------------------------------
--  Se emparejan por NOMBRE de rol y no por id: los ids pueden no coincidir
--  entre local y producción. Si en producción los roles se llaman distinto,
--  este INSERT no agrega nada y la verificación del paso 3 lo deja ver.
SET @menuid := (SELECT menu_id FROM seguridad_menu
                 WHERE menu_vista = 'consentimientoList' LIMIT 1);

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
 WHERE M.menu_vista = 'consentimientoList'
 ORDER BY R.rol_id;
