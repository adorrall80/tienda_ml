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
    $precioFinal = $producto->precio_final;
    $precio   = $precioFinal !== null && $precioFinal > 0
                  ? '$' . number_format($precioFinal, 0, ',', '.')
                  : 'Se regala';
    $orig     = $producto->precio_referencia ? '$' . number_format($producto->precio_referencia, 0, ',', '.') : null;
    $stars    = str_repeat('★', (int) round($producto->rating)) . str_repeat('☆', 5 - (int) round($producto->rating));
    $tiendaNombre = $producto->tienda?->nombre ?? 'Vendedor';
    $tiendaDescripcion = $producto->tienda?->descripcion;
    $tiendaInicial = strtoupper(substr($tiendaNombre, 0, 1));
    $deliveryOptions = $producto->delivery_type_labels;
    $costoEnvioTexto = $producto->envio_gratis
        ? 'Gratis'
        : ($producto->costo_envio !== null ? '$' . number_format($producto->costo_envio, 0, ',', '.') : 'A coordinar');
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
    @if(! empty($privatePreviewReason))
        <div class="private-preview-alert">
            <strong>Vista privada del vendedor</strong>
            <span>{{ $privatePreviewReason }}</span>
        </div>
    @endif

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

                {{-- Report Button --}}
                <div style="background-color: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 20px; text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    @auth
                        <a href="javascript:void(0);" onclick="document.getElementById('report-modal').showModal();" style="display: inline-flex; align-items: center; gap: 8px; color: #4b5563; font-size: 0.95rem; font-weight: 500; text-decoration: none;">
                            <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                            Denunciar esta publicación
                        </a>
                    @else
                        <a href="javascript:void(0);" onclick="requireLogin('{{ route('login.intended', ['to' => url()->current()]) }}')" style="display: inline-flex; align-items: center; gap: 8px; color: #4b5563; font-size: 0.95rem; font-weight: 500; text-decoration: none;">
                            <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                            Denunciar esta publicación
                        </a>
                    @endauth
                </div>

                {{-- Info --}}
                <p class="detail-condition">
                    @if($producto->estado_id)
                        <span class="detail-estado detail-estado--{{ $producto->estado_slug }}">
                            Estado: {{ $producto->estado_label }}
                        </span>
                        |
                    @endif
                    @if($producto->fecha_publicacion)
                        Publicado: {{ $producto->fecha_publicacion->format('d/m/Y') }} |
                    @endif
                    {{ number_format($producto->visitas, 0, ',', '.') }} visitas |
                    {{ number_format($producto->favorites_count, 0, ',', '.') }} favoritos
                </p>

                @auth
                <dialog id="report-modal" style="padding: 24px; border: none; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; font-size: 1.25rem;">Reportar Producto</h2>
                        <button onclick="document.getElementById('report-modal').close();" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">&times;</button>
                    </div>
                    <form id="report-form" method="POST" action="{{ route('productos.reportar', $producto) }}">
                        @csrf
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Motivo</label>
                            <select name="report_reason_id" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                                <option value="">Selecciona un motivo...</option>
                                @foreach(\App\Models\ReportReason::activos()->get() as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Comentarios (opcional)</label>
                            <textarea name="comentarios" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;" placeholder="Danos más detalles..."></textarea>
                        </div>
                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                            <button type="button" onclick="document.getElementById('report-modal').close();" style="padding: 8px 16px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Cancelar</button>
                            <button type="submit" style="padding: 8px 16px; border: none; background: #e02424; color: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Enviar Reporte</button>
                        </div>
                    </form>
                </dialog>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('report-form');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const url = form.getAttribute('action');
                                const formData = new FormData(form);
                                
                                fetch(url, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('report-modal').close();
                                    if (typeof window.showToast === 'function') {
                                        window.showToast(data.message || 'Reporte enviado', 'success');
                                    } else {
                                        alert(data.message || 'Reporte enviado');
                                    }
                                    form.reset();
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    if (typeof window.showToast === 'function') {
                                        window.showToast('Error al enviar reporte', 'error');
                                    }
                                });
                            });
                        }
                    });
                </script>
                @endauth

                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 15px;">
                    <h1 class="detail-title" style="margin: 0;">{{ $producto->nombre }}</h1>
                    
                    <div>
                        @auth
                            <form id="favorite-form" method="POST" action="{{ route('productos.favorito', $producto) }}">
                                @csrf
                                <button id="favorite-btn" type="submit" class="btn-favorite {{ $isFavorited ? 'active' : '' }}" style="background: none; border: none; cursor: pointer; padding: 5px; color: {{ $isFavorited ? '#e02424' : '#9ca3af' }};" title="{{ $isFavorited ? 'Quitar de favoritos' : 'Guardar favorito' }}">
                                    <svg id="favorite-icon" width="28" height="28" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </form>
                        @else
                            <a href="javascript:void(0);" onclick="requireLogin('{{ route('login.intended', ['to' => url()->current()]) }}')" style="color: #9ca3af; text-decoration: none; display: inline-block; padding: 5px;" title="Ingresa para guardar favorito">
                                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </a>
                        @endauth
                    </div>
                </div>

                <div id="favorite-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.8); color: white; padding: 20px 40px; border-radius: 8px; z-index: 10000; font-size: 18px; font-weight: bold; text-align: center; pointer-events: none; opacity: 0; transition: opacity 0.2s ease;">
                    Producto guardado en favoritos
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('favorite-form');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const url = form.getAttribute('action');
                                const formData = new FormData(form);
                                
                                fetch(url, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    const btn = document.getElementById('favorite-btn');
                                    const icon = document.getElementById('favorite-icon');
                                    const modal = document.getElementById('favorite-modal');
                                    
                                    if (data.status === 'added') {
                                        btn.style.color = '#e02424';
                                        icon.setAttribute('fill', 'currentColor');
                                        modal.innerText = 'Producto guardado en favoritos';
                                    } else {
                                        btn.style.color = '#9ca3af';
                                        icon.setAttribute('fill', 'none');
                                        modal.innerText = 'Producto quitado de favoritos';
                                    }
                                    
                                    modal.style.display = 'block';
                                    // Trigger reflow for transition
                                    void modal.offsetWidth;
                                    modal.style.opacity = '1';
                                    
                                    setTimeout(() => {
                                        modal.style.opacity = '0';
                                        setTimeout(() => {
                                            modal.style.display = 'none';
                                        }, 200);
                                    }, 1000);
                                })
                                .catch(error => console.error('Error:', error));
                            });
                        }
                    });
                </script>

                <div class="detail-rating">
                    <span class="stars">{{ $stars }}</span>
                    <span>{{ number_format($producto->rating, 1) }}</span>
                    <a href="#tab-reviews">({{ number_format($producto->rating_count, 0, ',', '.') }} reseñas)</a>
                    <span class="detail-store-chip">&#10003; {{ $tiendaNombre }}</span>
                </div>

                @if($orig)
                    <p class="detail-original">{{ $orig }}</p>
                @endif
                <p class="detail-price">
                    @if($precioFinal === null || $precioFinal <= 0)
                        <span class="price-regalo">¡Se regala!</span>
                    @else
                        {{ $precio }}
                        @if($desc)
                            <span class="detail-discount-badge">{{ $desc }}% OFF</span>
                        @endif
                    @endif
                </p>
                @if($producto->envio_gratis)
                    <div>
                        <span class="detail-shipping-badge">&#128666; Envío gratis</span>
                        <span class="detail-muted-inline">Coordina entrega con la tienda</span>
                    </div>
                @endif
                @if($deliveryOptions->isNotEmpty() || $producto->tiempo_entrega || $producto->costo_envio !== null)
                    <div class="delivery-summary">
                        @foreach($deliveryOptions as $option)
                            <span>{{ $option }}</span>
                        @endforeach
                        <span>Costo: {{ $costoEnvioTexto }}</span>
                        @if($producto->tiempo_entrega)
                            <span>Tiempo: {{ $producto->tiempo_entrega }}</span>
                        @endif
                    </div>
                @endif

                {{-- Cantidad y Botones --}}
                @if($producto->stock > 0)
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

                    <div class="buy-buttons product-actions">
                        <button class="btn-buy-now"
                                data-add-cart
                                data-cart-redirect="true"
                                data-id="{{ $producto->id }}"
                                data-title="{{ \Illuminate\Support\Str::limit($producto->nombre, 60, '') }}"
                                data-price="{{ $precioFinal }}"
                                data-stock="{{ $producto->stock }}"
                                data-img="{{ $producto->imagen }}"
                                data-slug="{{ $producto->slug }}"
                                data-cart-url="{{ route('carrito.index') }}">
                            Solicitar compra
                        </button>
                        <button class="btn-add-cart"
                                data-add-cart
                                data-id="{{ $producto->id }}"
                                data-title="{{ \Illuminate\Support\Str::limit($producto->nombre, 60, '') }}"
                                data-price="{{ $precioFinal }}"
                                data-stock="{{ $producto->stock }}"
                                data-img="{{ $producto->imagen }}"
                                data-slug="{{ $producto->slug }}">
                            Agregar al carrito
                        </button>
                    </div>
                @else
                    <div style="padding: 12px 16px; background-color: #fee2e2; border: 1px solid #f87171; border-radius: 8px; color: #991b1b; font-weight: 600; margin: 16px 0; text-align: center;">
                        🚫 Producto agotado por el momento
                    </div>
                    <div class="buy-buttons product-actions">
                        <button class="btn-buy-now" disabled style="opacity: 0.5; cursor: not-allowed; background-color: #9ca3af;">
                            Agotado
                        </button>
                    </div>
                @endif


                {{-- Mobile: vendedor y coordinacion --}}
                <div class="mobile-seller-payment">
                    <div class="mobile-seller-line">
                        <div class="seller-avatar" style="width:36px;height:36px;font-size:14px;flex-shrink:0;">
                            {{ $tiendaInicial }}
                        </div>
                        <div>
                            <p class="mobile-seller-name">{{ $tiendaNombre }}</p>
                            <p class="mobile-seller-level">&#11088; Tienda verificada</p>
                        </div>
                    </div>
                    <div class="mobile-payment-badges">
                        <span>&#128222; Contacto directo con la tienda</span>
                        <span>&#128221; Solicitud registrada en tu cuenta</span>
                        @if($producto->envio_gratis)<span>&#128666; Envío gratis</span>@endif
                    </div>
                </div>

                {{-- Flujo sin pago --}}
                <div class="detail-flow-box">
                    <h4>Cómo sigue la solicitud</h4>
                    <div>
                        <span>&#10003; Agregas el producto al carrito</span>
                        <span>&#10003; Confirmas la solicitud logueado</span>
                        <span>&#10003; La tienda recibe tus datos</span>
                        <span>&#10003; Coordinan entrega y pago por contacto</span>
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
                                <div class="product-html-description">{!! $producto->descripcion !!}</div>
                            @endif
                            @if($producto->productAttributes->isNotEmpty())
                                <div class="description-attributes">
                                    <h4>Atributos del producto</h4>
                                    <dl>
                                        @foreach($producto->productAttributes as $attribute)
                                            <div>
                                                <dt>{{ $attribute->nombre }}</dt>
                                                <dd>{{ $attribute->valor }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Specs --}}
                    <div class="tab-content" id="tab-specs" role="tabpanel">
                        <table class="specs-table">
                            <tbody>
                                <tr><td>Nombre</td><td>{{ $producto->nombre }}</td></tr>
                                @if($producto->sku)
                                <tr><td>Código producto</td><td>{{ $producto->sku }}</td></tr>
                                @endif
                                <tr><td>Categoría</td><td>{{ $producto->category?->nombre ?? '—' }}</td></tr>
                                <tr><td>Stock disponible</td><td>{{ $producto->stock }} unidades</td></tr>
                                @if($producto->fecha_publicacion)
                                <tr><td>Publicado</td><td>{{ $producto->fecha_publicacion->format('d/m/Y') }}</td></tr>
                                @endif
                                <tr><td>Visitas</td><td>{{ number_format($producto->visitas, 0, ',', '.') }}</td></tr>
                                <tr><td>Favoritos</td><td>{{ number_format($producto->favorites_count, 0, ',', '.') }}</td></tr>
                                @if($orig)
                                <tr><td>Precio normal</td><td>{{ $orig }}</td></tr>
                                @endif
                                <tr><td>Precio</td><td>{{ $precio }}</td></tr>
                                <tr><td>Entrega</td><td>{{ $deliveryOptions->isNotEmpty() ? $deliveryOptions->join(', ') : 'A coordinar con la tienda' }}</td></tr>
                                <tr><td>Costo envío</td><td>{{ $costoEnvioTexto }}</td></tr>
                                @if($producto->tiempo_entrega)
                                <tr><td>Tiempo entrega</td><td>{{ $producto->tiempo_entrega }}</td></tr>
                                @endif
                                @foreach($producto->productAttributes as $attribute)
                                <tr><td>{{ $attribute->nombre }}</td><td>{{ $attribute->valor }}</td></tr>
                                @endforeach
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
                            <h4>Opciones de entrega</h4>
                            @if($deliveryOptions->isNotEmpty())
                                <ul>
                                    @foreach($deliveryOptions as $option)
                                        <li>{{ $option }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>La entrega se coordina directamente con la tienda.</p>
                            @endif
                            <h4>Costo y tiempo</h4>
                            <p><strong>Costo estimado:</strong> {{ $costoEnvioTexto }}</p>
                            <p><strong>Tiempo estimado:</strong> {{ $producto->tiempo_entrega ?: 'A coordinar con la tienda' }}</p>
                            <p>Esta información es referencial. La entrega y el pago final se acuerdan por contacto directo con la tienda.</p>
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
                <p class="buy-box-label">Precio referencial</p>
                @if($orig)
                    <p class="detail-original buy-box-original">{{ $orig }}</p>
                @endif
                <p class="detail-price buy-box-price">
                    {{ $precio }}
                    @if($desc)<span class="detail-discount-badge detail-discount-badge-sm">{{ $desc }}% OFF</span>@endif
                </p>
                @if($producto->envio_gratis)
                    <span class="detail-shipping-badge buy-box-shipping">&#128666; Envío gratis</span>
                @endif

                <div class="buy-box-meta">
                    <div>&#128230; Stock: <strong style="{{ $producto->stock <= 0 ? 'color: #dc2626;' : '' }}">{{ $producto->stock > 0 ? $producto->stock . ' unidades' : 'Agotado (0 unidades)' }}</strong></div>
                    <div>&#127978; Tienda: <strong>{{ $tiendaNombre }}</strong></div>
                    <div>
                        &#128666; Entrega:
                        <strong>{{ $deliveryOptions->isNotEmpty() ? $deliveryOptions->join(', ') : 'A coordinar' }}</strong>
                    </div>
                    @if($producto->tiempo_entrega)
                        <div>&#9201; Tiempo: <strong>{{ $producto->tiempo_entrega }}</strong></div>
                    @endif
                    <div>&#128222; El cierre se coordina por contacto con la tienda</div>
                </div>

                @if($producto->stock > 0)
                    <div class="quantity-section product-actions">
                        <label class="buy-box-quantity-label">Cantidad:</label>
                        <div class="quantity-control">
                            <button class="qty-btn dec">&#8722;</button>
                            <input class="qty-input" type="number" value="1"
                                   min="1" max="{{ $producto->stock }}"
                                   data-max="{{ $producto->stock }}" data-min="1">
                            <button class="qty-btn inc">&#43;</button>
                        </div>
                    </div>

                    <div class="buy-buttons product-actions buy-box-actions">
                        <button class="btn-buy-now"
                                data-add-cart
                                data-cart-redirect="true"
                                data-id="{{ $producto->id }}"
                                data-title="{{ \Illuminate\Support\Str::limit($producto->nombre, 60, '') }}"
                                data-price="{{ $precioFinal }}"
                                data-stock="{{ $producto->stock }}"
                                data-img="{{ $producto->imagen }}"
                                data-slug="{{ $producto->slug }}"
                                data-cart-url="{{ route('carrito.index') }}">
                            Solicitar compra
                        </button>
                        <button class="btn-add-cart"
                                data-add-cart
                                data-id="{{ $producto->id }}"
                                data-title="{{ \Illuminate\Support\Str::limit($producto->nombre, 60, '') }}"
                                data-price="{{ $precioFinal }}"
                                data-stock="{{ $producto->stock }}"
                                data-img="{{ $producto->imagen }}"
                                data-slug="{{ $producto->slug }}">
                            Agregar al carrito
                        </button>
                    </div>
                @else
                    <div style="padding: 12px 16px; background-color: #fee2e2; border: 1px solid #f87171; border-radius: 8px; color: #991b1b; font-weight: 600; margin: 16px 0; text-align: center;">
                        🚫 Producto agotado
                    </div>
                    <div class="buy-buttons product-actions buy-box-actions">
                        <button class="btn-buy-now" disabled style="opacity: 0.5; cursor: not-allowed; background-color: #9ca3af;">
                            Agotado
                        </button>
                    </div>
                @endif

                <div class="buy-box-helper">La solicitud queda guardada en tu cuenta.</div>
            </div>

            {{-- Seller Card --}}
            <div class="seller-card">
                <h3>Información del vendedor</h3>
                <div class="seller-info">
                    <div class="seller-avatar">{{ $tiendaInicial }}</div>
                    <div>
                        <p class="seller-name">{{ $tiendaNombre }}</p>
                        <p class="seller-level">&#11088; Tienda verificada</p>
                        @if($tiendaDescripcion)
                            <p class="seller-description">{{ $tiendaDescripcion }}</p>
                        @endif
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

            {{-- Coordinacion --}}
            <div class="contact-card">
                <h3>Coordinación</h3>
                <div>
                    <div>&#128221; Confirma la solicitud desde el carrito</div>
                    <div>&#128222; La tienda verá tus datos de contacto</div>
                    <div>&#128241; El comprador contacta a la tienda por WhatsApp o teléfono</div>
                    <div>&#128230; Entrega y pago se acuerdan fuera de la plataforma</div>
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
    @if($producto->stock > 0)
        <button class="sticky-buy-btn"
                data-add-cart
                data-cart-redirect="true"
                data-id="{{ $producto->id }}"
                data-title="{{ \Illuminate\Support\Str::limit($producto->nombre, 60, '') }}"
                data-price="{{ $precioFinal }}"
                data-stock="{{ $producto->stock }}"
                data-img="{{ $producto->imagen }}"
                data-slug="{{ $producto->slug }}"
                data-cart-url="{{ route('carrito.index') }}">
            Solicitar compra
        </button>
    @else
        <button class="sticky-buy-btn" disabled style="opacity: 0.5; cursor: not-allowed; background-color: #9ca3af;">
            Agotado
        </button>
    @endif
</div>

<script>
    function requireLogin(url) {
        // Create the centered modal overlay
        const modal = document.createElement('div');
        modal.style.position = 'fixed';
        modal.style.top = '50%';
        modal.style.left = '50%';
        modal.style.transform = 'translate(-50%, -50%)';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.85)';
        modal.style.color = 'white';
        modal.style.padding = '24px 40px';
        modal.style.borderRadius = '12px';
        modal.style.zIndex = '10000';
        modal.style.fontSize = '18px';
        modal.style.fontWeight = 'bold';
        modal.style.textAlign = 'center';
        modal.style.boxShadow = '0 10px 25px rgba(0,0,0,0.3)';
        modal.style.opacity = '0';
        modal.style.transition = 'opacity 0.3s ease';
        
        modal.innerHTML = `
            <div style="margin-bottom: 12px;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto; display: block;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            Debes iniciar sesión para realizar esta acción.<br>
            <span style="font-size: 14px; font-weight: normal; color: #9ca3af; margin-top: 8px; display: block;">Redirigiendo al login...</span>
        `;
        
        document.body.appendChild(modal);
        
        // Trigger reflow and show
        void modal.offsetWidth;
        modal.style.opacity = '1';
        
        // Redirect after delay
        setTimeout(() => {
            window.location.href = url;
        }, 1800);
    }
</script>

</x-layouts.shop>
