<x-layouts.panel title="Usuarios">
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
        <a href="{{ route('admin.usuarios.index') }}" class="nav-item active">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            Usuarios
        </a>
        <a href="{{ route('admin.tiendas.index') }}" class="nav-item">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Tiendas
        </a>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Todos los usuarios ({{ $usuarios->total() }})</h3>
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="search-form">
                <input type="text" name="q" value="{{ $search }}" placeholder="Buscar nombre o email…" class="form-input-sm">
                <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                @if($search)
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm btn-outline">✕</a>
                @endif
            </form>
        </div>

        <table class="p-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Registrado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td class="text-muted">{{ $usuario->id }}</td>
                    <td>
                        <div class="user-row">
                            <span class="user-avatar-sm">{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                            {{ $usuario->name }}
                            @if($usuario->id === auth()->id())
                                <span class="badge badge-you">tú</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-muted">{{ $usuario->email }}</td>
                    <td>
                        @if($usuario->id === auth()->id())
                            @foreach($usuario->roles as $rolActual)
                                <span class="badge badge-secondary">{{ ucfirst($rolActual->name) }}</span>
                            @endforeach
                        @else
                        <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="inline-form">
                            @csrf @method('PUT')
                            <select name="rol" class="select-rol">
                                @foreach($roles as $rol)
                                    <option value="{{ $rol }}" @selected($usuario->hasRole($rol))>{{ ucfirst($rol) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm"
                                    onclick="return confirm('¿Cambiar rol de {{ addslashes($usuario->name) }}?')">
                                Guardar rol
                            </button>
                        </form>
                        @endif
                    </td>
                    <td class="text-muted">{{ $usuario->created_at->format('d/m/Y') }}</td>
                    <td>
                        @if($usuario->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($usuario->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon btn-icon-delete" title="Eliminar">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-row">No se encontraron usuarios.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($usuarios->hasPages())
        <div class="p-card-body pagination-wrap">
            {{ $usuarios->links('vendor.pagination.simple-tailwind') }}
        </div>
        @endif
    </div>
</x-layouts.panel>
