# Límites de transparencia del Agente: sin teclado ni ratón, aviso antes de cada Captura

El Agente **no lee el teclado ni el ratón** de ninguna forma: ni las teclas, ni si hubo uso. El Tiempo activo se define como el tiempo con la sesión de Windows desbloqueada dentro del Horario laboral, y la sesión bloqueada cuenta como Tiempo inactivo. Además, el Empleado recibe un aviso **3 segundos antes de cada Captura**, también las periódicas. Son decisiones de producto pensadas para la LOPDP (Ecuador) y para la confianza del personal, y valen más que la precisión de la medición.

## Considered Options

- **Detectar inactividad por input** (rechazada por el usuario): es lo habitual en herramientas de monitoreo y mide mejor la presencia real, pero se percibe como vigilancia del teclado.
- **Capturas periódicas sin aviso previo** (rechazada): da una imagen más "espontánea", pero contradice la promesa de transparencia del producto.

## Consequences

- Alguien que deja la sesión desbloqueada sin usar el Equipo cuenta como activo. Esto se acepta.
- El aviso previo permite cambiar de ventana antes de una Captura periódica. Esto se acepta.
- Tampoco se registran nunca el portapapeles, el micrófono, la cámara ni la copia de archivos, ni nada fuera del Horario laboral.
