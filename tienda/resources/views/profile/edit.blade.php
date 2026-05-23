<x-layouts.shop title="Mi cuenta">

<div class="container" style="padding-top:28px; padding-bottom:48px;">

    {{-- Encabezado --}}
    <div class="cuenta-header">
        <div class="cuenta-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div>
            <div class="cuenta-nombre">{{ $user->name }}</div>
            <div class="cuenta-email">{{ $user->email }}</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="cuenta-tabs">
        <button class="cuenta-tab active" data-tab="compras">Mis compras</button>
        <button class="cuenta-tab" data-tab="perfil">Mis datos</button>
        <button class="cuenta-tab" data-tab="vender">Vender</button>
        <button class="cuenta-tab" data-tab="clave">Cambiar contraseña</button>
    </div>

    {{-- Tab: Mis compras --}}
    <div class="cuenta-panel active" id="tab-compras">
        <div class="cuenta-card cuenta-card-wide">
            @if($orders->isNotEmpty())
                <div class="compras-list">
                    @foreach($orders as $order)
                        <article class="compra-card">
                            <div class="compra-head">
                                <div>
                                    <strong>{{ $order->numero }}</strong>
                                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="compra-total">
                                    <span>Total solicitud</span>
                                    <strong>${{ number_format($order->total, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            <div class="compra-meta">
                                <span>{{ ucfirst($order->estado) }}</span>
                                <span>{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'producto' : 'productos' }}</span>
                                <span>{{ $order->items->pluck('tienda_nombre')->filter()->unique()->count() }} {{ $order->items->pluck('tienda_nombre')->filter()->unique()->count() === 1 ? 'tienda' : 'tiendas' }}</span>
                            </div>

                            <div class="compra-items">
                                @foreach($order->items->groupBy('tienda_id') as $items)
                                    <div class="compra-store">
                                        <strong>{{ $items->first()->tienda?->nombre ?: $items->first()->tienda_nombre ?: 'Tienda sin nombre' }}</strong>
                                        <ul>
                                            @foreach($items as $item)
                                                <li>
                                                    <span>{{ $item->producto_nombre }}</span>
                                                    <strong>{{ $item->cantidad }} x ${{ number_format($item->precio_unitario, 0, ',', '.') }}</strong>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>

                            <a href="{{ route('cuenta.compras.show', $order) }}" class="compra-detail-link">
                                Ver contacto de tiendas
                            </a>
                        </article>
                    @endforeach
                </div>
            @else
            <div class="compras-empty">
                <svg width="64" height="64" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                <p class="compras-empty-title">Aún no tienes compras</p>
                <p class="compras-empty-sub">Cuando realices un pedido, aparecerá aquí.</p>
                <a href="{{ route('productos.index') }}" class="btn-comprar">Ver productos</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Tab: Mis datos --}}
    <div class="cuenta-panel" id="tab-perfil">
        <div class="cuenta-card">
            <h2 class="cuenta-section-title">Información personal</h2>

            @if(session('status') === 'profile-updated')
                <div class="cuenta-alert cuenta-alert-ok">Datos actualizados correctamente.</div>
            @endif

            <form method="POST" action="{{ route('cuenta.perfil.update') }}" class="cuenta-form">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label class="form-label" for="name">Nombre completo</label>
                    <input id="name" type="text" name="name"
                           value="{{ old('name', $user->name) }}"
                           class="form-input @error('name') form-input-error @enderror"
                           required autocomplete="name">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           class="form-input @error('email') form-input-error @enderror"
                           required autocomplete="username">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-cuenta">Guardar cambios</button>
            </form>
        </div>
    </div>

    {{-- Tab: Vender --}}
    <div class="cuenta-panel" id="tab-vender">
        <div class="cuenta-card">
            @if($user->hasRole('vendedor'))
                <h2 class="cuenta-section-title">Tu perfil vendedor está activo</h2>
                <p class="cuenta-helper-text">Puedes comprar como cliente y vender desde tu tienda.</p>
                <a href="{{ route('vendedor.panel') }}" class="btn-cuenta cuenta-action-link">Ir a mi tienda</a>
            @else
                <h2 class="cuenta-section-title">Quiero vender</h2>
                <p class="cuenta-helper-text">Al activar vendedor mantendrás tus opciones de cliente y podrás crear una tienda para publicar productos.</p>
                <form method="POST" action="{{ route('cuenta.vendedor.activar') }}" class="cuenta-form">
                    @csrf
                    <button type="submit" class="btn-cuenta">Activar vendedor</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Tab: Cambiar contraseña --}}
    <div class="cuenta-panel" id="tab-clave">
        <div class="cuenta-card">
            <h2 class="cuenta-section-title">Cambiar contraseña</h2>

            @if(session('status') === 'password-updated')
                <div class="cuenta-alert cuenta-alert-ok">Contraseña actualizada correctamente.</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="cuenta-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">Contraseña actual</label>
                    <input id="current_password" type="password" name="current_password"
                           class="form-input @error('current_password', 'updatePassword') form-input-error @enderror"
                           autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Nueva contraseña</label>
                    <input id="password" type="password" name="password"
                           class="form-input @error('password', 'updatePassword') form-input-error @enderror"
                           autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-input"
                           autocomplete="new-password">
                </div>

                <button type="submit" class="btn-cuenta">Actualizar contraseña</button>
            </form>
        </div>
    </div>

</div>

<script>
document.querySelectorAll('.cuenta-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.cuenta-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.cuenta-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
    });
});

// Abrir tab correcto si hay errores de validación o status
@if($errors->updatePassword->any())
    document.querySelector('[data-tab="clave"]').click();
@elseif(session('status') === 'password-updated')
    document.querySelector('[data-tab="clave"]').click();
@elseif(session('status') === 'profile-updated' || $errors->any())
    document.querySelector('[data-tab="perfil"]').click();
@endif
</script>

</x-layouts.shop>
