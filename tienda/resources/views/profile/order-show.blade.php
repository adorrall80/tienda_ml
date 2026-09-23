<x-layouts.shop title="Detalle de compra - TiendaMV">

@php
    $storeGroups = $order->items->groupBy('tienda_id');
@endphp

<div class="page-header">
    <div class="container">
        <p class="breadcrumb">
            <a href="{{ route('inicio') }}">Inicio</a><span>›</span>
            <a href="{{ route('cuenta.perfil') }}">Mi cuenta</a><span>›</span>
            Detalle de compra
        </p>
        <h1>Detalle de compra</h1>
        <p class="results-count">Solicitud {{ $order->numero }}</p>
    </div>
</div>

<div class="container checkout-confirmation" data-order-success>
    <section class="checkout-card checkout-confirmation-card">
        <h2>Contacto de tiendas</h2>
        <p>Esta solicitud se coordina directamente con cada tienda. Aqui puedes revisar productos, totales y datos de contacto.</p>

        <div class="checkout-warning-alert" role="alert">
            <img src="{{ asset('images/alerta-pago.svg') }}" alt="" aria-hidden="true">
            <div>
                <strong>Atencion</strong>
                No realices transferencias ni pagos sin coordinar primero la entrega con la tienda. Cada tienda es independiente y sus acuerdos de pago o entrega son responsabilidad de cada tienda y no son responsabilidad de esta plataforma.
            </div>
        </div>

        <dl class="checkout-order-meta">
            <div>
                <dt>Estado</dt>
                <dd>{{ $order->estadoLabel() }}</dd>
            </div>
            <div>
                <dt>Fecha</dt>
                <dd>{{ $order->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            @if ($order->cliente_telefono)
                <div>
                    <dt>Telefono comprador</dt>
                    <dd>{{ $order->cliente_telefono }}</dd>
                </div>
            @endif
            <div>
                <dt>Total</dt>
                <dd>${{ number_format($order->total, 0, ',', '.') }}</dd>
            </div>
        </dl>

        <div class="checkout-grand-total">
            <span>Siguiente accion</span>
            <strong style="font-size:16px">{{ $order->nextActionLabel() }}</strong>
        </div>

        <div class="checkout-store-contacts">
            @foreach ($storeGroups as $storeId => $items)
                @php
                    $store = $items->first()->tienda;
                    $storeName = $store?->nombre ?: $items->first()->tienda_nombre ?: 'Tienda sin nombre';
                    $storeEmail = $store?->contacto_email ?: $store?->user?->email;
                    $storePhone = $store?->telefono_visible ? $store?->contacto_telefono : null;
                    $storeWhatsapp = $store?->permite_whatsapp ? $store?->contacto_whatsapp : null;
                    $storeAddress = $store?->contacto_direccion;
                    $modalId = 'account-store-contact-modal-'.$loop->iteration;
                @endphp
                <article class="checkout-store-contact">
                    <div class="checkout-store-summary">
                        <div>
                            <strong>{{ $storeName }}</strong>
                            <span>{{ $items->count() }} {{ $items->count() === 1 ? 'producto solicitado' : 'productos solicitados' }}</span>
                        </div>
                        @if ($store?->descripcion)
                            <p>{{ $store->descripcion }}</p>
                        @endif
                    </div>
                    <div class="checkout-store-actions">
                        @if ($storeWhatsapp)
                            <a class="checkout-whatsapp-link" href="https://wa.me/{{ preg_replace('/\D+/', '', $storeWhatsapp) }}" target="_blank" rel="noopener">
                                WhatsApp {{ $storeWhatsapp }}
                            </a>
                        @else
                            <span class="checkout-contact-empty">Sin WhatsApp publico</span>
                        @endif
                        <strong class="checkout-store-total">${{ number_format($items->sum('total'), 0, ',', '.') }}</strong>
                        <button class="checkout-detail-btn" type="button" data-store-modal-open="{{ $modalId }}">
                            Ver detalle
                        </button>
                    </div>
                </article>

                <div class="checkout-modal" id="{{ $modalId }}" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title" hidden>
                    <div class="checkout-modal-backdrop" data-store-modal-close></div>
                    <section class="checkout-modal-panel">
                        <div class="checkout-modal-header">
                            <div>
                                <span>Detalle de tienda</span>
                                <h2 id="{{ $modalId }}-title">{{ $storeName }}</h2>
                            </div>
                            <button type="button" aria-label="Cerrar detalle" data-store-modal-close>&times;</button>
                        </div>

                        <dl class="checkout-modal-contact">
                            @if ($storeEmail)
                                <div>
                                    <dt>Email</dt>
                                    <dd><a href="mailto:{{ $storeEmail }}">{{ $storeEmail }}</a></dd>
                                </div>
                            @endif
                            @if ($storePhone)
                                <div>
                                    <dt>Telefono</dt>
                                    <dd><a href="tel:{{ preg_replace('/\s+/', '', $storePhone) }}">{{ $storePhone }}</a></dd>
                                </div>
                            @endif
                            @if ($storeWhatsapp)
                                <div>
                                    <dt>WhatsApp</dt>
                                    <dd><a href="https://wa.me/{{ preg_replace('/\D+/', '', $storeWhatsapp) }}" target="_blank" rel="noopener">{{ $storeWhatsapp }}</a></dd>
                                </div>
                            @endif
                            @if ($storeAddress)
                                <div>
                                    <dt>Direccion</dt>
                                    <dd>{{ $storeAddress }}</dd>
                                </div>
                            @endif
                        </dl>

                        <h3>Productos de esta tienda</h3>
                        <div class="checkout-confirmation-items">
                            @foreach ($items as $item)
                                <article class="checkout-confirmation-item">
                                    <div>
                                        <strong>{{ $item->producto_nombre }}</strong>
                                        <span>{{ $item->cantidad }} {{ $item->cantidad === 1 ? 'unidad' : 'unidades' }}</span>
                                    </div>
                                    <div>
                                        <span>${{ number_format($item->precio_unitario, 0, ',', '.') }} c/u</span>
                                        <strong>${{ number_format($item->total, 0, ',', '.') }}</strong>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>
            @endforeach
        </div>

        <div class="checkout-grand-total">
            <span>Total solicitud</span>
            <strong>${{ number_format($order->total, 0, ',', '.') }}</strong>
        </div>

        <h3>Historial de seguimiento</h3>
        <div class="checkout-confirmation-items">
            @forelse($order->statusHistories->sortByDesc('created_at') as $history)
                <article class="checkout-confirmation-item">
                    <div>
                        <strong>{{ \App\Models\Order::labelForStatus($history->estado_nuevo) }}</strong>
                        <span>{{ $history->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span>Actualizado por {{ $history->actor === 'admin' ? 'administracion' : 'tienda' }}</span>
                    </div>
                </article>
            @empty
                <article class="checkout-confirmation-item">
                    <div>
                        <strong>Sin cambios registrados</strong>
                        <span>El pedido se encuentra en estado inicial.</span>
                    </div>
                </article>
            @endforelse
        </div>

        <a class="cart-primary-link" href="{{ route('cuenta.perfil') }}">Volver a Mi cuenta</a>
    </section>
</div>

</x-layouts.shop>
