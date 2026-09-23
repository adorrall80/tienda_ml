<x-layouts.shop title="Carrito — TiendaMV">

<div class="page-header">
    <div class="container">
        <p class="breadcrumb">
            <a href="{{ route('inicio') }}">Inicio</a><span>›</span>
            Carrito
        </p>
        <h1>Mi carrito</h1>
        <p class="results-count" data-cart-page-count>Revisando productos...</p>
    </div>
</div>

<div class="container cart-page" data-cart-page>
    <section class="cart-content">
        <div class="cart-empty" data-cart-empty hidden>
            <div class="cart-empty-icon">🛒</div>
            <h2>Tu carrito está vacío</h2>
            <p>Agrega productos para verlos aquí antes de comprar.</p>
            <a href="{{ route('productos.index') }}" class="cart-primary-link">Ver más productos</a>
        </div>

        <div class="cart-list" data-cart-list></div>
    </section>

    <aside class="cart-summary" data-cart-summary hidden>
        <h2>Resumen de compra</h2>
        <div class="cart-summary-row">
            <span>Productos</span>
            <strong data-cart-summary-count>0</strong>
        </div>
        <div class="cart-summary-row">
            <span>Subtotal</span>
            <strong data-cart-summary-subtotal>$0</strong>
        </div>
        <div class="cart-summary-row">
            <span>Envío</span>
            <strong class="cart-free">Gratis</strong>
        </div>
        <div class="cart-summary-total">
            <span>Total</span>
            <strong data-cart-summary-total>$0</strong>
        </div>
        <button class="cart-checkout-btn" type="button" data-cart-checkout>
            Continuar compra
        </button>
        <button class="cart-clear-btn" type="button" data-cart-clear>
            Vaciar carrito
        </button>
    </aside>
</div>

</x-layouts.shop>
