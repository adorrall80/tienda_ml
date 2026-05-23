<x-layouts.panel title="Detalle de pedido">
    <x-slot name="nav">@include('vendedor._nav')</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="p-page-actions">
        <a href="{{ route('vendedor.pedidos.index') }}" class="btn btn-outline">Volver a pedidos</a>
    </div>

    <div class="info-boxes">
        <div class="info-box">
            <div class="info-box-icon ib-purple">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 4h16v16H4z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Pedido</span>
                <span class="info-box-number" style="font-size:18px">{{ $order->numero }}</span>
            </div>
        </div>
        <div class="info-box">
            <div class="info-box-icon ib-green">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 000 7H14a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Total de tu tienda</span>
                <span class="info-box-number">${{ number_format($storeTotal, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="info-box">
            <div class="info-box-icon ib-blue">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3H8v4h8z"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Productos</span>
                <span class="info-box-number">{{ $order->items->sum('cantidad') }}</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="p-card">
            <div class="p-card-header">
                <h3 class="p-card-title">Productos solicitados a {{ $tienda->nombre }}</h3>
            </div>
            <table class="p-table">
                <thead>
                    <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->producto_nombre }}</strong>
                                @if($item->producto_slug)
                                    <br><a href="{{ route('productos.show', $item->producto_slug) }}" target="_blank" class="link-action">Ver producto publico</a>
                                @endif
                            </td>
                            <td>{{ $item->cantidad }}</td>
                            <td>${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                            <td>${{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-card">
            <div class="p-card-header"><h3 class="p-card-title">Comprador</h3></div>
            <div class="p-card-body">
                <p><strong>{{ $order->cliente_nombre }}</strong></p>
                <p class="text-muted">{{ $order->cliente_email }}</p>
                @if($order->cliente_telefono)
                    <p class="text-muted">{{ $order->cliente_telefono }}</p>
                @endif
                <hr style="border:0;border-top:1px solid var(--p-border);margin:14px 0">
                <p><strong>Estado:</strong> {{ $order->estadoLabel() }}</p>
                <p class="text-muted">{{ $order->nextActionLabel() }}</p>
                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <form method="POST" action="{{ route('vendedor.pedidos.estado', $order) }}" class="cuenta-form" style="margin-top:14px">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label class="form-label" for="estado">Cambiar estado</label>
                        <select id="estado" name="estado" class="form-input">
                            @foreach(\App\Models\Order::ESTADOS as $value => $label)
                                <option value="{{ $value }}" @selected($order->estado === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top:10px">Guardar estado</button>
                </form>
                @if($order->notas)
                    <hr style="border:0;border-top:1px solid var(--p-border);margin:14px 0">
                    <p><strong>Nota del comprador</strong></p>
                    <p class="text-muted">{{ $order->notas }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="p-card">
            <div class="p-card-header"><h3 class="p-card-title">Historial de estados</h3></div>
            <div class="p-card-body">
                @forelse($order->statusHistories->sortByDesc('created_at') as $history)
                    <p style="margin-bottom:10px">
                        <strong>{{ \App\Models\Order::ESTADOS[$history->estado_anterior] ?? 'Sin estado' }}</strong>
                        a
                        <strong>{{ \App\Models\Order::ESTADOS[$history->estado_nuevo] ?? $history->estado_nuevo }}</strong>
                        <br>
                        <span class="text-muted">{{ ucfirst($history->actor) }} {{ $history->user?->name ? 'por '.$history->user->name : '' }} · {{ $history->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                @empty
                    <p class="text-muted">Aun no hay cambios de estado registrados.</p>
                @endforelse
            </div>
        </div>

        <div class="p-card">
            <div class="p-card-header"><h3 class="p-card-title">Notas internas</h3></div>
            <div class="p-card-body">
                <form method="POST" action="{{ route('vendedor.pedidos.notas', $order) }}" style="margin-bottom:16px">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="nota">Agregar nota</label>
                        <textarea id="nota" name="nota" class="form-input" rows="3" required>{{ old('nota') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top:10px">Guardar nota</button>
                </form>

                @forelse($order->internalNotes->sortByDesc('created_at') as $note)
                    <p style="margin-bottom:12px">
                        {{ $note->nota }}
                        <br>
                        <span class="text-muted">{{ ucfirst($note->actor) }} {{ $note->user?->name ? 'por '.$note->user->name : '' }} · {{ $note->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                @empty
                    <p class="text-muted">Sin notas internas.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.panel>
