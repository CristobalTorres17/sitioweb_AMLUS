SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE IF NOT EXISTS `adm_rol` (
  `rol` int NOT NULL AUTO_INCREMENT,
  `con` varchar(35) NOT NULL,
  PRIMARY KEY (`rol`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
CREATE TABLE IF NOT EXISTS `adm_usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usu` varchar(25) NOT NULL,
  `pas` tinytext NOT NULL,
  `rol` int NOT NULL,
  `tiq` varchar(20) NOT NULL,
  `nom` varchar(40) NOT NULL,
  `ape` varchar(40) NOT NULL,
  `tel` varchar(9) NOT NULL,
  `cel` varchar(9) NOT NULL,
  `mai` tinytext NOT NULL,
  `foto` longtext NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `usu` (`usu`) USING BTREE,
  KEY `rol` (`rol`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
CREATE TABLE IF NOT EXISTS `anno` (
  `id_anno` int NOT NULL AUTO_INCREMENT,
  `id_subnivel` int NOT NULL,
  `anno` int NOT NULL,
  `ruta` varchar(250) NOT NULL,
  PRIMARY KEY (`id_anno`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `archivos` (
  `id_archivo` int NOT NULL AUTO_INCREMENT,
  `id_menu` int NOT NULL,
  `id_submenu` int NOT NULL,
  `id_carpeta` int NOT NULL,
  `id_nivel` int NOT NULL,
  `id_subnivel` int NOT NULL,
  `id_anno` int NOT NULL,
  `nombre` longtext NOT NULL,
  `archivo` longtext NOT NULL,
  `anexo` longtext NOT NULL,
  `archivoAnexo` longtext NOT NULL,
  `fechaPublicacion` date NOT NULL,
  `ruta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_archivo`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `carpeta` (
  `id_carpeta` int NOT NULL AUTO_INCREMENT,
  `id_submenu` int NOT NULL,
  `carpeta` varchar(150) NOT NULL,
  `ruta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_carpeta`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `eventos` (
  `id_evento` int NOT NULL AUTO_INCREMENT,
  `id_usu` int unsigned NOT NULL,
  `correlativo` int DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `objetivo` longtext,
  `nombre` longtext,
  `listado` varchar(255) DEFAULT NULL,
  `estado` varchar(15) DEFAULT NULL,
  `eliminado` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id_evento`) USING BTREE,
  KEY `id_usu` (`id_usu`) USING BTREE,
  CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_usu`) REFERENCES `adm_usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
CREATE TABLE IF NOT EXISTS `galeria` (
  `id_galeria` int NOT NULL AUTO_INCREMENT,
  `id_evento` int DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `estado` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `elimindo` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id_galeria`) USING BTREE,
  KEY `id_evento` (`id_evento`) USING BTREE,
  CONSTRAINT `galeria_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
CREATE TABLE IF NOT EXISTS `historial` (
  `id_historial` int unsigned NOT NULL AUTO_INCREMENT,
  `usu` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `accion` varchar(250) NOT NULL,
  `lugar` varchar(250) NOT NULL,
  `id_lugar` varchar(5) DEFAULT NULL,
  `estado` varchar(150) NOT NULL,
  PRIMARY KEY (`id_historial`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
CREATE TABLE IF NOT EXISTS `menu` (
  `id_menu` int unsigned NOT NULL AUTO_INCREMENT,
  `menu` varchar(250) NOT NULL,
  `ruta` varchar(150) NOT NULL,
  PRIMARY KEY (`id_menu`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
CREATE TABLE IF NOT EXISTS `nivel` (
  `id_nivel` int NOT NULL AUTO_INCREMENT,
  `id_carpeta` int NOT NULL,
  `nivel` varchar(200) NOT NULL,
  `ruta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_nivel`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `noticias` (
  `id_noti` int unsigned NOT NULL AUTO_INCREMENT,
  `id_catenoti` int NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `encabezado` longtext NOT NULL,
  `detalle` longtext NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `fecha` varchar(250) NOT NULL,
  `hora` varchar(250) NOT NULL,
  `publicador` varchar(250) NOT NULL,
  PRIMARY KEY (`id_noti`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
CREATE TABLE IF NOT EXISTS `submenu` (
  `id_submenu` int unsigned NOT NULL AUTO_INCREMENT,
  `id_menu` int NOT NULL,
  `submenu` varchar(250) NOT NULL,
  `rutaSubmenu` varchar(150) NOT NULL,
  PRIMARY KEY (`id_submenu`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `subnivel` (
  `id_subNivel` int NOT NULL AUTO_INCREMENT,
  `id_nivel` int NOT NULL,
  `subnivel` varchar(200) NOT NULL,
  `ruta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_subNivel`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `vision` (
  `id_vision` int unsigned NOT NULL AUTO_INCREMENT,
  `vision` longtext NOT NULL,
  PRIMARY KEY (`id_vision`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
SET FOREIGN_KEY_CHECKS=1;
