# 13: Reglas de alerta y bandeja

**What to build:** El sistema genera Alertas cuando la actividad coincide con una Regla, y el Revisor las gestiona en una bandeja con pestañas, como en el mockup. Las Alertas son avisos para revisar, no acusaciones.

**Blocked by:** 06

**Status:** ready-for-agent

- [ ] Seis Reglas de alerta: Equipo sin reportar más de X min en horario, app o Sitio de una lista, sesión bloqueada más de Y min en horario, USB conectado, impresión de más de N páginas y Sitio de nube personal de una lista
- [ ] Cada Regla tiene un nivel de riesgo (alto, medio o bajo), configurable por el Administrador
- [ ] Los patrones de app y Sitio se comparan con comodines o "contiene", nunca con expresiones regulares (Q66)
- [ ] La evaluación de Reglas corre en Horizon sobre los Eventos ya guardados
- [ ] Bandeja con pestañas Nuevas, Revisadas y Descartadas, filtros por riesgo y búsqueda, limitada a las Áreas del Revisor
- [ ] El Revisor marca una Alerta como Revisada o Descartada con una nota opcional, y puede volver a marcarla como Nueva; queda el autor y la fecha
- [ ] Desde la Alerta se ve la Captura relacionada y se salta al Bloque de la Línea de tiempo
- [ ] Las Alertas nuevas aparecen en el Resumen y se envían por correo
- [ ] Tests de Pest: disparo de cada Regla, cambios de estado y alcance por Área
