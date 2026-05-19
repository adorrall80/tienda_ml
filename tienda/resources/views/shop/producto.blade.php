<x-layouts.shop :title="$producto->nombre . ' — TiendaMV'">

@php
    // Galería: imagen principal + imágenes adicionales de product_images (en orden)
    // La miniatura se obtiene reemplazando el tamaño de la URL almacenada
    $toThumb = fn(string $url) => preg_replace('/\/\d+\/\d+$/', '/100/100', $url);
    $toFull  = fn(string $url) => preg_replace('/\/\d+\/\d+$/', '/600/600', $url);

    $galeria = collect();

    if ($producto->imagen) {
        $galeria->push(['thumb' => $toThumb($producto->imagen), 'full' => $toFull($producto->imagen), 'alt' => $producto->nombre]);
    }
    foreach ($producto->images as $img) {
        $galeria->push(['thumb' => $toThumb($img->imagen), 'full' => $toFull($img->imagen), 'alt' => $producto->nombre]);
    }
    $galeria  = $galeria->values()->all();
    $desc     = $producto->porcentaje_descuento;
    $precio   = $producto->precio !== null && $producto->precio > 0
                  ? '$' . number_format($producto->precio, 0, ',', '.')
                  : 'Se regala';
    $orig     = $producto->precio_original ? '$' . number_format($producto->precio_original, 0, ',', '.') : null;
    $cuota    = ($producto->cuotas && $producto->cuotas > 1 && $producto->precio !== null && $producto->precio > 0)
                  ? '$' . number_format($producto->precio / $producto->cuotas, 0, ',', '.')
                  : null;
    $stars    = str_repeat('★', (int) round($producto->rating)) . str_repeat('☆', 5 - (int) round($producto->rating));
@endphp

{{-- Page Header --}}
<div class="page-header">
    <div class="container">
        <p class="breadcrumb">
            <a href="{{ route('inicio') }}">Inicio</a><span>›</span>
            @if($producto->category)
                <a href="{{ route('productos.index', ['cat' => $producto->category->slug]) }}">{{ $producto->category->nombre }}</a><span>›</span>
            @endif
            {{ \Illuminate\Support\Str::limit($producto->nombre, 50) }}
        </p>
    </div>
</div>

<div class="container">
    <div class="product-detail-layout">

        {{-- ===== COLUMNA IZQUIERDA ===== --}}
        <div>
            <div class="product-main-col">

                {{-- Galería --}}
                <div class="gallery">
                    @if(count($galeria) > 0)
                        <div class="thumbnails">
                            @foreach($galeria as $i => $img)
                                <div class="thumbnail {{ $i === 0 ? 'active' : '' }}" data-full="{{ $img['full'] }}">
                                    <img src="{{ $img['thumb'] }}" alt="{{ $img['alt'] }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                                </div>
                            @endforeach
                        </div>
                        <div class="main-image-wrap" title="Haz clic para zoom">
                            <img class="gallery-main"
                                 src="{{ $galeria[0]['full'] }}"
                                 alt="{{ $producto->nombre }}"
                                 id="main-product-img">
                        </div>
                    @else
                        <div class="no-img-placeholder-lg">
                            <svg width="80" height="80" fill="none" stroke="currentColor" stroke-width="0.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            <span>Sin imagen</span>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <p class="detail-condition">
                    @if($producto->estado)
                        <span class="detail-estado detail-estado--{{ $producto->estado }}">
                            Estado: {{ \App\Models\Product::ESTADOS[$producto->estado] }}
                        </span>
                        |
                    @endif
                    <a href="#">Reportar</a>
                </p>

                <h1 class="detail-title">{{ $producto->nombre }}</h1>

                <div class="detail-rating">
                    <span class="stars">{{ $stars }}</span>
                    <span>{{ number_format($producto->rating, 1) }}</span>
                    <a href="#tab-reviews">({{ number_format($producto->rating_count, 0, ',', '.') }} reseñas)</a>
                    <span style="margin-left:8px;color:var(--green)">&#10003; Vendedor oficial</span>
                </div>

                @if($orig)
                    <p class="detail-original">{{ $orig }}</p>
                @endif
                <p class="detail-price">
                    @if($producto->precio === null || $producto->precio <= 0)
                        <span class="price-regalo">¡Se regala!</span>
                    @else
                        {{ $precio }}
                        @if($desc)
                            <span class="detail-discount-badge">{{ $desc }}% OFF</span>
                        @endif
                    @endif
                </p>
                @if($cuota)
                    <p class="detail-installments">
                        en {{ $producto->cuotas }}x {{ $cuota }}
                        <strong style="color:var(--green)">sin interés</strong>
                    </p>
                @endif

                @if($producto->envio_gratis)
                    <div>
                        <span class="detail-shipping-badge">&#128666; Envío gratis</span>
                        <span style="font-size:13px;color:var(--text-muted);margin-left:6px;">Llega mañana</span>
                    </div>
                @endif

                {{-- Cantidad --}}
                <div class="quantity-section">
                    <label>
                        Cantidad:
                        <small>({{ $producto->stock }} disponibles)</small>
                    </label>
                    <div class="quantity-control product-actions">
                        <button class="qty-btn dec" aria-label="Disminuir">&#8722;</button>
                        <input class="qty-input" type="number" value="1"
                               min="1" max="{{ $producto->stock }}"
                               data-max="{{ $producto->stock }}"
                               data-min="1"
                               aria-label="Cantidad">
                        <button class="qty-btn inc" aria-label="Aumentar">&#43;</button>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="buy-buttons product-actions">
                    <button class="btn-buy-now"
                            data-add-cart
                            data-cart-redirect="true"
                            data-id="{{ $producto->id }}"
                            data-title="{{ addslashes(substr($producto->nombre, 0, 60)) }}"
                            data-price="{{ $producto->precio }}"
                            data-img="{{ $producto->imagen }}"
                            data-slug="{{ $producto->slug }}"
                            data-cart-url="{{ route('carrito.index') }}">
                        Comprar ahora
                    </button>
                    <button class="btn-add-cart"
                            data-add-cart
                            data-id="{{ $producto->id }}"
                            data-title="{{ addslashes(substr($producto->nombre, 0, 60)) }}"
                            data-price="{{ $producto->precio }}"
                            data-img="{{ $producto->imagen }}"
                            data-slug="{{ $producto->slug }}">
                        Agregar al carrito
                    </button>
                </div>

                {{-- Mobile: vendedor y pagos --}}
                <div class="mobile-seller-payment">
                    <div class="mobile-seller-line">
                        <div class="seller-avatar" style="width:36px;height:36px;font-size:14px;flex-shrink:0;">
                            {{ strtoupper(substr($producto->nombre, 0, 1)) }}
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:600;color:var(--blue);margin-bottom:2px;">Vendedor Oficial</p>
                            <p style="font-size:11px;color:var(--green);">&#11088; Platinum &middot; 99% ventas exitosas</p>
                        </div>
                    </div>
                    <div class="mobile-payment-badges">
                        @if($cuota)<span>&#128179; {{ $producto->cuotas }} cuotas sin interés</span>@endif
                        <span>&#128274; Compra protegida</span>
                        <span>&#8617; Devolución 30 días</span>
                        @if($producto->envio_gratis)<span>&#128666; Envío gratis</span>@endif
                    </div>
                </div>

                {{-- Lo que incluye --}}
                <div style="margin-top:20px;padding:16px;background:#f8f8f8;border-radius:8px;">
                    <h4 style="font-size:14px;font-weight:600;margin-bottom:12px;">Lo que incluye</h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;color:var(--text-light);">
                        <span>&#10003; Garantía oficial 1 año</span>
                        <span>&#10003; Devolución gratis 30 días</span>
                        <span>&#10003; Compra protegida</span>
                        <span>&#10003; Factura electrónica</span>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="tabs">
                    <div class="tab-list" role="tablist">
                        <button class="tab-btn active" role="tab" data-tab="desc" aria-selected="true">Descripción</button>
                        <button class="tab-btn" role="tab" data-tab="specs" aria-selected="false">Especificaciones</button>
                        <button class="tab-btn" role="tab" data-tab="reviews" aria-selected="false">
                            Reseñas ({{ number_format($producto->rating_count, 0, ',', '.') }})
                        </button>
                        <button class="tab-btn" role="tab" data-tab="shipping" aria-selected="false">Envío y devolución</button>
                    </div>

                    {{-- Descripción --}}
                    <div class="tab-content active" id="tab-desc" role="tabpanel">
                        <div class="description-text">
                            @if($producto->descripcion)
                                <p>{{ $producto->descripcion }}</p>
                            @endif
                            <h4>Características principales</h4>
                            <ul>
                                <li>Producto 100% original y nuevo</li>
                                <li>Garantía oficial de fábrica</li>
                                @if($producto->envio_gratis)<li>Envío gratis a todo Chile</li>@endif
                                @if($cuota)<li>{{ $producto->cuotas }} cuotas sin interés disponibles</li>@endif
                            </ul>
                        </div>
                    </div>

                    {{-- Specs --}}
                    <div class="tab-content" id="tab-specs" role="tabpanel">
                        <table class="specs-table">
                            <tbody>
                                <tr><td>Nombre</td><td>{{ $producto->nombre }}</td></tr>
                                <tr><td>Categoría</td><td>{{ $producto->category?->nombre ?? '—' }}</td></tr>
                                <tr><td>Stock disponible</td><td>{{ $producto->stock }} unidades</td></tr>
                                @if($producto->precio_original)
                                <tr><td>Precio original</td><td>{{ $orig }}</td></tr>
                                @endif
                                <tr><td>Precio</td><td>{{ $precio }}</td></tr>
                                @if($cuota)
                                <tr><td>Cuotas</td><td>{{ $producto->cuotas }}x {{ $cuota }} sin interés</td></tr>
                                @endif
                                <tr><td>Envío</td><td>{{ $producto->envio_gratis ? 'Envío gratis' : 'Con costo de envío' }}</td></tr>
                                <tr><td>Calificación</td><td>{{ $stars }} {{ number_format($producto->rating, 1) }}/5</td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Reviews --}}
                    <div class="tab-content" id="tab-reviews" role="tabpanel">
                        <div class="review-summary">
                            <div>
                                <div class="big-rating">{{ number_format($producto->rating, 1) }}</div>
                                <div class="stars" style="font-size:20px;">{{ $stars }}</div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">
                                    {{ number_format($producto->rating_count, 0, ',', '.') }} reseñas
                                </div>
                            </div>
                            <div class="rating-breakdown">
                                @php
                                    $pct5 = (int) round($producto->rating * 20);
                                    $pct4 = max(0, 100 - $pct5 - 4);
                                    $pct3 = max(0, 100 - $pct5 - $pct4 - 3);
                                @endphp
                                <div class="rating-bar-row"><span>5&#9733;</span><div class="rating-bar-outer"><div class="rating-bar-inner" style="width:{{ $pct5 }}%"></div></div><span>{{ $pct5 }}%</span></div>
                                <div class="rating-bar-row"><span>4&#9733;</span><div class="rating-bar-outer"><div class="rating-bar-inner" style="width:{{ $pct4 }}%"></div></div><span>{{ $pct4 }}%</span></div>
                                <div class="rating-bar-row"><span>3&#9733;</span><div class="rating-bar-outer"><div class="rating-bar-inner" style="width:{{ $pct3 }}%"></div></div><span>{{ $pct3 }}%</span></div>
                                <div class="rating-bar-row"><span>2&#9733;</span><div class="rating-bar-outer"><div class="rating-bar-inner" style="width:2%"></div></div><span>2%</span></div>
                                <div class="rating-bar-row"><span>1&#9733;</span><div class="rating-bar-outer"><div class="rating-bar-inner" style="width:1%"></div></div><span>1%</span></div>
                            </div>
                        </div>
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer-name">C***a R. <span class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span></span>
                                <span class="review-date">15 de enero de 2025</span>
                            </div>
                            <p class="review-body">Excelente producto. Llegó en perfectas condiciones, bien embalado y antes del plazo prometido. Totalmente recomendado.</p>
                        </div>
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer-name">M***o T. <span class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span></span>
                                <span class="review-date">3 de enero de 2025</span>
                            </div>
                            <p class="review-body">Muy buena calidad-precio. El vendedor respondió rápido y el envío fue gratis. Compraría de nuevo sin dudarlo.</p>
                        </div>
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer-name">V***a L. <span class="stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span></span>
                                <span class="review-date">28 de diciembre de 2024</span>
                            </div>
                            <p class="review-body">Buen producto en general. Cumple con lo prometido. Le quito una estrella por el embalaje, podría ser mejor.</p>
                        </div>
                    </div>

                    {{-- Envío --}}
                    <div class="tab-content" id="tab-shipping" role="tabpanel">
                        <div class="description-text">
                            <h4>{{ $producto->envio_gratis ? 'Envío gratis a todo Chile' : 'Envío a todo Chile' }}</h4>
                            <p>El tiempo estimado de entrega depende de tu ubicación:</p>
                            <ul>
                                <li><strong>Región Metropolitana:</strong> 1–2 días hábiles</li>
                                <li><strong>Ciudades principales (V, VIII, IX):</strong> 2–3 días hábiles</li>
                                <li><strong>Regiones extremas (I, XI, XII, XV):</strong> 4–7 días hábiles</li>
                            </ul>
                            <h4>Política de devoluciones</h4>
                            <p>Tienes 30 días para devolver el producto si no estás satisfecho. El producto debe estar en las mismas condiciones en que fue recibido.</p>
                            <h4>Garantía</h4>
                            <p>Este producto incluye garantía oficial de 12 meses. En caso de defecto de fábrica, el vendedor se hará cargo sin costo.</p>
                        </div>
                    </div>
                </div>

            </div>{{-- end .product-main-col --}}

            {{-- Productos relacionados --}}
            @if($relacionados->isNotEmpty())
            <div class="section">
                <h2 class="section-title">Productos relacionados</h2>
                <div class="scroll-section">
                    @foreach($relacionados as $rel)
                        <x-modules.product-card :producto="$rel" />
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- end left col --}}

        {{-- ===== COLUMNA DERECHA ===== --}}
        <div class="product-side-col">

            {{-- Buy Box --}}
            <div class="buy-box">
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">Precio</p>
                @if($orig)
                    <p class="detail-original" style="font-size:13px;">{{ $orig }}</p>
                @endif
                <p class="detail-price" style="font-size:28px;">
                    {{ $precio }}
                    @if($desc)<span class="detail-discount-badge" style="font-size:12px;">{{ $desc }}% OFF</span>@endif
                </p>
                @if($cuota)
                    <p class="detail-installments" style="font-size:13px;">
                        en {{ $producto->cuotas }}x {{ $cuota }}
                        <strong style="color:var(--green)">sin interés</strong>
                    </p>
                @endif

                @if($producto->envio_gratis)
                    <span class="detail-shipping-badge" style="font-size:12px;margin:12px 0;display:inline-flex;">
                        &#128666; Envío gratis — llega mañana
                    </span>
                @endif

                <div style="font-size:13px;color:var(--text-light);margin:8px 0 14px;">
                    <div>&#128230; Stock: <strong>{{ $producto->stock }} unidades</strong></div>
                    <div style="margin-top:6px;">&#127978; Vendido por <a href="#">TiendaMV Oficial</a></div>
                </div>

                <div class="quantity-section product-actions">
                    <label style="font-size:13px;">Cantidad:</label>
                    <div class="quantity-control">
                        <button class="qty-btn dec">&#8722;</button>
                        <input class="qty-input" type="number" value="1"
                               min="1" max="{{ $producto->stock }}"
                               data-max="{{ $producto->stock }}" data-min="1">
                        <button class="qty-btn inc">&#43;</button>
                    </div>
                </div>

                <div class="buy-buttons product-actions" style="margin-top:14px;">
                    <button class="btn-buy-now"
                            data-add-cart
                            data-cart-redirect="true"
                            data-id="{{ $producto->id }}"
                            data-title="{{ addslashes(substr($producto->nombre, 0, 60)) }}"
                            data-price="{{ $producto->precio }}"
                            data-img="{{ $producto->imagen }}"
                            data-slug="{{ $producto->slug }}"
                            data-cart-url="{{ route('carrito.index') }}">
                        Comprar ahora
                    </button>
                    <button class="btn-add-cart"
                            data-add-cart
                            data-id="{{ $producto->id }}"
                            data-title="{{ addslashes(substr($producto->nombre, 0, 60)) }}"
                            data-price="{{ $producto->precio }}"
                            data-img="{{ $producto->imagen }}"
                            data-slug="{{ $producto->slug }}">
                        Agregar al carrito
                    </button>
                </div>

                <div style="margin-top:14px;font-size:12px;color:var(--text-muted);text-align:center;">
                    &#128274; Compra 100% protegida
                </div>
            </div>

            {{-- Seller Card --}}
            <div class="seller-card">
                <h3>Información del vendedor</h3>
                <div class="seller-info">
                    <div class="seller-avatar">{{ strtoupper(substr($producto->nombre, 0, 1)) }}</div>
                    <div>
                        <p class="seller-name">TiendaMV Oficial</p>
                        <p class="seller-level">&#11088; Vendedor Platinum</p>
                    </div>
                </div>
                <div class="seller-stats">
                    <div class="stat-item"><div class="stat-value">99.4%</div><div class="stat-label">Ventas exitosas</div></div>
                    <div class="stat-item"><div class="stat-value">+50k</div><div class="stat-label">Ventas totales</div></div>
                    <div class="stat-item"><div class="stat-value">&lt;1h</div><div class="stat-label">Respuesta</div></div>
                    <div class="stat-item"><div class="stat-value">4.9 &#9733;</div><div class="stat-label">Calificación</div></div>
                </div>
                <a href="{{ route('productos.index', ['cat' => $producto->category?->slug]) }}" class="visit-store-btn">
                    Ver más productos
                </a>
            </div>

            {{-- Medios de pago --}}
            <div style="background:var(--white);border-radius:var(--radius-lg);padding:16px;box-shadow:var(--shadow);">
                <h3 style="font-size:13px;font-weight:600;margin-bottom:12px;">Medios de pago</h3>
                <div style="font-size:13px;color:var(--text-light);display:flex;flex-direction:column;gap:8px;">
                    <div>&#128179; Tarjetas de crédito hasta 24 cuotas</div>
                    <div>&#127970; Transferencia bancaria</div>
                    <div>&#128178; Webpay / Redcompra</div>
                    <div>&#128241; Mercado Pago / Khipu</div>
                </div>
            </div>

        </div>{{-- end right col --}}
    </div>{{-- end .product-detail-layout --}}
</div>{{-- end .container --}}

{{-- Sticky Buy Bar (móvil) --}}
<div class="sticky-buy-bar">
    <div class="sticky-price-col">
        @if($orig)<span class="sticky-original">{{ $orig }}</span>@endif
        <span class="sticky-price">{{ $precio }}</span>
    </div>
    <button class="sticky-buy-btn"
            data-add-cart
            data-cart-redirect="true"
            data-id="{{ $producto->id }}"
            data-title="{{ addslashes(substr($producto->nombre, 0, 60)) }}"
            data-price="{{ $producto->precio }}"
            data-img="{{ $producto->imagen }}"
            data-slug="{{ $producto->slug }}"
            data-cart-url="{{ route('carrito.index') }}">
        Comprar ahora
    </button>
</div>

</x-layouts.shop>
