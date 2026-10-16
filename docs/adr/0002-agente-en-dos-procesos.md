# Agente en dos procesos: servicio de Windows + proceso de sesión

Un servicio de Windows corre en la sesión 0 y no puede capturar el escritorio del usuario ni leer su ventana activa. Por eso el Agente se divide en dos procesos que hablan por un named pipe local:

- un **servicio** que mantiene la cola SQLite, envía los lotes al servidor y mantiene la conexión con Reverb;
- un **proceso de sesión**, uno por usuario con sesión iniciada, que toma las Capturas, lee la ventana activa y muestra el Aviso de monitoreo.

Se distribuye como un MSI firmado que se instala y desinstala como cualquier software corporativo, con permisos de administrador.

## Consequences

- Si el proceso de sesión no está corriendo, el servicio lo registra como un Evento de actividad visible en el panel; no se intenta ocultar ni forzar nada en el escritorio del Empleado.
- La cola local vive en el servicio, así que un corte de red o el cierre de sesión no pierden datos (límite: 7 días o 2 GB; se descartan primero las Capturas más antiguas).
