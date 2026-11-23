INSERT IGNORE INTO adm_rol (rol, con) VALUES (1, 'Super');

INSERT IGNORE INTO adm_usuarios (id, usu, pas, rol, tiq, nom, ape, tel, cel, mai, foto)
VALUES (1, 'practicante', MD5('unionlocal123!'), 1, '', 'Cuenta', 'de prueba', '', '', 'practicante@example.test', '');

INSERT IGNORE INTO eventos (id_evento, id_usu, correlativo, fecha, tipo, objetivo, nombre, listado, estado, eliminado)
VALUES (1, 1, 1, '2026-01-15', 'Noticias', 'Texto ficticio para verificar la conexión entre el panel y el portal.', 'Noticia de prueba La Unión Sur', 'demo.svg', 'Activo', '1');
