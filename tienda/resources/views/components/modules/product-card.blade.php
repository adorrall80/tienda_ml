@props(['producto'])
@php
    $desc    = $producto->porcentaje_descuento;
    $tags    = $producto->relationLoaded('tags') ? $producto->tags : collect();
    $isNuevo = $tags->contains('slug', 'nuevo');
    $isHot   = $tags->contains('slug', 'hot');
    $imagen  = $producto->imagen ?? 'https://picsum.photos/seed/' . $producto->slug . '/300/300';
    $precio  = '$' . number_format($producto->precio, 0, ',', '.');
    $orig    = $producto->precio_original
                 ? '$' . number_format($producto->precio_original, 0, ',', '.')
                 : null;
    $cuota   = ($producto->cuotas && $producto->cuotas > 1)
                 ? '$' . number_format($producto->precio / $producto->cuotas, 0, ',', '.')
                 : null;
    $stars   = str_repeat('★', (int) round($producto->rating))
             . str_repeat('☆', 5 - (int) round($producto->rating));
@endphp

<article class="product-card">
    <a href="{{ route('productos.show', $producto->slug) }}">
        <div class="product-img-wrap">
            <img src="{{ $imagen }}" alt="{{ $producto->nombre }}" loading="lazy">
            @if($isNuevo)
                <span class="product-badge new">NUEVO</span>
            @elseif($isHot)
                <span class="product-badge hot">HOT</span>
            @elseif($desc)
                <span class="product-badge">-{{ $desc }}%</span>
            @endif
        </div>
        <div class="product-info">
            <h3 class="product-title">{{ $producto->nombre }}</h3>
            @if($orig)
                <p class="product-original-price">{{ $orig }}</p>
            @endif
            <p class="product-price">{{ $precio }}</p>
            @if($desc)
                <p class="product-discount">{{ $desc }}% OFF</p>
            @endif
            @if($cuota)
                <p class="product-installments">en {{ $producto->cuotas }}x {{ $cuota }} sin interés</p>
            @endif
            @if($producto->envio_gratis)
                <p class="product-shipping">Envío gratis</p>
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
