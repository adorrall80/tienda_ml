# Categorías

Guía para consultar las categorías durante la carga de productos.

- Consultar las opciones reales en el formulario autenticado de creación de productos.
- La copia local obtiene las categorías desde `Category::orderBy('orden')->get()` y las presenta en HTML; no se encontró un endpoint JSON de categorías.
- Mantener el identificador y el nombre exactos para enviar `category_id`.
- Buscar sin distinguir mayúsculas y acentos; sugerir coincidencias cercanas sin inventarlas.
- Si el usuario dice “calzados” y existe “Calzado”, explicar la coincidencia. Si hay varias opciones posibles, preguntar.
- No crear categorías nuevas como consecuencia automática de una coincidencia fallida.
- No usar esta tabla como catálogo vigente: se completa únicamente con resultados comprobados del sitio.

| Nombre | Categoría padre (si corresponde) | Descripción |
| --- | --- | --- |
| | | |
