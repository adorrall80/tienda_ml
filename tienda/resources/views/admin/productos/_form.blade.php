@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0;padding-left:16px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@php
    $formatPrice = fn($value) => $value !== null && $value !== '' ? number_format((int) $value, 0, ',', '.') : '';
    $precioValue = old('precio', isset($producto) ? $formatPrice($producto->precio) : '');
    $precioOfertaValue = old('precio_oferta', isset($producto) ? $formatPrice($producto->precio_oferta) : '');
@endphp

<div class="form-grid-2">
    <div class="form-group" style="grid-column:1/-1">
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

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Nombre <span class="req">*</span></label>
        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="form-input" required>
    </div>

    <div class="form-group">
        <label class="form-label">Código producto / SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $producto->sku ?? '') }}" class="form-input" maxlength="50" placeholder="Ej: 000001, SL00998">
        <small class="form-help">Código interno opcional para identificar el producto.</small>
    </div>

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Descripción corta</label>
        <input type="text" name="descripcion_corta" value="{{ old('descripcion_corta', $producto->descripcion_corta ?? '') }}" class="form-input" maxlength="180">
        <small class="form-help">Texto breve para tarjetas y listados. Máximo 180 caracteres.</small>
    </div>

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Descripción completa</label>
        <div class="html-toolbar" aria-label="Formato descripción">
            <button type="button" class="html-tool" data-html-command="bold">B</button>
            <button type="button" class="html-tool" data-html-command="italic">I</button>
            <button type="button" class="html-tool" data-html-command="underline">U</button>
            <button type="button" class="html-tool" data-html-command="insertUnorderedList">Lista</button>
        </div>
        <input type="hidden" name="descripcion" class="html-description-input" value="{{ old('descripcion', $producto->descripcion ?? '') }}">
        <div class="html-editor" contenteditable="true" role="textbox" aria-multiline="true">{!! old('descripcion', $producto->descripcion ?? '') !!}</div>
        <small class="form-help">Permite HTML básico: negrita, cursiva, subrayado y listas.</small>
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
        <label class="form-label">Precio normal <small class="text-muted">(dejar vacío si es regalo)</small></label>
        <input type="text" name="precio" id="campo_precio" value="{{ $precioValue }}" class="form-input" inputmode="numeric" pattern="[0-9.]*" placeholder="0">
    </div>

    <div class="form-group">
        <label class="form-label">Precio oferta</label>
        <input type="text" name="precio_oferta" id="campo_precio_oferta" value="{{ $precioOfertaValue }}" class="form-input" inputmode="numeric" pattern="[0-9.]*">
        <small class="form-help">Si completas este campo, este será el precio final de venta publicado.</small>
    </div>

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

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Imagen del producto</label>
        @if(isset($producto) && $producto->imagen)
            <div class="product-image-current">
                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}">
                <span>Imagen actual</span>
            </div>
        @endif
        <input type="file" name="imagen_archivo" class="form-input" accept="image/jpeg,image/png,image/webp">
        <small class="form-help">Formatos: JPG, PNG o WebP. Máximo 4 MB.</small>
    </div>

    <div class="form-group">
        <label class="form-check">
            <input type="hidden" name="envio_gratis" value="0">
            <input type="checkbox" name="envio_gratis" value="1" @checked(old('envio_gratis', $producto->envio_gratis ?? false))>
            Envío gratis
        </label>
    </div>

    <div class="form-group">
        <label class="form-switch">
            <input type="hidden" name="activo" value="0">
            <input class="form-switch-input" type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo ?? true))>
            <span class="form-switch-slider" aria-hidden="true"></span>
            <span>Producto activo</span>
        </label>
    </div>
</div>

<script>
document.querySelectorAll('.html-editor').forEach((editor) => {
    const group = editor.closest('.form-group');
    const input = group.querySelector('.html-description-input');
    const sync = () => input.value = editor.innerHTML.trim();

    editor.addEventListener('input', sync);
    editor.addEventListener('keyup', sync);
    editor.addEventListener('blur', sync);
    editor.addEventListener('paste', () => setTimeout(sync, 0));
    group.querySelectorAll('[data-html-command]').forEach((button) => {
        button.addEventListener('click', () => {
            editor.focus();
            document.execCommand(button.dataset.htmlCommand, false, null);
            sync();
        });
    });
    editor.closest('form')?.addEventListener('submit', sync);
});
document.addEventListener('submit', () => {
    document.querySelectorAll('.html-editor').forEach((editor) => {
        const input = editor.closest('.form-group')?.querySelector('.html-description-input');
        if (input) input.value = editor.innerHTML.trim();
    });
}, true);
</script>
