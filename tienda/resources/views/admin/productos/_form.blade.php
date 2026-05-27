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
    $costoEnvioValue = old('costo_envio', isset($producto) ? $formatPrice($producto->costo_envio) : '');
    $selectedDeliveryTypeIds = collect(old('delivery_type_ids', isset($producto) ? $producto->deliveryTypes->pluck('id')->all() : []))
        ->map(fn($id) => (int) $id)
        ->all();
    $attributeRows = old('atributos');
    if ($attributeRows === null) {
        $attributeRows = isset($producto)
            ? $producto->productAttributes->map(fn($attr) => ['nombre' => $attr->nombre, 'valor' => $attr->valor])->values()->all()
            : [];
    }
    if (empty($attributeRows)) {
        $attributeRows = [['nombre' => '', 'valor' => '']];
    }
@endphp

<div class="product-tabs" data-product-tabs>
    <div class="product-tab-list" role="tablist" aria-label="Secciones del producto">
        <button type="button" class="product-tab-btn active" data-product-tab="basicos" role="tab" aria-selected="true">Datos básicos</button>
        <button type="button" class="product-tab-btn" data-product-tab="imagenes" role="tab" aria-selected="false">Imágenes</button>
        <button type="button" class="product-tab-btn" data-product-tab="atributos" role="tab" aria-selected="false">Atributos</button>
    </div>

    <div class="product-tab-panel active" data-product-panel="basicos" role="tabpanel">
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
        <select name="estado_id" class="form-input" required>
            <option value="">— Seleccionar —</option>
            @foreach($productConditions as $condition)
                <option value="{{ $condition->id }}" @selected((int) old('estado_id', $producto->estado_id ?? '') === $condition->id)>{{ $condition->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Stock <span class="req">*</span></label>
        <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}" class="form-input" min="0" required>
    </div>

    <div class="form-group">
        <label class="form-check">
            <input type="hidden" name="envio_gratis" value="0">
            <input type="checkbox" name="envio_gratis" value="1" @checked(old('envio_gratis', $producto->envio_gratis ?? false))>
            Envío gratis
        </label>
    </div>

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Entrega</label>
        <div class="delivery-options">
            @foreach($deliveryTypes as $deliveryType)
                <label class="form-check">
                    <input type="checkbox" name="delivery_type_ids[]" value="{{ $deliveryType->id }}" @checked(in_array($deliveryType->id, $selectedDeliveryTypeIds, true))>
                    {{ $deliveryType->nombre }}
                </label>
            @endforeach
        </div>
        <small class="form-help">Opciones informativas para coordinar con el comprador. No generan pago ni integración automática.</small>
    </div>

    <div class="form-group">
        <label class="form-label">Costo envío estimado</label>
        <input type="text" name="costo_envio" value="{{ $costoEnvioValue }}" class="form-input" inputmode="numeric" pattern="[0-9.]*" placeholder="Ej: 3.000">
        <small class="form-help">Opcional. Déjalo vacío si se coordina directamente o si es gratis.</small>
    </div>

    <div class="form-group">
        <label class="form-label">Tiempo entrega</label>
        <input type="text" name="tiempo_entrega" value="{{ old('tiempo_entrega', $producto->tiempo_entrega ?? '') }}" class="form-input" maxlength="120" placeholder="Ej: 24 horas, 2 a 3 días, coordinar">
    </div>

    <div class="form-group">
        <label class="form-switch">
            <input type="hidden" name="destacado" value="0">
            <input type="checkbox" name="destacado" value="1" class="form-switch-input" @checked(old('destacado', $producto->destacado ?? false))>
            <span class="form-switch-slider" aria-hidden="true"></span>
            Producto destacado
        </label>
        <small class="form-help">Los destacados tienen prioridad visual en listados y módulos de productos.</small>
    </div>

    <div class="form-group">
        <label class="form-label">Estado publicación <span class="req">*</span></label>
        <select name="estado_publicacion_id" class="form-input" required>
            @foreach(\App\Models\Product::ESTADOS_PUBLICACION as $val => $label)
                <option value="{{ $val }}" @selected((int) old('estado_publicacion_id', $producto->estado_publicacion_id ?? \App\Models\Product::PUBLICACION_ACTIVO) === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <small class="form-help">Activo se muestra en la tienda. Pausado y vendido no aparecen en el listado público.</small>
    </div>

    <div class="form-group">
        <label class="form-label">Estado revisión <span class="req">*</span></label>
        <select name="estado_revision_id" class="form-input" required>
            @foreach(\App\Models\Product::ESTADOS_REVISION as $val => $label)
                <option value="{{ $val }}" @selected((int) old('estado_revision_id', $producto->estado_revision_id ?? \App\Models\Product::REVISION_APROBADO) === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <small class="form-help">Solo los productos aprobados se muestran públicamente.</small>
    </div>

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Motivo rechazo</label>
        <textarea name="motivo_rechazo" class="form-input" rows="3" maxlength="500">{{ old('motivo_rechazo', $producto->motivo_rechazo ?? '') }}</textarea>
        <small class="form-help">Usar solo si el producto queda rechazado. Si está pendiente o aprobado, se limpia automáticamente.</small>
    </div>

    <div class="form-group">
        <label class="form-switch">
            <input type="hidden" name="bloqueado" value="0">
            <input type="checkbox" name="bloqueado" value="1" class="form-switch-input" @checked(old('bloqueado', $producto->bloqueado ?? false))>
            <span class="form-switch-slider" aria-hidden="true"></span>
            Producto bloqueado
        </label>
        <small class="form-help">Un producto bloqueado no se muestra y el vendedor no puede modificarlo.</small>
    </div>

    <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Motivo bloqueo</label>
        <textarea name="motivo_bloqueo" class="form-input" rows="3" maxlength="500">{{ old('motivo_bloqueo', $producto->motivo_bloqueo ?? '') }}</textarea>
        <small class="form-help">Usar si el producto queda bloqueado. Si se desbloquea, se limpia automáticamente.</small>
    </div>
</div>
    </div>

    <div class="product-tab-panel" data-product-panel="imagenes" role="tabpanel">
        <div class="form-grid-2">
            <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Imagen principal</label>
                @if(isset($producto) && $producto->imagen)
                    <div class="product-image-current">
                        <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}">
                        <span>Imagen principal actual</span>
                    </div>
                @endif
                <input type="file" name="imagen_archivo" class="form-input" accept="image/jpeg,image/png,image/webp">
                <small class="form-help">Formatos: JPG, PNG o WebP. Máximo 4 MB.</small>
            </div>

            <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Galería de imágenes</label>
                @if(isset($producto) && $producto->images->isNotEmpty())
                    <div class="gallery-current">
                        @foreach($producto->images as $image)
                            <div class="gallery-current-item">
                                <button type="button" class="gallery-preview-btn" data-gallery-preview="{{ $image->imagen }}" data-gallery-title="{{ $producto->nombre }}">
                                    <img src="{{ $image->imagen }}" alt="{{ $producto->nombre }}">
                                </button>
                                <div class="gallery-order-actions">
                                    <button type="button" class="gallery-order-btn" data-gallery-order-url="{{ route('admin.productos.imagenes.orden', [$producto, $image]) }}" data-gallery-direction="up">Mover antes</button>
                                    <button type="button" class="gallery-order-btn" data-gallery-order-url="{{ route('admin.productos.imagenes.orden', [$producto, $image]) }}" data-gallery-direction="down">Mover después</button>
                                </div>
                                <div class="gallery-delete-wrap">
                                    <button type="button" class="gallery-delete-btn" data-gallery-delete-url="{{ route('admin.productos.imagenes.destroy', [$producto, $image]) }}">Eliminar</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="form-placeholder">Este producto aún no tiene imágenes de galería.</div>
                @endif
                <input type="file" name="galeria_archivos[]" class="form-input" accept="image/jpeg,image/png,image/webp" multiple>
                <small class="form-help">Puedes seleccionar varias imágenes. Se guardan en la galería del producto.</small>
            </div>
        </div>
    </div>

    <div class="product-tab-panel" data-product-panel="atributos" role="tabpanel">
        <div class="attributes-editor" data-attributes-editor>
            <div class="attributes-list" data-attributes-list>
                @foreach($attributeRows as $index => $row)
                    <div class="attribute-row" data-attribute-row>
                        <div class="form-group">
                            <label class="form-label">Nombre atributo</label>
                            <input type="text" name="atributos[{{ $index }}][nombre]" value="{{ $row['nombre'] ?? '' }}" class="form-input" maxlength="100" placeholder="Ej: Marca">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Valor</label>
                            <input type="text" name="atributos[{{ $index }}][valor]" value="{{ $row['valor'] ?? '' }}" class="form-input" maxlength="255" placeholder="Ej: Samsung">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline attribute-remove" data-attribute-remove>Quitar</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-sm btn-outline" data-attribute-add>Agregar atributo</button>
            <small class="form-help">Ejemplos: marca, modelo, color, talla, año, material. Se muestran en la ficha pública.</small>
        </div>
    </div>
</div>

<div class="image-modal" data-image-modal hidden>
    <div class="image-modal-backdrop" data-image-modal-close></div>
    <div class="image-modal-dialog" role="dialog" aria-modal="true" aria-label="Vista previa de imagen">
        <button type="button" class="image-modal-close" data-image-modal-close aria-label="Cerrar">×</button>
        <img src="" alt="" data-image-modal-img>
    </div>
</div>

<script>
const productGalleryCsrfToken = '{{ csrf_token() }}';

document.querySelectorAll('[data-gallery-order-url]').forEach((button) => {
    button.addEventListener('click', async () => {
        const item = button.closest('.gallery-current-item');
        const gallery = item?.closest('.gallery-current');
        const target = button.dataset.galleryDirection === 'up'
            ? item?.previousElementSibling
            : item?.nextElementSibling;

        if (!item || !gallery || !target) return;

        item.classList.add('is-updating');

        try {
            const response = await fetch(button.dataset.galleryOrderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': productGalleryCsrfToken,
                    'Accept': 'text/html',
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    direction: button.dataset.galleryDirection,
                }),
            });

            if (!response.ok) throw new Error('No se pudo ordenar la imagen.');

            if (button.dataset.galleryDirection === 'up') {
                gallery.insertBefore(item, target);
            } else {
                gallery.insertBefore(target, item);
            }
        } catch (error) {
            alert(error.message);
        } finally {
            item.classList.remove('is-updating');
            refreshGalleryOrderButtons(gallery);
        }
    });
});

document.querySelectorAll('[data-gallery-delete-url]').forEach((button) => {
    button.addEventListener('click', async () => {
        if (!confirm('¿Eliminar esta imagen de galería?')) return;

        const item = button.closest('.gallery-current-item');
        const gallery = item?.closest('.gallery-current');
        button.disabled = true;

        try {
            const response = await fetch(button.dataset.galleryDeleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': productGalleryCsrfToken,
                    'Accept': 'text/html',
                },
                body: JSON.stringify({ _method: 'DELETE' }),
            });

            if (!response.ok) throw new Error('No se pudo eliminar la imagen.');

            item?.remove();
            refreshGalleryOrderButtons(gallery);
        } catch (error) {
            alert(error.message);
            button.disabled = false;
        }
    });
});

function refreshGalleryOrderButtons(gallery) {
    if (!gallery) return;

    const items = Array.from(gallery.querySelectorAll('.gallery-current-item'));
    items.forEach((item, index) => {
        const up = item.querySelector('[data-gallery-direction="up"]');
        const down = item.querySelector('[data-gallery-direction="down"]');
        if (up) up.disabled = index === 0;
        if (down) down.disabled = index === items.length - 1;
    });
}

document.querySelectorAll('.gallery-current').forEach(refreshGalleryOrderButtons);

document.querySelectorAll('[data-gallery-preview]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = document.querySelector('[data-image-modal]');
        const image = modal?.querySelector('[data-image-modal-img]');
        if (!modal || !image) return;

        image.src = button.dataset.galleryPreview;
        image.alt = button.dataset.galleryTitle || 'Imagen de galería';
        modal.hidden = false;
        document.body.classList.add('image-modal-open');
    });
});

document.querySelectorAll('[data-image-modal-close]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = button.closest('[data-image-modal]');
        const image = modal?.querySelector('[data-image-modal-img]');
        if (!modal || !image) return;

        modal.hidden = true;
        image.src = '';
        document.body.classList.remove('image-modal-open');
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('[data-image-modal]').forEach((modal) => {
        if (modal.hidden) return;
        modal.hidden = true;
        modal.querySelector('[data-image-modal-img]').src = '';
        document.body.classList.remove('image-modal-open');
    });
});

document.querySelectorAll('[data-product-tabs]').forEach((tabs) => {
    const buttons = tabs.querySelectorAll('[data-product-tab]');
    const panels = tabs.querySelectorAll('[data-product-panel]');
    const activate = (name) => {
        buttons.forEach((button) => {
            const active = button.dataset.productTab === name;
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.productPanel === name));
    };

    buttons.forEach((button) => button.addEventListener('click', () => activate(button.dataset.productTab)));

    tabs.closest('form')?.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const form = button.form;
            if (!form || form.checkValidity()) return;

            const invalid = form.querySelector(':invalid');
            const panel = invalid?.closest('[data-product-panel]');
            if (panel) activate(panel.dataset.productPanel);
            event.preventDefault();
            setTimeout(() => form.reportValidity(), 0);
        });
    });
});

document.querySelectorAll('[data-attributes-editor]').forEach((editor) => {
    const list = editor.querySelector('[data-attributes-list]');
    const add = editor.querySelector('[data-attribute-add]');
    const nextIndex = () => list.querySelectorAll('[data-attribute-row]').length;

    const refreshRemoveButtons = () => {
        const rows = list.querySelectorAll('[data-attribute-row]');
        rows.forEach((row) => {
            row.querySelector('[data-attribute-remove]').disabled = rows.length === 1;
        });
    };

    add?.addEventListener('click', () => {
        const index = nextIndex();
        const row = document.createElement('div');
        row.className = 'attribute-row';
        row.dataset.attributeRow = '';
        row.innerHTML = `
            <div class="form-group">
                <label class="form-label">Nombre atributo</label>
                <input type="text" name="atributos[${index}][nombre]" class="form-input" maxlength="100" placeholder="Ej: Marca">
            </div>
            <div class="form-group">
                <label class="form-label">Valor</label>
                <input type="text" name="atributos[${index}][valor]" class="form-input" maxlength="255" placeholder="Ej: Samsung">
            </div>
            <button type="button" class="btn btn-sm btn-outline attribute-remove" data-attribute-remove>Quitar</button>
        `;
        list.appendChild(row);
        refreshRemoveButtons();
    });

    list?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-attribute-remove]');
        if (!button || button.disabled) return;
        button.closest('[data-attribute-row]')?.remove();
        refreshRemoveButtons();
    });

    refreshRemoveButtons();
});

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
