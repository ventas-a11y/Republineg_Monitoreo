# 04: Política de monitoreo y consentimiento

**What to build:** Un Administrador publica una versión de la política de monitoreo. Cada Empleado la lee y la acepta en el panel, y queda registrado quién aceptó qué versión y cuándo. Sin la aceptación vigente, el sistema no acepta ningún dato de ese Empleado (consentimiento como control de acceso, Q43).

**Blocked by:** 02

**Status:** ready-for-agent

- [ ] El Administrador publica versiones de la política; las versiones anteriores quedan en el historial
- [ ] Al iniciar sesión, el Empleado ve la política vigente si no la ha aceptado y puede aceptarla
- [ ] Hay un registro de la aceptación (Empleado, versión, fecha) que no se puede alterar
- [ ] El Administrador ve qué Empleados tienen la aceptación pendiente
- [ ] Hay un servicio de consulta del tipo "¿tiene este Empleado el consentimiento vigente?" que falla cerrado: ante la duda responde que no. Lo usará la ingesta (ticket 06)
- [ ] Una política nueva deja pendiente la aceptación de todos
- [ ] Dar de baja a un Empleado invalida lo derivado de sus datos (resúmenes, Reportes programados, cachés). Se marca para los tickets 15 y 16
- [ ] Tests de Pest para la aceptación, para la falta de consentimiento y para la nueva versión de la política
