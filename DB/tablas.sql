-- sgpc.calles definition

CREATE TABLE `calles` (
  `id_calle` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_calle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.empresas definition

CREATE TABLE `empresas` (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.hoteles definition

CREATE TABLE `hoteles` (
  `id_hotel` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `direccion` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id_hotel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.nacionalidades definition

CREATE TABLE `nacionalidades` (
  `id_nacionalidad` int NOT NULL AUTO_INCREMENT,
  `nacionalidad` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_nacionalidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.recorridos definition

CREATE TABLE `recorridos` (
  `id_recorrido` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_recorrido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.usuarios_tipos definition

CREATE TABLE `tipos_usuarios` (
  `id_tipo_usuario` int NOT NULL AUTO_INCREMENT,
  `tipo_usuario` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_tipo_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.calles_recorridos definition

CREATE TABLE `calles_recorridos` (
  `id_calle_recorrido` int NOT NULL AUTO_INCREMENT,
  `id_recorrido` int NOT NULL,
  `id_calle` int NOT NULL,
  PRIMARY KEY (`id_calle_recorrido`),
  KEY `calles_recorridos_FK` (`id_recorrido`),
  KEY `calles_recorridos_FK_1` (`id_calle`),
  CONSTRAINT `calles_recorridos_FK` FOREIGN KEY (`id_recorrido`) REFERENCES `recorridos` (`id_recorrido`),
  CONSTRAINT `calles_recorridos_FK_1` FOREIGN KEY (`id_calle`) REFERENCES `calles` (`id_calle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.choferes definition

CREATE TABLE `choferes` (
  `id_chofer` int NOT NULL AUTO_INCREMENT,
  `dni` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_nacionalidad` int NOT NULL,
  PRIMARY KEY (`id_chofer`),
  KEY `choferes_FK` (`id_nacionalidad`),
  CONSTRAINT `choferes_FK` FOREIGN KEY (`id_nacionalidad`) REFERENCES `nacionalidades` (`id_nacionalidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.puntos_detencion definition

CREATE TABLE `puntos_detencion` (
  `id_punto_detencion` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_calle` int NOT NULL,
  PRIMARY KEY (`id_punto_detencion`),
  KEY `puntos_detencion_FK` (`id_calle`),
  CONSTRAINT `puntos_detencion_FK` FOREIGN KEY (`id_calle`) REFERENCES `calles` (`id_calle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.servicios definition

CREATE TABLE `servicios` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `interno` int NOT NULL,
  `dominio` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_servicio`),
  KEY `servicios_FK` (`id_empresa`),
  CONSTRAINT `servicios_FK` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.usuarios definition

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `cargo` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `sector` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `contrasenia` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_tipo_usuario` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  KEY `usuarios_FK` (`id_tipo_usuario`),
  CONSTRAINT `usuarios_FK` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipos_usuarios` (`id_tipo_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.permisos definition

CREATE TABLE `permisos` (
  `id_permiso` int NOT NULL AUTO_INCREMENT,
  `id_chofer` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_servicio` int NOT NULL,
  `tipo` enum('charter','linea','otros') COLLATE utf8mb4_spanish2_ci NOT NULL,
  `fecha_reserva` timestamp NOT NULL,
  `fecha_emision` timestamp NOT NULL,
  `es_arribo` tinyint(1) NOT NULL,
  `observacion` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_permiso`),
  KEY `permisos_FK` (`id_chofer`),
  KEY `permisos_FK_1` (`id_usuario`),
  KEY `permisos_FK_2` (`id_servicio`),
  CONSTRAINT `permisos_FK` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`),
  CONSTRAINT `permisos_FK_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `permisos_FK_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.recorridos_permisos definition

CREATE TABLE `recorridos_permisos` (
  `id_recorrido_permiso` int NOT NULL AUTO_INCREMENT,
  `id_permiso` int NOT NULL,
  `id_recorrido` int NOT NULL,
  PRIMARY KEY (`id_recorrido_permiso`),
  KEY `recorridos_permisos_FK` (`id_permiso`),
  KEY `recorridos_permisos_FK_1` (`id_recorrido`),
  CONSTRAINT `recorridos_permisos_FK` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`),
  CONSTRAINT `recorridos_permisos_FK_1` FOREIGN KEY (`id_recorrido`) REFERENCES `recorridos` (`id_recorrido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;


-- sgpc.reservas_puntos definition

CREATE TABLE `reservas_puntos` (
  `id_reserva_punto` int NOT NULL AUTO_INCREMENT,
  `fecha_horario` timestamp NOT NULL,
  `id_hotel` int DEFAULT NULL,
  `id_permiso` int NOT NULL,
  `id_punto_detencion` int NOT NULL,
  PRIMARY KEY (`id_reserva_punto`),
  KEY `reservas_puntos_FK` (`id_punto_detencion`),
  KEY `reservas_puntos_FK_1` (`id_hotel`),
  KEY `reservas_puntos_FK_2` (`id_permiso`),
  CONSTRAINT `reservas_puntos_FK` FOREIGN KEY (`id_punto_detencion`) REFERENCES `puntos_detencion` (`id_punto_detencion`),
  CONSTRAINT `reservas_puntos_FK_1` FOREIGN KEY (`id_hotel`) REFERENCES `hoteles` (`id_hotel`),
  CONSTRAINT `reservas_puntos_FK_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;