# Spec: Republicneg Monitoreo v1

Status: ready-for-agent

Referencias: [GLOSSARY.md](../../GLOSSARY.md) · [docs/adr/](../../docs/adr/) · [docs/ROADMAP.md](../../docs/ROADMAP.md) · Mockup: https://claude.ai/artifact/WNNqTGzFbU3zsacWLvDHjV

## Problem Statement

Intelicorp no tiene visibilidad sobre cómo se usan sus 25–30 computadoras durante la jornada: qué apps y Sitios se usan, cuánto tiempo hay sesión activa, si se conectan USB, qué se imprime o si alguien visita servicios de nube personal. Cuando surge una duda (una fuga de información, un uso indebido, un equipo que deja de funcionar), no hay evidencia ni forma de mirar la pantalla en el momento. Gerencia exige poder ver en vivo. Cualquier solución tiene que cumplir la LOPDP de Ecuador y no puede ser encubierta: el personal debe saber siempre que está siendo monitoreado y poder ver lo que se registra de sí mismo.

## Solution

Un Agente en cada Equipo registra los Eventos de actividad y toma Capturas, guardándolos primero en una cola local para que un corte de red no deje huecos, y los envía por lotes a un servidor central. Un panel web permite a los Revisores ver un Resumen de la jornada, la Línea de tiempo en Bloques de 10 minutos, las Capturas, la Vista en vivo, las Alertas y los Reportes, siempre limitados a sus Áreas. Toda acción sensible queda en la Auditoría. El Empleado ve un Aviso de monitoreo permanente, recibe un aviso 3 segundos antes de cada Captura y mientras dure una Vista en vivo, y puede entrar al panel para ver su propia actividad, sus Capturas y quién las abrió.

## User Stories

**Transparencia y Empleado**

1. Como Empleado, quiero ver siempre un ícono que indique que el monitoreo está activo, para saber en todo momento que mi Equipo registra actividad.
2. Como Empleado, quiero un aviso 3 segundos antes de cada Captura, periódica o bajo demanda, para que ninguna Captura me tome por sorpresa.
3. Como Empleado, quiero ver un borde y un aviso con el nombre del Revisor mientras alguien tiene abierta una Vista en vivo de mi pantalla, para saber quién me está viendo.
4. Como Empleado, quiero que el Agente indique en pantalla que está en pausa fuera del Horario laboral, para tener la certeza de que no se registra nada en mi tiempo personal.
5. Como Empleado, quiero entrar al panel y ver mi propia jornada (Bloques, apps, Sitios), para conocer exactamente lo que ve mi Revisor.
6. Como Empleado, quiero ver mis Capturas y quién abrió cada una y cuándo, para comprobar que nadie accede a ellas sin dejar rastro.
7. Como Empleado, quiero ver las Alertas que me afectan y la nota con que se cerraron, para poder aclarar cualquier malentendido.
8. Como Empleado, quiero aceptar por escrito la política de monitoreo antes de que se instale el Agente, para dar mi consentimiento informado.

**Agente y Equipo** 9. Como Administrador, quiero generar un Código de alta de un solo uso, para vincular un Equipo nuevo de forma segura. 10. Como Administrador, quiero revocar el acceso de un Equipo desde el panel, para cortarlo de inmediato si se pierde o se da de baja. 11. Como Administrador, quiero que el Agente guarde todo en una cola local mientras no hay red, para que un corte no deje huecos en la actividad. 12. Como Administrador, quiero que la cola local tenga un límite de 7 días o 2 GB y descarte primero las Capturas más antiguas, para que un Equipo desconectado mucho tiempo no llene su disco. 13. Como Revisor, quiero que la actividad se atribuya al usuario de Windows que tenía la sesión, para distinguir a los Empleados que comparten un Equipo. 14. Como Revisor, quiero que cada Captura incluya todos los monitores, una imagen por monitor, para no perder lo que pasa en una segunda pantalla. 15. Como Administrador, quiero distribuir el Agente como un MSI firmado por GPO, para instalarlo y actualizarlo como cualquier software corporativo.

**Registro de actividad** 16. Como Revisor, quiero saber qué app y ventana estaba en primer plano y durante cuánto tiempo, para entender en qué se fue la jornada. 17. Como Revisor, quiero ver qué Sitios se visitaron (según el título del navegador), para conocer el uso de la web. 18. Como Revisor, quiero que cada Bloque de 10 minutos se marque como activo, inactivo o sin sesión, para leer la jornada de un vistazo. 19. Como Revisor, quiero que el Tiempo activo cuente solo el tiempo con la sesión desbloqueada dentro del Horario laboral, para tener una métrica que no dependa de vigilar el teclado. 20. Como Revisor, quiero saber cuándo se conectó un USB (nombre y capacidad), para detectar posibles fugas de información. 21. Como Revisor, quiero saber qué documentos se imprimieron, cuántas páginas y en qué impresora, para detectar impresiones fuera de lo común. 22. Como Administrador, quiero configurar el Horario laboral general (America/Guayaquil) y excepciones por Empleado, para que solo se registre dentro de la jornada de cada uno. 23. Como Administrador, quiero una Lista de exclusión de apps y Sitios (banca, salud) en los que nunca se toma Captura periódica, para no guardar datos sensibles. 24. Como Administrador, quiero encender o apagar cada Módulo (capturas periódicas, Vista en vivo, USB, impresión, Sitios) para toda la empresa, para recoger solo lo necesario.

**Capturas y Vista en vivo** 25. Como Revisor, quiero ver las Capturas de un Empleado en una galería filtrable por persona y fecha, para revisar un momento concreto. 26. Como Revisor, quiero pedir una Captura bajo demanda escribiendo un Motivo, para obtener evidencia en el momento con una justificación registrada. 27. Como Revisor con el permiso correspondiente, quiero abrir una Vista en vivo de un Equipo con un Motivo, para ver qué ocurre ahora mismo. 28. Como Revisor, quiero que la Vista en vivo dure como máximo 5 minutos y pueda renovarla con un nuevo Motivo, para que no quede abierta indefinidamente. 29. Como Revisor, quiero guardar un frame de la Vista en vivo como Captura, para conservar una evidencia puntual. 30. Como Administrador, quiero que las Capturas se abran con enlaces que caducan a los 5 minutos, para que un enlace filtrado no sirva. 31. Como Administrador, quiero que fuera del Horario laboral no se puedan pedir Capturas bajo demanda ni Vistas en vivo, para respetar el tiempo personal.

**Panel del Revisor** 32. Como Revisor, quiero un Resumen con los Equipos en línea, la actividad promedio, las horas activas, las Capturas del día, las Alertas por revisar y las apps más usadas, para saber cómo va la jornada. 33. Como Revisor, quiero una Línea de tiempo con una cinta de Bloques por Empleado y poder elegir un Bloque para ver qué pasó en esos 10 minutos, para investigar con contexto. 34. Como Revisor, quiero filtrar la Línea de tiempo por Área, persona o "solo con alertas", para centrarme en lo relevante. 35. Como Revisor, quiero una tabla de Equipos con estado, última Captura y acciones de Vista en vivo y Capturas, para administrar el parque. 36. Como Revisor, quiero ver solo a los Empleados de mis Áreas, para no acceder a datos que no me corresponden. 37. Como usuario del panel (Administrador, Revisor o Empleado), quiero que mi cuenta exija 2FA o passkey, para proteger un panel con datos sensibles. 38. Como Administrador, quiero crear las cuentas del panel y asignar roles y Áreas, porque no debe existir el registro público.

**Alertas** 39. Como Administrador, quiero configurar Reglas de alerta con un nivel de riesgo (alto, medio, bajo), para priorizar la revisión. 40. Como Revisor, quiero una Alerta cuando un Equipo deja de reportar más de X minutos en horario, para detectar fallas o desconexiones. 41. Como Revisor, quiero una Alerta cuando se usa una app o Sitio de una lista configurada, para enterarme de usos no permitidos. 42. Como Revisor, quiero una Alerta cuando la sesión pasa bloqueada más de Y minutos en horario, para detectar ausencias prolongadas. 43. Como Revisor, quiero una Alerta cuando se conecta un USB, para revisar posibles fugas. 44. Como Revisor, quiero una Alerta cuando se imprimen más de N páginas, para revisar impresiones inusuales. 45. Como Revisor, quiero una Alerta cuando se visita un Sitio de nube personal de la lista (WeTransfer, Drive…), para revisar posibles salidas de información. 46. Como Revisor, quiero una bandeja de Alertas con pestañas Nuevas, Revisadas y Descartadas, filtros por riesgo y búsqueda, para trabajar en orden. 47. Como Revisor, quiero marcar una Alerta como Revisada o Descartada con una nota opcional y poder volver a marcarla como nueva, para dejar constancia de la explicación. 48. Como Revisor, quiero ver la Captura relacionada y saltar a ese Bloque en la Línea de tiempo desde la Alerta, para entender el contexto. 49. Como Revisor, quiero recibir las Alertas también por correo, para no depender de tener el panel abierto.

**Reportes y Clasificación** 50. Como Administrador de un Área, quiero clasificar apps y Sitios como productiva, neutral o no productiva, para que los Reportes reflejen la realidad de cada Área. 51. Como Revisor, quiero ver el tiempo por app y por Sitio, por Empleado y por Área, para saber en qué se usa la jornada. 52. Como Revisor, quiero generar un Reporte eligiendo tipo, periodo, alcance y formato (CSV o PDF), para compartirlo con gerencia. 53. Como Revisor, quiero programar el envío periódico de un Reporte por correo, para recibirlo sin pedirlo. 54. Como Revisor, quiero ver el historial de Reportes descargados, para recuperar uno anterior.

**Auditoría y Retención** 55. Como Administrador, quiero que cada apertura de Captura, Captura bajo demanda, Vista en vivo, cambio de Regla y cambio de rol quede en la Auditoría, para poder rendir cuentas. 56. Como Administrador, quiero plazos de Retención configurables (Capturas 30 días, eventos 90 días, resúmenes 12 meses, Auditoría 24 meses), para no guardar datos más tiempo del necesario. 57. Como Administrador, quiero que la Retención borre a la vez la imagen y su registro, para que no queden datos huérfanos.

## Implementation Decisions

- **Dos repositorios** ([ADR-0006](../../docs/adr/0006-agente-en-repositorio-aparte.md)). Este repositorio contiene el servidor y el panel. El Agente (.NET 10, Visual Studio) vive en un repositorio aparte. Este repo es el dueño del contrato de la API del Agente: está versionado con prefijo `v1` y documentado aquí, y sus tests de Pest actúan como test de contrato. Los tickets del Agente se llevan en su propio repositorio.
- **Agente en dos procesos** ([ADR-0002](../../docs/adr/0002-agente-en-dos-procesos.md)). Un servicio de Windows maneja la cola SQLite, el envío y la conexión con Reverb. Un proceso de sesión maneja las Capturas, la ventana activa, el Aviso de monitoreo, los eventos de sesión, USB e impresión. Se comunican por named pipe.
- **Límites de registro** ([ADR-0005](../../docs/adr/0005-limites-de-transparencia-del-agente.md)). Nunca se lee el teclado, el ratón, el portapapeles, el micrófono ni la cámara, ni se vigila la copia de archivos. El Tiempo activo es el tiempo con la sesión desbloqueada. Hay aviso de 3 s antes de cada Captura.
- **Sitios** ([ADR-0004](../../docs/adr/0004-sitios-por-titulo-de-ventana.md)). Se deducen del título de la ventana del navegador. La Alerta de nube personal se basa en una lista de Sitios, sin detalle de archivo.
- **Autenticación del Agente.** El Código de alta se canjea por un token Sanctum cuyo dueño es el Equipo; el token es revocable. Todo el tráfico va por HTTPS.
- **API de ingesta.** Recibe lotes de Eventos de actividad. Cada evento lleva un identificador generado por el Agente, de modo que reenviar un lote no duplica nada (es idempotente). Las Capturas se suben a Laravel por separado, y Laravel las escribe en Garage ([ADR-0003](../../docs/adr/0003-imagenes-en-garage-via-laravel.md)).
- **Reverb solo para señales** ([ADR-0001](../../docs/adr/0001-vista-en-vivo-por-frames-http.md)): capturar ahora, iniciar/detener Vista en vivo, frame listo, cambios de configuración y de Módulos.
- **Vista en vivo.** Frames JPEG por HTTP cada 1–2 s; solo el último frame se guarda en Valkey, con TTL corto. El servidor aplica el Motivo, el máximo de 5 minutos, el permiso y la Auditoría.
- **Capturas.** WebP de calidad media, máximo 1920 px de ancho, una imagen por monitor. El servidor re-codifica cada imagen y Horizon genera la miniatura. Garage es solo interno: el panel recibe las imágenes a través de Laravel con un enlace firmado de 5 minutos, y cada visualización pasa por la Auditoría.
- **Modelo de dominio principal.** Equipo, Área, Empleado (N:M con Equipo a través de la sesión de Windows), usuarios del panel con rol Administrador, Revisor o Empleado, y Revisor N:M con Área. Además: Evento de actividad, Captura, Sesión de Vista en vivo, Regla de alerta, Alerta (estado, nota, autor), Clasificación por Área, Lista de exclusión, Horario laboral y sus excepciones, Módulo, Reporte programado, Auditoría.
- **Bloques.** Se derivan de los Eventos de actividad: 10 minutos, de 08:00 a 18:00 en el ejemplo del mockup, siguiendo el Horario laboral configurado. Los resúmenes diarios se calculan en Horizon.
- **PostgreSQL 18** con índices por fecha. No se particionan tablas (25–30 Equipos).
- **Panel.** Usa el starter kit (Inertia + React + shadcn) con los tokens y el estilo del mockup. El registro público de Fortify se desactiva y se exige 2FA o passkey a todas las cuentas. Hay que corregir estos textos del mockup:
    - teclado/mouse → sesión desbloqueada;
    - "equipo" como grupo → "área" o "personas";
    - detalle de archivo en las Alertas de nube personal.
- **Seguridad.** Se aplican las decisiones Q43–Q62 y los controles de [docs/SEGURIDAD.md](../../docs/SEGURIDAD.md), basados en OWASP. En particular:
    - consentimiento como control de acceso, con fallo cerrado;
    - órdenes firmadas con Ed25519 del servidor al Agente;
    - lotes con HMAC y Content-Digest;
    - acciones de riesgo alto con reconfirmación cada 15 minutos;
    - interruptor global de pausa;
    - rotación del token de cada Equipo cada 90 días;
    - panel y API solo en la red interna (VPN para acceso remoto);
    - la API del Agente rechaza con 422 los campos desconocidos;
    - Reglas de alerta con comodines o "contiene", nunca regex;
    - matriz de autorización versionada como única fuente de los tests de acceso;
    - casos de abuso (`ABUSE_NNN`) como criterios de aceptación de los tickets.
- **Build.** La imagen se construye en GitHub Actions, se publica en GHCR y Dokploy la despliega por digest.
- **Despliegue.** Una imagen FrankenPHP sin Octane para app, horizon, reverb y scheduler, más postgres, valkey y garage, en Docker Compose sobre Dokploy. Traefik se encarga del TLS. Respaldos diarios.

## Testing Decisions

- Un buen test verifica el comportamiento externo a través de la costura más alta disponible, no los detalles internos.
- **Costura 1: API del Agente** (tests de feature con Pest y HTTP). Cubre:
    - ingesta por lotes e idempotencia;
    - token revocado → 401;
    - Código de alta de un solo uso;
    - Módulo apagado → los datos se rechazan o se ignoran;
    - nada se acepta fuera del Horario laboral.
- **Costura 2: rutas del panel** (Pest con aserciones de Inertia). Cubre:
    - alcance por Área (un Revisor del Área A no ve el Área B);
    - el Empleado solo ve lo suyo;
    - Motivo obligatorio;
    - cada acción sensible escribe en la Auditoría y es visible para el Empleado afectado.
    - Antecedentes: los tests de Auth y Settings que ya trae el starter kit.
- **Costura 3: jobs de Horizon** (Pest con almacenamiento y colas falsos). Cubre:
    - la Retención borra objeto y fila;
    - la evaluación de las 6 Reglas de alerta;
    - las miniaturas;
    - los resúmenes diarios;
    - los Reportes programados.
- **Costura 4: cola del Agente** (xUnit contra un servidor HTTP falso, **en el repositorio del Agente**). Cubre:
    - reenvío tras un corte;
    - los límites de 7 días / 2 GB;
    - el orden de descarte.
- **Extremo a extremo** (webapp-testing / Playwright): flujos de "Capturar ahora" y Vista en vivo, con la Auditoría visible.
- La CI corre contra PostgreSQL desde la Fase 0.

## Out of Scope

- Lectura de teclado o ratón (ni presencia de uso), portapapeles, micrófono, cámara y copia de archivos.
- Equipos personales (BYOD) y sistemas operativos que no sean Windows.
- Extensión de navegador, URL exacta y detalle de archivos subidos (fase posterior).
- Actualización automática del Agente (fase posterior; en v1 se distribuye por GPO).
- WebRTC para la Vista en vivo (documentado como alternativa en el ADR-0001).
- Laravel Octane.

## Further Notes

- Antes del piloto, la política de monitoreo tiene que estar revisada por legal (`legal:compliance-check`) y firmada por cada Empleado.
- Plazos de Retención por defecto: Capturas 30 días, eventos 90 días, resúmenes diarios 12 meses, Auditoría 24 meses.
- El mockup solo tiene terminadas Resumen, Línea de tiempo y Alertas. Equipos, Apps y sitios, Capturas, Reportes, Configuración y Vista del empleado se diseñan en la Fase 2 con el mismo estilo.
- Siguiente paso sugerido: `/to-tickets` sobre este spec, por fases del [ROADMAP](../../docs/ROADMAP.md).
