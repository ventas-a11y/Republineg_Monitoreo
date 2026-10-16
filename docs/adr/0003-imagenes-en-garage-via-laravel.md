# Imágenes en Garage, con Laravel como única puerta de entrada y salida

Las Capturas no se guardan en PostgreSQL ni en el disco de la app: van a un bucket privado de Garage (compatible con S3) sobre disco cifrado, y PostgreSQL guarda solo los metadatos. **Garage es 100 % interno.** El Agente sube cada Captura a Laravel, que la re-codifica a WebP y la escribe en Garage. El panel la muestra a través de Laravel con un enlace firmado que caduca a los 5 minutos (`temporarySignedRoute`), así que cada visualización pasa obligatoriamente por la Auditoría, que el Empleado puede ver.

_Revisado el 2026-10-15 (ronda de seguridad, [docs/SEGURIDAD.md](../SEGURIDAD.md) Q50): la versión inicial servía las imágenes con URLs prefirmadas de S3 directas al navegador._

## Considered Options

- **URLs prefirmadas de S3 directas al navegador** (rechazada en la revisión): obligaba a publicar el endpoint de Garage en Traefik, rompiendo la separación entre la red pública y la red de datos, y la Auditoría dependía de que la app registrara el acceso _antes_ de entregar la URL. Con pocos Revisores, que Laravel entregue las imágenes no cuesta casi nada.
- **Subida directa del Agente a Garage con URL prefirmada** (rechazada): obligaba a exponer Garage a los 30 Equipos. Con unas 3.000 Capturas diarias, Laravel absorbe la carga sin problema.

## Consequences

- El lifecycle de Garage es parcial (solo Expiration), así que la Retención la ejecuta un job de Horizon que borra el objeto y la fila juntos.
- Un enlace filtrado sirve como mucho 5 minutos y, además, exige sesión y permiso en el panel.
- Las respuestas con imágenes llevan `Cache-Control: no-store`.
