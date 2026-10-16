# 02: Cuentas internas, roles y Áreas

**What to build:** Un Administrador crea las cuentas del panel, les asigna el rol (Administrador, Revisor o Empleado) y asigna Áreas a los Revisores. Nadie puede registrarse por su cuenta y todas las cuentas usan 2FA o passkey. Desde este ticket, toda ruta nueva queda cubierta por la matriz de autorización.

**Blocked by:** 01

**Status:** ready-for-human

- [x] El registro público de Fortify está desactivado; solo un Administrador crea cuentas
- [x] Roles Administrador, Revisor y Empleado. Un Revisor puede tener varias Áreas y un Área varios Revisores
- [x] Cada cuenta exige 2FA o passkey antes de usar el panel (Q52). Contraseña de mínimo 8 caracteres, máximo de al menos 64, sin reglas de composición y comprobada contra contraseñas filtradas (Q53)
- [x] Hashing argon2id; sesión de 30 min de inactividad y 8 h como máximo, con cuenta regresiva; cookie con prefijo `__Host-` (en producción, por variables de entorno)
- [x] `TrustProxies` solo con la red de Traefik y `TrustHosts` con el host canónico
- [x] Policies con denegación por defecto; 404 uniforme; ULID en las URLs; `$fillable` explícito en todos los modelos
- [x] Una matriz de autorización versionada (rol × ruta × método → código esperado) alimenta un dataset de Pest
- [x] Un test falla si alguna ruta no tiene `auth` y Policy
- [x] Mismo mensaje y mismo código para cualquier fallo de login; throttle por cuenta
- [ ] Prueba manual del flujo completo (crear el primer Administrador, recibir la invitación, cambiar la contraseña, configurar 2FA, crear cuentas y Áreas)

## Decisiones tomadas en el ticket (2026-10-16)

- **Roles:** columna `role` con un enum PHP, sin paquete de permisos.
- **Áreas:** un Empleado pertenece a una sola Área; un Revisor revisa una o más (tabla `area_reviewer`).
- **ULID:** clave primaria de `users` y `areas`. Se modificaron las migraciones del starter kit porque no había datos.
- **Alta:** el Administrador crea la cuenta y la persona recibe por correo una contraseña temporal aleatoria (16 caracteres) que vence en 72 h. En el primer inicio de sesión debe cambiarla y luego configurar 2FA o passkey. Cambiarla verifica el correo. El correo se envía sin cola, para que la contraseña nunca quede guardada en Valkey.
- **Primer Administrador:** `php artisan monitoreo:crear-administrador {email} {nombre}`.
- **Un Administrador no puede quitarse su propio rol**, para que el panel nunca quede sin administradores.

## Notas

- **Defecto encontrado por la matriz:** el cambio de contraseña temporal validaba antes de responder 404; ahora autoriza primero.
- **Contraseñas filtradas:** `uncompromised()` solo se aplica en producción, porque necesita salida a Internet (Q70 sigue sin confirmar).
- **Ajustes tras la primera prueba manual (2026-10-16):**
    - se quitó el autoborrado de cuenta (solo el Administrador gestiona cuentas);
    - el correo solo lo cambia un Administrador; en Perfil se muestra como solo lectura;
    - la interfaz y los mensajes de Laravel quedaron en español (`lang/es`);
    - Seguridad muestra primero el aviso del paso 2 y las secciones de segundo factor, porque el formulario de contraseña hacía creer que el cambio no se había guardado;
    - "¿Olvidaste tu contraseña?" responde igual exista o no la cuenta (antes permitía averiguar qué correos están registrados).
- **Pendiente para el ticket 12:** cerrar las sesiones activas al reenviar una invitación.
