<x-layouts.panel title="Tiendas">
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

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Todas las tiendas ({{ $tiendas->total() }})</h3>
        </div>

        <table class="p-table">
            <thead>
                <tr>
                    <th>Tienda</th>
                    <th>Propietario</th>
                    <th>Productos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tiendas as $tienda)
                <tr>
                    <td>
                        <div>
                            <strong>{{ $tienda->nombre }}</strong><br>
                            <small class="text-muted">{{ $tienda->slug }}</small>
                        </div>
                    </td>
                    <td class="text-muted">
                        {{ $tienda->user->name ?? '—' }}<br>
                        <small>{{ $tienda->user->email ?? '' }}</small>
                    </td>
                    <td>{{ $tienda->productos_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.tiendas.toggle', $tienda) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge {{ $tienda->activa ? 'badge-success' : 'badge-secondary' }}" style="cursor:pointer;border:none;">
                                {{ $tienda->activa ? 'Activa' : 'Inactiva' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.tiendas.show', $tienda) }}" class="btn-icon btn-icon-view" title="Ver productos">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-row">No hay tiendas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($tiendas->hasPages())
        <div class="p-card-body pagination-wrap">
            {{ $tiendas->links('vendor.pagination.simple-tailwind') }}
        </div>
        @endif
    </div>
</x-layouts.panel>
