<x-layouts.shop title="TiendaMV — Compra y vende en Chile">

    <x-modules.hero-carousel />

    <x-modules.categorias-destacadas />

    <x-modules.productos-grid titulo="Ofertas del día" tag="oferta" :limite="8" />

    <x-modules.productos-scroll titulo="Más vendidos" tag="mas-vendido" :limite="7" />

    <x-modules.cta-banner />

</x-layouts.shop>
