# Estructura de la Home Publica

Este documento explica como esta construida la pagina inicial de la tienda publica y donde se debe modificar cada parte para futuras mantenciones.

## 1. Flujo principal

La portada publica sigue este flujo:

```text
routes/web.php
    -> HomeController@index
    -> resources/views/shop/inicio.blade.php
    -> <x-layouts.shop>
    -> <x-modules.*>
    -> modelos y base de datos
```

## 2. Ruta inicial

La ruta principal esta definida en `routes/web.php`:

```php
Route::get('/', [HomeController::class, 'index'])->name('inicio');
```

Cuando el usuario entra a la raiz del sitio, Laravel ejecuta el metodo `index` de `HomeController`.

## 3. Controlador de la home

Archivo: `app/Http/Controllers/Shop/HomeController.php`

Actualmente contiene:

```php
public function index()
{
    return view('shop.inicio');
}
```

Este controlador solo carga la vista inicial. No prepara datos directamente.

En la estructura actual, los datos de la portada los obtienen los propios componentes Blade.

## 4. Vista principal de inicio

Archivo: `resources/views/shop/inicio.blade.php`

Este archivo no contiene todo el HTML final de la pagina. Lo que hace es declarar que layout se usara y que modulos se van a mostrar dentro de ese layout.

Laravel interpreta las etiquetas que empiezan con `<x-...>` como componentes Blade:

- `<x-layouts.shop>` envuelve toda la pagina con el layout publico de la tienda.
- Todo lo que esta dentro de `<x-layouts.shop> ... </x-layouts.shop>` se inserta en el `{{ $slot }}` del layout.
- `<x-modules.hero-carousel />`, `<x-modules.categorias-destacadas />` y los demas `<x-modules.*>` son bloques reutilizables de la home.
- Los atributos como `title`, `titulo`, `tag` y `:limite` son parametros que se entregan al componente.

En simple: esta vista arma la pagina inicial usando piezas pequenas. El layout pone la estructura general del sitio y los modulos ponen cada seccion visible.

```php
<x-layouts.shop title="TiendaMV - Compra y vende en Chile">


    <x-modules.categorias-destacadas />

    <x-modules.productos-grid titulo="Ofertas del dia" tag="oferta" :limite="8" />

    <x-modules.productos-scroll titulo="Mas vendidos" tag="mas-vendido" :limite="7" />

    <x-modules.cta-banner />

</x-layouts.shop>
```

Esta vista funciona como un organizador de secciones. Aqui se define que modulos aparecen, en que orden aparecen y que parametros recibe cada modulo.

## 5. Layout publico

Archivo: `resources/views/components/layouts/shop.blade.php`

Se llama desde la vista con:

```php
<x-layouts.shop>
```

Este layout define la estructura general de la tienda publica:

```text
<head>
    CSS y JS de tienda
</head>
<body>
    navegacion movil
    header
    menu de categorias
    contenido de la pagina
    footer
</body>
```

La linea clave es:

```php
{{ $slot }}
```

Ahi Laravel inserta el contenido de `inicio.blade.php`.

## 6. Componentes de la home

Cada modulo normalmente tiene dos partes:

```text
app/View/Components/Modules/NombreModulo.php
resources/views/components/modules/nombre-modulo.blade.php
```

La clase PHP prepara los datos. La vista Blade pinta el HTML.

## 7. Hero carousel

Uso en la home:

```php
<x-modules.hero-carousel />
```

Clase:

```text
app/View/Components/Modules/HeroCarousel.php
```

Vista:

```text
resources/views/components/modules/hero-carousel.blade.php
```

Obtiene banners activos desde el modelo `Banner`:

```php
$this->slides = Banner::activos()->get();
```

Sirve para mostrar el carrusel principal de ofertas o campanas.

## 8. Categorias destacadas

Uso en la home:

```php
<x-modules.categorias-destacadas />
```

Este componente sirve para insertar en ese punto de la home el bloque visual de categorias destacadas.

Cuando Laravel encuentra esta linea, no la muestra como texto. La interpreta como un componente Blade y hace este recorrido:

```text
<x-modules.categorias-destacadas />
    -> app/View/Components/Modules/CategoriasDestacadas.php
    -> resources/views/components/modules/categorias-destacadas.blade.php
```

La clase PHP busca las categorias en la base de datos. La vista Blade decide como se dibujan esas categorias en pantalla.

En simple, esta linea significa:

```text
Laravel, inserta aqui el bloque de categorias destacadas usando las categorias activas del sistema.
```

Clase:

```text
app/View/Components/Modules/CategoriasDestacadas.php
```

Vista:

```text
resources/views/components/modules/categorias-destacadas.blade.php
```

Obtiene categorias activas y raiz:

```php
$this->categorias = Category::activas()->raiz()->get();
```

Esto significa:

- `Category`: usa el modelo de categorias.
- `activas()`: trae solo categorias activas.
- `raiz()`: trae solo categorias principales, sin categoria padre.
- `get()`: ejecuta la consulta y devuelve la coleccion de categorias.

Sirve para mostrar accesos rapidos por categoria.

Para mantencion:

- Si se quiere cambiar que categorias aparecen, revisar `app/View/Components/Modules/CategoriasDestacadas.php` y el modelo `app/Models/Category.php`.
- Si se quiere cambiar como se ven, revisar `resources/views/components/modules/categorias-destacadas.blade.php` y `resources/css/shop.css`.

## 9. Productos en grid

Uso en la home:

```php
<x-modules.productos-grid titulo="Ofertas del dia" tag="oferta" :limite="8" />
```

Clase:

```text
app/View/Components/Modules/ProductosGrid.php
```

Vista:

```text
resources/views/components/modules/productos-grid.blade.php
```

Busca productos publicados:

```php
Product::publicados()
```

Si recibe un tag, filtra por ese tag:

```php
$query->conTag($this->tag);
```

En este caso muestra productos con tag `oferta`.

## 10. Productos en scroll

Uso en la home:

```php
<x-modules.productos-scroll titulo="Mas vendidos" tag="mas-vendido" :limite="7" />
```

Clase:

```text
app/View/Components/Modules/ProductosScroll.php
```

Vista:

```text
resources/views/components/modules/productos-scroll.blade.php
```

Funciona parecido al grid, pero visualmente muestra productos en scroll horizontal.

## 11. Tarjeta de producto

Archivo:

```text
resources/views/components/modules/product-card.blade.php
```

Este componente define como se ve cada producto. Incluye imagen, nombre, tienda, precio, precio oferta, estado del producto, envio gratis, rating, favoritos y badges como destacado, nuevo, hot u oferta.

Si se modifica este archivo, el cambio afecta todas las tarjetas de producto que usen este componente.

## 12. CTA banner

Uso en la home:

```php
<x-modules.cta-banner />
```

Vista:

```text
resources/views/components/modules/cta-banner.blade.php
```

Muestra una llamada a la accion. El contenido puede cambiar segun el usuario: visitante, cliente, vendedor o admin.

## 13. CSS de la tienda publica

Archivo principal:

```text
resources/css/shop.css
```

Este archivo controla el diseno de header, buscador, menu de categorias, home, carrusel, tarjetas de producto, categorias, carrito, checkout, ficha de producto y responsive movil.

El layout lo carga usando Vite:

```php
@vite(['resources/css/shop.css', 'resources/js/shop.js'])
```

## 14. JavaScript de tienda

Archivo:

```text
resources/js/shop.js
```

Aqui se mantienen interacciones frontend como carrusel, menu movil, buscador, carrito, favoritos e interacciones de producto.

## 15. Reglas de mantencion

### Cambiar orden de secciones

Modificar:

```text
resources/views/shop/inicio.blade.php
```

### Cambiar diseno visual

Modificar:

```text
resources/css/shop.css
```

### Cambiar logica de productos mostrados

Modificar:

```text
app/View/Components/Modules/ProductosGrid.php
app/View/Components/Modules/ProductosScroll.php
```

### Cambiar tarjeta de producto

Modificar:

```text
resources/views/components/modules/product-card.blade.php
```

### Cambiar layout general

Modificar:

```text
resources/views/components/layouts/shop.blade.php
```

### Agregar un nuevo bloque reutilizable

Crear:

```text
app/View/Components/Modules/NuevoModulo.php
resources/views/components/modules/nuevo-modulo.blade.php
```

Luego usarlo en la home:

```php
<x-modules.nuevo-modulo />
```

## 16. Recomendacion tecnica

La home esta bien separada por componentes. Para mantener el proyecto ordenado:

- No poner consultas complejas directamente en `inicio.blade.php`.
- Usar componentes para bloques reutilizables.
- Mantener el diseno en `shop.css`.
- Mantener la logica de datos en clases dentro de `app/View/Components/Modules`.
- Usar modelos y scopes como `Product::publicados()` para no repetir condiciones.

## 17. Mapa rapido de responsabilidades

```text
routes/web.php
    Define URLs.

Controllers
    Preparan datos cuando una pagina tiene logica propia.

resources/views/shop/*.blade.php
    Arman paginas completas.

resources/views/components/modules/*.blade.php
    Pintan bloques reutilizables.

app/View/Components/Modules/*.php
    Buscan o preparan datos de esos bloques.

resources/css/shop.css
    Diseno visual de la tienda publica.

resources/js/shop.js
    Interacciones frontend de la tienda publica.
```
