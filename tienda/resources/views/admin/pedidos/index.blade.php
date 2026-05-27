<x-layouts.panel title="Pedidos globales">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>

    <div class="info-boxes">
        <div class="info-box">
            <div class="info-box-icon ib-purple">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 4h16v16H4z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Pedidos globales</span>
                <span class="info-box-number">{{ $ordersCount }}</span>
            </div>
        </div>
        <div class="info-box">
            <div class="info-box-icon ib-green">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 000 7H14a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Total solicitado</span>
                <span class="info-box-number">${{ number_format($totalSolicitado, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Solicitudes de compra</h3>
        </div>
        <table class="p-table">
            <thead>
                <tr><th>Pedido</th><th>Cliente</th><th>Tiendas</th><th>Productos</th><th>Total</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->numero }}</strong><br>
                            <span class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                            {{ $order->cliente_nombre }}<br>
                            <span class="text-muted">{{ $order->cliente_email }}</span>
                        </td>
                        <td>{{ $order->items->pluck('tienda_nombre')->filter()->unique()->count() }}</td>
                        <td>{{ $order->items->sum('cantidad') }}</td>
                        <td>${{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="badge badge-secondary">{{ $order->estadoLabel() }}</span></td>
                        <td><a href="{{ route('admin.pedidos.show', $order) }}" class="btn btn-sm btn-outline">Ver detalle</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">Todavia no existen pedidos.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
            <div class="pagination-wrap">{{ $orders->links() }}</div>
        @endif
    </div>
</x-layouts.panel>
