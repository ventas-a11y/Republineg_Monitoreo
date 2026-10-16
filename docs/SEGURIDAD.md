# Seguridad — Republicneg Monitoreo

Este documento reúne las medidas de seguridad del sistema, basadas en la [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org/). Las referencias entre corchetes, como **[Laravel]**, apuntan a la cheat sheet `https://cheatsheetseries.owasp.org/cheatsheets/<Nombre>_Cheat_Sheet.html`.

Complementa al [ROADMAP](ROADMAP.md), a los [ADRs](adr/) y al [spec v1](../.scratch/monitoreo-v1/spec.md). Las fases (F0–F4) son las del ROADMAP.

El sistema guarda Capturas de pantalla y la actividad de personas reales. Un fallo de seguridad aquí es, además, un incidente de datos personales bajo la LOPDP.

## 1. Fronteras de confianza

| Frontera                         | Qué cruza                                              | Qué no es confiable                                                                                    |
| -------------------------------- | ------------------------------------------------------ | ------------------------------------------------------------------------------------------------------ |
| Agente → API                     | Lotes de Eventos, Capturas, frames de Vista en vivo    | **Todo** lo que manda el Equipo: títulos de ventana, nombres de documentos y de USB, imágenes, relojes |
| Reverb → Agente                  | Órdenes (capturar ahora, Vista en vivo, configuración) | Cualquier mensaje sin firma válida del servidor                                                        |
| Navegador → panel                | Acciones de Revisores, Administradores y Empleados     | La interfaz: ocultar algo no es un control                                                             |
| App → Postgres / Valkey / Garage | Datos, colas, imágenes                                 | — (red interna, credenciales de mínimo privilegio)                                                     |

**F0:** dibujar el DFD con estas fronteras, revisarlo con STRIDE y asignar a cada amenaza una respuesta (mitigar, eliminar, transferir o aceptar). Se revisa cuando cambie la arquitectura. **[Threat_Modeling]**

## 2. Decisiones (rondas 6 y 7)

| #       | Decisión                                                                                                                                                                                                                                                                                                | Fuente OWASP                                   | Fase  |
| ------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------- | ----- |
| Q43     | **Consentimiento como control de acceso.** El servidor rechaza los datos de un Empleado sin la política vigente aceptada, y también si no puede comprobarlo (falla cerrado). Al dar de baja a un Empleado se invalidan resúmenes, Reportes programados y cachés derivados                               | AI Advertising §1                              | F1    |
| Q44     | **Órdenes firmadas del servidor al Agente** con Ed25519. La firma cubre Revisor, Equipo, acción, Motivo, emisión, expiración (≤ 60 s) y key-id. El Agente rechaza las órdenes sin firma, vencidas o repetidas, y solo tiene la clave pública                                                            | AI Advertising §4                              | F1    |
| Q45     | **Integridad de los lotes.** Cada lote lleva timestamp, Content-Digest (RFC 9530) y HMAC con un secreto propio del Equipo. El servidor verifica antes de leer el contenido, rechaza desfases de más de 5 min y deduplica por id de evento. El Agente confía solo en la CA emisora                       | AI Advertising §4                              | F1    |
| Q46     | **Acciones por nivel de riesgo.** Bajo: ver. Medio: abrir una Captura (Auditoría). Alto: Captura bajo demanda, Vista en vivo y cambios de Reglas, roles, Módulos o Retención; exige permiso explícito y reconfirmar la contraseña o la 2FA cada 15 min                                                  | AI Advertising §4, [Transaction_Authorization] | F2    |
| Q47     | **Incidentes.** Si se compromete el token de un Equipo: revocar, rotar y marcar como _sospechosos_ sus datos desde el último momento confiable. Interruptor global "pausar todo el monitoreo"                                                                                                           | AI Advertising §4–5                            | F2    |
| Q48     | **Cadena de suministro.** SBOM CycloneDX (composer, pnpm, imagen Docker; el repo del Agente genera el suyo), imágenes fijadas por digest, MSI y binarios firmados y verificados                                                                                                                         | AI Advertising §2, §5                          | F0/F4 |
| Q49     | **IA.** La v1 no usa IA. Si se agrega (clasificación automática, resúmenes), se aplican las secciones 2–3 de la cheat sheet AI Advertising: corpus firmado, entradas tratadas como no confiables, cachés aisladas                                                                                       | AI Advertising §2–3                            | —     |
| Q50     | **Garage 100 % interno.** Laravel entrega las imágenes con un enlace firmado de 5 min (`temporarySignedRoute`), así que cada visualización pasa obligatoriamente por la Auditoría ([ADR-0003](adr/0003-imagenes-en-garage-via-laravel.md))                                                              | [Network_Segmentation]                         | F2    |
| Q51     | **Sesión del panel:** 30 min de inactividad y 8 h como máximo (middleware propio), con cuenta regresiva antes de cerrar                                                                                                                                                                                 | [Session_Management]                           | F0    |
| Q52     | **2FA o passkey obligatorio para todas las cuentas**, incluidos los Empleados. Mínimo 8 caracteres, máximo ≥ 64, sin reglas de composición, permitido pegar                                                                                                                                             | [Authentication], [MFA]                        | F0    |
| Q53     | **Contraseñas filtradas:** `Password::uncompromised()` (HIBP con k-anonimato). Si no hay salida a Internet, una lista local                                                                                                                                                                             | [Authentication]                               | F0    |
| Q54     | **Sin TLS entre la app y Postgres** en v1: red Docker interna sin salida. Se revisa si cambia la topología                                                                                                                                                                                              | [Database]                                     | —     |
| Q55     | **`docker.sock`:** solo lo usan Dokploy y Traefik. Ningún contenedor de la app lo monta                                                                                                                                                                                                                 | [Docker]                                       | F0    |
| Q56     | **Secretos:** la clave privada Ed25519 y el secreto maestro de HMAC se montan como archivos (Docker/Dokploy), no como variables de entorno. El resto va en las variables de Dokploy, separadas por entorno                                                                                              | [Secrets_Management]                           | F0    |
| Q57     | **Re-codificar las Capturas** a WebP en el servidor: quita metadatos y neutraliza archivos políglotas                                                                                                                                                                                                   | [File_Upload]                                  | F1    |
| Q58     | **Token por Equipo:** rotación automática cada 90 días (el Agente la pide antes de vencer) y revocación inmediata                                                                                                                                                                                       | [WebSocket_Security], [REST_Security]          | F1    |
| Q59     | **HSTS** de 2 años con `includeSubDomains`, **sin** `preload` (dominio interno)                                                                                                                                                                                                                         | [HTTP_Headers]                                 | F2    |
| Q60     | **Restablecer la 2FA:** lo hace un Administrador tras verificar la identidad en persona, con Auditoría y aviso por correo. A otro Administrador se lo restablece un segundo Administrador                                                                                                               | [MFA]                                          | F2    |
| Q61     | **Trusted Types:** se prueba en F4, primero en modo de solo reporte                                                                                                                                                                                                                                     | [XSS]                                          | F4    |
| Q62/Q69 | **Certificados:** Let's Encrypt con DNS-01 y un hostname poco descriptivo; el Agente confía en las raíces ISRG. Intelicorp no tiene CA interna                                                                                                                                                          | [TLS]                                          | F4    |
| Q63     | **Solo red interna:** Traefik limita por IP (IPAllowList) el panel y la API del Agente a la red de Intelicorp; el acceso remoto es por VPN. Un portátil fuera de la red guarda en su cola local (7 días) y envía al volver                                                                              | [Credential_Stuffing], [Network_Segmentation]  | F0/F2 |
| Q64     | **El email sigue siendo el usuario de acceso** (Fortify). Con 2FA o passkey obligatorio, un usuario no predecible aporta poco                                                                                                                                                                           | [Credential_Stuffing]                          | —     |
| Q65     | **Contrato estricto:** la API del Agente rechaza con 422 los campos desconocidos; el panel los ignora                                                                                                                                                                                                   | [Input_Validation]                             | F1    |
| Q66     | **Reglas de alerta con comodines o "contiene"**, nunca con regex (evita ReDoS y es más fácil de configurar)                                                                                                                                                                                             | [Input_Validation]                             | F2    |
| Q67     | **Aceptar un riesgo residual** lo firma la Gerencia de TI de Intelicorp (nunca quien desarrolla), con análisis técnico y CVSS por escrito                                                                                                                                                               | [Vulnerable_Dependency_Management]             | F0    |
| Q68     | **Build en CI:** runner efímero de GitHub Actions, publicación en GHCR y despliegue en Dokploy por digest. El servidor de producción no construye código                                                                                                                                                | [Software_Supply_Chain_Security]               | F0    |
| Q70     | **Salida a Internet del servidor: por confirmar.** Se planifica asumiendo que sí la hay (HIBP, DNS-01, registros públicos). Si no la hubiera: lista local de contraseñas filtradas, certificados emitidos fuera del servidor (DNS-01 desde otra máquina) y un mirror interno de imágenes y dependencias | —                                              | F0    |

## 3. Controles adoptados

### Servidor (Laravel)

- **Configuración** (F0):
    - `APP_DEBUG=false`; directorios ≤ 775 y archivos ≤ 664. **[Laravel]**
    - php.ini: `expose_php=Off`, `display_errors=Off`, `log_errors=On`, `allow_url_fopen=Off`, `allow_url_include=Off`, `zend.exception_ignore_args=On`, y `post_max_size` / `upload_max_filesize` ajustados al tamaño máximo de una Captura. No se usa la lista completa de `disable_functions`, porque Horizon necesita `proc_open`. **[PHP_Configuration]**
- **Cookie de sesión** con prefijo `__Host-`, `secure`, `http_only`, `domain=null` y `same_site=lax`; driver de sesión en base de datos o Valkey para poder listar y cerrar sesiones. La sesión se regenera también al cambiar de rol o Área. **[Session_Management]** F0/F2
- **Asignación masiva:** `$fillable` explícito en todos los modelos; nunca `$guarded = []`, `unguard()` ni `forceFill` con datos de entrada; solo `$request->validated()`. **[Mass_Assignment]** F0
- **Hashing:** `argon2id` (m=19456 KiB, t=2, p=1) con rehash al iniciar sesión. **[Password_Storage]** F0
- **Login:**
    - mismo mensaje y mismo código para cualquier fallo, sin diferencias de tiempo;
    - throttle de Fortify por cuenta y no solo por IP;
    - la recuperación sigue disponible aunque la cuenta esté bloqueada. **[Authentication]** F0
- **Autorización:**
    - Policies en todos los modelos, con denegación por defecto;
    - consultas siempre a través del alcance (Área) del Revisor;
    - 404 tanto si el recurso no existe como si está prohibido;
    - ULID en las URLs de Equipos, Capturas y Reportes;
    - nunca se envían en los props de Inertia datos que la interfaz "oculta". **[Authorization]**, **[IDOR]** F0/F2
- **API del Agente:**
    - lista de métodos permitidos (405 al resto);
    - 415 si el Content-Type no es el esperado, 413 si es demasiado grande, 429 por rate limit;
    - tokens nunca en la URL; `Cache-Control: no-store`;
    - errores en formato `application/problem+json` (RFC 9457), sin trazas;
    - validaciones baratas (token, HMAC, tamaño) antes que las caras (decodificar la imagen). **[REST_Security]**, **[Error_Handling]**, **[DoS]** F1
- **Capturas:**
    - allowlist webp/png/jpeg validada por _magic bytes_;
    - límite de dimensiones y píxeles (contra bombas de descompresión);
    - clave en Garage generada por el servidor (ULID), sin usar nombres que mande el Agente;
    - bucket privado, sin listado, con credenciales distintas para escribir y para leer. **[File_Upload]** F1
- **Reverb:**
    - canal privado por Equipo, autorizado contra el Equipo dueño del token Sanctum; canales del panel autorizados por Policy y Área;
    - en `config/reverb.php` (verificado en el paquete): `allowed_origins` con solo el host del panel (por defecto viene `['*']`); `max_message_size` en su valor por defecto de 10 KB o menos (las órdenes firmadas son pequeñas y los frames no pasan por Reverb, ADR-0001); `accept_client_events_from` (por defecto `members`) en el valor que desactiva los client events, a verificar al instalar; `rate_limiting.enabled = true` con `terminate_on_limit`; `max_connections` acotado; `max_request_size` del servidor ajustado;
    - los sockets se cierran al cerrar sesión, al cambiar de rol o al revocar el Equipo, y la sesión se revalida cada ~30 min. **[WebSocket_Security]** F1/F2
- **Texto que viene del Equipo** (títulos, documentos, USB): normalización Unicode NFC, eliminación de caracteres invisibles y de control bidireccional, límite de longitud, escape siempre, y neutralización de fórmulas (`= + - @`) en CSV. **[XSS]**, AI Advertising §2 F1
- **Logging de seguridad:**
    - separado de la Auditoría, en JSON con el vocabulario OWASP (`authn_*`, `authz_fail`, `upload_validation`, `input_validation_fail`, `excess_rate_limit_exceeded`, `session_*`, `sys_*`);
    - nunca registra tokens, secretos HMAC, OTP, challenges WebAuthn ni IDs de sesión (como mucho su hash);
    - neutraliza CR/LF. **[Logging]**, **[Logging_Vocabulary]** F1
- **Retención también en logs y backups;** de los logs se excluyen los datos con consentimiento revocado. **[Logging]** F3
- **Rotación de claves probada:** `APP_KEY` con `APP_PREVIOUS_KEYS`; Ed25519 con key-id para que el Agente acepte la clave vieja y la nueva durante el cambio. **[Key_Management]** F3/F4

- **Proxies y hosts** (F0): `TrustProxies` solo con la IP o red de Traefik (nunca `*`) y `TrustHosts` con el host canónico. Así un `X-Forwarded-Host` falso no puede envenenar los enlaces firmados ni los de restablecimiento. **[Web_Cache_Security]**
- **Validación de los lotes del Agente** (F1):
    - número máximo de eventos por lote y límite de profundidad del JSON;
    - cada ítem validado (`type` contra un enum, campos obligatorios, null explícito);
    - validación semántica: timestamp ni futuro ni anterior al alta del Equipo, fin posterior al inicio;
    - UTF-8 malformado rechazado (`mb_check_encoding`) antes de normalizar a NFC, sin decodificar dos veces. **[Input_Validation]**
- **La cola también es una frontera:** los jobs de Horizon reciben ULIDs y releen desde la base; nunca reciben datos del Agente sin validar. **[Input_Validation]** F1
- **Caché en Valkey:** claves con prefijo por entorno y versión, con el Área o el usuario dentro de la clave; se autoriza antes de servir un acierto; las decisiones de autorización se cachean con TTL corto. El dashboard de Horizon (Gate `viewHorizon`) es solo para Administradores. **[Web_Cache_Security]** F0/F2
- **Cobertura de autorización:**
    - un test recorre todas las rutas y falla si alguna del panel o de la API no tiene `auth` y Policy/`can`;
    - una matriz de autorización versionada (rol × ruta × método → código esperado, incluidos anónimo, token de Equipo contra el panel y sesión contra la API del Agente) es la única fuente de un dataset de Pest;
    - toda ruta nueva tiene que estar en la matriz. **[Authorization]**, **[Authorization_Testing_Automation]** F0/F2
- **Casos de abuso:** cada caso tratado (`ABUSE_001`…) tiene un test Pest que cita su ID (`->group('abuse')`). **[Abuse_Case]** F1–F4
- **Credential stuffing** (F2):
    - tras varios restablecimientos de contraseña desde IPs o dispositivos distintos, la cuenta queda restringida hasta verificar la identidad;
    - métricas y alertas de intentos distribuidos sobre los eventos `authn_*`;
    - los bloqueos son temporales y hay un procedimiento para levantarlos. **[Credential_Stuffing]**
- **Cambio de email:** solo se activa tras verificar el correo nuevo. **[Input_Validation]** F2
- **Caché HTTP:** middleware global con `no-store` por defecto (también en errores, redirecciones, 401/403/404 y problem+json); solo los assets de Vite con hash llevan `public, max-age=31536000, immutable`. Restricciones `whereUlid` en los parámetros de ruta. **[Web_Cache_Security]** F2

### Panel

- **Cabeceras:**
    - `nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, COOP `same-origin`, CORP `same-site`;
    - `Permissions-Policy: camera=(), microphone=(), geolocation=()`, `X-Robots-Tag: noindex, nofollow`;
    - sin `Server` ni `X-Powered-By`, y sin `X-XSS-Protection`. **[HTTP_Headers]** F2
- **CSP estricta con nonce** (`'strict-dynamic'`):
    - `object-src 'none'`, `base-uri 'none'`, `form-action 'self'`, `frame-ancestors 'none'`;
    - `connect-src` con el wss de Reverb;
    - primero en modo Report-Only y después en enforce;
    - antes de implementarla, verificar con `search-docs` cómo encaja con Inertia 3. **[CSP]** F2
- **React:**
    - prohibido `dangerouslySetInnerHTML` con datos del Equipo;
    - los enlaces derivados de títulos solo pueden ser http/https. **[XSS]** F2
- **CSRF:**
    - se mantiene el de Laravel;
    - se rechazan las peticiones que cambian estado con `Sec-Fetch-Site: cross-site`;
    - "Capturar ahora" y "Vista en vivo" nunca por GET;
    - también se protege el alta de passkeys. **[CSRF]** F2
- **Confirmación ligada a sus parámetros:** antes de una Vista en vivo, una Captura bajo demanda o una exportación, se muestran el Empleado y el Equipo destino, y la confirmación del servidor queda ligada a ellos. **[Transaction_Authorization]** F2
- **Factores de autenticación:**
    - re-autenticar para desactivar la 2FA, cambiar el email o añadir o quitar passkeys;
    - avisar por correo de cada cambio y de los fallos de 2FA;
    - impedir borrar el último factor;
    - passkeys con userVerification requerida, RP ID estrecho, sin attestation y sin caer a un método más débil. **[MFA]**, **[Passkey]** F2
- **Sesiones:**
    - pantalla de sesiones activas con cierre remoto y aviso de inicio de sesión nuevo;
    - `Clear-Site-Data` al salir;
    - `Cache-Control: no-store` en páginas y en respuestas con Capturas. **[Session_Management]** F2
- **Descargas** de CSV/PDF con `Content-Disposition: attachment`. **[HTTP_Headers]** F3

### Infraestructura (Docker / Dokploy)

- **Redes:** red `frontend` (Traefik y app) separada de la red `backend` (Postgres, Valkey, Garage) con `internal: true`. Solo Traefik publica puertos. **[Network_Segmentation]** F0
- **Contenedores:**
    - imagen `dunglas/frankenphp:1.13-php8.5-trixie` (verificado: existe, con variantes bookworm/alpine), fijada por digest;
    - usuario no root siguiendo la receta oficial: `useradd`, `chown` de `/config/caddy` y `/data/caddy`, `USER appuser`; como Traefik termina el TLS, se escucha en un puerto alto (`SERVER_NAME=:8080`) y se quita `CAP_NET_BIND_SERVICE` con `setcap -r`; `cap_drop: [ALL]`, `no-new-privileges`;
    - `read_only` con tmpfs para `storage/framework` y `bootstrap/cache`; `/config` y `/data` de Caddy deben seguir escribibles (`XDG_CONFIG_HOME`, `XDG_DATA_HOME`);
    - límites de memoria, CPU y pids;
    - Dockerfile con base fijada, revisado con lint, Trivy y Gitleaks;
    - Docker Bench en F4. **[Docker]** F0/F4
- **Postgres:**
    - un rol owner para las migraciones y un rol de app solo DML;
    - `REVOKE UPDATE, DELETE` sobre la Auditoría al rol de app;
    - sin puerto publicado; bases separadas para dev y prod;
    - backups cifrados con restauración probada. **[Database]** F0
- **Traefik:**
    - el host de la API rechaza HTTP plano (solo el panel redirige a HTTPS);
    - `readTimeout`, `maxRequestBodyBytes`, límite de conexiones y timeouts de WebSocket;
    - TLS 1.3 por defecto (1.2 solo con ECDHE+AEAD), registros CAA, sin wildcard;
    - prueba con testssl.sh en F4. **[TLS]**, **[DoS]** F1/F4
- **Inventario de claves**, cada una con su propósito, cripto-periodo y responsable: `APP_KEY`, Ed25519, HMAC por Equipo, secreto de Reverb, credenciales de Garage, CA y certificado de firma del MSI. Plan escrito de compromiso y re-key; credenciales break-glass guardadas fuera del sistema. **[Key_Management]**, **[Secrets_Management]** F0
- **Logs fuera del host,** con alerta si se dejan de escribir y control de espacio en disco. NTP en el servidor y en los Equipos, porque la verificación de timestamp de los lotes depende del reloj. **[Logging]** F1/F4
- **HSTS gradual:** empezar con `max-age=86400; includeSubDomains`, inventariar los subdominios y subir a 2 años. Con HSTS no se puede saltar un certificado inválido, así que hay alertas de vencimiento y de fallo de renovación DNS-01. **[HSTS]** F2/F4
- **Artefactos (F4):** procedencia SLSA (attestation) de la imagen y del MSI, verificada antes de desplegar o distribuir; escaneo del artefacto final (secretos, componentes no autorizados) antes de firmar; logs de auditoría de GitHub, CI y GHCR enviados al almacén central. **[Software_Supply_Chain_Security]**
- **Opcional F4:** monitoreo en tiempo de ejecución (Falco) y alerta cuando un secreto se use desde una IP inesperada. **[Docker]**

### Agente (repositorio aparte)

- Token y secreto HMAC protegidos con DPAPI (ámbito LocalMachine para el servicio), nunca en `appsettings.json`; los secretos se guardan en buffers que se ponen a cero tras usarlos. **[DotNet_Security]**, **[Secrets_Management]** F1/F4
- Sin `BinaryFormatter` (se usa `System.Text.Json`); SQLite parametrizado; `AllowAutoRedirect=false`; TLS ≥ 1.2 (preferible 1.3); `Process.Start` solo con allowlist; análisis de dependencias NuGet en CI. **[DotNet_Security]** F1

### Procesos

- **CI/CD** (en los dos repos):
    - ramas protegidas con revisión obligatoria;
    - lockfiles versionados;
    - runners sin `--privileged`;
    - aprobación manual antes de desplegar en producción;
    - logs de pipeline sin secretos. **[CI_CD_Security]** F0
- **Casos de abuso (F0):** taller con IDs (`ABUSE_001`…) y al menos tres perfiles: Revisor abusivo (Vista en vivo sobre un conocido, exportación masiva), Empleado malicioso (manipula el reloj, detiene el Agente) y Administrador que se autoasigna Áreas. Cada caso queda como "a tratar", y entonces pasa como criterio de aceptación a los tickets, o como "riesgo aceptado" con su justificación. La lista se entrega al pentester de F4. **[Abuse_Case]**
- **Dependencias (F0):**
    - `composer audit` y `pnpm audit` (u OSV-Scanner/Trivy fs) en cada PR y cada noche (en CI desde el ticket 01); Dependency-Track consume los SBOM de Q48; prioridad a CISA KEV;
    - si hay parche, se actualiza y se corren los tests; si no, workaround documentado con su CVE;
    - las supresiones son por CVE, no por paquete;
    - cada dependencia nueva se evalúa antes de agregarla (mantenimiento, historial, respuesta a vulnerabilidades). **[Vulnerable_Dependency_Management]**, **[Software_Supply_Chain_Security]**
- **Cuentas de la plataforma (F0):** MFA obligatoria y mínimo privilegio en GitHub, Dokploy, GHCR y Packagist/npm/NuGet; rotación de las credenciales de CI. **[Software_Supply_Chain_Security]**
- **Recertificación de roles y Áreas** de los Revisores cada trimestre, contra la acumulación de permisos. **[Authorization]** F3
- **Petición de datos de un tercero** (autoridad o juzgado): procedimiento para avisar al Empleado cuando la ley lo permita. **[User_Privacy]** F3
- **Pentest y regresión en F4:**
    - Origin no autorizado y mensajes demasiado grandes en Reverb;
    - sockets que siguen abiertos tras cerrar sesión;
    - acceso a objetos de otra Área en todas las operaciones;
    - matriz de autorización: Administrador, Revisor del Área A, Revisor del Área B y Empleado, contra leer, crear, editar, borrar y exportar. **[Authorization]**, **[WebSocket_Security]**

## 4. No aplica

- **OAuth/OIDC/SAML/JWT:** no hay federación y Sanctum usa tokens opacos.
- **SMS/voz, preguntas de seguridad, MFA push, CAPTCHA:** no se usan esos factores, y CAPTCHA no hace falta con unas 30 cuentas internas.
- **DDoS en la nube:** el volumen es muy bajo.
- **Panic modes / Tor:** pensados para usuarios bajo coerción, no para un panel interno.
- **ZIP/Office/antivirus de subidas:** solo se suben imágenes, desde un Agente firmado, con nombre generado por el servidor.
- **Directivas `session.*` y `open_basedir` de PHP:** Laravel gestiona sus sesiones y FrankenPHP sirve desde `public/`.
- **HPKP, Expect-CT, X-XSS-Protection:** obsoletas.
- **Kubernetes, mTLS de usuario, ligar la sesión a la IP:** no hay K8s, y en una LAN con DHCP la IP no sirve como control.
- **Secciones de publicidad y pujas** de la cheat sheet AI Advertising.
- **Defensas anti-bot** (huellas de dispositivo, detección de headless, proof-of-work): el panel es interno y tiene 2FA.
- **Repositorio privado o proxy de artefactos:** desproporcionado para ~30 PCs, salvo que Q70 confirme que no hay salida a Internet.
- **Reglas de CDN y `Vary`:** no hay CDN y Traefik no cachea.

## 5. Verificaciones (2026-10-15)

- **Cheat sheets revisadas:** 36 del índice. Access_Control está deprecada y redirige a Authorization; Web_Cache_Deception no existe y se usó Web_Cache_Security.
- **Pendiente del usuario:** confirmar si el servidor tendrá salida a Internet (Q70).

- **Reverb:** claves confirmadas en `config/reverb.php` del paquete (sección Servidor). Queda por confirmar, al instalar, qué valor de `accept_client_events_from` desactiva los client events.
- **FrankenPHP:** existe `dunglas/frankenphp` con PHP 8.5 (1.13.x; trixie, bookworm, alpine), así que PHP 8.5 (D19) se mantiene. La documentación oficial trae la receta para correr sin root. No dice qué usuario usa por defecto, así que se asume root y se cambia explícitamente.
- **CSP con nonce:** confirmado. Un middleware llama a `Vite::useCspNonce()` (Laravel 13), Laravel agrega el nonce a los tags de Vite, y en `createInertiaApp({ nonce })` se pasa el mismo nonce para los estilos inline de Inertia 3 (barra de progreso y modal de error).
