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
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Crear tienda</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.panel>
