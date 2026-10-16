# 12: Auditoría, pausa global e incidentes

**What to build:** El Administrador consulta el registro de Auditoría, puede pausar todo el monitoreo de golpe en una emergencia y, si se compromete un Equipo, revocarlo y marcar sus datos recientes como sospechosos ([SEGURIDAD](../../../docs/SEGURIDAD.md) Q47).

**Blocked by:** 09

**Status:** ready-for-agent

- [ ] Pantalla de Auditoría consultable y filtrable (actor, acción, fecha, Equipo), solo para Administradores
- [ ] La Auditoría es inmutable: el rol de app de Postgres no puede actualizar ni borrar sus filas (REVOKE a nivel de base de datos)
- [ ] Interruptor global "pausar todo el monitoreo"; mientras está activo, el servidor avisa a los Agentes por Reverb y no acepta datos ni permite Capturas ni Vista en vivo
- [ ] Ante un Equipo comprometido: revocar su token, rotar su secreto y marcar como sospechosos sus datos desde el último momento confiable
- [ ] Cada una de estas acciones queda, a su vez, en la Auditoría
- [ ] Tests de Pest: inmutabilidad de la Auditoría, pausa global que bloquea la ingesta y marca de datos sospechosos
