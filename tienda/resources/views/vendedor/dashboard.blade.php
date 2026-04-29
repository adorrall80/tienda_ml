<x-layouts.panel title="Dashboard">
    <x-slot name="nav">@include('vendedor._nav')</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if(! $tienda)
        <div class="p-card" style="max-width:480px;margin:40px auto;text-align:center">
            <div class="p-card-body" style="padding:40px 30px">
                <svg width="48" height="48" fill="none" stroke="#6C757D" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:16px"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                <h3 style="margin-bottom:8px;font-size:18px">Aún no tienes una tienda</h3>
                <p style="color:#6C757D;margin-bottom:24px;font-size:14px">Crea tu tienda para empezar a publicar productos.</p>
                <a href="{{ route('vendedor.tienda.create') }}" class="btn btn-primary">Crear mi tienda</a>
            </div>
        </div>
    @else
        <div class="info-boxes" style="grid-template-columns:repeat(3,1fr)">
            <div class="info-box">
                <div class="info-box-icon ib-blue">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3H8v4h8z"/></svg>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Total productos</span>
                    <span class="info-box-number">{{ $tienda->productos()->count() }}</span>
                </div>
            </div>
            <div class="info-box">
                <div class="info-box-icon ib-green">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Activos</span>
                    <span class="info-box-number">{{ $tienda->productos()->where('activo', true)->count() }}</span>
                </div>
            </div>
            <div class="info-box">
                <div class="info-box-icon {{ $tienda->activa ? 'ib-green' : 'ib-red' }}">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Estado tienda</span>
                    <span class="info-box-number" style="font-size:16px">{{ $tienda->activa ? 'Activa' : 'Inactiva' }}</span>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="p-card">
                <div class="p-card-header">
                    <h3 class="p-card-title">Últimos productos</h3>
                    <a href="{{ route('vendedor.productos.index') }}" class="btn btn-sm btn-outline">Ver todos</a>
                </div>
                <table class="p-table">
                    <thead>
                        <tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $p)
                        <tr>
                            <td>
                                <div class="product-row">
                                    @if($p->imagen)
                                        <img src="{{ $p->imagen }}" alt="" class="product-thumb">
                                    @else
                                        <span class="product-thumb product-no-img" title="Sin imagen">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                        </span>
                                    @endif
                                    {{ Str::limit($p->nombre, 45) }}
                                </div>
                            </td>
                            <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
                            <td>{{ $p->stock }}</td>
                            <td>
                                <span class="badge {{ $p->activo ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $p->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('vendedor.productos.edit', $p) }}" class="btn-icon btn-icon-edit" title="Editar">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <a href="{{ route('productos.show', $p->slug) }}" class="btn-icon btn-icon-view" title="Ver" target="_blank">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="empty-row">No tienes productos aún. <a href="{{ route('vendedor.productos.create') }}">+ Agregar</a></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-card">
                <div class="p-card-header"><h3 class="p-card-title">Accesos rápidos</h3></div>
                <div class="p-card-body">
                    <div class="quick-links">
                        <a href="{{ route('vendedor.productos.create') }}" class="quick-link">+ Producto</a>
                        <a href="{{ route('vendedor.tienda.edit') }}" class="quick-link">Mi tienda</a>
                        <a href="{{ route('inicio') }}" class="quick-link" target="_blank">Ver tienda</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-layouts.panel>
