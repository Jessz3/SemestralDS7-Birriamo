# Birria-MO — Sistema de Eventos Deportivos

Proyecto para el Examen Final de **Desarrollo de Software VII**  
Universidad Tecnológica de Panamá — Facultad de Ingeniería en Sistemas Computacionales

---

# 1. Información General y Evidencia Práctica

## 1.1. Nombre del proyecto

**Birria-MO — Sistema de Gestión de Eventos Deportivos**

Birria-MO es una aplicación web desarrollada para administrar actividades deportivas, usuarios, participantes, organizadores, academias, instalaciones, entrenadores, árbitros, equipos, inscripciones, pagos, facturas, incidentes deportivos y estadísticas. El sistema utiliza PHP, MySQL, el patrón Modelo-Vista-Controlador y servicios criptográficos para proteger la integridad de las operaciones críticas.

## 1.2. Integrantes del equipo

| Integrante | Cédula | Rol dentro del desarrollo |
|---|---|---|
| Jesús Alveo | `8-1025-316` | Desarrollo backend, seguridad y documentación |
| Erick Hou | `8-1017-473` | Desarrollo backend, base de datos y documentación |
| Roniel Quintero | `8-1037-904` | Desarrollo de módulos, pruebas y documentación |
| Jessica Zheng | `8-1033-370` | Desarrollo de interfaz, pruebas y documentación |

## 1.3. Fecha del sistema y versión

- **Versión actual:** `v1.0.0`
- **Fecha de entrega:** 15 de julio de 2026
- **Grupo:** 1GS131
- **Estado:** Versión académica funcional para evaluación y demostración.

## 1.4. Demostración en video

Enlace directo al video donde el equipo explica la arquitectura, ejecuta los módulos principales y demuestra las validaciones de seguridad:

**Video:** `https://youtu.be/aJCvTU2BQ2o?si=EiqvEB7QS9b87las`

## 1.5. Repositorio y respaldo de la base de datos

- **Repositorio del proyecto:** https://github.com/Jessz3/SemestralDS7-Birriamo
- **Respaldo de base de datos:** [`database/schema.sql`](database/schema.sql)
- **URL externa del backup:** `https://github.com/Jessz3/SemestralDS7-Birriamo/blob/main/database/schema.sql`

## 1.6. Tecnologías utilizadas

- PHP 8.1 o superior.
- MySQL 8 o MariaDB 10.5 o superior.
- Apache con `mod_rewrite` o servidor incorporado de PHP.
- Composer con autoload PSR-4.
- PDO y sentencias preparadas.
- HTML5 y CSS3.
- TCPDF para facturas en PDF.
- Endroid QR Code para códigos QR.
- OpenSSL para RSA, AES-256 y firmas digitales.
- HMAC-SHA256 para integridad y trazabilidad.

---

# 2. Requisitos de Infraestructura — ¿Cómo hacerlo funcionar?

## 2.1. Entorno de ejecución

| Componente | Requisito |
|---|---|
| PHP | 8.1 o superior; recomendado PHP 8.3 |
| Base de datos | MySQL 8 o MariaDB 10.5+ |
| Servidor local | XAMPP, Laragon o Apache configurado manualmente |
| Dependencias | Composer 2 |
| Navegador | Chrome, Edge o Firefox actualizado |
| Sistema operativo | Windows 10/11 o Linux |

### Extensiones de PHP necesarias

- `pdo`
- `pdo_mysql`
- `mbstring`
- `openssl`
- `gd`
- `fileinfo`
- `json`
- `session`
- `hash`

## 2.2. Guía de despliegue rápido

### Paso 1. Clonar el repositorio

```bash
git clone https://github.com/Jessz3/SemestralDS7-Birriamo.git
cd SemestralDS7-Birriamo
```

También puede descomprimirse directamente el archivo ZIP del proyecto dentro de la carpeta pública de XAMPP o Laragon.

### Paso 2. Instalar las dependencias

```bash
composer install
```

Composer instalará TCPDF, Endroid QR Code y sus dependencias.

### Paso 3. Crear e importar la base de datos

Desde una terminal:

```bash
mysql -u root -p < database/schema.sql
```

También puede importarse `database/schema.sql` desde phpMyAdmin. El script crea la base de datos `eventos_deportivos`, sus tablas, relaciones, vistas y datos semilla.

> El script contiene instrucciones para recrear la base de datos. Se recomienda respaldar cualquier información previa antes de ejecutarlo nuevamente.

### Paso 4. Configurar las credenciales locales

Copiar `.env.example` como `.env`:

```bash
cp .env.example .env
```

En Windows puede copiarse manualmente. Luego se deben ajustar los valores:

```env
DB_HOST=localhost
DB_NAME=eventos_deportivos
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
APP_HMAC_KEY=REEMPLAZAR_POR_UNA_LLAVE_ALEATORIA
```

Para generar una llave HMAC segura:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

### Paso 5. Ejecutar el sistema

Con el servidor incorporado de PHP:

```bash
php -S localhost:8000 -t public
```

Abrir en el navegador:

```text
http://localhost:8000
```

En XAMPP o Laragon se recomienda configurar el `DocumentRoot` para que apunte a la carpeta `public/`.

## 2.3. Arquitectura resumida

El sistema utiliza el patrón MVC con un controlador frontal en `public/index.php`.

```text
public/             Front Controller, CSS, imágenes, QR y facturas
src/Config/         Configuración y conexión PDO
src/Core/           Router, Controller base y Model base
src/Controllers/    Controladores por módulo
src/Models/         Acceso a datos y entidades del negocio
src/Security/       Hashing, HMAC, RSA y contratos criptográficos
src/Utils/          Sanitización y validaciones reutilizables
views/              Vistas organizadas por módulo
vendor/             Dependencias de Composer
database/schema.sql Estructura y datos semilla
```

---

# 3. Matriz de Roles y Credenciales de Prueba

La siguiente matriz permite al evaluador ingresar rápidamente a los escenarios disponibles.

| Rol de usuario | Usuario de acceso | Contraseña de prueba | Permisos / Qué puede hacer |
|---|---|---|---|
| Administrador | `admin` | `Admin123` | Control total, gestión de usuarios, catálogos, actividades, configuración, estadísticas, bitácora y verificación de integridad. |
| Operador | `[CREAR USUARIO OPERADOR]` | `[COMPLETAR]` | Gestiona deportes, instalaciones, academias, organizadores, entrenadores, árbitros y actividades. No tiene acceso al módulo de usuarios. |
| Organizador | `[COMPLETAR USUARIO DE PRUEBA]` | `[COMPLETAR]` | Crea y administra actividades, revisa inscripciones, registra incidentes y evalúa árbitros. |
| Participante | `[COMPLETAR USUARIO DE PRUEBA]` | `[COMPLETAR]` | Consulta actividades, registra equipos, realiza inscripciones y consulta facturas. |

> El usuario administrador está incluido en `database/schema.sql`. Antes de la evaluación, el equipo debe crear o confirmar las credenciales funcionales de Operador, Organizador y Participante y sustituir los marcadores de esta tabla.

## 3.1. Reglas de acceso por rol

- El **Administrador** tiene acceso a todos los módulos.
- El **Operador** puede alimentar la información operativa, pero no puede visualizar ni modificar el módulo de usuarios.
- El **Organizador** administra sus actividades deportivas, inscripciones, incidentes y evaluaciones.
- El **Participante** interactúa con las actividades, equipos, inscripciones, pagos y facturas disponibles para su cuenta.
- El sistema debe responder con acceso denegado cuando un actor intenta entrar a un módulo que no corresponde a su rol.

---

# 4. Directrices Técnicas y Reglas del Backend

## 4.1. Control de acceso seguro

La autenticación se implementa principalmente en los siguientes archivos:

- `src/Controllers/AuthController.php`
- `src/Models/Usuario.php`
- `src/Security/HashPasswordService.php`
- `src/Utils/Validaciones.php`
- `views/auth/login.php`

El sistema almacena las contraseñas con Bcrypt y costo 12 mediante `password_hash()` y las verifica con `password_verify()`. Después de un inicio de sesión exitoso se regenera el identificador de sesión. Los intentos fallidos se registran en la cuenta y pueden provocar un bloqueo temporal.

La política académica solicitada establece contraseñas de **8 a 12 caracteres**. La validación debe comprobar el mínimo, el máximo, al menos una mayúscula y un número. Si la versión actual solo valida el mínimo, debe ajustarse `Validaciones::passwordSegura()` antes de la entrega definitiva.

> **Importante:** la versión analizada bloquea la cuenta después de cinco intentos fallidos. Si la rúbrica exige exactamente el tercer intento, debe modificarse el límite en el modelo o controlador y actualizarse esta documentación.

Las operaciones relevantes, como accesos, creación de usuarios y modificaciones, quedan registradas en la tabla `bitacora`.

## 4.2. Mitigación OWASP y aplicación de DRY

### Protección implementada

- PDO con sentencias preparadas para reducir el riesgo de inyección SQL.
- Sanitización centralizada en `src/Utils/Sanitizacion.php`.
- Validaciones reutilizables en `src/Utils/Validaciones.php`.
- Codificación de salida con `htmlspecialchars()` en las vistas.
- Tokens CSRF generados en sesión y validados por el controlador base.
- Restricciones de acceso según el rol autenticado.
- Cabeceras de seguridad configuradas en `public/.htaccess`.
- Manejo global de errores para evitar la exposición de trazas y datos sensibles.

### Flujo CSRF

1. El servidor crea un token aleatorio y lo almacena en la sesión.
2. La vista lo incorpora como campo oculto del formulario.
3. El navegador o Postman envía el token en la petición POST.
4. El controlador ejecuta `verifyCsrf()`.
5. La comparación se realiza con `hash_equals()`.
6. Si el token no existe o es inválido, la operación se rechaza.

El principio DRY se aplica al centralizar comportamientos comunes en:

- `src/Core/Controller.php`
- `src/Core/Model.php`
- `src/Utils/Sanitizacion.php`
- `src/Utils/Validaciones.php`
- `src/Security/FirmaDigitalHmacService.php`
- Modelo de bitácora reutilizable por los módulos.

> Las acciones que modifican información deben usar POST y validar CSRF. Las rutas GET que todavía cambien estados deben convertirse a POST antes de considerar esta protección completa.

## 4.3. Sello de integridad y firmas digitales

El proyecto utiliza HMAC-SHA256 para crear un sello sobre los datos críticos antes o después de almacenarlos. Este sello permite detectar alteraciones realizadas directamente en la base de datos, ya que la verificación vuelve a construir la cadena canónica y compara la firma calculada con la registrada.

Archivos principales:

- `src/Security/FirmaDigitalHmacService.php`
- `src/Security/FirmaDigitalRsaService.php`
- `src/Security/TransformadorSeguridadInterface.php`
- Modelos de `Bitacora`, `Factura`, `Pago` y entidades críticas.

Se protegen principalmente:

- Registros de bitácora.
- Actividades y cambios críticos.
- Pagos.
- Facturas.
- Hash SHA-256 de los PDF generados.

Además, el sistema genera pares de llaves RSA-2048 para usuarios y almacena la llave privada cifrada con AES-256. HMAC garantiza integridad y autenticidad del servidor; RSA permite aproximarse al no repudio cuando la firma se ejecuta con la llave privada bajo control exclusivo del usuario.

## 4.4. Contratos y principios SOLID

La interfaz `TransformadorSeguridadInterface` define un contrato común para las operaciones de protección y verificación. La arquitectura permite utilizar implementaciones como:

- `HashPasswordService`
- `FirmaDigitalHmacService`
- `FirmaDigitalRsaService`

La separación entre modelos, controladores, vistas, validadores y servicios criptográficos demuestra el principio de Responsabilidad Única. Los servicios pueden sustituirse o ampliarse sin trasladar los algoritmos específicos a la lógica de negocio.

## 4.5. Flujo de inscripción y facturación

1. El organizador crea una actividad en estado `BORRADOR`.
2. Al publicarla, cambia a `PUBLICADA` y se genera un token público y un código QR.
3. La inscripción por equipo queda en `PENDIENTE_APROBACION`.
4. Al aprobarla, se registra el pago y se emite una factura.
5. La inscripción individual puede aprobarse inmediatamente, según la política implementada.
6. La factura calcula el ITBMS utilizando la configuración del sistema.
7. TCPDF genera el documento PDF y se almacena su hash SHA-256.
8. Si una actividad se cancela, el sistema crea solicitudes de devolución para las facturas afectadas.

## 4.6. Módulos incluidos

| Módulo | Archivos o componentes principales |
|---|---|
| Login y cambio de contraseña | `AuthController`, `Usuario`, vistas de autenticación |
| Usuarios y roles | `UsuarioController`, `Usuario`, llaves RSA |
| Deportes | `DeporteController`, `Deporte` |
| Instalaciones | `InstalacionController`, `Instalacion` |
| Academias y organizadores | `AcademiaController`, `OrganizadorController` |
| Entrenadores y árbitros | `EntrenadorController`, `ArbitroController` |
| Equipos y jugadores | `EquipoController`, modelos de equipo y jugador |
| Actividades | `ActividadController`, `Actividad` |
| Inscripciones | `InscripcionController` |
| Pagos y facturas | `FacturaController`, `Pago`, `Factura`, TCPDF |
| Códigos QR | Endroid QR Code y token público de actividad |
| Incidentes y estadísticas | `EstadisticaController`, modelos relacionados |
| Página pública y contacto | `PublicController`, vistas públicas y mensajes |
| Configuración | `ConfiguracionController` |

---

# 5. Manual de Usuario Operativo — Flujo de Pantallas

Esta sección debe completarse con capturas reales del sistema. Cada espacio indica la imagen que debe insertarse y una explicación sugerida.

## 5.1. Acceso a la página pública

El visitante abre la dirección principal del sistema. Desde esta pantalla puede conocer la importancia de la plataforma, consultar las tecnologías utilizadas, revisar actividades públicas y utilizar el formulario de contacto.

> **ESPACIO PARA CAPTURA 1 — Página pública / Inicio**  
> Insertar aquí una imagen completa de la página pública.

```text
[ CAPTURA DE PANTALLA: PÁGINA PÚBLICA ]
```

## 5.2. Inicio de sesión

El usuario selecciona la opción de inicio de sesión, introduce su nombre de usuario y contraseña y envía el formulario. El sistema valida las credenciales, el estado de la cuenta y los intentos fallidos antes de mostrar el panel correspondiente al rol.

> **ESPACIO PARA CAPTURA 2 — Formulario de inicio de sesión**

```text
[ CAPTURA DE PANTALLA: LOGIN ]
```

> **ESPACIO PARA CAPTURA 3 — Mensaje de credenciales inválidas o bloqueo**

```text
[ CAPTURA DE PANTALLA: VALIDACIÓN DEL LOGIN ]
```

## 5.3. Menú principal por rol

Después de autenticarse, el sistema muestra las opciones autorizadas. El administrador visualiza todos los módulos; el operador no debe visualizar el módulo de usuarios.

> **ESPACIO PARA CAPTURA 4 — Menú del Administrador**

```text
[ CAPTURA DE PANTALLA: MENÚ ADMINISTRADOR ]
```

> **ESPACIO PARA CAPTURA 5 — Menú del Operador sin módulo de usuarios**

```text
[ CAPTURA DE PANTALLA: MENÚ OPERADOR ]
```

## 5.4. Gestión de usuarios

El administrador accede al módulo de usuarios para agregar, buscar, modificar, habilitar o deshabilitar cuentas. Al registrar un usuario se selecciona su rol y se generan los elementos de seguridad correspondientes.

> **ESPACIO PARA CAPTURA 6 — Listado y búsqueda de usuarios**

```text
[ CAPTURA DE PANTALLA: LISTADO DE USUARIOS ]
```

> **ESPACIO PARA CAPTURA 7 — Formulario para agregar usuario**

```text
[ CAPTURA DE PANTALLA: CREAR USUARIO ]
```

> **ESPACIO PARA CAPTURA 8 — Formulario para modificar o deshabilitar usuario**

```text
[ CAPTURA DE PANTALLA: MODIFICAR USUARIO ]
```

## 5.5. Gestión de catálogos

El administrador u operador puede gestionar deportes, instalaciones, academias, entrenadores, árbitros y organizadores. Cada módulo permite consultar, agregar, modificar y cambiar el estado de los registros según los permisos asignados.

> **ESPACIO PARA CAPTURA 9 — Catálogo de deportes**

```text
[ CAPTURA DE PANTALLA: DEPORTES ]
```

> **ESPACIO PARA CAPTURA 10 — Instalaciones deportivas**

```text
[ CAPTURA DE PANTALLA: INSTALACIONES ]
```

> **ESPACIO PARA CAPTURA 11 — Academias, entrenadores u organizadores**

```text
[ CAPTURA DE PANTALLA: CATÁLOGOS COMPLEMENTARIOS ]
```

## 5.6. Creación y publicación de actividades

El usuario autorizado abre el formulario de actividades, registra el deporte, la instalación, el organizador, las fechas, modalidad, cupos, costos y reglas. La actividad se crea como borrador y posteriormente puede publicarse. La publicación genera el token público y el código QR.

> **ESPACIO PARA CAPTURA 12 — Formulario de creación de actividad**

```text
[ CAPTURA DE PANTALLA: CREAR ACTIVIDAD ]
```

> **ESPACIO PARA CAPTURA 13 — Listado y estados de actividades**

```text
[ CAPTURA DE PANTALLA: LISTADO DE ACTIVIDADES ]
```

> **ESPACIO PARA CAPTURA 14 — Detalle público y código QR**

```text
[ CAPTURA DE PANTALLA: ACTIVIDAD PUBLICADA Y QR ]
```

## 5.7. Registro de equipos e inscripciones

El participante o representante registra un equipo y sus jugadores. Posteriormente selecciona una actividad disponible y envía la inscripción individual o por equipo. Las inscripciones por equipo deben ser revisadas por el organizador.

> **ESPACIO PARA CAPTURA 15 — Registro de equipo y jugadores**

```text
[ CAPTURA DE PANTALLA: EQUIPO Y JUGADORES ]
```

> **ESPACIO PARA CAPTURA 16 — Inscripción individual o por equipo**

```text
[ CAPTURA DE PANTALLA: FORMULARIO DE INSCRIPCIÓN ]
```

> **ESPACIO PARA CAPTURA 17 — Aprobación o rechazo de inscripción**

```text
[ CAPTURA DE PANTALLA: GESTIÓN DE INSCRIPCIONES ]
```

## 5.8. Pago y facturación

Al aprobar una inscripción que requiere pago, el sistema registra la transacción, calcula el ITBMS y genera una factura en PDF. La factura queda asociada al pago y contiene firmas o hashes para verificar su integridad.

> **ESPACIO PARA CAPTURA 18 — Registro o estado del pago**

```text
[ CAPTURA DE PANTALLA: PAGO ]
```

> **ESPACIO PARA CAPTURA 19 — Vista de la factura**

```text
[ CAPTURA DE PANTALLA: FACTURA ]
```

> **ESPACIO PARA CAPTURA 20 — PDF generado con TCPDF**

```text
[ CAPTURA DE PANTALLA: FACTURA PDF ]
```

## 5.9. Incidentes, árbitros y estadísticas

El organizador puede registrar incidentes ocurridos durante las actividades y evaluar el desempeño de los árbitros. El sistema presenta estadísticas relacionadas con actividades, inscripciones, ingresos, incidentes y evaluaciones.

> **ESPACIO PARA CAPTURA 21 — Registro de incidente**

```text
[ CAPTURA DE PANTALLA: INCIDENTES ]
```

> **ESPACIO PARA CAPTURA 22 — Evaluación de árbitro**

```text
[ CAPTURA DE PANTALLA: EVALUACIÓN DE ÁRBITRO ]
```

> **ESPACIO PARA CAPTURA 23 — Panel de estadísticas**

```text
[ CAPTURA DE PANTALLA: ESTADÍSTICAS ]
```

## 5.10. Bitácora e integridad

El administrador consulta la bitácora para revisar el usuario responsable, módulo, acción, dirección IP, fecha y firma HMAC. Esta sección permite demostrar que las acciones críticas dejan evidencia verificable.

> **ESPACIO PARA CAPTURA 24 — Bitácora de auditoría**

```text
[ CAPTURA DE PANTALLA: BITÁCORA ]
```

> **ESPACIO PARA CAPTURA 25 — Verificación de firma o integridad**

```text
[ CAPTURA DE PANTALLA: VERIFICACIÓN DE INTEGRIDAD ]
```

## 5.11. Cambio de contraseña y cierre de sesión

Todos los usuarios autenticados pueden cambiar su contraseña desde el módulo correspondiente. Al finalizar sus operaciones deben cerrar la sesión para invalidar el acceso actual.

> **ESPACIO PARA CAPTURA 26 — Cambio de contraseña**

```text
[ CAPTURA DE PANTALLA: CAMBIAR CONTRASEÑA ]
```

> **ESPACIO PARA CAPTURA 27 — Cierre de sesión o regreso al login**

```text
[ CAPTURA DE PANTALLA: CIERRE DE SESIÓN ]
```

---

# 6. Pruebas rápidas y evidencia de seguridad

Para la sustentación se recomienda demostrar como mínimo:

1. Inicio de sesión correcto.
2. Rechazo de credenciales inválidas.
3. Bloqueo temporal después del número configurado de intentos.
4. Acceso denegado del Operador al módulo de usuarios.
5. Rechazo de una petición POST sin token CSRF o con token inválido.
6. Validación y sanitización de campos.
7. Creación, búsqueda y modificación de un registro.
8. Publicación de una actividad y generación del código QR.
9. Inscripción, pago y generación de factura.
10. Consulta de la bitácora y verificación de integridad.

Ejemplo de prueba CSRF desde Postman:

- Ejecutar una petición POST válida con sesión y `csrf_token`.
- Repetir la petición eliminando o modificando el token.
- Confirmar que el servidor rechaza la operación con el código o mensaje configurado.

---

# 7. Alcance actual y mejoras futuras

El esquema de base de datos también incluye componentes preparados para ampliaciones posteriores, como:

- Invitaciones a academias y equipos.
- Calendario con varias jornadas por actividad.
- Recuperación de contraseña mediante tokens.
- Flujo de pagos pendientes o en revisión.
- Pasarela de pago real.
- Notificaciones y mensajería.
- Mayor integración de firmas RSA por usuario para no repudio.
- Conversión de todas las acciones de modificación a POST con CSRF.
- Configuración obligatoria de HTTPS y Content Security Policy en producción.

---

# 8. Licencia y uso académico

Este proyecto fue desarrollado con fines académicos para la asignatura Desarrollo de Software VII. Los datos de prueba, usuarios y credenciales deben sustituirse antes de utilizar el sistema fuera de un entorno controlado.
