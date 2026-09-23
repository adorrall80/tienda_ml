<span class="nav-section-label">Mi panel</span>
<a href="{{ route('vendedor.panel') }}" class="nav-item {{ request()->routeIs('vendedor.panel') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    Dashboard
</a>

<span class="nav-section-label">Catálogo</span>
<a href="{{ route('vendedor.productos.index') }}" class="nav-item {{ request()->routeIs('vendedor.productos.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3H8v4h8z"/></svg>
    Mis productos
</a>
<a href="{{ route('vendedor.pedidos.index') }}" class="nav-item {{ request()->routeIs('vendedor.pedidos.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2l1.5 3L6 8l-1.5-3z"/><path d="M18 2l1.5 3L18 8l-1.5-3z"/><path d="M4 10h16v10a2 2 0 01-2 2H6a2 2 0 01-2-2z"/><path d="M8 14h8"/><path d="M8 18h5"/></svg>
    Pedidos
</a>

<span class="nav-section-label">Configuración</span>
@if(auth()->user()->tienda)
<a href="{{ route('vendedor.tienda.edit') }}" class="nav-item {{ request()->routeIs('vendedor.tienda.edit') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
    Mi tienda
</a>
@else
<a href="{{ route('vendedor.tienda.create') }}" class="nav-item {{ request()->routeIs('vendedor.tienda.create') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
    Crear tienda
</a>
@endif
