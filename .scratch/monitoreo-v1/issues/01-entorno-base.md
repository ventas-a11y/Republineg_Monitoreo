# 01: Entorno base

**What to build:** Un desarrollador clona el repositorio, levanta el entorno local con Docker Compose y tiene la app corriendo sobre PHP 8.5 con PostgreSQL 18, Valkey 8, Garage y Reverb. La CI valida cada cambio contra PostgreSQL. Ver [ROADMAP](../../../docs/ROADMAP.md) Fase 0 y [SEGURIDAD](../../../docs/SEGURIDAD.md).

**Blocked by:** None (can start immediately)

**Status:** ready-for-human

- [x] PHP 8.5 fijado en composer, en la CI y en la imagen Docker (D19)
- [x] Imagen `dunglas/frankenphp:1.13-php8.5`, fijada por digest, sin Octane, con usuario no root, puerto alto, sin capacidades extra, `no-new-privileges` y sistema de archivos de solo lectura con tmpfs donde haga falta
- [x] Compose local con los servicios app, horizon, scheduler, postgres, valkey y garage. La red `backend` es interna, sin salida a Internet, y solo se publica el puerto de entrada. **Reverb queda pendiente** (ver notas)
- [x] En Postgres, un rol owner para las migraciones y un rol de app solo con permisos DML. Postgres sin puerto publicado (en local, solo en 127.0.0.1)
- [x] `.env.example` apunta a Postgres, Valkey (colas y caché) y Garage, con secretos vacíos y el comando para generar cada uno
- [x] La CI corre `composer ci:check` contra Postgres, más `composer audit` y `pnpm audit`
- [x] Endurecimiento de php.ini: `expose_php`, `display_errors` y `allow_url_*` desactivados
- [ ] `composer ci:check` en verde **en la CI** (en local pasa todo; falta el primer push)
- [ ] Verificar con Docker: `docker compose up -d`, migraciones con `--database=pgsql_owner`, y build de la imagen con `docker compose --profile app up -d --build`

## Notas de implementación (2026-10-16)

- **Por qué `ready-for-human`:** el usuario del sistema no tiene permiso sobre el socket de Docker, así que el Compose solo se validó con `docker compose config`. El build de la imagen y el arranque real los tiene que verificar una persona.
- **Reverb pospuesto:** `laravel/reverb` 1.12.0 exige `guzzlehttp/psr7` 2.x y el proyecto usa Guzzle 8. La rama `main` de Reverb ya lo admite; se instala cuando salga la versión, en el ticket 06 o 09. El servicio está comentado en `compose.yaml`.
- **pnpm:** se migró de npm a pnpm 12.9.0 y se ejecutó `vp migrate` (Vite+ 0.3.3), que corrigió 3 vulnerabilidades críticas de `tinypool`.
- **Ruta `/settings`:** pasó de `Route::redirect` a una ruta solo GET, porque Wayfinder 0.1.21 no sabe tipar el verbo `QUERY` de Laravel 13. Se puede volver a `Route::redirect` cuando Wayfinder 0.1.22 o posterior tenga al menos dos semanas.
- **PHPStan en esta máquina:** el PHP local es estático (musl) y no puede cargar la extensión "turbo" de PHPStan en procesos paralelos. Se valida con `vendor/bin/phpstan analyse --debug`. En la CI no ocurre.
