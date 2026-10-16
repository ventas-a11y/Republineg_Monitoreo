# 11: Vista del empleado

**What to build:** Un Empleado entra al panel y ve exactamente lo que el sistema registra de sí mismo: su jornada, sus Capturas y quién abrió cada una y cuándo. Es la pantalla "Así te ve el panel" del mockup y es clave para la transparencia ([SEGURIDAD](../../../docs/SEGURIDAD.md) Q33).

**Blocked by:** 07, 08

**Status:** ready-for-agent

- [ ] El Empleado solo ve sus propios datos; nunca los de otra persona
- [ ] Ve su Línea de tiempo del día con la misma cinta de Bloques del ticket 08
- [ ] Ve sus propias Capturas
- [ ] Por cada Captura, ve qué Revisor la abrió y cuándo (leído de la Auditoría)
- [ ] Ve el estado de su consentimiento y la versión de política que aceptó
- [ ] Tests de Pest: un Empleado no puede acceder a los datos de otro (404) ni a las rutas de Revisor
