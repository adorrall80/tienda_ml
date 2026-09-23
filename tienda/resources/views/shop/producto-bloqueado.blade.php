<x-layouts.shop :title="'Producto Bloqueado — TiendaMV'">
    <div style="max-width: 800px; margin: 80px auto; padding: 40px 20px; text-align: center;">
        <svg width="80" height="80" fill="none" stroke="#e02424" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 20px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h1 style="font-size: 2rem; color: #111827; margin-bottom: 16px;">Producto no disponible</h1>
        <p style="font-size: 1.125rem; color: #4b5563; margin-bottom: 30px;">
            El producto <strong>{{ $producto->nombre }}</strong> ha sido bloqueado por no cumplir normativas del portal.
        </p>
        <a href="{{ route('productos.index') }}" style="display: inline-block; padding: 12px 24px; background-color: #fde047; color: #111827; text-decoration: none; font-weight: 600; border-radius: 8px;">
            Volver a la tienda
        </a>
    </div>
</x-layouts.shop>
