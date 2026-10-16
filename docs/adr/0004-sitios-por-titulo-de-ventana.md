# Sitios detectados por el título de la ventana, sin extensión de navegador

En la primera versión, el Sitio se deduce del título de la ventana del navegador en primer plano. No se instala ninguna extensión ni se intercepta tráfico. Así no hay un segundo componente que desplegar en cada navegador, y se registra menos de lo necesario en lugar de más.

## Consequences

- No se puede saber qué archivo se subió, de qué tamaño ni si la cuenta es personal. La Regla de alerta de nube personal se dispara por visitar un Sitio de una lista configurable (WeTransfer, Drive…), y el texto del panel lo dice así.
- Si más adelante se necesita la URL exacta o el detalle de las subidas, la opción prevista es una extensión administrada por política de Chrome/Edge, en una fase posterior y con su propio ADR.
