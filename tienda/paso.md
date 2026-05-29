# Pasos despues del deploy FTP

## 0. Checklist rapido despues de cada deploy

Entrar a la carpeta del proyecto:

```bash
cd /home/jatoyris/Despliegue_test/pruebas
PHP84=/opt/cpanel/ea-php84/root/usr/bin/php
```

Ejecutar siempre despues de cada deploy:

```bash
$PHP84 artisan config:clear
$PHP84 artisan route:clear
$PHP84 artisan view:clear
$PHP84 artisan cache:clear
```

Si el deploy trae migraciones nuevas:

```bash
$PHP84 artisan migrate --force
```

Si es una instalacion limpia de pruebas:

```bash
$PHP84 artisan migrate:fresh --force
$PHP84 artisan db:seed --class=InitialDataSeeder --force
$PHP84 artisan db:seed --class=DemoDataSeeder --force
```

Si cambio `composer.json` o `composer.lock`, o si `vendor` esta incompleto:

```bash
rm -rf vendor
$PHP84 /usr/local/bin/composer install --no-dev --prefer-dist --optimize-autoloader
```

Si aparece error de vistas, cache o carpetas faltantes:

```bash
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
chmod -R 775 storage bootstrap/cache
```

Verificacion final:

```bash
$PHP84 artisan --version
$PHP84 artisan migrate:status
```

Abrir el sitio:

```text
http://tiendatest.esremate.cl/pruebas/public/
```

## 1. Entrar a la carpeta del proyecto

En la Terminal de cPanel, entrar a la carpeta donde quedo publicado el sitio de pruebas:

```bash
cd /home/jatoyris/Despliegue_test/pruebas
```

## 2. Definir PHP 8.4

Guardar la ruta de PHP 8.4 en una variable temporal de la terminal:

```bash
PHP84=/opt/cpanel/ea-php84/root/usr/bin/php
```

Este comando no muestra nada. Para validar que quedo bien, ejecutar:

```bash
$PHP84 -v
```

## 3. Instalar vendor

Como la carpeta `vendor` no se sube por FTP, se instala directamente en el servidor:

```bash
$PHP84 /usr/local/bin/composer install --no-dev --prefer-dist --optimize-autoloader
```

Si `vendor` quedo incompleto o danado, borrarlo y reinstalarlo:

```bash
rm -rf vendor
$PHP84 /usr/local/bin/composer install --no-dev --prefer-dist --optimize-autoloader
```

## 4. Crear carpetas necesarias de Laravel

Crear las carpetas que Laravel necesita para vistas, cache y sesiones:

```bash
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
chmod -R 775 storage bootstrap/cache
```

Verificar que las carpetas existen:

```bash
ls -ld storage/framework/views storage/framework/cache storage/framework/sessions bootstrap/cache
```

Si el comando muestra las carpetas, esta correcto.

## 5. Verificar que Laravel responde

Ejecutar:

```bash
$PHP84 artisan --version
```

Debe mostrar la version de Laravel, por ejemplo:

```text
Laravel Framework 11.x
```

## 6. Revisar estado de migraciones

Ejecutar:

```bash
$PHP84 artisan migrate:status
```

Si muestra este mensaje:

```text
Migration table not found.
```

significa que Laravel conecta a la base de datos, pero la base aun esta vacia y falta ejecutar las migraciones.

Si aparece error de conexion, SQLite o Access denied, revisar el archivo `.env`.

## 7. Crear tablas desde cero en pruebas

En ambiente de pruebas, si la base debe quedar limpia, ejecutar:

```bash
$PHP84 artisan migrate:fresh --force
```

Este comando borra las tablas existentes y vuelve a crear toda la estructura.

No usar `migrate:fresh` en produccion, porque elimina datos.

Si no se quiere borrar datos, usar:

```bash
$PHP84 artisan migrate --force
```

## 8. Cargar datos iniciales

Ejecutar:

```bash
$PHP84 artisan db:seed --class=InitialDataSeeder --force
```

Este seeder crea los datos minimos del sistema, como roles, estados, tipos base y usuario administrador inicial.

## 9. Cargar datos demo con ejemplos

Solo en ambiente de pruebas, ejecutar:

```bash
$PHP84 artisan db:seed --class=DemoDataSeeder --force
```

Este seeder carga usuarios demo, tiendas, productos, pedidos y datos de ejemplo.

No ejecutar el seeder demo en produccion.

## 10. Reparar `.htaccess` si las rutas limpias fallan

Si la home carga, pero rutas como `/productos`, `/login` o `/carrito` dan error 404 y solo funcionan con `index.php`, revisar el archivo:

```bash
ls -la public/.htaccess
```

Si el archivo existe pero pesa `0`, esta vacio y debe editarse:

```bash
nano public/.htaccess
```

Contenido recomendado:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

Guardar en nano:

```text
CTRL + O
Enter
CTRL + X
```

Probar una ruta sin `index.php`:

```text
http://tiendatest.esremate.cl/pruebas/public/productos
```

## 11. Produccion: apuntar dominio a `public`

En produccion, el dominio no deberia abrir con `/public` ni con `/index.php`.

El Document Root del dominio debe apuntar directo a:

```text
/home/jatoyris/Despliegue_test/pruebas/public
```

o a la carpeta `public` equivalente de produccion.

Luego ajustar `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://DOMINIO_FINAL
```

Limpiar cache:

```bash
$PHP84 artisan config:clear
$PHP84 artisan route:clear
$PHP84 artisan view:clear
$PHP84 artisan cache:clear
```

Probar:

```text
https://DOMINIO_FINAL/productos
```

## 12. Si aparece 404 al navegar desde botones del sitio

Sintoma:

```text
http://tiendatest.esremate.cl/checkout
```

da 404, pero la ruta correcta deberia incluir `/pruebas/public`:

```text
http://tiendatest.esremate.cl/pruebas/public/checkout
```

Causa frecuente: algun JavaScript usa rutas duras como:

```js
window.location.href = '/checkout';
```

Correccion en codigo:

- Definir la URL base del sitio desde Laravel.
- Usar esa URL base en JavaScript para construir rutas.

Despues de corregir JS, ejecutar en local:

```bash
npm run build
```

Subir deploy y limpiar cache en servidor:

```bash
$PHP84 artisan config:clear
$PHP84 artisan route:clear
$PHP84 artisan view:clear
$PHP84 artisan cache:clear
```

## 13. Si aparece 403 al editar un producto del vendedor

Sintoma:

```text
403 PROHIBIDO
```

al entrar a una ruta como:

```text
/mi-tienda/productos/19/editar
```

Causa frecuente: el producto no pertenece a la tienda del vendedor logueado.

El sistema protege la edicion con esta logica:

```php
abort_if($producto->tienda_id !== $tienda?->id, 403);
```

Revisar en la base de datos:

- `products.id`
- `products.tienda_id`
- tienda asociada al usuario logueado

El `products.tienda_id` debe coincidir con la tienda del vendedor actual.

Si el producto fue creado mientras habia datos demo mezclados o una sesion antigua, puede quedar asociado a otra tienda. En pruebas, la forma limpia es:

```bash
$PHP84 artisan migrate:fresh --force
$PHP84 artisan db:seed --class=InitialDataSeeder --force
$PHP84 artisan db:seed --class=DemoDataSeeder --force
```

Luego repetir el flujo desde un usuario nuevo.

## 14. Automatizar pasos con `post_deploy.sh`

En cPanel Terminal se puede crear un script para ejecutar los pasos post deploy.

Crear archivo:

```bash
nano post_deploy.sh
```

Contenido:

```bash
#!/bin/bash

set -e

PHP84=/opt/cpanel/ea-php84/root/usr/bin/php

RUN_COMPOSER=false
RUN_FRESH_DEMO=false
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
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
chmod -R 775 storage bootstrap/cache

echo "== Base de datos =="
if [ "$RUN_FRESH_DEMO" = true ]; then
    echo "== Modo fresh demo: limpiar tablas y cargar demo =="
    $PHP84 artisan migrate:fresh --force
    $PHP84 artisan db:seed --class=InitialDataSeeder --force
    $PHP84 artisan db:seed --class=DemoDataSeeder --force
else
    echo "== Modo normal: aplicar migraciones pendientes =="
    $PHP84 artisan migrate --force
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
```

Guardar en nano:

```text
CTRL + O
Enter
CTRL + X
```

Dar permisos de ejecucion:

```bash
chmod +x post_deploy.sh
```

Uso normal despues de un deploy:

```bash
./post_deploy.sh --test
```

Uso normal en produccion:

```bash
./post_deploy.sh --prod
```

Uso cuando cambio `composer.json`, `composer.lock` o `vendor` quedo incompleto:

```bash
./post_deploy.sh --test --composer
```

Uso en pruebas cuando se quiere borrar la base, recrear tablas y cargar demo:

```bash
./post_deploy.sh --test --fresh-demo
```

Uso en pruebas cuando se quiere reinstalar vendor y tambien reiniciar la base demo:

```bash
./post_deploy.sh --test --composer --fresh-demo
```

Resumen:

- `--test`: usa `/home/jatoyris/tienda_mv_test`.
- `--prod`: usa `/home/jatoyris/public_html`.
- `--composer`: reinstala `vendor`.
- `--fresh-demo`: borra tablas, recrea migraciones y carga datos demo.
- `--test --composer --fresh-demo`: usa test, reinstala `vendor` y reinicia datos demo.
