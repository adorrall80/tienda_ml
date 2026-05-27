<x-layouts.panel title="Nuevo producto">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>

    <div class="p-card product-editor-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Nuevo producto</h3>
            <a href="{{ route('admin.productos.index') }}" class="btn btn-sm btn-outline">← Volver</a>
        </div>
        <div class="p-card-body">
            <form method="POST" action="{{ route('admin.productos.store') }}" class="product-form" enctype="multipart/form-data">
                @csrf

                @include('admin.productos._form')

                <div class="form-actions">
                    <button type="submit" name="guardar_accion" value="guardar" class="btn btn-primary">Guardar</button>
                    <button type="submit" name="guardar_accion" value="nuevo" class="btn btn-outline">Guardar y agregar nuevo</button>
                    <button type="submit" name="guardar_accion" value="listado" class="btn btn-outline">Guardar y volver al listado</button>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.panel>
