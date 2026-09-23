<x-layouts.panel title="Editar producto">
    <x-slot name="nav">@include('vendedor._nav')</x-slot>

    <div class="p-card product-editor-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Editar: {{ Str::limit($producto->nombre, 50) }}</h3>
            <div class="p-card-actions">
                <a href="{{ route('vendedor.productos.preview', $producto) }}" class="btn btn-sm btn-outline" target="_blank" rel="noopener" title="Ver vista previa en tienda">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    Vista previa
                </a>
                <a href="{{ route('vendedor.productos.index') }}" class="btn btn-sm btn-outline">← Volver</a>
            </div>
        </div>
        <div class="p-card-body">
            <form method="POST" action="{{ route('vendedor.productos.update', $producto) }}" enctype="multipart/form-data" data-review-lock-check-url="{{ route('vendedor.productos.estado-revision', $producto) }}">
                @csrf @method('PUT')
                @include('vendedor.productos._form')
                <div class="form-actions">
                    <button type="submit" name="guardar_accion" value="guardar" class="btn btn-primary">Guardar</button>
                    <button type="submit" name="guardar_accion" value="nuevo" class="btn btn-outline">Guardar y agregar nuevo</button>
                    <button type="submit" name="guardar_accion" value="listado" class="btn btn-outline">Guardar y volver al listado</button>
                    <a href="{{ route('vendedor.productos.index') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.panel>
