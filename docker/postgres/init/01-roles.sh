#!/usr/bin/env bash
# Crea la base de la app con dos roles separados (docs/SEGURIDAD.md, Database):
#   - owner: dueño del esquema; solo lo usan las migraciones.
#   - app:   solo DML (SELECT/INSERT/UPDATE/DELETE); es el que usa la aplicación.
# Lo ejecuta la imagen oficial de Postgres una sola vez, al crear el volumen.
set -euo pipefail

: "${MONITOREO_DB:?Falta MONITOREO_DB}"
: "${MONITOREO_OWNER_USER:?Falta MONITOREO_OWNER_USER}"
: "${MONITOREO_OWNER_PASSWORD:?Falta MONITOREO_OWNER_PASSWORD}"
: "${MONITOREO_APP_USER:?Falta MONITOREO_APP_USER}"
: "${MONITOREO_APP_PASSWORD:?Falta MONITOREO_APP_PASSWORD}"

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname postgres \
    -v db="$MONITOREO_DB" \
    -v owner="$MONITOREO_OWNER_USER" -v owner_pw="$MONITOREO_OWNER_PASSWORD" \
    -v app="$MONITOREO_APP_USER" -v app_pw="$MONITOREO_APP_PASSWORD" <<-'SQL'
    CREATE ROLE :"owner" LOGIN PASSWORD :'owner_pw';
    CREATE ROLE :"app" LOGIN PASSWORD :'app_pw';
    CREATE DATABASE :"db" OWNER :"owner";
    REVOKE ALL ON DATABASE :"db" FROM PUBLIC;
    GRANT CONNECT ON DATABASE :"db" TO :"app";
SQL

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$MONITOREO_DB" \
    -v owner="$MONITOREO_OWNER_USER" -v app="$MONITOREO_APP_USER" <<-'SQL'
    ALTER SCHEMA public OWNER TO :"owner";
    REVOKE CREATE ON SCHEMA public FROM PUBLIC;
    GRANT USAGE ON SCHEMA public TO :"app";
    ALTER DEFAULT PRIVILEGES FOR ROLE :"owner" IN SCHEMA public
        GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO :"app";
    ALTER DEFAULT PRIVILEGES FOR ROLE :"owner" IN SCHEMA public
        GRANT USAGE, SELECT ON SEQUENCES TO :"app";
SQL
