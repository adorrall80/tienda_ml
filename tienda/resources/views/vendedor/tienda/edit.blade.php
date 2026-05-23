<x-layouts.panel title="Mi tienda">
    <x-slot name="nav">@include('vendedor._nav')</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="p-card" style="max-width:560px">
        <div class="p-card-header">
            <h3 class="p-card-title">Configuración de mi tienda</h3>
            <span class="badge {{ $tienda->activa ? 'badge-success' : 'badge-secondary' }}">
                {{ $tienda->activa ? 'Activa' : 'Inactiva' }}
            </span>
        </div>
        <div class="p-card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0;padding-left:16px">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('vendedor.tienda.update') }}">
                @csrf @method('PUT')
                <div class="form-grid-2">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Nombre de la tienda <span class="req">*</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre', $tienda->nombre) }}" class="form-input" required>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" rows="3" class="form-input">{{ old('descripcion', $tienda->descripcion) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email de contacto</label>
                        <input type="email" name="contacto_email" value="{{ old('contacto_email', $tienda->contacto_email) }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="contacto_telefono" value="{{ old('contacto_telefono', $tienda->contacto_telefono) }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="contacto_whatsapp" value="{{ old('contacto_whatsapp', $tienda->contacto_whatsapp) }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dirección de atención</label>
                        <input type="text" name="contacto_direccion" value="{{ old('contacto_direccion', $tienda->contacto_direccion) }}" class="form-input">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" value="{{ $tienda->slug }}" class="form-input" disabled style="background:#F8F9FA;color:#6C757D">
                        <small class="text-muted">El slug no puede modificarse.</small>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.panel>
