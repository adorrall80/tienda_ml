# Analisis marketplace: productos

## Objetivo

Revisar paso a paso que datos necesita un producto para que la tienda funcione como marketplace, sin agregar campos de golpe ni volver pesado el formulario.

## Criterio base

- El demo puede usar imagenes por URL para ser liviano.
- El demo seeder debe crear datos de ejemplo, pero no sobrescribir productos existentes editados desde el panel.
- Los productos reales creados desde admin o vendedor suben imagen local desde el sitio.
- El marketplace no tiene pagos online por ahora: la compra es una solicitud y luego comprador/tienda coordinan contacto, entrega y pago.
- Todo cambio debe mantener la experiencia simple para vendedor y comprador.

## 1. Datos basicos del producto

- [x] 1.1 Nombre.
- [x] 1.2 Descripcion corta.
- [x] 1.3 Descripcion completa.
- [x] 1.4 Categoria.
- [ ] 1.5 Subcategoria.
- [x] 1.6 Estado del producto por ID: 1 nuevo, 2 usado, 3 reacondicionado.
- [x] 1.7 Precio normal/base, sin decimales.
- [x] 1.8 Precio oferta final opcional, sin decimales.
- [ ] 1.9 Moneda.
- [x] 1.10 Cantidad / stock.
- [x] 1.11 SKU o codigo interno.

## 2. Informacion del vendedor

- [x] 2.1 ID tienda.
- [x] 2.2 ID usuario vendedor via tienda.
- [x] 2.3 Nombre visible vendedor desde tienda.
- [ ] 2.4 Tipo vendedor: vecino, emprendedor, tienda formal.
- [ ] 2.5 Rating vendedor.
- [ ] 2.6 Cantidad de ventas.

## 3. Ubicacion

- [ ] 3.1 Region.
- [ ] 3.2 Comuna.
- [ ] 3.3 Barrio / sector.
- [ ] 3.4 Direccion aproximada opcional.
- [ ] 3.5 Coordenadas GPS opcionales para mapa.
- [ ] 3.6 Ocultar direccion exacta y mostrar solo zona segura.

## 4. Imagenes y multimedia

- [x] 4.1 Imagen principal.
- [x] 4.2 Galeria de imagenes con subida multiple, eliminacion, orden y vista ampliada.
- [ ] 4.3 Video opcional.
- [x] 4.4 Diferenciar imagen demo por URL vs imagen real local.

## 5. Publicacion

- [x] 5.1 Fecha publicacion.
- [ ] 5.2 Fecha vencimiento.
- [x] 5.3 Estado publicacion por ID: 1 activo, 2 pausado, 3 vendido. No se usa eliminado.
- [x] 5.4 Destacado.
- [x] 5.5 Cantidad visitas.
- [x] 5.6 Cantidad favoritos.

## 6. Entrega

- [x] 6.1 Retiro en domicilio.
- [x] 6.2 Delivery.
- [x] 6.3 Envio por courier.
- [x] 6.4 Costo envio.
- [x] 6.5 Tiempo entrega.
- [x] 6.6 Migrar tipos de envio a mantenedor por ID.

## 7. Seguridad y moderacion

- [x] 7.1 Estado revision por ID: 1 pendiente, 2 aprobado, 3 rechazado, 4 en revision por admin.
- [x] 7.2 Motivo rechazo.
- [ ] 7.3 Reportes usuarios.
- [x] 7.4 Bloqueado.
- [x] 7.5 Validacion de palabras bloqueadas.

## 8. Contacto

- [ ] 8.1 Permite chat.
- [x] 8.2 Permite WhatsApp.
- [x] 8.3 Telefono visible.
- [ ] 8.4 Chat interno.

## 9. Caracteristicas dinamicas

- [x] 9.1 Tabla `product_attributes`.
- [x] 9.2 Campos: product_id, nombre, valor, orden.
- [x] 9.3 Ejemplos: marca, modelo, color, talla, ano, material.
- [x] 9.4 Definir que atributos se muestran en ficha.
- [x] 9.5 Reemplazar cualquier caracteristica fija en la ficha publica por atributos reales desde BBDD.
- [ ] 9.6 Definir filtros publicos por atributos.

## 10. Clasificacion util

- [ ] 10.1 Condicion negociable.
- [ ] 10.2 Permite permuta.
- [ ] 10.3 Urgencia venta.
- [ ] 10.4 Etiquetas/tags.

## 11. Escalabilidad futura

- [ ] 11.1 Historial precio.
- [ ] 11.2 Boost / publicacion destacada.
- [ ] 11.3 Integracion pagos futura.
- [ ] 11.4 Comision marketplace futura.

## 12. Busqueda y filtros

- [ ] 12.1 Texto busqueda indexado.
- [ ] 12.2 Filtro por comuna.
- [x] 12.3 Filtro por categoria.
- [x] 12.4 Filtro por precio.
- [x] 12.5 Filtro por estado usando `estado_id`.
- [ ] 12.6 Filtro por distancia.

## 13. Tablas recomendadas

- [x] 13.1 `products`.
- [x] 13.2 `product_images`.
- [x] 13.3 `product_attributes`.
- [x] 13.4 `categories`.
- [x] 13.5 `subcategories` o jerarquia en `categories`.
- [x] 13.6 `favorites`.
- [x] 13.7 `delivery_types`.
- [x] 13.8 `product_conditions`.
- [x] 13.9 `order_statuses`.
- [ ] 13.10 `messages`.
- [ ] 13.11 `reports`.

## 14. Modelo minimo recomendado para esta etapa

- [x] 14.1 Producto: tienda_id.
- [x] 14.2 Producto: usuario vendedor via tienda.
- [x] 14.3 Producto: categoria_id.
- [x] 14.4 Producto: nombre.
- [x] 14.5 Producto: descripcion corta y completa.
- [x] 14.6 Producto: precio y precio oferta.
- [x] 14.7 Producto: estado_producto.
- [x] 14.8 Producto: stock.
- [ ] 14.9 Producto: comuna o zona.
- [x] 14.10 Producto: fecha_publicacion.
- [x] 14.11 Producto: estado_publicacion_id.

## 15. Estado actual del proyecto

- [x] 15.1 Existe `products`.
- [x] 15.2 Existe `product_images`.
- [x] 15.3 Existe `categories`.
- [x] 15.4 Existe `tags` y relacion producto-tag.
- [x] 15.5 Existe `tiendas`.
- [x] 15.6 Producto tiene tienda, categoria, nombre, descripcion, descripcion_corta, precio, precio_oferta, stock, imagen, envio_gratis, rating, activo y estado_id.
- [x] 15.7 Producto real creado desde panel sube imagen local.
- [x] 15.8 Demo seeder mantiene imagenes por URL.
- [x] 15.9 Producto registra fecha de publicacion y cantidad de visitas.
- [x] 15.10 Producto puede marcarse como destacado para prioridad visual.
- [x] 15.11 Producto registra opciones informativas de entrega: retiro, delivery, courier, costo y tiempo.
- [x] 15.12 Producto registra estado de revision y motivo de rechazo para moderacion.
- [x] 15.13 Producto puede quedar bloqueado por administracion con motivo de bloqueo.
- [x] 15.14 Producto tiene atributos dinamicos nombre/valor para especificaciones.
- [x] 15.15 Producto tiene tipos de entrega por mantenedor `delivery_types` y relacion con productos.
- [x] 15.16 Producto tiene condicion por mantenedor `product_conditions` usando `products.estado_id`.

## 16. Pendiente de decision

- [ ] 16.1 Que campos son obligatorios al crear producto.
- [ ] 16.2 Que campos van en tienda y no en producto.
- [ ] 16.3 Que campos van en etapa 1 y cuales se dejan para etapa 2.
- [x] 16.4 `precio_original` se elimina del modelo funcional. La unica opcion viva es `precio` normal y `precio_oferta` final opcional.
- [x] 16.5 `estado_id` queda como condicion del producto y `estado_publicacion_id` queda como publicacion.
- [x] 16.6 Los tipos de envio quedan funcionando desde mantenedor por ID. Los campos simples quedan sincronizados solo como soporte tecnico durante la transicion.

## 17. Criterios visuales del formulario

- [x] 17.1 En PC, el formulario de producto debe ocupar el ancho util disponible del panel.
- [x] 17.2 En PC, organizar campos por secciones y columnas para reducir scroll.
- [x] 17.3 En movil, el formulario debe apilarse a una columna.
- [x] 17.4 En movil, inputs, selects y botones deben ocupar 100% del ancho disponible.
- [x] 17.5 Los mensajes de ayuda deben quedar bajo el campo correspondiente.
- [x] 17.6 Los precios en formularios deben mostrarse con punto como separador de miles, ejemplo `259.990`.
- [x] 17.7 Los listados de productos admin/vendedor permiten paginar por 10, 20 o 50 registros.
- [x] 17.8 Formularios de producto tienen acciones: guardar, guardar y agregar nuevo, guardar y volver al listado.
- [x] 17.9 Formulario de producto organizado en tabs: datos basicos, imagenes y atributos pendiente.
- [x] 17.10 Los controles de galeria no deben crear formularios dentro del formulario principal, para no romper el boton Guardar.
- [x] 17.11 Ordenar o eliminar imagenes de galeria no debe refrescar toda la pagina.

## 18. Reglas de precio definidas

- [x] 18.1 `precio` es el precio normal/base.
- [x] 18.2 `precio_oferta` es opcional.
- [x] 18.3 Si `precio_oferta` existe, es el precio final publicado y usado en carrito/orden.
- [x] 18.4 `precio_oferta` no puede ser mayor que `precio`.
- [x] 18.5 Ambos precios se guardan como enteros, sin decimales.
- [x] 18.6 En pantalla se muestran con separador de miles por punto.
- [x] 18.7 En formularios se puede escribir `39.990`, pero se guarda `39990`.
- [x] 18.8 No se mantiene compatibilidad funcional con `precio_original`.

## 19. Seguridad del comprador

- [x] 19.1 Al terminar una solicitud, mostrar alerta roja indicando no realizar transferencias ni pagos sin coordinar primero la entrega con la tienda.
- [x] 19.2 Repetir la misma alerta en el detalle de compra de Mi cuenta.
- [x] 19.3 Informar que cada tienda es independiente y que sus acuerdos de pago o entrega son responsabilidad de cada tienda, no de la plataforma.
- [x] 19.4 Usar icono local `public/images/alerta-pago.svg` en la alerta.

## 20. Pedidos y seguimiento

- [x] 20.1 Revisar estados actuales de pedido: pendiente, confirmado, preparado, entregado, cancelado.
- [x] 20.2 Revisar como lo ve el comprador en Mi cuenta.
- [x] 20.3 Revisar como lo ve el vendedor en Mi tienda.
- [x] 20.4 Revisar como lo ve admin en Pedidos.
- [x] 20.5 Ajustar textos para que no hablen de pago online ni integraciones de pago.
- [x] 20.6 Definir quien puede marcar un pedido como entregado: vendedor y admin.
- [x] 20.7 Dejar historial claro: quien cambio el estado y cuando.
- [x] 20.8 Confirmar que pedidos con productos de varias tiendas se separan correctamente por tienda.

## 21. Mantenedores

- [x] 21.1 Definir que mantenedores van primero.
- [x] 21.2 Crear mantenedor de estados de producto.
- [x] 21.3 Crear mantenedor de tipos de entrega.
- [x] 21.4 Crear mantenedor de estados de pedido.
- [ ] 21.5 Decidir cuales son editables por admin y cuales son internos del sistema.
- [ ] 21.6 Migrar gradualmente los IDs fijos actuales a tablas mantenedoras sin romper datos.
- [x] 21.7 Recomendacion inicial: partir por tipos de entrega porque ya esta pendiente en `6.6` y toca menos logica interna.

## Registro de avance

### 2026-05-21

- Se creo este documento para revisar el modelo marketplace paso a paso.
- Se registro la diferencia entre imagen demo por URL e imagen real subida local.
- Decision `1.2`: agregar descripcion corta para tarjetas/listados, separada de la descripcion completa.
- Decision `1.7` y `1.8`: `precio` queda como precio normal/base; `precio_oferta` queda como precio opcional de descuento.
- Decision `16.4`: no se mantiene compatibilidad con `precio_original`. El modelo queda en una sola opcion: `precio` normal y `precio_oferta` final opcional.
- Criterio formulario `1.8`: mostrar mensaje bajo `precio_oferta`: "Si completas este campo, este sera el precio final de venta publicado".
- Criterio calculo `1.8`: si `precio_oferta` existe, la ficha, tarjetas, carrito y orden deben usar `precio_oferta` como precio final; `precio` queda como precio normal de referencia.
- Decision visual `17`: el formulario de productos debe usar 100% del ancho util en PC y adaptarse a una columna en movil.
- Implementado `14.5`: se agrega `descripcion_corta` al producto y a los formularios admin/vendedor.
- Implementado `14.6`: se agrega `precio_oferta`, se elimina `precio_original` del flujo funcional y el demo seeder usa `precio` + `precio_oferta`.
- Decision `1.7` y `1.8`: los precios se guardan sin decimales, como enteros en pesos.
- Decision visual `1.7` y `1.8`: los precios se muestran con punto como separador de miles, por ejemplo `259.990`.
- Verificacion tecnica: migraciones ejecutadas, demo seeder ejecutado, build OK y tests unitarios/feature OK.

### 2026-05-22

- Se actualiza el analisis para reflejar los cambios reales aplicados al modelo de precios.
- `precio` y `precio_oferta` quedan documentados como enteros en pesos, sin decimales.
- Los formularios admin/vendedor muestran precios con punto de miles y normalizan el valor antes de validar/guardar.
- Ejemplo definido: el usuario ve/escribe `15.990`, el sistema guarda `15990`.
- Verificacion previa: formulario admin mostro `15.990` y `10.000`, tests pasaron con `96 passed (302 assertions)` y build OK.

- Decision: se elimina `cuotas` del producto porque el marketplace no usa pagos ni financiamiento integrado por ahora.
- Implementado: `cuotas` se retira del modelo funcional, formularios, vistas publicas, seeder demo y tests.
- Implementado: `activo` se mantiene como estado de publicacion simple y se muestra en formularios como switch visual.
- Confirmado: `estado_id` del producto existe en el formulario y modelo como ID numerico para nuevo/usado/reacondicionado.
- Implementado: se agrega `sku` como codigo interno opcional del producto. Ejemplos visibles: `000001`, `SL00998`.
- Implementado: `descripcion_corta` queda antes de `descripcion` en el formulario.
- Implementado: `descripcion` usa editor visual basico y guarda HTML sanitizado para negrita, cursiva, subrayado y listas.
- Implementado: la busqueda de productos revisa `nombre`, `descripcion_corta` y `descripcion`.
- Decision: las caracteristicas principales no deben quedar escritas en duro en la ficha publica. Se dejan para la etapa de atributos dinamicos desde BBDD.
- Decision: el demo seeder no debe pisar productos existentes. Si el producto ya existe por `slug`, conserva los cambios hechos desde admin/vendedor.
- Revision checklist: se marcan como implementados tienda del producto, usuario vendedor via tienda, nombre visible de tienda, imagen principal, galeria, filtros por categoria/precio y tablas base `products`, `product_images`, `categories`.
- Implementado: filtro publico por estado del producto (`1` nuevo, `2` usado, `3` reacondicionado) en el listado de productos.
- Decision: el estado del producto se maneja con `estado_id` numerico para facilitar una futura tabla mantenedora de estados.
- Implementado: se migra `products.estado` texto a `products.estado_id` numerico. Mapeo actual: `1 = Nuevo`, `2 = Usado`, `3 = Reacondicionado`.
- Decision: el estado de publicacion no incluye eliminado. El borrado queda como accion aparte.
- Implementado: se agrega `products.estado_publicacion_id` numerico. Mapeo actual: `1 = Activo`, `2 = Pausado`, `3 = Vendido`.
- Implementado: paginacion configurable en productos de admin y vendedor con opciones 10, 20 y 50 por pagina.
- Implementado: botones de guardado en productos admin/vendedor para quedarse editando, crear otro producto o volver al listado.
- Implementado: formulario admin/vendedor separado en tabs. Imagen principal queda en tab Imagenes; Atributos queda como tab pendiente sin tabla ni logica todavia.
- Implementado: galeria real de producto usando `product_images`: subida multiple desde admin/vendedor, miniaturas actuales en editar y eliminacion individual.
- Implementado: la galeria permite ordenar imagenes con flechas y abrir una vista grande en modal al pinchar la miniatura.
- Correccion: los botones de galeria se cambiaron a acciones JavaScript sin formularios anidados, porque los formularios internos rompian el formulario principal y podian impedir que funcionara el boton Guardar.
- Verificacion: suite completa OK con `108 passed (353 assertions)` y build OK.
- Ajuste visual: los controles de orden de galeria cambian de flechas a botones `Mover antes` y `Mover despues`, actualizan la tarjeta en pantalla sin recargar toda la pagina y deshabilitan la accion cuando la imagen ya esta al inicio o al final.

### 2026-05-23

- Implementado `5.1`: se agrega `products.fecha_publicacion`. Si el producto nace activo, queda publicado con fecha actual. Si se activa despues, se completa la fecha solo si estaba vacia.
- Implementado `5.5`: se agrega `products.visitas` como contador entero. Cuenta una visita por producto en la misma sesion del navegador, evitando que F5 infle el contador.
- Implementado visual: la ficha publica muestra fecha de publicacion y visitas; los listados admin/vendedor muestran fecha bajo el producto y columna de visitas.
- Decision: `fecha_vencimiento` y favoritos quedan pendientes porque requieren reglas de negocio separadas.
- Implementado `5.4`: se agrega `products.destacado` como booleano independiente de tags. Tags siguen siendo etiquetas comerciales; destacado define prioridad visual.
- Decision `5.4`: solo el admin puede marcar productos como destacados. El vendedor no ve ni puede guardar ese campo.
- Implementado visual: formulario admin tiene switch `Producto destacado`; tarjetas publicas muestran etiqueta `DESTACADO`; listados admin/vendedor muestran badge `Destacado` si el admin lo marco.
- Implementado orden: el listado publico por relevancia y los modulos de portada priorizan productos destacados.
- Implementado `5.6` y `13.6`: se agrega tabla `favorites` para guardar productos favoritos por usuario.
- Regla favoritos: solo usuarios registrados pueden guardar favoritos; el mismo producto se guarda una vez por usuario; al volver a presionar se quita.
- Implementado visual: ficha publica permite guardar/quitar favorito y muestra contador; tarjetas muestran cantidad si existe; Mi cuenta tiene tab `Favoritos`; listados admin/vendedor muestran cantidad de favoritos.
- Implementado bloque `6`: el producto registra retiro en domicilio, delivery propio, envio por courier, costo de envio y tiempo estimado.
- Decision bloque `6`: estos campos son informativos; no agregan pago online ni integraciones externas. La entrega y el pago se coordinan por contacto con la tienda.
- Implementado visual bloque `6`: admin y vendedor pueden editar entrega; la ficha publica y la confirmacion de solicitud muestran las opciones por producto.
- Implementado demo bloque `6`: el demo seeder carga productos con opciones de entrega para probar el marketplace funcional.
- Decision `6.6`: los tipos de envio deben quedar como mantenedor por ID mas adelante, similar a los estados. Modelo futuro sugerido: `delivery_types` y relacion `product_delivery_types`.
- Implementado `7.1`: se agrega `products.estado_revision_id` con mapeo `1 = Pendiente`, `2 = Aprobado`, `3 = Rechazado`, `4 = En revision por admin`.
- Implementado `7.2`: se agrega `products.motivo_rechazo` para explicar por que un producto fue rechazado.
- Regla moderacion: solo productos activos, publicados y aprobados se muestran en la tienda publica.
- Regla vendedor: todo producto creado o editado desde el panel vendedor queda pendiente de revision, aunque intente enviar otro estado.
- Regla admin: el admin puede dejar el producto pendiente, aprobarlo o rechazarlo desde el formulario de producto.
- Ajuste regla revision: cuando el admin coloca un producto en `En revision por admin`, el vendedor no puede editarlo, eliminarlo, activar/pausar ni modificar su galeria hasta que el admin cambie ese estado.
- Medida frontend: al guardar desde el formulario vendedor, el navegador consulta el estado actual de revision. Si el producto esta `En revision por admin`, cancela el guardado antes de enviar cambios.
- Implementado `7.4`: se agrega `products.bloqueado` y `products.motivo_bloqueo`.
- Regla bloqueo: un producto bloqueado no se muestra publicamente aunque este activo y aprobado.
- Regla bloqueo vendedor: si el producto esta bloqueado, el vendedor no puede editarlo, eliminarlo, activar/pausar ni modificar su galeria hasta que admin lo desbloquee.
- Implementado `8.2` y `8.3`: el contacto se maneja a nivel tienda, no a nivel producto.
- Regla contacto tienda: la tienda puede permitir u ocultar WhatsApp y puede mostrar u ocultar telefono al comprador.
- Regla contacto comprador: checkout y detalle de compra respetan esos permisos; si WhatsApp no esta permitido, no aparece el boton.
- Decision `8.1` y `8.4`: chat interno queda pendiente porque requiere modulo de mensajes separado.
- Implementado bloque `9`: se crea tabla `product_attributes` para atributos dinamicos del producto.
- Implementado visual bloque `9`: admin y vendedor pueden agregar filas nombre/valor en el tab Atributos del formulario de producto.
- Implementado ficha publica bloque `9`: la pestaña Especificaciones muestra los atributos reales desde BBDD.
- Implementado demo bloque `9`: el demo seeder agrega atributos iniciales a productos demo solo si no tienen atributos.
- Decision `9.6`: filtros por atributos quedan pendientes para definir reglas por categoria y evitar filtros demasiado genericos.
- Implementado bloque `19`: la confirmacion de solicitud y el detalle de compra muestran una alerta roja para no pagar ni transferir sin coordinar primero con la tienda.
- Implementado visual `19.4`: se agrega icono local de advertencia en `public/images/alerta-pago.svg` y se muestra en la alerta de seguridad.
- Propuesto bloque `20`: revisar pedidos y seguimiento desde comprador, vendedor y admin.
- Implementado `20`: estados de pedido quedan como pendiente, confirmado, preparado, entregado y cancelado. Comprador, vendedor y admin muestran etiquetas desde el modelo; vendedor y admin pueden cambiar estados; el historial registra actor, usuario y fecha.
- Propuesto bloque `21`: crear mantenedores, recomendando partir por tipos de entrega antes de mover estados internos.
- Implementado `21.3`: se crea mantenedor admin de tipos de entrega en `delivery_types`.
- Implementado `6.6`: productos admin/vendedor seleccionan tipos de entrega desde mantenedor y se guardan en relacion `delivery_type_product`.
- Implementado migracion inicial: tipos por defecto `Retiro en domicilio`, `Delivery propio` y `Envio por courier`.
- Implementado demo: el demo seeder sincroniza los productos de ejemplo con los tipos de entrega del mantenedor.
- Nota tecnica: los campos antiguos de entrega en `products` quedan sincronizados durante la transicion para no romper vistas ni datos existentes.
- Verificacion: AdminPanelTest, VendedorPanelTest, PublicShopTest, CheckoutTest y build OK.
- Implementado `21.2`: se crea mantenedor admin de estados de producto en `product_conditions`.
- Implementado migracion inicial: estados por defecto `Nuevo`, `Usado` y `Reacondicionado`, conservando IDs 1, 2 y 3.
- Implementado uso real: formularios admin/vendedor y filtro publico leen los estados desde `product_conditions`.
- Verificacion `21.2`: AdminPanelTest, VendedorPanelTest, PublicShopTest, CheckoutTest y build OK.
- Implementado UX mantenedores: los numeros de productos/pedidos asociados se pueden pinchar para ver que registros usan cada estado o tipo de entrega.
- Implementado UX producto: editar producto tiene boton `Vista previa` con icono de ojo para ver la ficha aunque no este publicada.
- Implementado UX tienda publica: boton visible `Limpiar filtros`, filtro por tipo de entrega y resumen de entrega en tarjeta/ficha.
- Implementado `21.4`: se crea mantenedor admin de estados de pedido en `order_statuses`.
- Implementado migracion inicial `order_statuses`: `pendiente`, `confirmado`, `preparado`, `cancelado`, `entregado`.
- Implementado uso real `21.4`: admin/vendedor cambian estados de pedido usando `order_statuses`; comprador, vendedor y admin muestran etiquetas desde el mantenedor.
- Ejecutado `php artisan migrate` en MySQL para crear `order_statuses`.
- Verificacion `21.4`: AdminPanelTest, VendedorPanelTest, CheckoutTest, ProfileTest y build OK.
