# Pruebas del sistema

## 0. Elegir URL de prueba

Antes de comenzar, preguntar que ambiente se quiere probar:

1. `http://tiendatest.esremate.cl`
2. `https://esremate.cl`

Anotar URL elegida:

```text
URL: http://tiendatest.esremate.cl
```

## Pasos de pruebas

### 1. Abrir un producto

- Entrar a la URL elegida.
- Abrir un producto desde el listado o desde la portada.
- Verificar que cargue la ficha del producto.
- Revisar que se vea:
  - Nombre del producto.
  - Precio.
  - Tienda.
  - Opciones de entrega.
  - Botones de compra/carrito.

Estado:

```text
OK - Probado con Samsung Galaxy A54 5G 128GB Awesome Black.
```

### 2. Buscar un producto

Antes de probar, preguntar:

```text
Que producto quiere buscar? celular / telefono / zapatilla
```

Luego:

- Escribir el producto en el buscador.
- Ejecutar la busqueda.
- Verificar que los resultados incluyan coincidencias por:
  - Nombre.
  - Descripcion corta.
  - Descripcion completa.

Estado:

```text
PARCIAL
- Busqueda "celular" devuelve 0 resultados en http://tiendatest.esremate.cl/productos?q=celular.
- Busqueda "telefono" devuelve 2 resultados en http://tiendatest.esremate.cl/productos?q=telefono.
- Busqueda "zapatilla" devuelve 1 resultado en http://tiendatest.esremate.cl/productos?q=zapatilla.
  Resultado: Zapatillas Nike Air Max 270 Hombre Negro/Blanco.
Observacion: conviene agregar sinonimos o tags para que "celular" encuentre productos tipo telefono.
```

### 3. Crear cuenta

- Entrar a `Crea tu cuenta`.
- Completar el formulario de registro.
- Crear el usuario.
- Verificar que el sistema deje la sesion iniciada.
- Verificar que aparezcan las opciones de cuenta del usuario.

Datos usados:

```text
Nombre: Usuario Prueba
Email: prueba@admin.cl
Password: 12345678
```

Estado:

```text
OK - Cuenta creada y sesion iniciada. Se muestra Usuario Prueba en la cabecera.
```

### 4. Agregar producto al carrito

- Buscar o abrir un producto.
- Presionar `Agregar al carrito`.
- Verificar que el contador del carrito aumente.

Producto usado:

```text
Zapatillas Nike Air Max 270 Hombre Negro/Blanco
```

Estado:

```text
OK - Producto agregado al carrito. El contador subio a 1.
```

### 5. Completar compra como solicitud

- Agregar mas de un producto al carrito.
- Entrar al carrito.
- Presionar `Continuar compra`.
- Completar telefono y mensaje opcional.
- Presionar `Enviar solicitud`.
- Verificar pantalla final.

Datos usados:

```text
Producto 1: Zapatillas Nike Air Max 270 Hombre Negro/Blanco
Producto 2: Samsung Galaxy A54 5G 128GB Awesome Black
Telefono comprador: +56954584991
Mensaje: Mensaje de prueba 1780107333, coordinar entrega por WhatsApp.
```

Resultado esperado:

```text
Solicitud enviada.
Debe mostrar alerta de no realizar pagos sin coordinar con la tienda.
Debe mostrar tienda, WhatsApp y total por tienda.
```

Estado:

```text
OK - Solicitud creada ORD-20260530-567463 por $344.980.
Se muestra alerta de seguridad, telefono comprador y contacto WhatsApp de TiendaMV Oficial.
```

### 6. Ver compra en Mi cuenta

- Entrar a `Mi cuenta`.
- Revisar pestaña o seccion `Mis compras`.
- Confirmar que aparece la solicitud creada.

Estado:

```text
OK - En Mi cuenta aparece ORD-20260530-567463, total $344.980, estado Pendiente, 2 productos y 1 tienda.
```

### 7. Activar vendedor

- Entrar a `Mi cuenta`.
- Ir a la pestaña `Vender`.
- Presionar `Activar vendedor`.
- Verificar que el sistema lleve a crear tienda.

Estado:

```text
OK - Usuario Prueba fue activado como vendedor y redirigido a /mi-tienda/crear-tienda.
```

### 8. Crear tienda

- Completar formulario de crear tienda.
- Presionar `Crear tienda`.
- Verificar redireccion al dashboard vendedor.

Datos usados:

```text
Nombre: Tienda Prueba Usuario 1780107711
Email: prueba@admin.cl
Telefono: +56911112222
WhatsApp: +56911112222
Direccion: Santiago Centro
```

Estado:

```text
OK - Tienda creada. El sistema redirige a /mi-tienda y muestra "Tienda creada! Ya puedes agregar productos.".
```

### 9. Crear producto como vendedor

- Entrar a `+ Agregar`.
- Completar datos demo del producto.
- Guardar y volver al listado.
- Verificar que el producto aparezca en `Mis productos`.

Datos usados:

```text
Nombre: Producto Demo Marketplace 1780107936
SKU: DEMO1780107938
Categoria: Ropa y Moda
Precio normal: $45.990
Precio oferta: $39.990
Stock: 80
Estado producto: Nuevo
Entrega: Retiro en domicilio + Delivery propio
```

Estado:

```text
OK - Producto creado y visible en Mis productos. Estado publicacion Activo, revision Pendiente.
```

### 10. Ver producto creado desde el icono de ojo

- En `Mis productos`, presionar el icono de ojo `Ver`.
- Verificar que abra la ficha del producto.

Resultado observado:

```text
FALLA - El ojo abre /productos/producto-demo-marketplace-1780107936 y muestra 404 NO ENCONTRADO.
```

Observacion:

```text
El producto esta Activo pero con revision Pendiente. Publicamente no aparece hasta aprobarse.
Recomendacion: el ojo del vendedor deberia abrir la vista previa privada /mi-tienda/productos/{id}/vista-previa cuando el producto no esta aprobado.
```

## Flujo completo repetible

1. Elegir ambiente:
   - Test: `http://tiendatest.esremate.cl`
   - Produccion: `https://esremate.cl`
2. Abrir un producto desde la portada y validar ficha.
3. Buscar un producto:
   - Preguntar: `Que producto quiere buscar?`
   - Probar busqueda y revisar resultados.
4. Crear cuenta nueva:
   - Nombre de prueba.
   - Email de prueba.
   - Password de prueba.
5. Buscar un producto y agregarlo al carrito.
6. Buscar un segundo producto y agregarlo al carrito.
7. Entrar al carrito y validar:
   - Productos.
   - Cantidades.
   - Subtotal.
   - Total.
8. Presionar `Continuar compra`.
9. Completar telefono y mensaje.
10. Presionar `Enviar solicitud`.
11. Verificar pantalla final:
   - Numero de orden.
   - Alerta de seguridad.
   - Total.
   - Tienda.
   - WhatsApp de tienda.
12. Entrar a `Mi cuenta`.
13. Validar que la compra aparezca en `Mis compras`.
14. Ir a pestana `Vender`.
15. Presionar `Activar vendedor`.
16. Crear tienda con datos de prueba.
17. Entrar a `+ Agregar`.
18. Crear producto como vendedor.
19. Guardar y volver al listado.
20. Validar que el producto aparece en `Mis productos`.
21. Presionar el ojo `Ver`.
22. Si el producto esta pendiente de revision, validar comportamiento esperado:
   - Publico: puede dar 404.
   - Recomendado: deberia abrir vista previa privada.

## Pendientes detectados

```text
1. Busqueda "celular" no encuentra productos tipo telefono.
2. Icono ojo de vendedor abre URL publica y da 404 si el producto esta pendiente.
3. Producto nuevo queda en revision Pendiente; definir si admin debe aprobarlo antes de publicarse.
```

## Ejecucion completa 2026-05-30

Ambiente probado:

```text
http://tiendatest.esremate.cl
```

Datos creados:

```text
Usuario: Usuario Test Auto 1780108866
Email: prueba_auto_1780108866514@admin.cl
Password: 12345678
Orden: ORD-20260530-598536
Tienda: Tienda Test Auto 1780109225
Producto vendedor: Producto Test Marketplace 1780109265
SKU: TEST1780109265
```

Resultado:

```text
OK - Home carga correctamente.
OK - Ficha publica de Samsung Galaxy A54 abre correctamente.
OK - Busqueda "zapatilla" devuelve resultado y abre Zapatillas Nike Air Max 270.
OK - Registro de cuenta nueva y sesion iniciada.
OK - Carrito con 2 productos: Zapatillas Nike + Samsung Galaxy.
OK - Checkout crea solicitud ORD-20260530-598536 por $344.980.
OK - Pantalla final muestra alerta de seguridad de pagos/transferencias.
OK - Mi cuenta muestra la solicitud creada.
OK - Usuario activa vendedor.
OK - Usuario crea tienda y entra al panel vendedor.
OK - Usuario crea producto vendedor y vuelve al listado.
FALLA - Ojo "Ver" del listado vendedor no cambio de pagina para producto pendiente.
```

Observaciones:

```text
1. En el navegador automatizado hubo controles duplicados mobile/desktop; se resolvio usando el boton visible exacto.
2. El campo stock quedo en 80 durante la prueba, aunque se intento escribir 8. Revisar limpieza del input numerico si se quiere exactitud visual.
3. El ojo "Ver" debe revisarse: si el producto esta Pendiente, deberia abrir vista previa privada o mostrar mensaje claro.
```

## Ejecucion local 2026-05-30

Ambiente probado:

```text
http://127.0.0.1:8012
```

Datos creados:

```text
Usuario: Usuario Local Test 1780110211
Email: local_test_1780110211212@admin.cl
Password: 12345678
Orden: ORD-20260530-868746
Tienda: Tienda Local Test 1780110409
Producto vendedor: Producto Local Test 1780110455
SKU: LOCAL1780110455
```

Resultado:

```text
OK - Home local carga TiendaMV.
OK - Ficha publica de Samsung Galaxy abre correctamente.
OK - Busqueda "zapatilla" encuentra Zapatillas Nike Air Max 270.
OK - Registro de usuario nuevo local y sesion iniciada.
OK - Carrito limpio con 2 productos y total $344.980.
OK - Checkout crea solicitud ORD-20260530-868746.
OK - Pantalla final muestra alerta de seguridad.
OK - Mi cuenta muestra la solicitud creada.
OK - Usuario activa vendedor.
OK - Usuario crea tienda local.
OK - Usuario sube imagen manual antes de guardar.
OK - Usuario crea producto vendedor y vuelve al listado.
```

Observaciones:

```text
1. Al guardar producto local, el stock quedo en 80 aunque se intento escribir 8; revisar limpieza del input numerico.
2. Producto queda Activo con revision Pendiente, igual que en test publicado.
3. La URL publica del producto pendiente ahora muestra vista privada si el vendedor dueño esta logueado.
4. Una URL inexistente muestra pantalla "Error 404 no encontrado" con layout TiendaMV.
```
