# 06: Ingesta de Eventos de actividad

**What to build:** El servidor recibe del Agente lotes de Eventos de actividad (app y ventana en primer plano, Sitio, sesión bloqueada o desbloqueada, USB, impresión) y los guarda sin duplicados. Solo acepta los datos permitidos: Empleado con consentimiento vigente, dentro del Horario laboral y con el Módulo encendido. El Administrador configura el Horario laboral y los Módulos desde el panel.

**Blocked by:** 04, 05

**Status:** ready-for-agent

- [ ] El servidor verifica el HMAC, el Content-Digest y la ventana de tiempo (±5 min) **antes** de leer el contenido; si alguno falla, responde 401 sin procesar nada
- [ ] Idempotencia: reenviar un lote no duplica eventos (deduplicación por id de evento)
- [ ] Validación estricta: tamaño máximo del lote, `type` contra un enum, campos obligatorios, 422 ante campos desconocidos (Q65), timestamps coherentes, UTF-8 válido
- [ ] El texto que llega del Equipo se normaliza (NFC, sin caracteres invisibles ni de control bidireccional) y se recorta a una longitud máxima
- [ ] Se rechazan los datos de Empleados sin consentimiento vigente, los de fuera del Horario laboral y los de Módulos apagados
- [ ] Horario laboral general (America/Guayaquil) con excepciones por Empleado, editable por el Administrador
- [ ] Encendido y apagado global de los Módulos (capturas periódicas, Vista en vivo, USB, impresión, Sitios); el cambio se avisa al Agente por Reverb
- [ ] Límite de frecuencia por Equipo (429)
- [ ] Los jobs de Horizon reciben ULIDs y releen los datos desde la base
- [ ] Tests de Pest para cada motivo de rechazo y para la idempotencia
