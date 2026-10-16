# syntax=docker/dockerfile:1.7

# Una sola imagen para app, horizon, reverb y scheduler (cada uno con su comando).
# FrankenPHP en modo clásico, sin Octane; Traefik termina el TLS (ver docs/SEGURIDAD.md).
ARG FRANKENPHP_IMAGE=dunglas/frankenphp:1.13-php8.5-trixie@sha256:2a9c6663f73ad1e401909634206bd25e13bed5b94d86eefa3b5ec64c985b4595
ARG NODE_IMAGE=node:24-trixie-slim

FROM ${NODE_IMAGE} AS node

FROM ${FRANKENPHP_IMAGE} AS base

RUN install-php-extensions pdo_pgsql redis intl pcntl bcmath zip gd opcache

WORKDIR /app

# Dependencias de Composer de producción.
FROM base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative --no-scripts \
    && php artisan package:discover --ansi

# Assets de Vite. Necesita PHP porque el plugin de Wayfinder ejecuta artisan.
FROM vendor AS assets

COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/corepack/dist/corepack.js /usr/local/bin/corepack \
    && corepack enable pnpm

# pnpm sale del campo "packageManager" de package.json (versión y hash fijados).
RUN pnpm install --frozen-lockfile && pnpm run build

# Imagen final: código de solo lectura, proceso sin root, puerto alto.
FROM base AS runtime

ARG APP_USER=appuser
ARG APP_UID=1000

ENV SERVER_NAME=:8080 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    XDG_CONFIG_HOME=/config \
    XDG_DATA_HOME=/data

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/conf.d/app.ini "$PHP_INI_DIR/conf.d/zz-app.ini"

COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

RUN useradd --uid "${APP_UID}" --no-create-home --shell /usr/sbin/nologin "${APP_USER}" \
    && setcap -r /usr/local/bin/frankenphp || true \
    && mkdir -p storage/app/private storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs \
    && chown -R "${APP_USER}:${APP_USER}" storage /config/caddy /data/caddy \
    && chmod -R 775 storage

USER ${APP_USER}

EXPOSE 8080
