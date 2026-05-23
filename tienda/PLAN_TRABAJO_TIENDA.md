# Plan de trabajo tienda

## Objetivo

Convertir la tienda desde un catalogo funcional a una tienda operativa basica, manteniendo tests verdes y avanzando por iteraciones pequenas.

## Estado actual

- [x] 0.1 Analisis inicial de estructura Laravel, rutas, modelos, vistas y JS.
- [x] 0.2 Identificacion de brechas principales: checkout pendiente, carrito local, busqueda vacia, enlace "Quiero vender" sin flujo, datos fijos de vendedor.
- [x] 0.3 Reparacion de tests existentes.
- [x] 0.4 Suite actual verificada: `58 passed (147 assertions)`.

## Iteracion 1: estabilizar base

Objetivo: dejar una base confiable para seguir desarrollando.

- [x] 1.1 Corregir tests heredados de Breeze para rutas actuales.
- [x] 1.2 Crear roles requeridos durante tests.
- [x] 1.3 Ejecutar suite completa.
- [x] 1.4 Agregar tests propios para tienda publica.
- [x] 1.5 Agregar tests propios para panel vendedor.
- [x] 1.6 Agregar tests propios para panel admin.

## Iteracion 2: corregir flujo publico

Objetivo: eliminar incoherencias visibles antes de construir checkout.

- [x] 2.1 Reparar enlace "Quiero vender".
- [x] 2.2 Implementar sugerencias reales en `/buscar/sugerencias`.
- [x] 2.3 Filtrar catalogo publico para mostrar solo productos activos de tiendas activas.
- [x] 2.4 Mostrar datos reales de la tienda vendedora en el detalle del producto.
- [x] 2.5 Agregar tests para busqueda, productos visibles y flujo vendedor.
- [x] 2.6 Ejecutar suite completa.

## Iteracion 3: checkout basico

Objetivo: registrar compras reales sin pasarela de pago todavia.

- [x] 3.1 Crear migracion `orders`.
- [x] 3.2 Crear migracion `order_items`.
- [x] 3.3 Crear modelos `Order` y `OrderItem`.
- [x] 3.4 Crear controlador de checkout.
- [x] 3.5 Convertir carrito frontend en orden persistida.
- [x] 3.6 Validar stock antes de confirmar compra.
- [x] 3.7 Descontar stock al confirmar orden.
- [x] 3.8 Crear pantalla de confirmacion de compra.
- [x] 3.9 Agregar tests de checkout basico.
- [x] 3.10 Ejecutar suite completa.

## Iteracion 4: paneles operativos

Objetivo: que clientes, vendedores y admin puedan gestionar pedidos.

- [x] 4.1 Panel cliente: ver mis compras.
- [x] 4.2 Panel vendedor: ver pedidos recibidos.
- [x] 4.3 Panel admin: ver pedidos globales.
- [x] 4.4 Agregar estados de pedido: pendiente, confirmado, cancelado, enviado, entregado.
- [x] 4.5 Agregar acciones basicas para cambiar estado.
- [x] 4.6 Agregar tests de permisos y visualizacion de pedidos.

## Iteracion 5: operacion sin pago

Objetivo: mejorar el seguimiento de solicitudes sin integrar pagos ni servicios externos por ahora.

- [x] 5.1 Agregar historial de cambios de estado.
- [x] 5.2 Mostrar historial en detalle de pedido para admin y vendedor.
- [x] 5.3 Mostrar historial resumido al comprador en `Mi cuenta`.
- [x] 5.4 Agregar notas internas del vendedor/admin en el pedido.
- [x] 5.5 Agregar aviso visual de siguiente accion segun estado.
- [x] 5.6 Agregar tests del seguimiento sin pago.

## Iteracion 6: diseno visual

Objetivo: redisenar la experiencia sin mezclar cambios esteticos con logica critica.

- [x] 6.1 Definir referencia visual y prioridad de pantallas.
- [x] 6.2 Redisenar header y navegacion.
- [x] 6.3 Redisenar home y modulos publicos.
- [ ] 6.4 Redisenar ficha de producto.
- [ ] 6.5 Redisenar carrito y checkout.
- [ ] 6.6 Revisar responsive en mobile y desktop.

## Iteracion 7: seguridad operativa

Objetivo: endurecer entradas de texto, cabeceras HTTP y controles anti-abuso desde el panel admin.

- [x] 7.1 Reemplazar HTML dinamico inseguro en carrito, checkout y busqueda.
- [x] 7.2 Agregar cabeceras HTTP de seguridad.
- [x] 7.3 Limitar busqueda publica y sugerencias.
- [x] 7.4 Exigir imagenes externas por `https://`.
- [x] 7.5 Crear mantenedor admin de palabras/patrones bloqueados.
- [x] 7.6 Validar textos antes de guardar contra palabras bloqueadas.
- [x] 7.7 Agregar tests de seguridad.

## Decisiones pendientes

- [x] D.1 Definir si un cliente puede convertirse en vendedor desde la app o solo admin asigna el rol.
- [x] D.2 Definir pasarela de pago: no se integran pagos ni servicios externos por ahora.
- [x] D.3 Definir datos minimos de despacho requeridos.
- [ ] D.4 Definir si productos gratis pueden pasar por checkout con total `0`.

## Registro de avance

### 2026-05-19

- Se corrigieron tests existentes.
- Resultado: `php artisan test` pasa completo con `25 passed (61 assertions)`.
- Siguiente foco recomendado: Iteracion 2, flujo publico.

### 2026-05-20

- Se agregaron tests propios para tienda publica.
- Se activo SQLite en memoria para el entorno de test en `phpunit.xml`.
- Se corrigio `ExampleTest` para preparar la base con `RefreshDatabase`.
- Se agregaron tests propios para panel vendedor.
- Se agregaron tests propios para panel admin.
- Iteracion 1 completada.
- Se reparo el enlace "Quiero vender" en header desktop y menu movil.
- Se conecto el CTA "Empezar a vender" al mismo flujo.
- Se implementaron sugerencias reales de busqueda para productos activos y categorias activas.
- Se filtro el catalogo publico para mostrar solo productos activos de tiendas activas.
- Se reemplazaron datos fijos del vendedor por datos reales de la tienda en el detalle del producto.
- Se agregaron tests de redireccion por rol para completar flujo vendedor.
- Iteracion 2 completada.
- Resultado actual: `php artisan test` pasa completo con `56 passed (140 assertions)`.
- Se crearon migraciones `orders` y `order_items` para checkout basico.
- Se crearon modelos `Order` y `OrderItem` con relaciones y snapshots de compra.
- Resultado actual: `php artisan test` pasa completo con `58 passed (147 assertions)`.
- Se conecto checkout basico: formulario de compra, creacion de orden, validacion de stock, descuento de stock y pantalla de confirmacion.
- Se agregaron pruebas feature para checkout exitoso, stock insuficiente y tienda inactiva.
- Se agrego Iteracion 6 para cambios de diseno visual.
- Iteracion 3 completada.
- Resultado actual: `php artisan test` pasa completo con `62 passed (166 assertions)`.
- Revision visual desde cero completada: logout, navegacion como invitado, agregar producto, carrito, checkout y confirmacion.
- Revision visual con dos productos completada: orden `2` creada en MySQL con dos items y carrito vaciado al confirmar.
- Se cambio el cierre de checkout: sin pago en linea, la confirmacion muestra datos de contacto de la tienda.
- Se agregaron campos publicos de contacto para tiendas: email, telefono, WhatsApp y direccion.
- Se agrego al seeder una segunda tienda demo (`Tienda Norte Demo`) con producto propio para probar checkout multi-tienda.
- Revision visual multi-tienda completada en `/checkout/3`: se muestran contactos separados para `TiendaMV Oficial` y `Tienda Norte Demo`.
- Se redisenio la confirmacion multi-tienda: tarjeta por tienda con WhatsApp visible y modal con datos completos + productos de esa tienda.
- Se agregaron mas productos demo a `Tienda Norte Demo` para revisar pedidos con varios productos por tienda.
- Revision visual con orden `4`: 2 tiendas, 6 productos en total, WhatsApp visible en tarjeta y modales verificados para cada tienda.
- Se movio el total por tienda a la tarjeta junto a WhatsApp y `Ver detalle`; se elimino el resumen por tienda y se dejo total general inferior.
- Se conecto `Mi cuenta > Mis compras` con ordenes reales del usuario registrado, mostrando solicitudes, tiendas y productos.
- Se agrego prueba para confirmar que checkout logueado guarda `user_id` y que `Mi cuenta` lista las compras.
- Resultado actual: `php artisan test` pasa completo con `66 passed (185 assertions)`.
- Se cambio el flujo para exigir login en `/checkout` y al enviar solicitud; el carrito sigue libre para invitados.
- Se separaron seeders:
  - `InitialDataSeeder`: roles, admin `admin@admin.cl` con clave `123`, categorias y tags base.
  - `DemoDataSeeder`: admin, vendedor y cliente con clave `123`, tiendas demo, banners, productos y escenarios multi-tienda.
  - `DatabaseSeeder`: ejecuta solo datos iniciales por defecto.
- Seeders inicial y demo verificados en MySQL: 3 roles, 8 categorias, 4 tags, 2 tiendas y 18 productos.
- `DemoDataSeeder` ahora crea/sincroniza usuarios demo con clave `123`: `admin@admin.cl`, `vendedor@tiendamv.cl`, `cliente@tiendamv.cl`.
- Prueba limpia ejecutada: `migrate:fresh`, `InitialDataSeeder`, `DemoDataSeeder` y suite completa.
- Resultado tras prueba limpia: 3 usuarios, 3 roles, 8 categorias, 4 tags, 3 banners, 2 tiendas, 18 productos, 54 imagenes; tests `67 passed (187 assertions)`.
- Se agrego flujo en `Mi cuenta > Vender`: un cliente puede activar rol vendedor manteniendo rol cliente y luego crear su tienda.
- Se endurecio cambio de rol en admin: no auto-submit, requiere boton/confirmacion, no permite cambiar el propio rol y `vendedor` conserva `cliente`.
- Flujo completo probado: usuario deslogueado se registra como comprador, crea solicitud, activa vendedor, crea tienda y publica producto visible en catalogo.
- Flujo visual manual completado con usuario: registro, compra, activacion vendedor, tienda `josex`, producto `producto 3xds` publicado y visible en catalogo.
- Se completo `4.1`: checkout para usuarios logueados sin datos de despacho, datos de comprador tomados desde la sesion, confirmacion protegida por propietario y detalle de compra en `Mi cuenta`.
- Resultado actual: `php artisan test` pasa completo con `73 passed (205 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
- Se completo `4.2`: panel vendedor con resumen de pedidos recibidos, listado `/mi-tienda/pedidos`, detalle seguro por pedido y filtro para mostrar solo productos de la tienda del vendedor.
- Resultado actual: `php artisan test` pasa completo con `76 passed (219 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
- Se completo `4.3`: panel admin con resumen de pedidos, listado global `/admin/pedidos`, detalle de pedido con productos agrupados por tienda y permisos solo admin.
- Resultado actual: `php artisan test` pasa completo con `80 passed (236 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
- Se completo `4.4` y `4.5`: admin y vendedor pueden cambiar estado del pedido entre pendiente, confirmado, cancelado, enviado y entregado desde el detalle.
- Se completo `4.6`: pruebas de permisos, visualizacion y cambios de estado para cliente, vendedor y admin.
- Iteracion 4 completada.
- Resultado actual: `php artisan test` pasa completo con `85 passed (248 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
- Se reemplazo la antigua Iteracion 5 de pago real por `operacion sin pago`: historial, notas y avisos de seguimiento. No se incluye exportacion.
- Se completo Iteracion 5: historial de estados, seguimiento visible para comprador/admin/vendedor, notas internas y aviso de siguiente accion segun estado.
- Se crearon migraciones `order_status_histories` y `order_internal_notes`, aplicadas en MySQL con `php artisan migrate`.
- Resultado actual: `php artisan test` pasa completo con `91 passed (277 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
- Se completo Iteracion 7 de seguridad operativa: XSS mitigado en JS, cabeceras HTTP, throttle de sugerencias, imagenes solo HTTPS y mantenedor admin `/admin/seguridad/palabras-bloqueadas`.
- Se creo la regla `NoReservedAttackWords` y la tabla `security_blocked_terms`, aplicada en MySQL con `php artisan migrate`.
- Resultado actual: `php artisan test` pasa completo con `96 passed (298 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.

### 2026-05-21

- Se definio `6.1`: prioridad visual en este orden: header/navegacion, home, ficha de producto, carrito/checkout y responsive.
- Criterio visual: mantener identidad tipo marketplace, mejorar lectura/jerarquia, no mezclar cambios esteticos con logica de negocio y priorizar pantallas operativas sobre marketing.
- Se completo `6.2`: header y navegacion con buscador mas legible, acciones compactas, menu movil mas estable y espaciado de letras normalizado.
- Revision visual en navegador: home carga correctamente, header/navegacion sin overflow horizontal.
- Resultado actual: `php artisan test` pasa completo con `96 passed (298 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
- Se completo `6.3`: home mantiene estructura modular separada (`hero`, categorias, grid, scroll y CTA), con clases propias por modulo y tarjetas de producto mostrando tienda de origen.
- Revision visual en navegador: home con 4 modulos publicos, 8 categorias, 15 productos visibles y sin overflow horizontal.
- Resultado actual: `php artisan test` pasa completo con `96 passed (298 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
- Se cambio carga de imagen de producto: admin y vendedor suben archivo JPG/PNG/WebP en vez de pegar URL.
- Se creo/verifico enlace `public/storage` para servir imagenes subidas desde `/storage/products/...`.
- Se ajusto ficha de producto para hablar de solicitud y coordinacion con tienda, sin prometer pagos online.
- Resultado actual: `php artisan test` pasa completo con `96 passed (302 assertions)`.
- Resultado frontend: `npm run build` completado correctamente.
