<x-layouts.panel title="Crear mi tienda">
    <x-slot name="nav">@include('vendedor._nav')</x-slot>

    <div class="p-card" style="max-width:560px;margin:0 auto">
        <div class="p-card-header">
            <h3 class="p-card-title">Crear mi tienda</h3>
        </div>
        <div class="p-card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0;padding-left:16px">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('vendedor.tienda.store') }}">
                @csrf
                <div class="form-grid-2">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Nombre de tu tienda <span class="req">*</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-input" required placeholder="Ej: Tienda de Juan">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" rows="3" class="form-input" placeholder="Cuéntale a tus clientes qué vendes…">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email de contacto</label>
                        <input type="email" name="contacto_email" value="{{ old('contacto_email') }}" class="form-input" placeholder="ventas@mitienda.cl">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="contacto_telefono" value="{{ old('contacto_telefono') }}" class="form-input" placeholder="+56 2 2345 6789">
                    </div>
                    <div class="form-group">
                        <label class="form-check">
                            <input type="hidden" name="telefono_visible" value="0">
                            <input type="checkbox" name="telefono_visible" value="1" @checked(old('telefono_visible', true))>
                            Mostrar teléfono al comprador
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="contacto_whatsapp" value="{{ old('contacto_whatsapp') }}" class="form-input" placeholder="+56 9 8765 4321">
                    </div>
                    <div class="form-group">
                        <label class="form-check">
                            <input type="hidden" name="permite_whatsapp" value="0">
                            <input type="checkbox" name="permite_whatsapp" value="1" @checked(old('permite_whatsapp', true))>
                            Permitir contacto por WhatsApp
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dirección de atención</label>
                        <input type="text" name="contacto_direccion" value="{{ old('contacto_direccion') }}" class="form-input" placeholder="Comuna, ciudad o local">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Crear tienda</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.panel>
