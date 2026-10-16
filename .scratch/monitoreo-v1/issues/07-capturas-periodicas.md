# 07: Capturas periódicas

**What to build:** El servidor recibe las Capturas que envía el Agente, las guarda de forma segura y un Revisor puede verlas en una galería filtrable por persona y fecha. Cada vez que alguien abre una Captura queda en la Auditoría, y el Empleado puede verlo ([ADR-0003](../../../docs/adr/0003-imagenes-en-garage-via-laravel.md)).

**Blocked by:** 06

**Status:** ready-for-agent

- [ ] Recepción con las mismas comprobaciones de firma, consentimiento, Horario y Módulo del ticket 06
- [ ] Solo se aceptan imágenes webp/png/jpeg validadas por su contenido real (magic bytes), con límite de tamaño y de dimensiones
- [ ] El servidor re-codifica cada imagen a WebP y le quita los metadatos (Q57); Horizon genera la miniatura
- [ ] Se guardan en un bucket privado de Garage, solo accesible en la red interna, con una clave generada por el servidor
- [ ] Lista de exclusión de apps y Sitios configurable por el Administrador; el Agente la recibe y no toma Capturas periódicas de ellos
- [ ] Galería con filtros por persona y fecha, limitada a las Áreas del Revisor
- [ ] Cada imagen se entrega solo a través de un enlace firmado que caduca a los 5 minutos y exige sesión, con `no-store`
- [ ] Cada apertura queda en la Auditoría (quién, cuándo, qué Captura)
- [ ] Tests de Pest: tipo no permitido, Revisor de otra Área (404), enlace caducado y registro en la Auditoría
