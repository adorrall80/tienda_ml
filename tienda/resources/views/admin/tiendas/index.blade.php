<x-layouts.panel title="Tiendas">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>

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
