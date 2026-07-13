# Lista de Documentacion Pendiente — Sistema de Eventos Deportivos

Basado en las hojas "Requisitos del Sistema", "Documentacion", "Keys Management" y
"Valor Estrategico del Sistema" del Excel de la propuesta de examen.

## 1. Documentacion con UML (0.1 pts) — hoja "Documentacion"

Puntaje distribuido: Requisitos no funcionales (4), Modelado funcional (4),
Modelo estructural (4), Modelo dinamico (4), Base de datos (4) = 20 pts totales.

### 1.1 Requisitos de Instalacion / No Funcionales
- [ ] Requisitos de Hardware (RAM, procesador, espacio en disco recomendado).
- [ ] Requisitos de Software (PHP 8.1+, MySQL/MariaDB, Composer, Apache/Nginx).
- [ ] Pasos generales de instalacion (puedes basarte en el `README.md` del proyecto y ampliarlo con capturas).
- [ ] Requisitos funcionales (lista numerada R1, R2, R3... — copia y redacta a partir de los 20 requisitos del Excel).
- [ ] Requisitos de rendimiento (tiempos de respuesta esperados, concurrencia minima).
- [ ] Requisitos logicos de base de datos (identificados como R1, R2... ligados a las tablas de `database/schema.sql`).
- [ ] Restricciones de diseno (ej. PHP 8.1+, arquitectura MVC obligatoria, PSR-4).
- [ ] Atributos del sistema (seguridad, usabilidad, mantenibilidad, escalabilidad).
- [ ] Numeracion de paginas en todo el documento (penalizacion de -3 si falta).

### 1.2 Modelado Funcional (Casos de Uso)
- [ ] Diagrama de casos de uso general (actores: Administrador, Operador, Organizador, Participante).
- [ ] Descripcion textual de cada caso de uso principal, como minimo:
  - Iniciar sesion
  - Gestionar usuarios (CRUD)
  - Gestionar deportes / instalaciones / academias / organizadores / arbitros
  - Registrar equipo y jugadores
  - Crear y gestionar actividad deportiva
  - Inscribirse a una actividad (por equipo e individual)
  - Aprobar/rechazar inscripcion
  - Emitir factura
  - Reportar incidente
  - Evaluar arbitro
  - Consultar estadisticas

### 1.3 Modelo Estructural
- [ ] Diagrama de Clases del Negocio (Usuario, Actividad, Equipo, Jugador, Inscripcion, Factura, Arbitro, etc.) **con las relaciones entre clases claramente dibujadas** (multiplicidad, asociaciones, herencia si aplica). *Nota de la retroalimentacion previa: asegurate de que las relaciones entre clases sean visibles, no solo las clases sueltas.*
- [ ] Diagrama de Componentes (Front Controller, Controllers, Models, Views, Security, Utils, Base de Datos).

### 1.4 Modelo Dinamico
- [ ] Diagramas de Actividad — uno por proceso clave, **cada uno en su propia pagina** (ej. inscripcion por equipo, emision de factura, cancelacion de actividad con reembolso).
- [ ] Diagramas de Secuencia — para los flujos criticos: Login, Crear Actividad + firma HMAC, Emitir Factura + firma digital, Inscripcion individual.
- [ ] Diagramas de Estado — **solo para objetos individuales relevantes** (ej. ciclo de vida de una `Actividad`: PROGRAMADA → EN_CURSO → FINALIZADA/CANCELADA; ciclo de una `Inscripcion`: PENDIENTE → APROBADA/RECHAZADA/CANCELADA). Manten cada diagrama simple: transiciones de estado sobre un unico objeto, no del sistema completo.

### 1.5 Base de Datos
- [ ] Diagrama Entidad/Relacion (puedes generarlo desde `database/schema.sql` con MySQL Workbench, dbdiagram.io o similar).
- [ ] Script de base de datos exportado (`database/schema.sql` ya incluido en el proyecto — adjuntarlo tal cual o el dump final con datos de prueba).

## 2. Cumplir con los Requisitos del Proyecto (0.6 pts)
- [ ] Verificar en tu documentacion que cada uno de los 20 requisitos de la hoja "Requisitos del Sistema" este explicitamente cubierto y referenciado (usa la tabla del `README.md` como punto de partida).
- [ ] Incluir capturas de pantalla de cada modulo funcionando.

## 3. Video Explicativo (0.05 pts)
- [ ] Grabar un video (5-10 min recomendado) mostrando:
  - Login y control de errores.
  - CRUD de al menos 2-3 modulos de catalogo.
  - Creacion de una actividad y su codigo QR.
  - Inscripcion de un equipo, aprobacion y generacion de factura con ITBMS.
  - Verificacion de integridad (mostrar que un registro alterado directamente en BD se marca como invalido).
  - Estadisticas del sistema.
- [ ] Subir el video y enlazarlo dentro de la documentacion **y tambien en el resumen/README del repositorio**.

## 4. URLs obligatorias en la documentacion (0.1 pts)
- [ ] URL del repositorio (GitHub/GitLab).
- [ ] URL del backup de la base de datos (puede ser un enlace a Google Drive con el `.sql`).
- [ ] Ambas URLs deben aparecer tanto en el documento formal como en el README del repositorio.

## 5. Seguridad — Documento de Key Management (referencia: hoja "Keys Management")
- [ ] Explicar por que la llave privada RSA de cada usuario **no se guarda en texto plano** (riesgo de suplantacion por el propio administrador de la BD).
- [ ] Documentar que la llave privada se cifra con AES-256 usando una passphrase que solo el usuario conoce (implementado en `FirmaDigitalRsaService::cifrarLlavePrivada`).
- [ ] Mencionar la alternativa de HSM (Hardware Security Module) como mejora futura si no se dispone de uno.
- [ ] Explicar el mecanismo de HMAC-SHA256 usado para la integridad de Actividades y Facturas (por que se eligio HMAC para integridad y RSA para no repudio).

## 6. Valor Estrategico del Sistema (para la presentacion/sustentacion)
Redactar 3-4 parrafos cortos (puedes basarte en la hoja "Valor Estrategico del Sistema" del Excel) cubriendo:
- [ ] Seguridad proactiva basada en OWASP.
- [ ] Arquitectura escalable orientada a objetos (MVC + PSR-4).
- [ ] Integridad y no repudio (HMAC + RSA).
- [ ] Resiliencia tecnica de la capa de seguridad (contrato `TransformadorSeguridadInterface`).

## 7. Otros detalles menores de la rubrica
- [ ] Confirmar que el sistema cuenta con CSS en todas las vistas (penalizacion de -20 pts si no); ya incluido en `public/assets/css/style.css`.
- [ ] Confirmar menus horizontales en todas las paginas autenticadas (ya incluido en `views/layout/main.php`).
- [ ] Revision preliminar con el profesor antes de la entrega final (0.1 pts ya obtenidos segun el Excel).
