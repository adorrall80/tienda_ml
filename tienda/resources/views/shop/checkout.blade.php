<x-layouts.shop title="Checkout - TiendaMV">

<div class="page-header">
    <div class="container">
        <p class="breadcrumb">
            <a href="{{ route('inicio') }}">Inicio</a><span>›</span>
            <a href="{{ route('carrito.index') }}">Carrito</a><span>›</span>
            Checkout
        </p>
        <h1>Finalizar compra</h1>
        <p class="results-count">Revisa tu solicitud. La coordinacion final se hara directo con cada tienda.</p>
    </div>
</div>

<div class="container checkout-page" data-checkout-page>
    <form id="checkout-form" class="checkout-form" method="POST" action="{{ route('checkout.store') }}">
        @csrf

        @if ($errors->any())
            <div class="checkout-alert" role="alert">
                {{ $errors->first('cart') ?: 'Revisa los datos del formulario antes de continuar.' }}
            </div>
        @endif

        <section class="checkout-card">
            <h2>Tus datos</h2>
            <div class="checkout-grid">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <div class="form-input checkout-readonly">{{ $user->name }}</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="form-input checkout-readonly">{{ $user->email }}</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cliente_telefono">Telefono o WhatsApp opcional</label>
                    <input class="form-input @error('cliente_telefono') form-input-error @enderror" id="cliente_telefono" name="cliente_telefono" value="{{ old('cliente_telefono') }}">
                    @error('cliente_telefono') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group checkout-wide">
                    <label class="form-label" for="notas">Mensaje opcional para la tienda</label>
                    <textarea class="form-input checkout-textarea @error('notas') form-input-error @enderror" id="notas" name="notas" rows="4">{{ old('notas') }}</textarea>
                    @error('notas') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <input type="hidden" name="cart_payload" value="{{ old('cart_payload') }}" data-checkout-payload>
    </form>

    <aside class="cart-summary checkout-summary">
        <h2>Resumen</h2>
        <div class="checkout-items" data-checkout-list></div>
        <div class="cart-summary-row">
            <span>Productos</span>
            <strong data-checkout-count>0</strong>
        </div>
        <div class="cart-summary-row">
            <span>Subtotal</span>
            <strong data-checkout-subtotal>$0</strong>
        </div>
        <div class="cart-summary-row">
            <span>Envio</span>
            <strong class="cart-free">Gratis</strong>
        </div>
        <div class="cart-summary-total">
            <span>Total</span>
            <strong data-checkout-total>$0</strong>
        </div>
        <button class="cart-checkout-btn" type="submit" form="checkout-form" data-checkout-submit>
            Enviar solicitud
        </button>
        <a class="cart-clear-btn checkout-back-link" href="{{ route('carrito.index') }}">Volver al carrito</a>
    </aside>
</div>

</x-layouts.shop>
