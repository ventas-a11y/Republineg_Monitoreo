# Vista en vivo por frames HTTP, con Reverb solo para señalizar

Gerencia exige la Vista en vivo en la primera versión. El Agente sube un frame JPEG por HTTP cada 1–2 segundos, Laravel guarda solo el último frame en Valkey con un TTL de pocos segundos y avisa por Reverb que hay uno nuevo, y el panel lo descarga. Los frames no se persisten, salvo que el Revisor pulse "guardar como Captura". Con 25–30 Equipos y una sola Vista en vivo a la vez, esto da una fluidez suficiente sin componentes nuevos.

## Considered Options

- **WebRTC** (rechazada por ahora): video fluido de baja latencia, pero exige un servidor STUN/TURN, señalización propia, codecs en el Agente .NET y depurar NAT dentro de la red de Intelicorp. Es desproporcionado para 30 Equipos. Se vuelve a evaluar si se necesitan más de ~2 fps o varias vistas simultáneas.
- **Frames por Reverb** (rechazada): Reverb usa el protocolo Pusher, con mensajes JSON de tamaño limitado; meter imágenes en base64 ahí satura el canal de señalización.

## Consequences

- Reverb nunca transporta imágenes: solo eventos (`iniciar`, `detener`, `frame listo`, `capturar ahora`).
- Las salvaguardas (aviso visible con el nombre del Revisor, Motivo, máximo 5 min renovables, Auditoría) se aplican en el servidor, no solo en la interfaz.
