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
- [x] 1.6 Estado del producto: nuevo, usado, reacondicionado.
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
- [x] 4.2 Galeria de imagenes.
- [ ] 4.3 Video opcional.
- [x] 4.4 Diferenciar imagen demo por URL vs imagen real local.

## 5. Publicacion

- [ ] 5.1 Fecha publicacion.
- [ ] 5.2 Fecha vencimiento.
- [ ] 5.3 Estado publicacion: activo, pausado, vendido, eliminado.
- [ ] 5.4 Destacado.
- [ ] 5.5 Cantidad visitas.
- [ ] 5.6 Cantidad favoritos.

## 6. Entrega

- [ ] 6.1 Retiro en domicilio.
- [ ] 6.2 Delivery.
- [ ] 6.3 Envio por courier.
- [ ] 6.4 Costo envio.
- [ ] 6.5 Tiempo entrega.

## 7. Seguridad y moderacion

- [ ] 7.1 Estado revision: pendiente, aprobado, rechazado.
- [ ] 7.2 Motivo rechazo.
- [ ] 7.3 Reportes usuarios.
- [ ] 7.4 Bloqueado.
- [ ] 7.5 Validacion de palabras bloqueadas.

## 8. Contacto

- [ ] 8.1 Permite chat.
- [ ] 8.2 Permite WhatsApp.
- [ ] 8.3 Telefono visible.
- [ ] 8.4 Chat interno.

## 9. Caracteristicas dinamicas

- [ ] 9.1 Tabla `producto_atributos`.
- [ ] 9.2 Campos: producto_id, nombre, valor.
- [ ] 9.3 Ejemplos: marca, modelo, color, talla, ano, material.
- [ ] 9.4 Definir que atributos se muestran en ficha y filtros.
- [ ] 9.5 Reemplazar cualquier caracteristica fija en la ficha publica por atributos reales desde BBDD.

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
- [x] 12.5 Filtro por estado.
- [ ] 12.6 Filtro por distancia.

## 13. Tablas recomendadas

- [x] 13.1 `products`.
- [x] 13.2 `product_images`.
- [ ] 13.3 `product_attributes`.
- [x] 13.4 `categories`.
- [x] 13.5 `subcategories` o jerarquia en `categories`.
- [ ] 13.6 `favorites`.
- [ ] 13.7 `messages`.
- [ ] 13.8 `reports`.

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
- [ ] 14.10 Producto: fecha_publicacion.
- [ ] 14.11 Producto: estado_publicacion.

## 15. Estado actual del proyecto

- [x] 15.1 Existe `products`.
- [x] 15.2 Existe `product_images`.
- [x] 15.3 Existe `categories`.
- [x] 15.4 Existe `tags` y relacion producto-tag.
- [x] 15.5 Existe `tiendas`.
- [x] 15.6 Producto tiene tienda, categoria, nombre, descripcion, descripcion_corta, precio, precio_oferta, stock, imagen, envio_gratis, rating, activo y estado.
- [x] 15.7 Producto real creado desde panel sube imagen local.
- [x] 15.8 Demo seeder mantiene imagenes por URL.

## 16. Pendiente de decision

- [ ] 16.1 Que campos son obligatorios al crear producto.
- [ ] 16.2 Que campos van en tienda y no en producto.
- [ ] 16.3 Que campos van en etapa 1 y cuales se dejan para etapa 2.
- [x] 16.4 `precio_original` se elimina del modelo funcional. La unica opcion viva es `precio` normal y `precio_oferta` final opcional.
- [ ] 16.5 Si `estado` actual se divide en `estado_producto` y `estado_publicacion`.

## 17. Criterios visuales del formulario

- [x] 17.1 En PC, el formulario de producto debe ocupar el ancho util disponible del panel.
- [x] 17.2 En PC, organizar campos por secciones y columnas para reducir scroll.
- [x] 17.3 En movil, el formulario debe apilarse a una columna.
- [x] 17.4 En movil, inputs, selects y botones deben ocupar 100% del ancho disponible.
- [x] 17.5 Los mensajes de ayuda deben quedar bajo el campo correspondiente.
- [x] 17.6 Los precios en formularios deben mostrarse con punto como separador de miles, ejemplo `259.990`.

## 18. Reglas de precio definidas

- [x] 18.1 `precio` es el precio normal/base.
- [x] 18.2 `precio_oferta` es opcional.
- [x] 18.3 Si `precio_oferta` existe, es el precio final publicado y usado en carrito/orden.
- [x] 18.4 `precio_oferta` no puede ser mayor que `precio`.
- [x] 18.5 Ambos precios se guardan como enteros, sin decimales.
- [x] 18.6 En pantalla se muestran con separador de miles por punto.
- [x] 18.7 En formularios se puede escribir `39.990`, pero se guarda `39990`.
- [x] 18.8 No se mantiene compatibilidad funcional con `precio_original`.

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
- Confirmado: `estado` del producto ya existe en el formulario y modelo como nuevo/usado/reacondicionado.
- Implementado: se agrega `sku` como codigo interno opcional del producto. Ejemplos visibles: `000001`, `SL00998`.
- Implementado: `descripcion_corta` queda antes de `descripcion` en el formulario.
- Implementado: `descripcion` usa editor visual basico y guarda HTML sanitizado para negrita, cursiva, subrayado y listas.
- Implementado: la busqueda de productos revisa `nombre`, `descripcion_corta` y `descripcion`.
- Decision: las caracteristicas principales no deben quedar escritas en duro en la ficha publica. Se dejan para la etapa de atributos dinamicos desde BBDD.
- Decision: el demo seeder no debe pisar productos existentes. Si el producto ya existe por `slug`, conserva los cambios hechos desde admin/vendedor.
- Revision checklist: se marcan como implementados tienda del producto, usuario vendedor via tienda, nombre visible de tienda, imagen principal, galeria, filtros por categoria/precio y tablas base `products`, `product_images`, `categories`.
- Implementado: filtro publico por estado del producto (`nuevo`, `usado`, `reacondicionado`) en el listado de productos.
