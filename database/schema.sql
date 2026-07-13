-- ============================================================
-- SISTEMA DE GESTIÓN DE ACTIVIDADES DEPORTIVAS (Birria-MO)
-- Base de datos completa
-- Compatible con MySQL 8 y MariaDB
-- Nombre de base de datos restaurado a "eventos_deportivos"
-- para mantener continuidad con el resto del proyecto (.env, docs).
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS eventos_deportivos;

CREATE DATABASE eventos_deportivos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE eventos_deportivos;

-- ============================================================
-- 1. USUARIOS
-- ============================================================

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,

    cedula_pasaporte VARCHAR(30) NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NULL,
    fecha_nacimiento DATE NULL,

    sexo ENUM(
        'MASCULINO',
        'FEMENINO',
        'OTRO',
        'NO_ESPECIFICA'
    ) NULL,

    usuario VARCHAR(60) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,

    rol ENUM(
        'ADMINISTRADOR',
        'OPERADOR',
        'ORGANIZADOR',
        'PARTICIPANTE'
    ) NOT NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    requiere_cambio_password TINYINT(1)
        NOT NULL DEFAULT 0,

    intentos_fallidos SMALLINT UNSIGNED
        NOT NULL DEFAULT 0,

    bloqueado_hasta DATETIME NULL,
    ultimo_acceso DATETIME NULL,

    creado_por INT UNSIGNED NULL,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_usuarios_correo
        UNIQUE (correo),

    CONSTRAINT uq_usuarios_usuario
        UNIQUE (usuario),

    CONSTRAINT uq_usuarios_cedula
        UNIQUE (cedula_pasaporte),

    CONSTRAINT fk_usuarios_creado_por
        FOREIGN KEY (creado_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 2. PARTICIPANTES
-- ============================================================

CREATE TABLE participantes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,

    contacto_emergencia_nombre VARCHAR(150) NULL,
    contacto_emergencia_telefono VARCHAR(30) NULL,

    observaciones_medicas VARCHAR(500) NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_registro DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_participantes_usuario
        UNIQUE (usuario_id),

    CONSTRAINT fk_participantes_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================
-- 3. SEGURIDAD RSA
-- ============================================================

CREATE TABLE claves_rsa_usuario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,

    clave_publica MEDIUMTEXT NOT NULL,
    clave_privada_cifrada MEDIUMTEXT NOT NULL,

    algoritmo VARCHAR(50)
        NOT NULL DEFAULT 'RSA-2048',

    huella_publica VARCHAR(128) NOT NULL,

    activa TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_revocacion DATETIME NULL,

    CONSTRAINT uq_claves_rsa_huella
        UNIQUE (huella_publica),

    CONSTRAINT fk_claves_rsa_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE historial_passwords (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,

    password_hash VARCHAR(255) NOT NULL,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_historial_password_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE tokens_recuperacion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,

    token_hash VARCHAR(255) NOT NULL,

    fecha_expiracion DATETIME NOT NULL,

    utilizado TINYINT(1) NOT NULL DEFAULT 0,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_tokens_recuperacion
        UNIQUE (token_hash),

    CONSTRAINT fk_tokens_recuperacion_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- ============================================================
-- 4. BITÁCORA
-- ============================================================

CREATE TABLE bitacora (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NULL,

    modulo VARCHAR(80) NOT NULL,
    accion VARCHAR(80) NOT NULL,

    tabla_afectada VARCHAR(80) NULL,
    registro_id VARCHAR(80) NULL,

    descripcion TEXT NULL,

    datos_anteriores JSON NULL,
    datos_nuevos JSON NULL,

    direccion_ip VARCHAR(45) NULL,
    agente_usuario VARCHAR(500) NULL,

    firma_digital LONGTEXT NULL,
    algoritmo_firma VARCHAR(50) NULL,

    fecha_evento DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bitacora_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 5. DEPORTES
-- ============================================================

CREATE TABLE deportes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(500) NULL,

    es_equipo TINYINT(1) NOT NULL DEFAULT 0,

    minimo_jugadores SMALLINT UNSIGNED NULL,
    maximo_jugadores SMALLINT UNSIGNED NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_deportes_nombre
        UNIQUE (nombre)
) ENGINE = InnoDB;

-- ============================================================
-- 6. INSTALACIONES
-- ============================================================

CREATE TABLE instalaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,

    tipo ENUM(
        'CANCHA',
        'GIMNASIO',
        'PISCINA',
        'ESTADIO',
        'PISTA',
        'SALON',
        'OTRO'
    ) NOT NULL,

    descripcion TEXT NULL,

    direccion VARCHAR(255) NOT NULL,
    provincia VARCHAR(100) NOT NULL DEFAULT 'Panamá',
    distrito VARCHAR(100) NULL,
    corregimiento VARCHAR(100) NULL,

    espacio_disponible VARCHAR(150) NULL,

    capacidad_invitados INT UNSIGNED
        NOT NULL DEFAULT 0,

    costo_base DECIMAL(10,2)
        NOT NULL DEFAULT 0.00,

    latitud DECIMAL(10,8) NULL,
    longitud DECIMAL(11,8) NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- ============================================================
-- 7. ACADEMIAS
-- ============================================================

CREATE TABLE academias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(180) NOT NULL,
    ruc VARCHAR(40) NULL,

    descripcion TEXT NULL,

    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    direccion VARCHAR(255) NULL,

    logo VARCHAR(255) NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_academias_nombre
        UNIQUE (nombre),

    CONSTRAINT uq_academias_ruc
        UNIQUE (ruc)
) ENGINE = InnoDB;

CREATE TABLE academia_deportes (
    academia_id INT UNSIGNED NOT NULL,
    deporte_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (
        academia_id,
        deporte_id
    ),

    CONSTRAINT fk_academia_deportes_academia
        FOREIGN KEY (academia_id)
        REFERENCES academias(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_academia_deportes_deporte
        FOREIGN KEY (deporte_id)
        REFERENCES deportes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================
-- 8. ORGANIZADORES
-- ============================================================

CREATE TABLE organizadores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,
    academia_id INT UNSIGNED NULL,

    tipo_organizador ENUM(
        'INDEPENDIENTE',
        'ACADEMIA',
        'ENTRENADOR',
        'EMPRESA',
        'COMITE',
        'OTRO'
    ) NOT NULL DEFAULT 'INDEPENDIENTE',

    nombre_comercial VARCHAR(180) NULL,
    descripcion TEXT NULL,

    verificado TINYINT(1) NOT NULL DEFAULT 0,

    fecha_verificacion DATETIME NULL,
    verificado_por INT UNSIGNED NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_registro DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_organizadores_usuario
        UNIQUE (usuario_id),

    CONSTRAINT fk_organizadores_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_organizadores_academia
        FOREIGN KEY (academia_id)
        REFERENCES academias(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_organizadores_verificado_por
        FOREIGN KEY (verificado_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 9. ENTRENADORES
-- ============================================================

CREATE TABLE entrenadores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    organizador_id INT UNSIGNED NULL,
    academia_id INT UNSIGNED NULL,

    nombre_completo VARCHAR(160) NOT NULL,

    cedula VARCHAR(30) NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,

    certificaciones TEXT NULL,

    anios_experiencia SMALLINT UNSIGNED NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_entrenadores_cedula
        UNIQUE (cedula),

    CONSTRAINT fk_entrenadores_organizador
        FOREIGN KEY (organizador_id)
        REFERENCES organizadores(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_entrenadores_academia
        FOREIGN KEY (academia_id)
        REFERENCES academias(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

CREATE TABLE entrenador_deportes (
    entrenador_id INT UNSIGNED NOT NULL,
    deporte_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (
        entrenador_id,
        deporte_id
    ),

    CONSTRAINT fk_entrenador_deportes_entrenador
        FOREIGN KEY (entrenador_id)
        REFERENCES entrenadores(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_entrenador_deportes_deporte
        FOREIGN KEY (deporte_id)
        REFERENCES deportes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================
-- 10. ÁRBITROS
-- ============================================================

CREATE TABLE arbitros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre_completo VARCHAR(160) NOT NULL,

    cedula VARCHAR(30) NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,

    licencia VARCHAR(80) NULL,
    experiencia TEXT NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_arbitros_cedula
        UNIQUE (cedula),

    CONSTRAINT uq_arbitros_licencia
        UNIQUE (licencia)
) ENGINE = InnoDB;

CREATE TABLE arbitro_deportes (
    arbitro_id INT UNSIGNED NOT NULL,
    deporte_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (
        arbitro_id,
        deporte_id
    ),

    CONSTRAINT fk_arbitro_deportes_arbitro
        FOREIGN KEY (arbitro_id)
        REFERENCES arbitros(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_arbitro_deportes_deporte
        FOREIGN KEY (deporte_id)
        REFERENCES deportes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================
-- 11. ACTIVIDADES
-- ============================================================

CREATE TABLE actividades (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    organizador_id INT UNSIGNED NOT NULL,
    deporte_id INT UNSIGNED NOT NULL,
    instalacion_id INT UNSIGNED NOT NULL,
    entrenador_id INT UNSIGNED NULL,

    tipo ENUM(
        'BIRRIA',
        'ENTRENAMIENTO',
        'TORNEO',
        'EVENTO'
    ) NOT NULL,

    modalidad ENUM(
        'INDIVIDUAL',
        'EQUIPO',
        'MIXTA'
    ) NOT NULL,

    nombre VARCHAR(180) NOT NULL,
    descripcion TEXT NOT NULL,
    reglas TEXT NULL,

    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    fecha_cierre_inscripcion DATETIME NULL,

    edad_minima SMALLINT UNSIGNED NULL,
    edad_maxima SMALLINT UNSIGNED NULL,

    cupos_disponibles INT UNSIGNED
        NOT NULL DEFAULT 0,

    capacidad_invitados INT UNSIGNED
        NOT NULL DEFAULT 0,

    requiere_pago TINYINT(1)
        NOT NULL DEFAULT 0,

    costo_inscripcion DECIMAL(10,2)
        NOT NULL DEFAULT 0.00,

    costo_instalacion DECIMAL(10,2)
        NOT NULL DEFAULT 0.00,

    imagen VARCHAR(255) NULL,

    codigo_qr VARCHAR(255) NULL,
    token_publico CHAR(64) NOT NULL,

    estado ENUM(
        'BORRADOR',
        'PUBLICADA',
        'CERRADA',
        'FINALIZADA',
        'CANCELADA',
        'TRASLADADA'
    ) NOT NULL DEFAULT 'BORRADOR',

    motivo_cancelacion TEXT NULL,

    actividad_origen_id INT UNSIGNED NULL,

    fecha_publicacion DATETIME NULL,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_actividades_token
        UNIQUE (token_publico),

    CONSTRAINT fk_actividades_organizador
        FOREIGN KEY (organizador_id)
        REFERENCES organizadores(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_actividades_deporte
        FOREIGN KEY (deporte_id)
        REFERENCES deportes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_actividades_instalacion
        FOREIGN KEY (instalacion_id)
        REFERENCES instalaciones(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_actividades_entrenador
        FOREIGN KEY (entrenador_id)
        REFERENCES entrenadores(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_actividades_origen
        FOREIGN KEY (actividad_origen_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

CREATE TABLE actividad_arbitros (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    actividad_id INT UNSIGNED NOT NULL,
    arbitro_id INT UNSIGNED NOT NULL,

    rol VARCHAR(80)
        NOT NULL DEFAULT 'Árbitro principal',

    estado ENUM(
        'ASIGNADO',
        'CONFIRMADO',
        'RECHAZADO',
        'FINALIZADO'
    ) NOT NULL DEFAULT 'ASIGNADO',

    fecha_asignacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_actividad_arbitro
        UNIQUE (
            actividad_id,
            arbitro_id
        ),

    CONSTRAINT fk_actividad_arbitros_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_actividad_arbitros_arbitro
        FOREIGN KEY (arbitro_id)
        REFERENCES arbitros(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================
-- 12. EQUIPOS
-- ============================================================

CREATE TABLE equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    participante_id INT UNSIGNED NOT NULL,

    academia_id INT UNSIGNED NULL,
    deporte_id INT UNSIGNED NOT NULL,

    nombre VARCHAR(150) NOT NULL,
    avatar VARCHAR(255) NULL,

    descripcion VARCHAR(500) NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_equipos_participante
        FOREIGN KEY (participante_id)
        REFERENCES participantes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_equipos_academia
        FOREIGN KEY (academia_id)
        REFERENCES academias(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_equipos_deporte
        FOREIGN KEY (deporte_id)
        REFERENCES deportes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================
-- 13. JUGADORES
-- Los jugadores no necesitan cuenta de usuario.
-- ============================================================

CREATE TABLE jugadores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    equipo_id INT UNSIGNED NOT NULL,

    nombre_completo VARCHAR(160) NOT NULL,

    fecha_nacimiento DATE NULL,
    edad SMALLINT UNSIGNED NOT NULL,

    peso_kg DECIMAL(5,2) NULL,

    posicion VARCHAR(80) NULL,
    numero_camiseta SMALLINT UNSIGNED NULL,

    capitan TINYINT(1) NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_jugadores_equipo
        FOREIGN KEY (equipo_id)
        REFERENCES equipos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- ============================================================
-- 14. INVITACIONES
-- ============================================================

CREATE TABLE invitaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    actividad_id INT UNSIGNED NOT NULL,

    academia_id INT UNSIGNED NULL,
    equipo_id INT UNSIGNED NULL,

    correo_destino VARCHAR(150) NULL,
    mensaje TEXT NULL,

    token CHAR(64) NOT NULL,

    estado ENUM(
        'PENDIENTE',
        'ACEPTADA',
        'RECHAZADA',
        'VENCIDA'
    ) NOT NULL DEFAULT 'PENDIENTE',

    fecha_envio DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_respuesta DATETIME NULL,
    fecha_expiracion DATETIME NULL,

    CONSTRAINT uq_invitaciones_token
        UNIQUE (token),

    CONSTRAINT fk_invitaciones_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_invitaciones_academia
        FOREIGN KEY (academia_id)
        REFERENCES academias(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_invitaciones_equipo
        FOREIGN KEY (equipo_id)
        REFERENCES equipos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- ============================================================
-- 15. INSCRIPCIONES INDIVIDUALES
-- ============================================================

CREATE TABLE inscripciones_individuales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    actividad_id INT UNSIGNED NOT NULL,
    participante_id INT UNSIGNED NOT NULL,

    estado ENUM(
        'PENDIENTE_PAGO',
        'PAGO_PENDIENTE_VALIDACION',
        'PENDIENTE_APROBACION',
        'APROBADA',
        'RECHAZADA',
        'CANCELADA',
        'FINALIZADA'
    ) NOT NULL DEFAULT 'PENDIENTE_PAGO',

    observaciones TEXT NULL,

    reglas_aceptadas TINYINT(1)
        NOT NULL DEFAULT 0,

    fecha_inscripcion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_aprobacion DATETIME NULL,
    aprobado_por INT UNSIGNED NULL,

    CONSTRAINT uq_inscripcion_individual
        UNIQUE (
            actividad_id,
            participante_id
        ),

    CONSTRAINT fk_inscripcion_ind_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_inscripcion_ind_participante
        FOREIGN KEY (participante_id)
        REFERENCES participantes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_inscripcion_ind_aprobado
        FOREIGN KEY (aprobado_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 16. INSCRIPCIONES POR EQUIPO
-- ============================================================

CREATE TABLE inscripciones_equipos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    actividad_id INT UNSIGNED NOT NULL,
    equipo_id INT UNSIGNED NOT NULL,

    estado ENUM(
        'PENDIENTE_PAGO',
        'PAGO_PENDIENTE_VALIDACION',
        'PENDIENTE_APROBACION',
        'APROBADA',
        'RECHAZADA',
        'CANCELADA',
        'FINALIZADA'
    ) NOT NULL DEFAULT 'PENDIENTE_PAGO',

    observaciones TEXT NULL,
    reglas_facilitadas TEXT NULL,

    reglas_aceptadas TINYINT(1)
        NOT NULL DEFAULT 0,

    fecha_inscripcion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_aprobacion DATETIME NULL,
    aprobado_por INT UNSIGNED NULL,

    CONSTRAINT uq_inscripcion_equipo
        UNIQUE (
            actividad_id,
            equipo_id
        ),

    CONSTRAINT fk_inscripcion_equipo_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_inscripcion_equipo_equipo
        FOREIGN KEY (equipo_id)
        REFERENCES equipos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_inscripcion_equipo_aprobado
        FOREIGN KEY (aprobado_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 17. CALENDARIO Y JORNADAS
-- ============================================================

CREATE TABLE calendario_actividad_fechas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    actividad_id INT UNSIGNED NOT NULL,

    titulo VARCHAR(180) NOT NULL,

    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,

    instalacion_id INT UNSIGNED NULL,

    descripcion VARCHAR(500) NULL,

    numero_jornada SMALLINT UNSIGNED NULL,

    estado ENUM(
        'PROGRAMADA',
        'REALIZADA',
        'CANCELADA',
        'TRASLADADA'
    ) NOT NULL DEFAULT 'PROGRAMADA',

    CONSTRAINT fk_calendario_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_calendario_instalacion
        FOREIGN KEY (instalacion_id)
        REFERENCES instalaciones(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 18. PAGOS
-- ============================================================

CREATE TABLE pagos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    inscripcion_individual_id BIGINT UNSIGNED NULL,
    inscripcion_equipo_id BIGINT UNSIGNED NULL,

    participante_id INT UNSIGNED NOT NULL,

    metodo_pago ENUM(
        'EFECTIVO',
        'TRANSFERENCIA',
        'TARJETA',
        'YAPPY',
        'OTRO'
    ) NOT NULL,

    referencia VARCHAR(120) NULL,

    monto DECIMAL(10,2) NOT NULL,

    comprobante VARCHAR(255) NULL,

    estado ENUM(
        'PENDIENTE',
        'EN_REVISION',
        'APROBADO',
        'RECHAZADO',
        'DEVUELTO',
        'ANULADO'
    ) NOT NULL DEFAULT 'PENDIENTE',

    fecha_pago DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_validacion DATETIME NULL,

    validado_por INT UNSIGNED NULL,

    observaciones TEXT NULL,

    CONSTRAINT fk_pagos_inscripcion_individual
        FOREIGN KEY (inscripcion_individual_id)
        REFERENCES inscripciones_individuales(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_pagos_inscripcion_equipo
        FOREIGN KEY (inscripcion_equipo_id)
        REFERENCES inscripciones_equipos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_pagos_participante
        FOREIGN KEY (participante_id)
        REFERENCES participantes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_pagos_validado_por
        FOREIGN KEY (validado_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 19. FACTURAS
-- ============================================================

CREATE TABLE facturas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    numero_factura VARCHAR(30) NOT NULL,

    pago_id BIGINT UNSIGNED NOT NULL,

    participante_id INT UNSIGNED NOT NULL,
    actividad_id INT UNSIGNED NOT NULL,
    equipo_id INT UNSIGNED NULL,

    nombre_cliente VARCHAR(180) NOT NULL,
    identificacion_cliente VARCHAR(40) NULL,
    correo_cliente VARCHAR(150) NULL,

    fecha_venta DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    subtotal DECIMAL(10,2) NOT NULL,

    tasa_itbms DECIMAL(5,2)
        NOT NULL DEFAULT 7.00,

    itbms DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,

    estado ENUM(
        'EMITIDA',
        'ANULADA',
        'DEVUELTA'
    ) NOT NULL DEFAULT 'EMITIDA',

    ruta_pdf VARCHAR(255) NULL,

    pdf_hash_sha256 CHAR(64) NULL,

    firma_digital LONGTEXT NULL,
    certificado_publico MEDIUMTEXT NULL,

    algoritmo_firma VARCHAR(50)
        DEFAULT 'SHA256withRSA',

    formato_documento VARCHAR(30)
        NOT NULL DEFAULT 'PDF/A',

    fecha_firma DATETIME NULL,

    CONSTRAINT uq_facturas_numero
        UNIQUE (numero_factura),

    CONSTRAINT uq_facturas_pago
        UNIQUE (pago_id),

    CONSTRAINT fk_facturas_pago
        FOREIGN KEY (pago_id)
        REFERENCES pagos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_facturas_participante
        FOREIGN KEY (participante_id)
        REFERENCES participantes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_facturas_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_facturas_equipo
        FOREIGN KEY (equipo_id)
        REFERENCES equipos(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

CREATE TABLE factura_detalles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    factura_id BIGINT UNSIGNED NOT NULL,

    descripcion VARCHAR(255) NOT NULL,

    cantidad DECIMAL(10,2)
        NOT NULL DEFAULT 1.00,

    precio_unitario DECIMAL(10,2) NOT NULL,

    subtotal_linea DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_factura_detalles_factura
        FOREIGN KEY (factura_id)
        REFERENCES facturas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- ============================================================
-- 20. DEVOLUCIONES
-- ============================================================

CREATE TABLE devoluciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    pago_id BIGINT UNSIGNED NOT NULL,
    factura_id BIGINT UNSIGNED NULL,
    actividad_id INT UNSIGNED NOT NULL,

    motivo TEXT NOT NULL,

    monto DECIMAL(10,2) NOT NULL,

    estado ENUM(
        'SOLICITADA',
        'APROBADA',
        'PROCESADA',
        'RECHAZADA'
    ) NOT NULL DEFAULT 'SOLICITADA',

    referencia_devolucion VARCHAR(120) NULL,

    solicitada_por INT UNSIGNED NULL,
    procesada_por INT UNSIGNED NULL,

    fecha_solicitud DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_proceso DATETIME NULL,

    CONSTRAINT fk_devoluciones_pago
        FOREIGN KEY (pago_id)
        REFERENCES pagos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_devoluciones_factura
        FOREIGN KEY (factura_id)
        REFERENCES facturas(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_devoluciones_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_devoluciones_solicitada_por
        FOREIGN KEY (solicitada_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_devoluciones_procesada_por
        FOREIGN KEY (procesada_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 21. INCIDENTES DEPORTIVOS
-- ============================================================

CREATE TABLE incidentes_deportivos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    actividad_id INT UNSIGNED NOT NULL,

    reportado_por INT UNSIGNED NULL,

    arbitro_id INT UNSIGNED NULL,
    equipo_id INT UNSIGNED NULL,
    jugador_id INT UNSIGNED NULL,

    tipo ENUM(
        'LESION',
        'CONDUCTA_ANTIDEPORTIVA',
        'EXPULSION',
        'DAÑO_INSTALACION',
        'SUSPENSION',
        'OTRO'
    ) NOT NULL,

    gravedad ENUM(
        'LEVE',
        'MODERADA',
        'GRAVE',
        'CRITICA'
    ) NOT NULL DEFAULT 'LEVE',

    descripcion TEXT NOT NULL,
    acciones_tomadas TEXT NULL,

    fecha_incidente DATETIME NOT NULL,

    estado ENUM(
        'ABIERTO',
        'EN_REVISION',
        'RESUELTO',
        'CERRADO'
    ) NOT NULL DEFAULT 'ABIERTO',

    fecha_creacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_incidentes_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_incidentes_reportado_por
        FOREIGN KEY (reportado_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_incidentes_arbitro
        FOREIGN KEY (arbitro_id)
        REFERENCES arbitros(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_incidentes_equipo
        FOREIGN KEY (equipo_id)
        REFERENCES equipos(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_incidentes_jugador
        FOREIGN KEY (jugador_id)
        REFERENCES jugadores(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 22. EVALUACIONES DE ÁRBITROS
-- ============================================================

CREATE TABLE evaluaciones_arbitros (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    actividad_id INT UNSIGNED NOT NULL,
    arbitro_id INT UNSIGNED NOT NULL,
    organizador_id INT UNSIGNED NOT NULL,

    puntuacion TINYINT UNSIGNED NOT NULL,

    puntualidad TINYINT UNSIGNED NULL,
    conocimiento_reglas TINYINT UNSIGNED NULL,
    imparcialidad TINYINT UNSIGNED NULL,
    manejo_actividad TINYINT UNSIGNED NULL,

    comentario TEXT NULL,

    fecha_evaluacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_evaluacion_arbitro
        UNIQUE (
            actividad_id,
            arbitro_id,
            organizador_id
        ),

    CONSTRAINT fk_evaluaciones_actividad
        FOREIGN KEY (actividad_id)
        REFERENCES actividades(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_evaluaciones_arbitro
        FOREIGN KEY (arbitro_id)
        REFERENCES arbitros(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_evaluaciones_organizador
        FOREIGN KEY (organizador_id)
        REFERENCES organizadores(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================
-- 23. PÁGINA PÚBLICA - CONTÁCTENOS
-- ============================================================

CREATE TABLE mensajes_contacto (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NULL,

    asunto VARCHAR(180) NOT NULL,
    mensaje TEXT NOT NULL,

    estado ENUM(
        'NUEVO',
        'LEIDO',
        'RESPONDIDO',
        'CERRADO'
    ) NOT NULL DEFAULT 'NUEVO',

    fecha_envio DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atendido_por INT UNSIGNED NULL,
    fecha_atencion DATETIME NULL,

    CONSTRAINT fk_mensajes_atendido_por
        FOREIGN KEY (atendido_por)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- ============================================================
-- 24. CONFIGURACIÓN DEL SISTEMA
-- ============================================================

CREATE TABLE configuracion_sistema (
    clave VARCHAR(100) PRIMARY KEY,

    valor TEXT NOT NULL,

    descripcion VARCHAR(255) NULL,

    fecha_actualizacion DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- ============================================================
-- 25. ÍNDICES
-- ============================================================

CREATE INDEX idx_usuarios_rol_activo
    ON usuarios (rol, activo);

CREATE INDEX idx_bitacora_usuario_fecha
    ON bitacora (usuario_id, fecha_evento);

CREATE INDEX idx_actividades_estado_fecha
    ON actividades (estado, fecha_inicio);

CREATE INDEX idx_actividades_organizador
    ON actividades (organizador_id);

CREATE INDEX idx_actividades_deporte
    ON actividades (deporte_id);

CREATE INDEX idx_equipos_participante
    ON equipos (participante_id);

CREATE INDEX idx_jugadores_equipo
    ON jugadores (equipo_id);

CREATE INDEX idx_inscripciones_ind_estado
    ON inscripciones_individuales (estado);

CREATE INDEX idx_inscripciones_equipo_estado
    ON inscripciones_equipos (estado);

CREATE INDEX idx_pagos_estado_fecha
    ON pagos (estado, fecha_pago);

CREATE INDEX idx_facturas_fecha
    ON facturas (fecha_venta);

CREATE INDEX idx_incidentes_actividad
    ON incidentes_deportivos (actividad_id);

CREATE INDEX idx_evaluaciones_arbitro
    ON evaluaciones_arbitros (arbitro_id);

-- ============================================================
-- 26. VISTA DE ACTIVIDADES PÚBLICAS
-- ============================================================

CREATE VIEW vw_actividades_publicas AS
SELECT
    a.id,
    a.token_publico,
    a.nombre,
    a.tipo,
    a.modalidad,
    a.descripcion,
    a.fecha_inicio,
    a.fecha_fin,
    a.edad_minima,
    a.edad_maxima,
    a.cupos_disponibles,
    a.requiere_pago,
    a.costo_inscripcion,
    a.imagen,
    a.codigo_qr,

    d.nombre AS deporte,

    i.nombre AS instalacion,
    i.direccion,

    CONCAT(
        u.nombre,
        ' ',
        u.apellido
    ) AS organizador,

    a.estado

FROM actividades a

INNER JOIN deportes d
    ON d.id = a.deporte_id

INNER JOIN instalaciones i
    ON i.id = a.instalacion_id

INNER JOIN organizadores o
    ON o.id = a.organizador_id

INNER JOIN usuarios u
    ON u.id = o.usuario_id

WHERE a.estado = 'PUBLICADA'
AND a.fecha_inicio >= CURRENT_TIMESTAMP;

-- ============================================================
-- 27. VISTA DE DESEMPEÑO DE ÁRBITROS
-- ============================================================

CREATE VIEW vw_desempeno_arbitros AS
SELECT
    ar.id AS arbitro_id,

    ar.nombre_completo,

    COUNT(ea.id) AS total_evaluaciones,

    ROUND(
        AVG(ea.puntuacion),
        2
    ) AS promedio_general,

    ROUND(
        AVG(ea.puntualidad),
        2
    ) AS promedio_puntualidad,

    ROUND(
        AVG(ea.conocimiento_reglas),
        2
    ) AS promedio_reglas,

    ROUND(
        AVG(ea.imparcialidad),
        2
    ) AS promedio_imparcialidad,

    ROUND(
        AVG(ea.manejo_actividad),
        2
    ) AS promedio_manejo

FROM arbitros ar

LEFT JOIN evaluaciones_arbitros ea
    ON ea.arbitro_id = ar.id

GROUP BY
    ar.id,
    ar.nombre_completo;

-- ============================================================
-- 28. DATOS INICIALES
-- ============================================================

-- Contraseña inicial del administrador: Admin123
-- Hash verificado, generado con password_hash('Admin123', PASSWORD_BCRYPT, ['cost' => 12])

INSERT INTO usuarios (
    nombre,
    apellido,
    correo,
    telefono,
    usuario,
    password_hash,
    rol,
    activo,
    requiere_cambio_password
)
VALUES (
    'Administrador',
    'Principal',
    CONCAT('admin', '.sistema', '@', 'utp', '.edu', '.pa'),
    '6000-0000',
    'admin',
    '$2y$12$6lKmNFOwihDFfreCpg1s4uyIgso2udjEpkiujBXKXvHP/3k8/eEaS',
    'ADMINISTRADOR',
    1,
    0
);

INSERT INTO deportes (
    nombre,
    descripcion,
    es_equipo,
    minimo_jugadores,
    maximo_jugadores
)
VALUES
(
    'Fútbol',
    'Disciplina deportiva de equipo.',
    1,
    5,
    22
),
(
    'Baloncesto',
    'Disciplina deportiva de equipo.',
    1,
    5,
    15
),
(
    'Voleibol',
    'Disciplina deportiva de equipo.',
    1,
    6,
    14
),
(
    'Béisbol',
    'Disciplina deportiva de equipo.',
    1,
    9,
    25
),
(
    'Atletismo',
    'Disciplina deportiva individual o por relevos.',
    0,
    NULL,
    NULL
),
(
    'Natación',
    'Disciplina acuática individual o por equipos.',
    0,
    NULL,
    NULL
);

INSERT INTO configuracion_sistema (
    clave,
    valor,
    descripcion
)
VALUES
(
    'NOMBRE_SISTEMA',
    'Sistema de Eventos Deportivos',
    'Nombre público del sistema.'
),
(
    'ITBMS_PORCENTAJE',
    '7.00',
    'Porcentaje de ITBMS aplicado a las facturas.'
),
(
    'MONEDA',
    'PAB',
    'Código de moneda utilizado por el sistema.'
),
(
    'PREFIJO_FACTURA',
    'FAC',
    'Prefijo utilizado para generar números de factura.'
),
(
    'RSA_BITS',
    '2048',
    'Tamaño mínimo de las llaves RSA.'
),
(
    'INTENTOS_LOGIN_MAXIMOS',
    '5',
    'Cantidad máxima de intentos fallidos de inicio de sesión.'
);

INSERT INTO bitacora (
    usuario_id,
    modulo,
    accion,
    descripcion
)
VALUES (
    1,
    'SISTEMA',
    'INSTALACION',
    'Creación inicial de la base de datos.'
);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
