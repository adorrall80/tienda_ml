<x-layouts.panel title="Productos">
    <x-slot name="nav">
        <span class="nav-section-label">Principal</span>
        <a href="{{ route('admin.dashboard') }}" class="nav-item">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <span class="nav-section-label">Catálogo</span>
        <a href="{{ route('admin.productos.index') }}" class="nav-item active">
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

    <x-slot name="actions">
        <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">+ Nuevo producto</a>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Todos los productos ({{ $productos->total() }})</h3>
            <form method="GET" action="{{ route('admin.productos.index') }}" class="search-form">
                <input type="text" name="q" value="{{ $search }}" placeholder="Buscar producto…" class="form-input-sm">
                <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                @if($search)
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-sm btn-outline">✕</a>
                @endif
            </form>
        </div>

        <table class="p-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Tienda</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Condición</th>
                    <th>Estado</th>
                    <th>Acciones</th>
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
                            <div>
                                <div>{{ Str::limit($p->nombre, 50) }}</div>
                                <small class="text-muted">{{ $p->slug }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $p->category->nombre ?? '—' }}</td>
                    <td class="text-muted">{{ $p->tienda->nombre ?? '—' }}</td>
                    <td>
                        @if(is_null($p->precio))
                            <span class="badge badge-regalo">Regalo</span>
                        @else
                            ${{ number_format($p->precio, 0, ',', '.') }}
                        @endif
                    </td>
                    <td>{{ $p->stock }}</td>
                    <td>
                        @if($p->estado)
                            <span class="badge badge-estado-{{ $p->estado }}">
                                {{ \App\Models\Product::ESTADOS[$p->estado] }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.productos.toggle', $p) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge {{ $p->activo ? 'badge-success' : 'badge-secondary' }}" style="cursor:pointer;border:none;">
                                {{ $p->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.productos.edit', $p) }}" class="btn-icon btn-icon-edit" title="Editar">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <a href="{{ route('productos.show', $p->slug) }}" class="btn-icon btn-icon-view" title="Ver en tienda" target="_blank">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.productos.destroy', $p) }}" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar {{ addslashes(Str::limit($p->nombre, 30)) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-delete" title="Eliminar">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="empty-row">No hay productos.</td></tr>
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
