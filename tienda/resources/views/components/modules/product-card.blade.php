@props(['producto'])
@php
    $desc    = $producto->porcentaje_descuento;
    $tags    = $producto->relationLoaded('tags') ? $producto->tags : collect();
    $isNuevo = $tags->contains('slug', 'nuevo');
    $isHot   = $tags->contains('slug', 'hot');
    $precioFinal = $producto->precio_final;
    $precio  = $precioFinal !== null && $precioFinal > 0
                 ? '$' . number_format($precioFinal, 0, ',', '.')
                 : 'Se regala';
    $orig    = $producto->precio_referencia
                 ? '$' . number_format($producto->precio_referencia, 0, ',', '.')
                 : null;
    $stars   = str_repeat('★', (int) round($producto->rating))
             . str_repeat('☆', 5 - (int) round($producto->rating));
    $deliveryOptions = $producto->delivery_type_labels;
@endphp

<article class="product-card">
    <a href="{{ route('productos.show', $producto->slug) }}">
        <div class="product-img-wrap">
            @if($producto->imagen)
                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" loading="lazy">
            @else
                <div class="no-img-placeholder">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    <span>Sin imagen</span>
                </div>
            @endif
            @if($producto->destacado)
                <span class="product-badge featured">DESTACADO</span>
            @elseif($isNuevo)
                <span class="product-badge new">NUEVO</span>
            @elseif($isHot)
                <span class="product-badge hot">HOT</span>
            @elseif($desc)
                <span class="product-badge">-{{ $desc }}%</span>
            @endif
        </div>
        <div class="product-info">
            @if($producto->estado_id)
                <span class="product-estado product-estado--{{ $producto->estado_slug }}">
                    Estado: {{ $producto->estado_label }}
                </span>
            @endif
            <h3 class="product-title">{{ $producto->nombre }}</h3>
            @if($producto->relationLoaded('tienda') && $producto->tienda)
                <p class="product-store">{{ $producto->tienda->nombre }}</p>
            @endif
            @if($orig)
                <p class="product-original-price">{{ $orig }}</p>
            @endif
            @if($precioFinal === null || $precioFinal <= 0)
                <p class="product-price"><span class="price-regalo">¡Se regala!</span></p>
            @else
                <p class="product-price">{{ $precio }}</p>
            @endif
            @if($desc)
                <p class="product-discount">{{ $desc }}% OFF</p>
            @endif
            @if($producto->envio_gratis)
                <p class="product-shipping">Envío gratis</p>
            @endif
            @if($deliveryOptions->isNotEmpty())
                <p class="product-delivery-options">{{ $deliveryOptions->take(2)->join(' · ') }}</p>
            @endif
            @if(isset($producto->favorites_count) && $producto->favorites_count > 0)
                <p class="product-favorites">{{ number_format($producto->favorites_count, 0, ',', '.') }} favoritos</p>
            @endif
            @if($producto->rating)
                <div class="product-rating">
                    <span class="stars">{{ $stars }}</span>
                    @if($producto->rating_count)
                        <span>({{ number_format($producto->rating_count, 0, ',', '.') }})</span>
                    @endif
                </div>
            @endif
        </div>
    </a>
</article>
