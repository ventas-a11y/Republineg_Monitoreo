# 17: Despliegue en Dokploy

**What to build:** La aplicación se despliega en un entorno de staging sobre Dokploy, con la imagen construida en CI, TLS válido, solo accesible desde la red interna, y con respaldos cuya restauración se ha probado.

**Blocked by:** 01

**Status:** ready-for-agent

- [ ] La imagen se construye en GitHub Actions, se publica en GHCR y Dokploy la despliega por digest; producción no construye código (Q68)
- [ ] El stack de Compose corre en Dokploy: app, horizon, reverb, scheduler, postgres, valkey y garage
- [ ] TLS con Let's Encrypt por DNS-01 y un hostname poco descriptivo (Q62); el host de la API rechaza HTTP plano
- [ ] El panel y la API solo son accesibles desde la red interna de Intelicorp (IPAllowList en Traefik); el acceso remoto es por VPN (Q63)
- [ ] Respaldos diarios de Postgres y de Garage en otro disco, con una restauración probada
- [ ] Timeouts y límite de tamaño de petición en Traefik
- [ ] Los secretos (clave Ed25519, secreto maestro HMAC) se montan como archivos, no como variables de entorno (Q56)
- [ ] Documento de despliegue con los pasos y las variables necesarias
