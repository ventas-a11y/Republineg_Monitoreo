# 10: Vista en vivo

**What to build:** Un Revisor con permiso explícito observa la pantalla de un Equipo de su Área en tiempo real, siempre con el Empleado avisado. La sesión está acotada en el tiempo y queda registrada de principio a fin ([ADR-0001](../../../docs/adr/0001-vista-en-vivo-por-frames-http.md), [SEGURIDAD](../../../docs/SEGURIDAD.md) Q10).

**Blocked by:** 09

**Status:** ready-for-agent

- [ ] Mismos requisitos que el ticket 09: permiso explícito, Motivo obligatorio, reconfirmación de identidad, orden firmada con Ed25519, solo en Horario laboral y con el Módulo encendido
- [ ] Durante toda la sesión, el Empleado ve en su pantalla un aviso visible con el nombre del Revisor (lo muestra el Agente)
- [ ] La sesión dura como máximo 5 minutos; renovarla exige registrar un Motivo nuevo
- [ ] Transporte por frames: el Agente sube frames por HTTP, solo el último vive en Valkey con TTL corto, y Reverb solo señaliza iniciar, detener y frame listo. Los frames no se guardan
- [ ] El Revisor puede guardar un frame como Captura; en ese caso pasa por el flujo del ticket 07 y queda en la Auditoría
- [ ] El inicio y el fin de cada sesión quedan en la Auditoría (Revisor, Equipo, Motivo, duración)
- [ ] Tests de Pest: sin permiso, otra Área, expiración a los 5 minutos y registro de inicio y fin
