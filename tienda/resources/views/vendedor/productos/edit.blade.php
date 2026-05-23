<x-layouts.panel title="Editar producto">
    <x-slot name="nav">@include('vendedor._nav')</x-slot>

    <div class="p-card product-editor-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Editar: {{ Str::limit($producto->nombre, 50) }}</h3>
            <a href="{{ route('vendedor.productos.index') }}" class="btn btn-sm btn-outline">← Volver</a>
        </div>
        <div class="p-card-body">
            <form method="POST" action="{{ route('vendedor.productos.update', $producto) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('vendedor.productos._form')
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Actualizar producto</button>
                    <a href="{{ route('vendedor.productos.index') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.panel>
