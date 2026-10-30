<x-layouts.panel :title="$category->exists ? 'Editar categoría' : 'Nueva categoría'">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>
    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="p-card">
        <div class="p-card-body">
            <form method="POST" action="{{ $category->exists ? route('admin.categorias.update', $category) : route('admin.categorias.store') }}" class="cuenta-form">
                @csrf
                @if($category->exists) @method('PUT') @endif
                <div class="form-group">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" class="form-input" maxlength="120" value="{{ old('nombre', $category->nombre) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="slug">Identificador en la URL</label>
                    <input id="slug" name="slug" class="form-input" maxlength="150" pattern="[a-z0-9]+(-[a-z0-9]+)*" value="{{ old('slug', $category->slug) }}" aria-describedby="slug-help">
                    <small id="slug-help" class="text-muted">Ejemplo: herramientas. Si lo dejas vacío se genera al crear; al editar se conserva el actual. Cambiarlo modifica los enlaces de filtros.</small>
                </div>
                <div class="form-group">
                    <label class="form-label" for="parent_id">Categoría padre</label>
                    <select id="parent_id" name="parent_id" class="form-input">
                        <option value="">Sin padre (categoría principal)</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $parent->id)>{{ $parent->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="icono">Icono (emoji, opcional)</label>
                    <input id="icono" name="icono" class="form-input" maxlength="40" value="{{ old('icono', $category->icono) }}" placeholder="🔧">
                </div>
                <div class="form-group">
                    <label class="form-label" for="orden">Orden</label>
                    <input id="orden" name="orden" type="number" class="form-input" min="0" max="255" required value="{{ old('orden', $category->orden) }}">
                </div>
                <label class="form-check">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $category->activo))> Activa en la navegación de la tienda
                </label>
                <p class="text-muted">Desactivar oculta la categoría del menú; sus productos conservan su estado de publicación.</p>
                <button type="submit" class="btn btn-primary">Guardar categoría</button>
                <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</x-layouts.panel>
