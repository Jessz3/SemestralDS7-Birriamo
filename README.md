# Sistema de Eventos Deportivos

Proyecto para el Examen Final — **Desarrollo de Software VII**
Universidad Tecnologica de Panama · Facultad de Ingenieria en Sistemas Computacionales

## Arquitectura

- **Patron MVC** con Front Controller (`public/index.php`).
- **PSR-1** (convenciones basicas de codigo) y **PSR-4** (autoload de clases) via Composer.
- **PDO** con conexion Singleton (`src/Config/Database.php`), sentencias preparadas en todos los modelos.
- **Base de datos de 30 tablas + 2 vistas** (`database/schema.sql`), incluyendo Usuarios, Participantes, Organizadores, Entrenadores, Arbitros, Academias, Actividades, Equipos/Jugadores, Inscripciones, Pagos, Facturas, Devoluciones, Incidentes, Evaluaciones de arbitros, Bitacora, Mensajes de contacto y Configuracion del sistema.
- **Contratos / Programacion por Interfaces**: `TransformadorSeguridadInterface` unifica el hashing de contrasenas (`HashPasswordService`) y la firma digital (`FirmaDigitalHmacService`, `FirmaDigitalRsaService`).
- **Integridad y trazabilidad centralizada**: cada accion relevante (crear/publicar/cancelar actividad, aprobar inscripcion, login, etc.) se registra en la tabla `bitacora` con su propia firma HMAC-SHA256, verificable en cualquier momento (`Models\Bitacora::verificarIntegridad`).
- **No repudio de usuarios**: todo usuario (Administrador, Operador u Organizador) recibe un par de llaves RSA-2048 al crearse, guardadas en `claves_rsa_usuario`. La llave privada se cifra con AES-256 usando una passphrase que solo esa persona conoce.
- **Facturacion**: cada factura queda ligada a un `Pago` aprobado, calcula el ITBMS con la tasa configurable en `configuracion_sistema`, y se firma digitalmente (HMAC-SHA256); el PDF generado con TCPDF ademas guarda su propio hash SHA-256 (`pdf_hash_sha256`) como capa adicional de integridad.
- **Utilitarios estaticos** `Sanitizacion` y `Validaciones` centralizan OWASP/DRY para todo el sistema.

## Estructura de carpetas

```
public/            Front controller, assets (CSS), uploads, .htaccess
src/
  Config/          Conexion a base de datos (PDO Singleton) + cargador de .env
  Core/            Router, Controller base, Model base
  Security/        Contratos e implementaciones criptograficas
  Utils/           Sanitizacion y Validaciones
  Models/          Un modelo por entidad principal
  Controllers/     Un controlador por modulo
views/             Vistas organizadas por modulo + layouts compartidos
database/schema.sql Script completo de base de datos (con datos semilla)
```

## Requisitos

- PHP 8.1 o superior (extensiones: pdo_mysql, mbstring, openssl, gd)
- MySQL 8 o MariaDB 10.5+
- Composer
- Servidor web Apache (con `mod_rewrite`) o el servidor embebido de PHP

## Instalacion

1. Instalar dependencias:
   ```bash
   composer install
   ```
   Esto instalara `tecnickcom/tcpdf` (generacion de facturas en PDF).

2. Crear la base de datos ejecutando el script (es seguro re-ejecutarlo: hace `DROP DATABASE` + `CREATE DATABASE` al inicio):
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. Copiar `.env.example` a `.env` y ajustar tus credenciales:
   ```bash
   cp .env.example .env
   ```
   Variables disponibles: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_CHARSET`, `APP_HMAC_KEY`.

4. Genera tu propia llave `APP_HMAC_KEY` para produccion:
   ```bash
   php -r "echo bin2hex(random_bytes(32));"
   ```
   Copia el resultado en `.env`.

5. Levantar el proyecto:
   ```bash
   php -S localhost:8000 -t public
   ```
   o configurando un Virtual Host de Apache/XAMPP apuntando a la carpeta `public/`.

6. Usuario de prueba (ver `database/schema.sql`): `admin` / `Admin123` (rol ADMINISTRADOR).

## Como funciona el flujo de cuentas (importante)

En esta version, **todo el que participa en el sistema tiene una cuenta en `usuarios`**, con un rol especifico:

- Los cuatro roles pueden registrarse y consultarse desde `/usuarios` (solo Administrador).
- `ADMINISTRADOR` / `OPERADOR`: staff interno; pueden cambiar entre ambos roles.
- Al crear un `ORGANIZADOR` o `PARTICIPANTE` desde `/usuarios`, el sistema crea tambien su perfil funcional relacionado.
- El autorregistro publico en `/registro` reutiliza el mismo flujo seguro y limita los roles a `ORGANIZADOR` y `PARTICIPANTE`.
- `ORGANIZADOR`: se crea desde `/organizadores/crear`. El formulario genera automaticamente su cuenta de usuario y su par de llaves RSA.
- `PARTICIPANTE`: se crea automaticamente y de forma transparente cuando:
  - Se registra un equipo (`/equipos/crear`), usando los datos del representante.
  - Alguien se inscribe individualmente a una actividad publica (`/inscripciones/individual/crear`).
  Si ya existe un usuario con ese correo, se reutiliza su cuenta en vez de duplicarla.

Estas cuentas de Participante/Organizador no estan pensadas para iniciar sesion en este panel administrativo (por eso llevan contrasena aleatoria); existen para satisfacer las llaves foraneas del esquema (`participantes.usuario_id`, `organizadores.usuario_id`) y quedar disponibles para un futuro portal de autoservicio.

## Flujo de inscripcion y facturacion

1. Se crea una actividad en estado `BORRADOR` y se **publica** (`PUBLICADA`) para que aparezca en la pagina publica y admita inscripciones.
2. Inscripcion por equipo: el equipo queda `PENDIENTE_APROBACION`; al aprobarla se registra un `Pago` (APROBADO) y se emite la `Factura` automaticamente.
3. Inscripcion individual (publica, sin login): se crea el Participante si no existe, se aprueba de inmediato, se registra el pago y se emite la factura — el usuario ve su factura al instante.
4. Al **cancelar** una actividad, se generan automaticamente solicitudes de `Devolucion` por cada factura ya emitida, y las inscripciones pasan a `CANCELADA`.

## Modulos incluidos (mapeo con la rubrica del examen)

| # | Requisito | Modulo/Archivo |
|---|---|---|
| 1 | Login | `AuthController`, `views/auth/login.php` |
| 2 | CRUD Usuarios + no repudio (RSA) | `UsuarioController`, `ClaveRsaUsuario`, `FirmaDigitalRsaService` |
| 3 | Catalogo de Deportes | `DeporteController` |
| 4 | Instalaciones Deportivas | `InstalacionController` |
| 5 | Academias y Organizadores | `AcademiaController`, `OrganizadorController` |
| 7 | Inscripcion por equipo | `EquipoController`, `InscripcionController`, `Participante` |
| 8 | Inscripcion individual | `InscripcionController` |
| 9 | CRUD de Actividades (workflow Borrador→Publicada→Cerrada→Finalizada/Cancelada, con devoluciones) | `ActividadController`, `Devolucion` |
| 10 | Codigo QR por actividad (token_publico) | `views/actividades/ver.php`, `PublicController::evento` |
| 11 | Conexion mediante clase | `src/Config/Database.php` |
| 12 | Control de errores | `public/index.php` (handlers globales), validaciones por modulo |
| 13 | Sanitizar y validar datos | `src/Utils/Sanitizacion.php`, `src/Utils/Validaciones.php` |
| 14 | OWASP / DRY / SOLID | Ver seccion siguiente |
| 15 | Contratos / Programacion por Interfaces | `src/Security/TransformadorSeguridadInterface.php` |
| 17 | Pagina publica (contactenos con formulario real, importancia, stack) | `views/public/inicio.php`, `MensajeContacto` |
| 18 | Facturacion con TCPDF + ITBMS configurable + firma digital + hash del PDF | `FacturaController`, `Models/Factura.php`, `Models/Pago.php` |
| 19 | Estadisticas, incidentes y evaluacion de arbitros (multi-criterio) | `EstadisticaController`, `ArbitroController`, vista `vw_desempeno_arbitros` |
| 20 | CSS + menus horizontales | `public/assets/css/style.css`, `views/layout/main.php` |

## Buenas practicas aplicadas

- **OWASP**: sentencias preparadas (sin concatenacion de SQL), `htmlspecialchars` en todas las salidas, tokens CSRF en formularios, bloqueo de cuenta tras 5 intentos fallidos, cabeceras de seguridad en `.htaccess`.
- **DRY**: logica repetida centralizada en `Controller` (base), `Sanitizacion`, `Validaciones`, `FirmaDigitalHmacService::cadenaCanonica`, `Bitacora::registrar`.
- **SOLID**: cada modelo/controlador tiene una responsabilidad; el contrato `TransformadorSeguridadInterface` permite sustituir el algoritmo de firma sin tocar la logica de negocio (Open/Closed).

## Alcance y modulos pendientes de interfaz

El script de base de datos incluye tablas que quedaron listas a nivel de esquema pero **sin pantallas propias todavia** (puedes ampliarlas como trabajo adicional si el tiempo lo permite):

- `invitaciones` (invitar academias/equipos a una actividad).
- `calendario_actividad_fechas` (jornadas multiples dentro de una misma actividad).
- `tokens_recuperacion` (recuperacion de contrasena por correo).
- Flujo de pago pendiente/en revision (`pagos.estado = PENDIENTE`/`EN_REVISION`) — actualmente el sistema aprueba el pago de forma inmediata al aprobar la inscripcion, simulando pago en sitio.

## Pendiente para el estudiante

- Generar los diagramas UML solicitados (ver `LISTA_DOCUMENTACION.md`).
- Grabar el video explicativo del proyecto.
- Subir el repositorio y el backup de la base de datos, y colocar ambas URLs en la documentacion.
