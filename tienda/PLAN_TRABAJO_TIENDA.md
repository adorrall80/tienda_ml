# Plan de trabajo tienda

## Objetivo

Convertir la tienda desde un catalogo funcional a una tienda operativa basica, manteniendo tests verdes y avanzando por iteraciones pequenas.

## Estado actual

- [x] 0.1 Analisis inicial de estructura Laravel, rutas, modelos, vistas y JS.
- [x] 0.2 Identificacion de brechas principales: checkout pendiente, carrito local, busqueda vacia, enlace "Quiero vender" sin flujo, datos fijos de vendedor.
- [x] 0.3 Reparacion de tests existentes.
- [x] 0.4 Suite actual verificada: `43 passed (109 assertions)`.

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

- [ ] 2.1 Reparar enlace "Quiero vender".
- [ ] 2.2 Implementar sugerencias reales en `/buscar/sugerencias`.
- [ ] 2.3 Filtrar catalogo publico para mostrar solo productos activos de tiendas activas.
- [ ] 2.4 Mostrar datos reales de la tienda vendedora en el detalle del producto.
- [ ] 2.5 Agregar tests para busqueda, productos visibles y flujo vendedor.
- [ ] 2.6 Ejecutar suite completa.

## Iteracion 3: checkout basico

Objetivo: registrar compras reales sin pasarela de pago todavia.

- [ ] 3.1 Crear migracion `orders`.
- [ ] 3.2 Crear migracion `order_items`.
- [ ] 3.3 Crear modelos `Order` y `OrderItem`.
- [ ] 3.4 Crear controlador de checkout.
- [ ] 3.5 Convertir carrito frontend en orden persistida.
- [ ] 3.6 Validar stock antes de confirmar compra.
- [ ] 3.7 Descontar stock al confirmar orden.
- [ ] 3.8 Crear pantalla de confirmacion de compra.
- [ ] 3.9 Agregar tests de checkout basico.
- [ ] 3.10 Ejecutar suite completa.

## Iteracion 4: paneles operativos

Objetivo: que clientes, vendedores y admin puedan gestionar pedidos.

- [ ] 4.1 Panel cliente: ver mis compras.
- [ ] 4.2 Panel vendedor: ver pedidos recibidos.
- [ ] 4.3 Panel admin: ver pedidos globales.
- [ ] 4.4 Agregar estados de pedido: pendiente, confirmado, cancelado, enviado.
- [ ] 4.5 Agregar acciones basicas para cambiar estado.
- [ ] 4.6 Agregar tests de permisos y visualizacion de pedidos.

## Iteracion 5: pago real

Objetivo: integrar una pasarela de pago cuando el checkout interno este estable.

- [ ] 5.1 Definir proveedor de pago.
- [ ] 5.2 Crear servicio de integracion.
- [ ] 5.3 Manejar retorno exitoso.
- [ ] 5.4 Manejar rechazo o abandono.
- [ ] 5.5 Sincronizar estado de pago con orden.
- [ ] 5.6 Agregar tests del flujo de pago simulado.

## Decisiones pendientes

- [ ] D.1 Definir si un cliente puede convertirse en vendedor desde la app o solo admin asigna el rol.
- [ ] D.2 Definir pasarela de pago: Webpay, Mercado Pago, Khipu u otra.
- [ ] D.3 Definir datos minimos de despacho requeridos.
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
- Resultado actual: `php artisan test` pasa completo con `43 passed (109 assertions)`.
