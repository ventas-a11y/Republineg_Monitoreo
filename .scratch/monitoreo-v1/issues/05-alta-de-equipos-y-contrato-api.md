# 05: Alta y revocación de Equipos + contrato de la API v1

**What to build:** Un Administrador genera un Código de alta de un solo uso, el Agente lo canjea y el Equipo aparece en el panel con su propio token, que se puede revocar en cualquier momento. Este ticket también publica el contrato `v1` de la API, del que depende el repositorio del Agente ([ADR-0006](../../../docs/adr/0006-agente-en-repositorio-aparte.md)).

**Blocked by:** 02

**Status:** ready-for-agent

- [ ] Código de alta de un solo uso, que caduca y queda registrado en la Auditoría
- [ ] El canje entrega un token Sanctum cuyo dueño es el Equipo, más el secreto HMAC del Equipo; ninguno de los dos se vuelve a mostrar después
- [ ] Rotación del token cada 90 días, que el Agente pide antes de que venza (Q58)
- [ ] Revocación inmediata desde el panel: el token revocado recibe 401
- [ ] Lista de Equipos en el panel con su estado (en línea / sin reportar) y la fecha del último contacto
- [ ] Contrato `v1` documentado en este repositorio: rutas con prefijo `v1`, autenticación, firma HMAC con Content-Digest y timestamp, formato de errores `problem+json`, códigos 401/403/405/413/415/422/429, y formato de las órdenes firmadas con Ed25519
- [ ] La API rechaza tokens en la URL y responde con `no-store`
- [ ] Tests de Pest: código de un solo uso, código caducado, token revocado, rotación
