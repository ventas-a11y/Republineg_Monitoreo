# 18: Endurecimiento final

**What to build:** Antes del piloto, se activan y verifican los controles de seguridad que faltan, de modo que el sistema quede listo para el pentest y para ponerse en producción ([SEGURIDAD](../../../docs/SEGURIDAD.md) Fase 4).

**Blocked by:** 10, 13, 15, 17

**Status:** ready-for-agent

- [ ] Cabeceras de seguridad y CSP con nonce en modo enforce (antes se corre en modo de solo reporte)
- [ ] Configuración endurecida de Reverb: `allowed_origins` solo con el host del panel, client events desactivados, rate limiting encendido, tamaño de mensaje acotado
- [ ] La matriz de autorización cubre todas las rutas; un test falla si alguna queda fuera
- [ ] Tests de los casos de abuso (`ABUSE_NNN`) definidos en el taller, agrupados en `->group('abuse')`
- [ ] HSTS gradual, con alerta de vencimiento del certificado
- [ ] Procedencia SLSA y escaneo de secretos de la imagen final antes de firmarla
- [ ] `security-review` y las revisiones de Trail of Bits sin hallazgos abiertos
- [ ] testssl y Docker Bench sin hallazgos críticos
- [ ] Todo listo para el pentest y el piloto en 3–5 Equipos con la política firmada
