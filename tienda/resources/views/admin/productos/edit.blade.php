<x-layouts.panel title="Editar producto">
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
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Usuarios
        </a>
        <a href="{{ route('admin.tiendas.index') }}" class="nav-item">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Tiendas
        </a>
    </x-slot>

    <div class="p-card product-editor-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Editar: {{ Str::limit($producto->nombre, 50) }}</h3>
            <a href="{{ route('admin.productos.index') }}" class="btn btn-sm btn-outline">← Volver</a>
        </div>
        <div class="p-card-body">
            <form method="POST" action="{{ route('admin.productos.update', $producto) }}" class="product-form" enctype="multipart/form-data">
                @csrf @method('PUT')

                @include('admin.productos._form')

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Actualizar producto</button>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.panel>
