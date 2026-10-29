# Auditoría local de seguridad — 29 de octubre de 2026

## Resultado

**Hay problemas pendientes: este resultado no permite certificar el sitio como seguro para producción.**

Se revisaron código, configuración efectiva, dependencias PHP/JavaScript y el servidor local en http://127.0.0.1:8000. Se ejecutaron pruebas automatizadas con SQLite en memoria, sin usar la base MySQL de la tienda. No se cambiaron configuración, dependencias ni código de aplicación. Se añadieron pruebas que documentan dos vulnerabilidades y se conservaron evidencias sin contraseñas.

## PHP y avisos recientes

- PHP activo: **8.3.36**, compilado el 20/10/2026; `php.ini` en `C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.ini`.
- Según el [aviso oficial de PHP](https://www.php.net/archive/2026.php#2026-10-21-5), esta versión contiene las correcciones para **CVE-2026-109618 y CVE-2026-109619**. Se verificó versión/configuración; no se intentó corrupción de memoria ni ejecución remota.
- `session.upload_progress.enabled=0`, `session.serialize_handler=php`: cumple la mitigación recomendada para CVE-2026-115560. No se encontraron cambios del serializador en el código de aplicación revisado.
- **Pendiente: Brotli 1.2.0**, confirmado mediante `curl_version()['brotli_version']`. PHP anunció el 26/10 una reconstrucción Windows **8.3.36-1 con Brotli 1.2.1**, para CVE-2026-111497. La versión de PHP permanece 8.3.36: comprobar solo `php -v` no detecta esta diferencia. Fuente: [PHP, archivo oficial de octubre](https://www.php.net/archive/2026.php).
- El camino de ataque de Brotli requiere descompresión automática de respuestas HTTP por cURL. No se encontró uso directo de clientes HTTP salientes en `app`, `config` y `routes`; no se demostró explotación en esta tienda. Actualizar el paquete Windows y reiniciar los procesos PHP sigue siendo la acción recomendada.

## Hallazgos reproducidos

### 1. Redirección abierta después del login — media

`routes/web.php:26` acepta `to` sin restringir su origen y lo guarda en `url.intended`. El controlador de login utiliza `redirect()->intended()`.

Prueba aislada: visitar `/login/intended?to=https%3A%2F%2Fexample.invalid%2Faudit` y autenticarse con el usuario creado en SQLite produce `Location: https://example.invalid/audit`.

Impacto: un enlace de la tienda puede llevar al usuario a una página externa después de autenticarse, útil para phishing. No se observó transferencia de la contraseña en esa redirección.

Recomendación: aceptar únicamente rutas locales válidas y rechazar esquemas, hosts externos y rutas que comiencen con `//`.

### 2. Metadatos de productos privados accesibles sin autenticación — media

`app/Http/Controllers/Shop/ProductsController.php:197` devuelve nombre y precio incluso si el producto no es público. Solo oculta stock y disponibilidad.

Prueba aislada: el detalle de un producto pausado y pendiente devuelve 404, pero `/carrito/validar-stock?ids=ID` devuelve su nombre y precio. Se reprodujo con un producto ficticio, sin consultar datos privados reales para demostrarlo.

Recomendación: filtrar por visibilidad antes de construir la respuesta; conservar la vista privada solo para propietario/administrador autorizado.

### 3. Entrada malformada produce error y expone depuración — media; alta si se publica así

Petición real: `GET /productos?precio_min%5B%5D=1` devuelve **500** y una página con detalles internos de la excepción. La configuración efectiva tiene `APP_DEBUG=true`.

Recomendación: validar filtros como escalares y responder con error de validación; usar `APP_DEBUG=false` en producción. Mantener modo local no sustituye la validación.

### 4. Configuración y archivos sensibles — riesgo de despliegue

- La conexión actual utiliza **root de MySQL sin contraseña**. No se probó acceso remoto a MySQL. Crear un usuario limitado a la base de la tienda y con contraseña para despliegues.
- Git tiene versionados `.env_` y `.env_test`, con valores no vacíos en claves sensibles, incluidos `APP_KEY` y campos de contraseña. No se publican sus valores en el informe. Comprobar cuáles son reales o reutilizados; retirar esos archivos del seguimiento y rotar secretos efectivos si hubo exposición. Borrar solo el archivo actual no lo elimina del historial.
- Cookie de sesión: HttpOnly=true, SameSite=lax, Secure no configurado. Es compatible con este entorno HTTP local; producción requiere HTTPS y Secure.
- El servidor probado escucha exclusivamente en 127.0.0.1:8000. Existe otro proceso local en 8080 cuyo sitio no se auditó.

## Dependencias

`composer audit`: **44 avisos en 12 paquetes**, no necesariamente 44 fallos distintos ni explotables: hay entradas duplicadas o condicionadas por características no usadas. Severidades informadas: 1 crítica, 15 altas, 21 medias, 6 bajas y 1 sin severidad.

| Paquete | Avisos |
| --- | ---: |
| guzzlehttp/guzzle | 9 |
| guzzlehttp/psr7 | 4 |
| laravel/framework | 5 |
| league/commonmark | 12 |
| league/flysystem | 1 |
| symfony/http-foundation | 2 |
| symfony/http-kernel | 1 |
| symfony/mailer | 1 |
| symfony/mime | 2 |
| symfony/polyfill-intl-idn | 1 |
| symfony/routing | 2 |
| symfony/yaml | 4 |

Laravel instalado: **11.51.0**. El aviso crítico **CVE-2026-114509** afecta a TokenGuard con credenciales de API no escalares en determinadas bases de datos. La configuración revisada usa el guard `web` de sesión; no se encontró un guard API basado en token, por lo que no se confirmó ese camino de explotación. Consultar el [aviso del mantenedor](https://github.com/laravel/framework/security/advisories/GHSA-f9x3-7vq9-cph6). Planificar actualización a una rama mantenida y versiones corregidas, con pruebas de compatibilidad.

`npm audit`: **12 paquetes afectados**: 2 críticos, 8 altos, 2 moderados. Los críticos son `concurrently` y su dependencia `shell-quote`; también aparecen axios, vite, postcss y dependencias transitivas. Están declarados como herramientas de desarrollo, aunque axios puede formar parte del JavaScript compilado. No equivalen a 12 vulnerabilidades remotas confirmadas en la web publicada. Vite no estaba escuchando en 5173. El registro indica correcciones disponibles; no se ejecutó `audit fix` ni actualizaciones automáticas.

## Pruebas y controles que funcionaron

- Suite existente: **134 pruebas, 520 aserciones, todas pasan**.
- Nuevas pruebas de seguridad: **3 pruebas, 14 aserciones; 2 fallan** al exigir protección contra los hallazgos 1 y 2; la limitación de intentos de login pasa. Las pruebas fallidas son evidencia pendiente de corrección, no se alteraron para ocultar los problemas.
- 15 peticiones HTTP locales de bajo impacto: portada, archivos sensibles, autenticación, búsqueda, entrada malformada, stock y CSRF.
- `.env`, `.env_test`, `.git/config`, `composer.json`, `test_csrf.php`: 404; `/storage/logs/laravel.log`: 403.
- Invitados en `/admin`, `/mi-tienda` y `/checkout`: redirigidos al login.
- POST al login sin token CSRF: 419.
- Búsqueda con una cadena SQL de prueba: 200 sin errores SQL visibles. Revisión del buscador: usa parámetros de Eloquent, no concatena SQL crudo. Esto no descarta SQLi en cada flujo del sistema.
- Búsqueda con `<script>alert(1)</script>`: no se reflejó el script sin escapar. Autocompletado usa `textContent`. Las descripciones permiten HTML con saneamiento propio; no se realizó una campaña exhaustiva de evasiones de navegador.
- Cabeceras presentes: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy y CSP. La CSP actual restringe base, objetos, marcos y formularios, pero no define `script-src`: no constituye una protección completa contra XSS.
- Suite existente cubre separación de roles, acceso a productos/pedidos de otras tiendas, propietario del pedido, stock y rechazo de archivos no imagen. No se encontró uso directo de `eval`, ejecución de shell o deserialización insegura en el código de aplicación revisado.

## Reproducción y evidencias

Desde la carpeta `tienda`, usar el PHP indicado arriba. La extensión SQLite se habilita solo para el proceso de pruebas; no se modifica php.ini:

```powershell
$env:APP_ENV='testing'
$env:DB_CONNECTION='sqlite'
$env:DB_DATABASE=':memory:'
$env:CACHE_STORE='array'
$env:SESSION_DRIVER='array'
& 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe' -d extension=pdo_sqlite -d extension=sqlite3 vendor\phpunit\phpunit\phpunit
```

La primera ejecución encontró SQLite deshabilitado; se repitió con las extensiones cargadas para ese proceso y la suite original pasó.

Evidencias locales bajo `storage/app/security-audit/`: `composer.json`, `npm.json`, `http.json`, `tests.txt`, `tests.xml`, `security-tests.txt`, `security-tests.xml` y `runtime.php`. Esta carpeta está ignorada por Git. Pruebas nuevas: `tests/Feature/Security/SecurityAuditTest.php`.

## Prioridad sugerida

1. Actualizar el paquete PHP Windows para incluir Brotli corregido y reiniciar todos los workers que lo usen.
2. Corregir redirección externa, filtración de productos privados y validación de filtros; volver a ejecutar las pruebas nuevas.
3. Actualizar dependencias PHP y JavaScript con revisión de compatibilidad y recompilar assets.
4. Preparar configuración de producción: debug apagado, HTTPS, cookies seguras, usuario MySQL limitado y gestión de secretos.

## Límites

Es una revisión local de alcance amplio, no una certificación ni una auditoría del hosting productivo. No se ejecutaron exploits de corrupción de memoria/RCE, pruebas de agotamiento de recursos, borrado de datos, carga sostenida, escaneo externo o búsqueda forense de una intrusión previa. No se validaron TLS, firewall, Apache/FPM, copias de seguridad ni infraestructura de producción. Una versión vulnerable o un aviso de dependencia no demuestra por sí mismo que haya ocurrido un ataque.
