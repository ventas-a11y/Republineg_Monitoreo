# 04: Política de monitoreo y consentimiento

**What to build:** Un Administrador publica una versión de la política de monitoreo. Cada Empleado la lee y la acepta en el panel, y queda registrado quién aceptó qué versión y cuándo. Sin la aceptación vigente, el sistema no acepta ningún dato de ese Empleado (consentimiento como control de acceso, Q43).

**Blocked by:** 02

**Status:** ready-for-human

- [x] El Administrador publica versiones de la política; las versiones anteriores quedan en el historial
- [x] Al iniciar sesión, el Empleado ve la política vigente si no la ha aceptado y puede aceptarla
- [x] Hay un registro de la aceptación (Empleado, versión, fecha, IP, navegador) que no se puede alterar: el modelo lo impide y, en PostgreSQL, el rol de la app no tiene UPDATE ni DELETE sobre la tabla (verificado localmente)
- [x] El Administrador ve qué Empleados tienen la aceptación pendiente
- [x] Hay un servicio de consulta del tipo "¿tiene este Empleado el consentimiento vigente?" que falla cerrado (`CheckCurrentConsent`). Lo usará la ingesta (ticket 06)
- [x] Una política nueva deja pendiente la aceptación de todos
- [ ] Dar de baja a un Empleado invalida lo derivado de sus datos (resúmenes, Reportes programados, cachés). Queda para los tickets 15 y 16, porque esos datos todavía no existen
- [x] Tests de Pest para la aceptación, para la falta de consentimiento y para la nueva versión de la política
- [ ] Prueba manual: publicar la primera versión con el texto revisado por legal

## Decisiones (2026-10-17)

- **Quién acepta:** solo los Empleados, porque son las personas monitoreadas. Revisores y Administradores pueden leerla, pero no se les exige.
- **Formato:** Markdown escrito por el Administrador. El servidor lo convierte a HTML descartando el HTML incrustado y los enlaces inseguros.
- **Orden de acceso al panel:** contraseña temporal → política → correo verificado → segundo factor.
- **Si se publica una versión nueva mientras alguien lee la anterior:** la aceptación de la versión vieja se rechaza y se pide leer la nueva.
- **Sin política publicada:** el Empleado entra al panel (no hay nada que aceptar), pero el servicio de consentimiento responde "no", así que no se reciben datos de ningún Equipo.

## Notas

- **Texto legal:** la política real debe redactarla o revisarla asesoría legal (LOPDP) antes del piloto. El sistema no trae un texto por defecto.
- **Aviso por correo:** al publicar una versión nueva no se avisa por correo a los Empleados; lo verán al entrar. Se puede agregar si hace falta.
