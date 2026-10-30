<x-layouts.panel title="Categorías">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary">Nueva categoría</a>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="p-card">
        <div class="p-card-header"><h3 class="p-card-title">Categorías del catálogo</h3></div>
        <div class="p-card-body">
            <p class="text-muted">El orden menor aparece primero. Las categorías principales activas aparecen en la navegación de la tienda.</p>
            <table class="p-table p-table-mobile-cards">
                <thead><tr><th>Nombre</th><th>Padre</th><th>Orden</th><th>Estado</th><th>Productos</th><th>Acciones</th></tr></thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td data-label="Nombre">{{ $category->icono }} {{ $category->nombre }}<br><small class="text-muted">{{ $category->slug }}</small></td>
                            <td data-label="Padre">{{ $category->parent?->nombre ?? 'Principal' }}</td>
                            <td data-label="Orden">{{ $category->orden }}</td>
                            <td data-label="Estado">{{ $category->activo ? 'Activa' : 'Inactiva' }}</td>
                            <td data-label="Productos">{{ $category->products_count }}</td>
                            <td data-label="Acciones"><a href="{{ route('admin.categorias.edit', $category) }}" class="btn btn-sm btn-primary" aria-label="Editar {{ $category->nombre }}">Editar</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="mobile-card-empty">Todavía no hay categorías. Crea la primera para organizar tus productos.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>
</x-layouts.panel>
