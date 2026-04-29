<x-layouts.panel title="Dashboard">
    <x-slot name="nav">
        <span class="nav-section-label">Principal</span>
        <a href="{{ route('admin.dashboard') }}" class="nav-item active">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <span class="nav-section-label">Catálogo</span>
        <a href="{{ route('admin.productos.index') }}" class="nav-item">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3H8v4h8z"/></svg>
            Productos
        </a>

        <span class="nav-section-label">Usuarios</span>
        <a href="{{ route('admin.usuarios.index') }}" class="nav-item">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            Usuarios
        </a>
        <a href="{{ route('admin.tiendas.index') }}" class="nav-item">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Tiendas
        </a>
    </x-slot>

    {{-- Info boxes --}}
    <div class="info-boxes">
        <div class="info-box">
            <div class="info-box-icon ib-cyan">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3H8v4h8z"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Productos</span>
                <span class="info-box-number">{{ $stats['productos'] }}</span>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-icon ib-green">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Usuarios totales</span>
                <span class="info-box-number">{{ $stats['usuarios'] }}</span>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-icon ib-yellow">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Vendedores</span>
                <span class="info-box-number">{{ $stats['vendedores'] }}</span>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-icon ib-red">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Clientes</span>
                <span class="info-box-number">{{ $stats['clientes'] }}</span>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="dashboard-grid">

        <div class="p-card">
            <div class="p-card-header">
                <h3 class="p-card-title">Productos recientes</h3>
                <a href="#" class="btn btn-primary btn-sm">+ Nuevo producto</a>
            </div>
            <table class="p-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Product::latest()->take(8)->get() as $p)
                    <tr>
                        <td>
                            <div class="product-row">
                                @if($p->imagen)
                                    <img src="{{ $p->imagen }}" alt="{{ $p->nombre }}" class="product-thumb">
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
                            <a href="{{ route('productos.show', $p->slug) }}" class="link-action" target="_blank">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-card">
            <div class="p-card-header">
                <h3 class="p-card-title">Accesos rápidos</h3>
            </div>
            <div class="p-card-body">
                <div class="quick-links">
                    <a href="{{ route('admin.productos.create') }}" class="quick-link">+ Producto</a>
                    <a href="{{ route('admin.usuarios.index') }}" class="quick-link">Usuarios</a>
                    <a href="{{ route('admin.tiendas.index') }}" class="quick-link">Tiendas</a>
                    <a href="{{ route('inicio') }}" class="quick-link" target="_blank">Ver tienda</a>
                </div>
            </div>
        </div>

    </div>

</x-layouts.panel>
