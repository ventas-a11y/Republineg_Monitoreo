# 09: Captura bajo demanda

**What to build:** Un Revisor pulsa "Capturar ahora" sobre un Equipo de su Área, escribe un Motivo y reconfirma su identidad. El servidor envía la orden firmada al Agente, el Empleado ve el aviso y la Captura aparece en la galería con el Motivo registrado en la Auditoría.

**Blocked by:** 07

**Status:** ready-for-agent

- [ ] El Motivo es obligatorio y el servidor rechaza la petición sin él
- [ ] Acción de riesgo alto: exige el permiso correspondiente y reconfirmar la contraseña o la 2FA si pasaron más de 15 minutos (Q46)
- [ ] Antes de confirmar se muestran el Empleado y el Equipo destino, y la confirmación queda ligada a ellos
- [ ] La orden viaja por el canal privado del Equipo en Reverb, firmada con Ed25519: Revisor, Equipo, acción, Motivo, emisión, expiración ≤ 60 s y key-id (Q44)
- [ ] Fuera del Horario laboral o con el Módulo apagado, la acción no está disponible
- [ ] Solo por POST, protegido contra CSRF
- [ ] Queda en la Auditoría: quién, cuándo, Equipo y Motivo
- [ ] Tests de Pest: sin Motivo, otra Área, fuera de horario, firma de la orden verificable con la clave pública
