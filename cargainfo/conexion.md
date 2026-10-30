# Conexión y prueba de carga

## Estado

Inspección del código local y conexión pública completadas. Creación pendiente de autenticación y ficha de producto.
No se ha creado ningún producto ni se ha modificado el código de la tienda.

## Comprobación remota: 2026-10-29

- URL: `https://tienda.esremate.cl`.
- `GET /`: HTTP 200, título “TiendaMV — Compra y vende en Chile”.
- `GET /login`: HTTP 200, formulario de contraseña y token CSRF presentes.
- `GET /mi-tienda/productos/crear` sin sesión: redirige a `/login`.
- La conexión HTTPS funciona. No se ha probado aún el acceso autenticado ni se han consultado categorías privadas.

## Conexión encontrada

Cliente HTTP preparado en `cliente.py`. Ejecutar `python cargainfo/cliente.py` desde la raíz del proyecto. Solicita correo y contraseña oculta en terminal, mantiene cookies solo en memoria y consulta las opciones del formulario de administración. Esta primera prueba no crea productos. No constituye una API JSON nueva ni requiere cambios en Laravel.

En el formulario publicado observado tras el acceso, el selector de tienda no ofrecía opciones. Hay que volver a comprobarlo con el cliente HTTP; una tienda existente es obligatoria para la carga desde administración.

- `GET /login`: formulario con token CSRF.
- `POST /login`: `email`, `password` y token CSRF, conservando las cookies de sesión.
- Las rutas de productos requieren una sesión con rol de vendedor o administrador.
- No hay `routes/api.php` ni registro de rutas API en `bootstrap/app.php` en esta copia.
- Las rutas de creación devuelven HTML y redirecciones; no deben documentarse como API REST con token.

## Prueba pendiente

1. Recibir la URL pública y comprobar el formulario de acceso mediante una petición de lectura.
2. Configurar el acceso autorizado sin guardar contraseñas, tokens ni cookies en archivos versionados o en estos MD.
3. Consultar el formulario de creación y sus categorías, condiciones y tiendas disponibles según el rol.
4. Recoger la ficha de prueba por conversación. Los ejemplos anteriores no constituyen datos confirmados para esta carga.
5. Completar los campos obligatorios y guardar una sola vez el producto solicitado.
6. Comprobar el identificador y los datos persistidos. Informar el enlace y el estado de publicación/revisión.

Si la versión publicada cuenta con una API distinta, utilizar su contrato real en lugar de asumir que coincide con las rutas locales.
