# Carga de productos

Guía para cargar productos mediante conversación. Reglas verificadas en el código local; contrastar con el sitio publicado antes de enviar.

## Conversación

- Conservar los datos indicados por el usuario y preguntar solamente por los obligatorios pendientes.
- Traducir título a `nombre`, valor a `precio` y categoría al `category_id` consultado en el sitio.
- No inventar identificadores, stock ni condiciones del producto.
- Consultar las opciones vigentes de categoría y condición. Si hay varias coincidencias, pedir al usuario que elija.
- Mostrar la ficha antes de guardar y respetar el estado de publicación elegido.
- Ante errores de validación, conservar el borrador y preguntar por las correcciones.
- Confirmar la creación con el identificador o enlace obtenido del sitio. No asumir éxito por recibir HTTP 200 o una redirección.
- No repetir automáticamente una creación si se pierde la respuesta: comprobar primero si el producto existe.

## Campos y rutas existentes

La copia local usa sesión web y CSRF, no una API con token configurada.

| Campo | Requisito |
| --- | --- |
| `nombre` | Obligatorio, hasta 255 caracteres |
| `category_id` | Obligatorio, categoría existente |
| `stock` | Obligatorio, entero mayor o igual a cero |
| `estado_id` | Obligatorio, condición existente |
| `estado_publicacion_id` | Obligatorio: 1 activo, 2 pausado, 3 vendido |
| `tienda_id` | Obligatorio en administración; en vendedor lo determina la sesión |
| `precio` | Opcional según el controlador; entero mayor o igual a cero |
| `descripcion`, `descripcion_corta`, `sku` | Opcionales; descripción corta hasta 180 caracteres, SKU hasta 50 |
| `imagen_archivo` | Opcional, archivo JPG, JPEG, PNG o WebP de hasta 4096 KB |

- Vendedor: `GET /mi-tienda/productos/crear`, `POST /mi-tienda/productos`.
- Administrador: `GET /admin/productos/crear`, `POST /admin/productos`.
- El vendedor crea productos pendientes de revisión. El administrador tiene reglas adicionales de revisión.
- Enviar precios sin separadores de miles. Las imágenes requieren carga de archivos; una URL no equivale a `imagen_archivo`.

## Ficha de recopilación

| Nombre | SKU | Categoría | Descripción | Precio | Stock | Imágenes |
| --- | --- | --- | --- | --- | --- | --- |
| | | | | | | |

## Observaciones

- Indicar la categoría según el archivo `categorias.md`.
- Para las imágenes, indicar el nombre del archivo o su URL.
