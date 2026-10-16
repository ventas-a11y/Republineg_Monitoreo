# Roadmap — Republicneg Monitoreo v1

Herramienta interna de Intelicorp para monitorear de forma transparente sus 25–30 computadoras: actividad por app y Sitio, Tiempo activo, Capturas periódicas y bajo demanda, Vista en vivo, USB, impresión, Alertas, Reportes y Auditoría. El Empleado siempre ve un aviso.

- Vocabulario: [GLOSSARY.md](../GLOSSARY.md)
- Decisiones de arquitectura: [docs/adr/](adr/)
- Spec v1 (para dividir en tickets): [.scratch/monitoreo-v1/spec.md](../.scratch/monitoreo-v1/spec.md)
- Medidas de seguridad (OWASP): [docs/SEGURIDAD.md](SEGURIDAD.md)
- Mockup de referencia: https://claude.ai/artifact/WNNqTGzFbU3zsacWLvDHjV. Están terminadas Resumen, Línea de tiempo y Alertas; las demás pantallas solo están descritas.

## Stack

| Pieza         | Elección                                                                                                                                                                                                                                                                                |
| ------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Agente        | C# / .NET 10 LTS en **repositorio aparte** (Visual Studio): servicio de Windows + proceso de sesión, cola SQLite, MSI firmado ([ADR-0002](adr/0002-agente-en-dos-procesos.md), [ADR-0006](adr/0006-agente-en-repositorio-aparte.md)). Este repo es el dueño del contrato de la API `v1` |
| Servidor      | Laravel 13 sobre PHP 8.5, ingesta HTTP por lotes, Sanctum (un token por Equipo)                                                                                                                                                                                                         |
| Tiempo real   | Laravel Reverb, solo para señales ([ADR-0001](adr/0001-vista-en-vivo-por-frames-http.md))                                                                                                                                                                                               |
| Datos         | PostgreSQL 18 (metadatos) · Garage S3 sobre disco cifrado, solo en la red interna (imágenes servidas por Laravel con enlace firmado de 5 min, [ADR-0003](adr/0003-imagenes-en-garage-via-laravel.md))                                                                                   |
| Colas y caché | Valkey 8 + Laravel Horizon (miniaturas, Retención, Alertas, Reportes)                                                                                                                                                                                                                   |
| Panel         | React 19 + Inertia 3 + Tailwind 4 + shadcn (starter kit oficial), estilo del mockup · pnpm 12 + Vite+ (`vite` como alias de `@voidzero-dev/vite-plus-core`, según `vp migrate`)                                                                                                         |
| Despliegue    | Docker Compose sobre Dokploy · imagen `dunglas/frankenphp:1.13-php8.5` sin Octane, construida en GitHub Actions, publicada en GHCR y desplegada por digest · Traefik con TLS de Let's Encrypt (DNS-01) · solo red interna, con VPN para acceso remoto                                   |

## Fases

| Fase                                  | Objetivo                                                 | Entregables clave                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        | Criterio de salida                                                                                                                          |
| ------------------------------------- | -------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------- |
| **0 — Fundaciones y legal**           | Dejar el terreno listo antes de escribir funcionalidades | ~~`git init`~~ (hecho; los commits los hace el usuario); PHP 8.5 en composer, CI y Docker; Compose local (Postgres 18, Valkey 8, Garage, Reverb, Horizon) con imagen FrankenPHP; `.env` hacia Postgres; registro público de Fortify desactivado; roles Administrador/Revisor/Empleado y Áreas; 2FA o passkey obligatorio para todas las cuentas; controles F0 de [SEGURIDAD](SEGURIDAD.md) (modelo de amenazas, redes Docker separadas, contenedores sin root, roles de Postgres, sesión, hashing, secretos); tokens del mockup en `resources/css/app.css`; skills externas instaladas; política de monitoreo (LOPDP) revisada por legal | `composer ci:check` en verde contra Postgres; política aprobada por legal                                                                   |
| **1 — Ingesta y Agente mínimo**       | Que la actividad fluya del Equipo al servidor sin huecos | **Aquí:** contrato de la API `v1` documentado; Equipo + Código de alta + token Sanctum; API de ingesta por lotes idempotente. **Repo del Agente:** servicio + proceso de sesión; cola SQLite (7 días / 2 GB); Aviso de monitoreo en bandeja; Eventos de actividad (app, ventana, Sitio, sesión bloqueada/desbloqueada, USB, impresión); Horario laboral; Módulos encendibles; Capturas periódicas con aviso de 3 s y Lista de exclusión → Laravel → Garage; miniaturas                                                                                                                                                                   | Tras un corte de red de 1 h en un Equipo de prueba, no queda ningún hueco en el servidor                                                    |
| **2 — Panel del Revisor**             | Ver y actuar                                             | Resumen y Línea de tiempo (cinta de Bloques de 10 min) según el mockup; Equipos; Capturas (galería); "Capturar ahora" con Motivo; **Vista en vivo** con salvaguardas; Auditoría; Vista del empleado (su jornada, sus Capturas, quién las abrió); diseño de las pantallas pendientes                                                                                                                                                                                                                                                                                                                                                      | Un Revisor del Área A no puede ver nada del Área B (test); toda acción sensible aparece en la Auditoría y en la vista del Empleado afectado |
| **3 — Alertas, reportes y retención** | Valor de gestión                                         | 6 Reglas de alerta con riesgo y bandeja Nueva/Revisada/Descartada; Clasificación productiva/neutral/no productiva por Área; Apps y sitios; generador de Reportes (CSV y PDF), envíos programados e historial; job de Retención; resúmenes diarios                                                                                                                                                                                                                                                                                                                                                                                        | La Retención borra a la vez el objeto en Garage y la fila en Postgres (test)                                                                |
| **4 — Endurecimiento y piloto**       | Llevarlo a producción con seguridad                      | `security-review` + Trail of Bits; controles F4 de [SEGURIDAD](SEGURIDAD.md) (pentest, matriz de autorización, testssl, Docker Bench, Trusted Types en modo de solo reporte); MSI firmado; stack en Dokploy con TLS; respaldos diarios (Postgres y Garage) con prueba de restauración; piloto en 3–5 Equipos con política firmada                                                                                                                                                                                                                                                                                                        | Piloto estable 2 semanas; restauración probada; luego despliegue a los 25–30 Equipos                                                        |
| **Posterior**                         | Mejoras                                                  | Extensión de navegador administrada (URL exacta, detalle de subidas); actualización automática firmada del Agente; WebRTC si la Vista en vivo necesita más fluidez                                                                                                                                                                                                                                                                                                                                                                                                                                                                       | —                                                                                                                                           |

## Hallazgos

1. **Base del repo.** Es el starter kit oficial de Laravel 13.35, todavía sin modificar. Incluye React 19.3, Inertia 3.9, Tailwind 4.3, shadcn (new-york/Radix), Fortify (registro, reset, verificación, 2FA, passkeys), Wayfinder, Pest 5, Larastan, Pint y Boost 2.10. El único modelo es `User`.
2. **Paquetes faltantes.** No están instalados Reverb, Horizon, Sanctum ni el driver S3 de Flysystem. La app usa sqlite/database/log.
3. **Versión de PHP.** El CLI local y la CI ya usan PHP 8.5, y Laravel 13 soporta 8.3–8.5. Se fija 8.5 en todas partes.
4. **Registro público.** Fortify lo trae activo, y en una herramienta interna debe desactivarse.
5. **Instrucciones para agentes.** Boost genera `AGENTS.md`; `CLAUDE.md` lo importa y agrega la sección "Agent skills".
6. **Garage.** Soporta URLs prefirmadas, CORS y multipart, aunque al final no se usan las prefirmadas para no exponer Garage ([ADR-0003](adr/0003-imagenes-en-garage-via-laravel.md)). El lifecycle es parcial (solo Expiration), por eso la Retención la hace Horizon.
7. **Soporte de versiones.** Laravel 13 recibe parches de seguridad hasta marzo de 2028; .NET 10 es LTS hasta noviembre de 2028.
8. **Reverb.** Usa el protocolo Pusher (mensajes JSON pequeños): no sirve para transportar imágenes.
9. **Captura desde un servicio.** Un servicio de Windows no puede capturar el escritorio del usuario; hace falta un proceso en la sesión.
10. **Escala.** 30 Equipos × una Captura cada 5 min ≈ 3.000 imágenes al día. No hace falta particionar tablas.
11. **Riesgo legal.** Las Capturas pueden contener datos sensibles de terceros. Lo mitigan la Lista de exclusión, la Retención corta, el cifrado en reposo, la Auditoría visible para el Empleado y el aviso previo.
12. **Mockup frente a decisiones.** Hay que corregir:
    - el texto "La actividad mide uso de teclado y mouse" (contradice el [ADR-0005](adr/0005-limites-de-transparencia-del-agente.md));
    - el uso de "equipo" como grupo de personas ("Hoy en el equipo" → "Hoy en tu área");
    - el detalle de archivo en las Alertas de nube personal (no es detectable sin extensión, [ADR-0004](adr/0004-sitios-por-titulo-de-ventana.md)).
13. **Qué se puede detectar sin extensión.** USB (nombre y capacidad) e impresión (documento, páginas, impresora, desde el log PrintService de Windows, que se habilita por GPO) sí se pueden detectar sin instalar nada extra.

14. **`.gitignore` del starter kit.** Ignoraba `/CLAUDE.md` y `/AGENTS.md`. Ahora se versionan para que todo el equipo comparta las instrucciones. `/.claude`, `/boost.json` y `/.mcp.json` siguen ignorados porque son locales de cada máquina: cada desarrollador corre `php artisan boost:install`.

## Skills para agentes de código

**Ya disponibles** (Boost y plugins instalados):

| Área                | Skills                                                                                                 |
| ------------------- | ------------------------------------------------------------------------------------------------------ |
| Laravel             | `laravel-best-practices`, `fortify-development`, `wayfinder-development`, `testing-best-practices`     |
| Panel               | `inertia-react-development`, `tailwindcss-development`, `vercel:react-best-practices`, `vercel:shadcn` |
| Diseño              | `impeccable`, `ui-ux-pro-max`, `modern-web-guidance`, `dataviz` (Reportes y Resumen)                   |
| Calidad y seguridad | `security-review`, `code-review`, `simplify`                                                           |
| Legal               | `legal:compliance-check` (política LOPDP)                                                              |
| Flujo de trabajo    | `to-spec`, `to-tickets`, `tdd`, `diagnosing-bugs`, `codebase-design`, `domain-modeling`                |

**Externas.** [vercel-labs/skills](https://github.com/vercel-labs/skills) es el CLI instalador (`npx skills add`); las skills de Vercel están en [vercel-labs/agent-skills](https://github.com/vercel-labs/agent-skills).

Instaladas el 2026-10-16 con el CLI fijado en `skills@1.7.0`. Las versiones quedan registradas en `skills-lock.json`. Los archivos van a `.claude/skills/`, que no se versiona, así que cada desarrollador las instala en su máquina:

```bash
npx skills@1.7.0 add vercel-labs/agent-skills -a claude-code --copy -y \
  -s web-design-guidelines vercel-react-best-practices vercel-composition-patterns
```

Pendiente:

```bash
npx skills@1.7.0 add supabase/agent-skills -a claude-code -s supabase-postgres-best-practices   # solo la parte Postgres genérica
```

```
/plugin marketplace add trailofbits/skills    # differential-review, insecure-defaults, supply-chain-risk-auditor
/plugin marketplace add anthropics/skills     # webapp-testing (Playwright sobre el panel)
```

**Descartadas:**

- Skills de terceros para Docker y .NET: autores pequeños, sin verificar.
- `laravel-cloud` y el deploy de Vercel: desplegamos en Dokploy.
- No hay una skill oficial para .NET; el Agente (en su propio repositorio) se cubre con ADRs y tests xUnit.
