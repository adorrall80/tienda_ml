<x-layouts.panel title="Mis productos">
    <x-slot name="nav">@include('vendedor._nav')</x-slot>
    <x-slot name="actions">
        <a href="{{ route('vendedor.productos.create') }}" class="btn btn-primary">+ Nuevo producto</a>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Productos de {{ $tienda->nombre }} ({{ $productos->total() }})</h3>
        </div>
        <table class="p-table">
            <thead>
                <tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Condición</th><th>Estado</th><th>Acciones</th></tr>
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
                        <form method="POST" action="{{ route('vendedor.productos.toggle', $p) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge {{ $p->activo ? 'badge-success' : 'badge-secondary' }}" style="cursor:pointer;border:none;">
                                {{ $p->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('vendedor.productos.edit', $p) }}" class="btn-icon btn-icon-edit" title="Editar">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <a href="{{ route('productos.show', $p->slug) }}" class="btn-icon btn-icon-view" title="Ver" target="_blank">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <form method="POST" action="{{ route('vendedor.productos.destroy', $p) }}" style="display:inline"
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
                <tr><td colspan="6" class="empty-row">No tienes productos. <a href="{{ route('vendedor.productos.create') }}">+ Agregar uno</a></td></tr>
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
