# Republicneg Monitoreo

Herramienta interna de Intelicorp para monitorear, de forma transparente y con aviso visible, la actividad de las computadoras de la empresa durante la jornada laboral.

## Personas y estructura

**Equipo**:
Computadora de la empresa que tiene instalado el Agente y cuya actividad se registra.
_Evitar_: dispositivo, PC, máquina; "equipo" en el sentido de grupo de personas

**Área**:
Grupo de Empleados de la empresa (Ventas, Soporte, Contabilidad…) que comparte reglas y clasificación de apps.
_Evitar_: equipo, departamento, grupo

**Empleado**:
Persona que usa uno o más Equipos y cuya actividad se registra. En la interfaz se le puede llamar "persona".
_Evitar_: usuario (ambiguo con la cuenta del panel), colaborador, trabajador

**Revisor**:
Usuario del panel que consulta la actividad de las Áreas que tiene asignadas y puede pedir una Captura bajo demanda o una Vista en vivo.
_Evitar_: supervisor, auditor, monitor

**Administrador**:
Usuario del panel con alcance global que configura Áreas, reglas, módulos, retención y accesos.
_Evitar_: superusuario, admin (en textos)

## Registro de actividad

**Agente**:
Software instalado en cada Equipo que registra la actividad, toma las Capturas y muestra el Aviso de monitoreo.
_Evitar_: cliente, spyware, programa espía

**Código de alta**:
Código de un solo uso que un Administrador genera para vincular un Equipo nuevo con el servidor.
_Evitar_: clave de instalación, licencia

**Aviso de monitoreo**:
Indicación siempre visible para el Empleado de que el monitoreo está activo, junto con los avisos puntuales antes de cada Captura y durante una Vista en vivo.
_Evitar_: notificación (a secas)

**Evento de actividad**:
Registro de qué ocurrió en un Equipo durante un intervalo: app y ventana en primer plano, Sitio, cambio de estado de la sesión, conexión de USB o impresión.
_Evitar_: log, actividad (como sustantivo contable)

**Sitio**:
Sitio web identificado a partir del título de la ventana del navegador en primer plano.
_Evitar_: URL, página, dominio

**Bloque**:
Tramo de 10 minutos de la jornada de un Empleado, clasificado como activo, inactivo o sin sesión.
_Evitar_: franja, slot, celda

**Tiempo activo**:
Tiempo, dentro del Horario laboral, en que la sesión de Windows del Empleado está desbloqueada.
_Evitar_: productividad, tiempo productivo, uso de teclado

**Tiempo inactivo**:
Tiempo, dentro del Horario laboral, en que la sesión del Empleado está abierta pero bloqueada.
_Evitar_: ocio, tiempo muerto

**Sin sesión**:
Estado de un Equipo apagado o sin ninguna sesión de Windows iniciada.
_Evitar_: desconectado (que se refiere a que el Equipo no reporta)

**Horario laboral**:
Franja de días y horas en que se registra la actividad; fuera de ella el Agente no registra nada.
_Evitar_: turno, jornada (cuando se habla de la configuración)

**Clasificación**:
Etiqueta productiva, neutral o no productiva que un Área asigna a una app o Sitio.
_Evitar_: categoría, puntuación

## Capturas

**Captura**:
Imagen de la pantalla de un Equipo, una por monitor. Puede ser periódica o bajo demanda.
_Evitar_: screenshot, foto, pantallazo

**Captura bajo demanda**:
Captura que un Revisor pide en el momento, siempre con un Motivo.
_Evitar_: captura manual, captura forzada

**Lista de exclusión**:
Apps y Sitios en los que nunca se toma una Captura periódica; de ellos solo se registra el Evento de actividad.
_Evitar_: lista blanca, whitelist

**Vista en vivo**:
Observación continua y temporal de la pantalla de un Equipo por un Revisor, con Aviso de monitoreo y Motivo, por un tiempo máximo.
_Evitar_: streaming, espejo, control remoto

**Motivo**:
Justificación escrita y obligatoria que el Revisor da para una Captura bajo demanda o una Vista en vivo.
_Evitar_: razón, comentario

## Control

**Módulo**:
Capacidad de registro que el Administrador enciende o apaga para toda la empresa: capturas periódicas, Vista en vivo, USB, impresión, Sitios.
_Evitar_: feature, plugin

**Regla de alerta**:
Condición configurada, con un nivel de riesgo alto, medio o bajo, que genera una Alerta cuando se cumple.
_Evitar_: política, trigger

**Alerta**:
Aviso, generado por una Regla de alerta, de que algo merece revisión. No es una acusación. Puede estar Nueva, Revisada o Descartada.
_Evitar_: incidente, infracción, violación

**Auditoría**:
Registro inalterable de las acciones sensibles de Revisores y Administradores, incluido cada acceso a una Captura.
_Evitar_: bitácora, historial

**Retención**:
Plazo tras el cual los datos registrados se eliminan de forma definitiva.
_Evitar_: archivado, purga
