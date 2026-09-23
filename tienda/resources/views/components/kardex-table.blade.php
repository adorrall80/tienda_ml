
<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
    <h3 style="margin: 0; font-size: 1.2rem;">Historial de Movimientos: {{ $producto->nombre }}</h3>
    <button type="button" onclick="document.getElementById('kardexModal').close()" class="btn btn-outline" style="padding: 4px 8px;">Cerrar</button>
</div>
<div style="max-height: 400px; overflow-y: auto;">
    <table class="p-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Fecha</th>
                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Usuario</th>
                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Tipo</th>
                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Referencia</th>
                <th style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">Cant.</th>
                <th style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php $balance = 0; @endphp
            @forelse($movements as $m)
                @php $balance += $m->cantidad; @endphp
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $m->user ? $m->user->name : 'Sistema' }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ ucfirst(str_replace('_', ' ', $m->tipo)) }} <br><small style="color: #6c757d;">{{ $m->notas }}</small></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $m->order_id ? 'Pedido #' . $m->order->numero : '-' }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right; color: {{ $m->cantidad > 0 ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                        {{ $m->cantidad > 0 ? '+' : '' }}{{ $m->cantidad }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">{{ $balance }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No hay movimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>