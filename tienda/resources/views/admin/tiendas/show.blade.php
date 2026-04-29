<x-layouts.panel title="Tienda: {{ $tienda->nombre }}">
    <x-slot name="nav">
        <span class="nav-section-label">Principal</span>
        <a href="{{ route('admin.dashboard') }}" class="nav-item">
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
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Usuarios
        </a>
        <a href="{{ route('admin.tiendas.index') }}" class="nav-item active">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Tiendas
        </a>
    </x-slot>

    <div class="info-boxes" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
        <div class="info-box">
            <div class="info-box-icon ib-blue">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Tienda</span>
                <span class="info-box-number" style="font-size:16px">{{ $tienda->nombre }}</span>
            </div>
        </div>
        <div class="info-box">
            <div class="info-box-icon ib-green">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Propietario</span>
                <span class="info-box-number" style="font-size:16px">{{ $tienda->user->name ?? '—' }}</span>
            </div>
        </div>
        <div class="info-box">
            <div class="info-box-icon ib-yellow">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Productos</span>
                <span class="info-box-number">{{ $productos->total() }}</span>
            </div>
        </div>
    </div>

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Productos de {{ $tienda->nombre }}</h3>
            <a href="{{ route('admin.tiendas.index') }}" class="btn btn-sm btn-outline">← Volver</a>
        </div>

        <table class="p-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
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
                            {{ Str::limit($p->nombre, 50) }}
                        </div>
                    </td>
                    <td class="text-muted">{{ $p->category->nombre ?? '—' }}</td>
                    <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>
                        <span class="badge {{ $p->activo ? 'badge-success' : 'badge-secondary' }}">
                            {{ $p->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.productos.edit', $p) }}" class="btn-icon btn-icon-edit" title="Editar">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-row">Esta tienda no tiene productos.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($productos->hasPages())
        <div class="p-card-body pagination-wrap">
            {{ $productos->links('vendor.pagination.simple-tailwind') }}
        </div>
        @endif
    </div>
</x-layouts.panel>
