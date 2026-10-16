# 15: Reportes

**What to build:** El Revisor genera un Reporte eligiendo tipo, periodo, alcance y formato, lo descarga, puede programar su envío periódico por correo y consulta el historial de descargas, como en la pantalla Reportes del mockup.

**Blocked by:** 14

**Status:** ready-for-agent

- [ ] Generador con tipo, periodo, alcance (Empleado o Área) y formato CSV o PDF
- [ ] Los tipos cubren los reportes de la v1: activo/inactivo por Empleado y día, top de apps y Sitios por Empleado y Área
- [ ] La generación corre en Horizon; el CSV neutraliza las fórmulas (`= + - @`)
- [ ] Envíos programados por correo, con una cadencia configurable
- [ ] Historial de Reportes generados, descargables de nuevo con enlace firmado
- [ ] El alcance respeta las Áreas del Revisor; las descargas llevan `Content-Disposition: attachment`
- [ ] Un Empleado dado de baja queda excluido de los Reportes programados (gancho del ticket 04)
- [ ] Tests de Pest: generación en ambos formatos, neutralización de fórmulas CSV y alcance por Área
