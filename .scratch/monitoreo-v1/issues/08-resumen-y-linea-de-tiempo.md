# 08: Resumen y Línea de tiempo

**What to build:** El Revisor abre el panel y ve el Resumen de la jornada y la Línea de tiempo, como en el mockup, con datos reales: una cinta de Bloques de 10 minutos por Empleado, clasificados como activo, inactivo o sin sesión.

**Blocked by:** 03, 06

**Status:** ready-for-agent

- [ ] Los Bloques se calculan a partir de los Eventos de actividad: activo = sesión desbloqueada, inactivo = bloqueada, sin sesión = Equipo apagado o sin sesión iniciada (D31)
- [ ] Resumen: Equipos en línea, actividad promedio, horas activas, Capturas del día, Alertas por revisar (vacío hasta el ticket 13) y apps más usadas
- [ ] Línea de tiempo con filtros por Área, persona y "solo con alertas"; al elegir un Bloque se ve qué pasó en esos 10 minutos
- [ ] La cinta de jornada es un componente React reutilizable y accesible por teclado
- [ ] Textos corregidos respecto al mockup: la actividad mide la sesión desbloqueada, no el teclado ni el mouse (ADR-0005)
- [ ] Todo queda limitado a las Áreas del Revisor; los props de Inertia no incluyen datos de otras Áreas
- [ ] Tests de Pest para el cálculo de Bloques y para el alcance por Área
