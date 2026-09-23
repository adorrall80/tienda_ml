<x-layouts.panel title="Estados de pedido">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="dashboard-grid">
        <div class="p-card">
            <div class="p-card-header">
                <h3 class="p-card-title">Estados actuales</h3>
            </div>
            <div class="p-card-body">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Orden</th>
                            <th>Activo</th>
                            <th>Pedidos</th>
                            <th>Guardar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statuses as $status)
                            @php($formId = 'order-status-'.$status->id)
                            <tr>
                                <td>
                                    <input form="{{ $formId }}" type="text" name="nombre" value="{{ old('nombre', $status->nombre) }}" class="form-input" maxlength="80" required>
                                </td>
                                <td><span class="text-muted">{{ $status->slug }}</span></td>
                                <td>
                                    <div class="order-stepper" data-order-stepper>
                                        <button type="button" class="order-stepper-btn" data-order-step="up" aria-label="Subir orden">↑</button>
                                        <input form="{{ $formId }}" type="number" name="orden" value="{{ old('orden', $status->orden) }}" class="form-input order-stepper-input" min="0">
                                        <button type="button" class="order-stepper-btn" data-order-step="down" aria-label="Bajar orden">↓</button>
                                    </div>
                                </td>
                                <td>
                                    <label class="form-check">
                                        <input form="{{ $formId }}" type="hidden" name="activo" value="0">
                                        <input form="{{ $formId }}" type="checkbox" name="activo" value="1" @checked(old('activo', $status->activo))>
                                        Activo
                                    </label>
                                </td>
                                <td>
                                    @if($status->orders_count > 0)
                                        <a class="assoc-count-link" href="{{ route('admin.mantenedores.estados-pedido.index', ['estado' => $status->slug]) }}">
                                            {{ $status->orders_count }}
                                        </a>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    <form id="{{ $formId }}" method="POST" action="{{ route('admin.mantenedores.estados-pedido.update', $status) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                    </form>
                                </td>
                                <td>
                                    @if($status->orders_count === 0)
                                        <form method="POST" action="{{ route('admin.mantenedores.estados-pedido.destroy', $status) }}" onsubmit="return confirm('Eliminar este estado de pedido?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    @else
                                        <span class="text-muted">En uso</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty-row">Aun no hay estados de pedido.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-card">
            <div class="p-card-header">
                <h3 class="p-card-title">Nuevo estado</h3>
            </div>
            <div class="p-card-body">
                <form method="POST" action="{{ route('admin.mantenedores.estados-pedido.store') }}" class="cuenta-form">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="order_status_nombre">Nombre</label>
                        <input id="order_status_nombre" type="text" name="nombre" value="{{ old('nombre') }}" class="form-input" maxlength="80" required placeholder="Ej: Listo para retiro">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="order_status_orden">Orden</label>
                        <div class="order-stepper" data-order-stepper>
                            <button type="button" class="order-stepper-btn" data-order-step="up" aria-label="Subir orden">↑</button>
                            <input id="order_status_orden" type="number" name="orden" value="{{ old('orden', 0) }}" class="form-input order-stepper-input" min="0">
                            <button type="button" class="order-stepper-btn" data-order-step="down" aria-label="Bajar orden">↓</button>
                        </div>
                    </div>
                    <label class="form-check">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" checked>
                        Activo
                    </label>
                    <button type="submit" class="btn btn-primary" style="margin-top:12px">Crear estado</button>
                </form>
            </div>
        </div>
    </div>

    @if($selectedStatus)
        <div class="p-card" style="margin-top:16px">
            <div class="p-card-header">
                <h3 class="p-card-title">Pedidos asociados: {{ $selectedStatus->nombre }}</h3>
                <a href="{{ route('admin.mantenedores.estados-pedido.index') }}" class="btn btn-sm btn-outline">Cerrar</a>
            </div>
            <div class="p-card-body">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($associatedOrders as $order)
                            <tr>
                                <td>{{ $order->numero }}</td>
                                <td>{{ $order->cliente_nombre }}</td>
                                <td>${{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.pedidos.show', $order) }}" class="btn btn-sm btn-primary">Ver pedido</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-row">No hay pedidos asociados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.panel>
