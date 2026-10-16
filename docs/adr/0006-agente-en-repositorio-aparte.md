# El Agente vive en un repositorio aparte; este repo es el dueño del contrato de la API

El Agente (.NET 10) se desarrolla en su propio repositorio con Visual Studio, separado de este repositorio, que contiene el servidor Laravel y el panel. Cada parte usa las herramientas nativas de su plataforma, y no se mezclan la CI de PHP/Node con la de .NET.

## Considered Options

- **Monorepo con una carpeta del Agente** (descartada): un solo cambio podía tocar la API en ambos lados, pero obligaba a trabajar la solución .NET dentro de un repositorio Laravel y a mantener dos pipelines en una misma CI.

## Consequences

- **El contrato de la API del Agente se define en este repo**: rutas versionadas bajo un prefijo `v1`, formato de los lotes y de las Capturas, señales de Reverb. Los tests de feature de Pest sobre esa API actúan como test de contrato. Un cambio incompatible exige una versión `v2` mientras convivan Agentes antiguos (en v1 se actualizan manualmente por GPO).
- Los ADRs que describen el comportamiento del Agente ([0002](0002-agente-en-dos-procesos.md), [0004](0004-sitios-por-titulo-de-ventana.md), [0005](0005-limites-de-transparencia-del-agente.md)) siguen aquí como decisiones de sistema, y el repositorio del Agente los referencia.
- Los tests de la cola local (xUnit) y el MSI viven en el repositorio del Agente.
