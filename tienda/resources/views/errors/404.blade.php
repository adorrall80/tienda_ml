<x-layouts.shop title="Error 404 no encontrado — TiendaMV">
    <div class="page-header">
        <div class="container">
            <p class="breadcrumb">
                <a href="{{ route('inicio') }}">Inicio</a><span>›</span>
                Error 404
            </p>
        </div>
    </div>

    <div class="container">
        <section class="not-found-page">
            <div class="not-found-icon">404</div>
            <h1>Error 404 no encontrado</h1>
            <p>El producto o pagina que buscas no esta disponible.</p>
            <div class="not-found-actions">
                <a href="{{ route('inicio') }}" class="btn-buy-now">Ir al inicio</a>
                <a href="{{ route('productos.index') }}" class="btn-add-cart">Ver productos</a>
            </div>
        </section>
    </div>
</x-layouts.shop>
