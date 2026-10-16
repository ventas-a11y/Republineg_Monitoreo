# 16: Retención y resúmenes diarios

**What to build:** El sistema borra de forma definitiva los datos cuando vence su plazo de Retención y precalcula los resúmenes diarios que alimentan el Resumen y los Reportes.

**Blocked by:** 07

**Status:** ready-for-agent

- [ ] Plazos de Retención por defecto configurables: Capturas 30 días, eventos 90 días, resúmenes diarios 12 meses, Auditoría 24 meses (D8)
- [ ] Un job de Horizon aplica la Retención y borra a la vez el objeto en Garage y la fila en Postgres; no quedan datos huérfanos
- [ ] La Retención alcanza también a los logs; de ellos se excluyen los datos con consentimiento revocado
- [ ] Un job diario calcula los resúmenes por Empleado (tiempo activo e inactivo, top de apps y Sitios)
- [ ] Al dar de baja a un Empleado se invalidan sus resúmenes (gancho del ticket 04)
- [ ] Tests de Pest: el borrado elimina imagen y fila juntas, y el resumen diario se calcula bien
