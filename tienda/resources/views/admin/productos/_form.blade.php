@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0;padding-left:16px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="form-grid-2">
    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Nombre <span class="req">*</span></label>
        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="form-input" required>
    </div>

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" rows="3" class="form-input">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label class="form-label">Categoría <span class="req">*</span></label>
        <select name="category_id" class="form-input" required>
            <option value="">— Seleccionar —</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $producto->category_id ?? '') == $cat->id)>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Tienda <span class="req">*</span></label>
        <select name="tienda_id" class="form-input" required>
            <option value="">— Seleccionar —</option>
            @foreach($tiendas as $t)
                <option value="{{ $t->id }}" @selected(old('tienda_id', $producto->tienda_id ?? '') == $t->id)>
                    {{ $t->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Precio <small class="text-muted">(dejar vacío si es regalo)</small></label>
        <input type="number" name="precio" id="campo_precio" value="{{ old('precio', $producto->precio ?? '') }}" class="form-input" min="0" step="1" placeholder="0">
    </div>

    <div class="form-group">
        <label class="form-label">Precio original (antes de descuento)</label>
        <input type="number" name="precio_original" id="campo_precio_original" value="{{ old('precio_original', $producto->precio_original ?? '') }}" class="form-input" min="0" step="1">
    </div>

    <script>
        document.getElementById('campo_precio').addEventListener('input', function () {
            if (this.value === '0' || this.value === '') {
                document.getElementById('campo_precio_original').value = this.value;
            }
        });
    </script>

    <div class="form-group">
        <label class="form-label">Estado del producto <span class="req">*</span></label>
        <select name="estado" class="form-input" required>
            <option value="">— Seleccionar —</option>
            @foreach(\App\Models\Product::ESTADOS as $val => $label)
                <option value="{{ $val }}" @selected(old('estado', $producto->estado ?? '') === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Stock <span class="req">*</span></label>
        <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}" class="form-input" min="0" required>
    </div>

    <div class="form-group">
        <label class="form-label">Cuotas</label>
        <input type="number" name="cuotas" value="{{ old('cuotas', $producto->cuotas ?? '') }}" class="form-input" min="1">
    </div>

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">URL imagen</label>
        <input type="url" name="imagen" value="{{ old('imagen', $producto->imagen ?? '') }}" class="form-input" placeholder="https://…">
    </div>

    <div class="form-group">
        <label class="form-check">
            <input type="hidden" name="envio_gratis" value="0">
            <input type="checkbox" name="envio_gratis" value="1" @checked(old('envio_gratis', $producto->envio_gratis ?? false))>
            Envío gratis
        </label>
    </div>

    <div class="form-group">
        <label class="form-check">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo ?? true))>
            Producto activo
        </label>
    </div>
</div>
