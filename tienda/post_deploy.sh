#!/bin/bash

set -e

PHP84=/opt/cpanel/ea-php84/root/usr/bin/php

RUN_COMPOSER=false
RUN_FRESH_DEMO=false
RUN_SEED_INITIAL=false
APP_PATH=""

for arg in "$@"; do
    if [ "$arg" = "--test" ]; then
        APP_PATH="/home/jatoyris/tienda_mv_test"
    fi

    if [ "$arg" = "--prod" ]; then
        APP_PATH="/home/jatoyris/public_html"
    fi

    if [ "$arg" = "--composer" ]; then
        RUN_COMPOSER=true
    fi

    if [ "$arg" = "--fresh-demo" ]; then
        RUN_FRESH_DEMO=true
    fi

    if [ "$arg" = "--seed-initial" ]; then
        RUN_SEED_INITIAL=true
    fi
done

if [ -z "$APP_PATH" ]; then
    echo "ERROR: debes indicar ambiente: --test o --prod"
    exit 1
fi

cd "$APP_PATH"

echo "== PHP =="
$PHP84 -v

echo "== Carpeta =="
pwd

if [ "$RUN_COMPOSER" = true ]; then
    echo "== Reinstalar vendor =="
    rm -rf vendor
    $PHP84 /usr/local/bin/composer install --no-dev --prefer-dist --optimize-autoloader
fi

echo "== Crear carpetas Laravel =="
mkdir -p storage/app/public
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
chmod -R 775 storage bootstrap/cache

echo "== Enlace publico de storage =="
$PHP84 artisan storage:link --force

echo "== Base de datos =="
if [ "$RUN_FRESH_DEMO" = true ]; then
    echo "== Modo fresh demo: limpiar tablas y cargar demo =="
    $PHP84 artisan migrate:fresh --force
    $PHP84 artisan db:seed --class=InitialDataSeeder --force
    $PHP84 artisan db:seed --class=DemoDataSeeder --force
else
    echo "== Modo normal: aplicar migraciones pendientes =="
    $PHP84 artisan migrate --force

    if [ "$RUN_SEED_INITIAL" = true ]; then
        echo "== Cargar datos iniciales =="
        $PHP84 artisan db:seed --class=InitialDataSeeder --force
    fi
fi

echo "== Limpiar cache =="
$PHP84 artisan config:clear
$PHP84 artisan route:clear
$PHP84 artisan view:clear
$PHP84 artisan cache:clear

echo "== Verificacion =="
$PHP84 artisan --version
$PHP84 artisan migrate:status

echo "Post deploy terminado OK"
