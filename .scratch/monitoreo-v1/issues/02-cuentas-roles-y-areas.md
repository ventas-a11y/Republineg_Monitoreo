# 02: Cuentas internas, roles y Áreas

**What to build:** Un Administrador crea las cuentas del panel, les asigna el rol (Administrador, Revisor o Empleado) y asigna Áreas a los Revisores. Nadie puede registrarse por su cuenta y todas las cuentas usan 2FA o passkey. Desde este ticket, toda ruta nueva queda cubierta por la matriz de autorización.

**Blocked by:** 01

**Status:** ready-for-agent

- [ ] El registro público de Fortify está desactivado; solo un Administrador crea cuentas
- [ ] Roles Administrador, Revisor y Empleado. Un Revisor puede tener varias Áreas y un Área varios Revisores
- [ ] Cada cuenta exige 2FA o passkey antes de usar el panel (Q52). Contraseña de mínimo 8 caracteres, máximo de al menos 64, sin reglas de composición y comprobada contra contraseñas filtradas (Q53)
- [ ] Hashing argon2id; sesión de 30 min de inactividad y 8 h como máximo, con cuenta regresiva; cookie con prefijo `__Host-`
- [ ] `TrustProxies` solo con la red de Traefik y `TrustHosts` con el host canónico
- [ ] Policies con denegación por defecto; 404 uniforme; ULID en las URLs; `$fillable` explícito en todos los modelos
- [ ] Una matriz de autorización versionada (rol × ruta × método → código esperado) alimenta un dataset de Pest
- [ ] Un test falla si alguna ruta no tiene `auth` y Policy
- [ ] Mismo mensaje y mismo código para cualquier fallo de login; throttle por cuenta
