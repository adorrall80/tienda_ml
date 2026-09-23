<span class="nav-section-label">Principal</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    Dashboard
</a>

<span class="nav-section-label">Catálogo</span>
<a href="{{ route('admin.productos.index') }}" class="nav-item {{ request()->routeIs('admin.productos.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3H8v4h8z"/></svg>
    Productos
</a>

<span class="nav-section-label">Usuarios</span>
<a href="{{ route('admin.usuarios.index') }}" class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
    Usuarios
</a>
<a href="{{ route('admin.tiendas.index') }}" class="nav-item {{ request()->routeIs('admin.tiendas.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
    Tiendas
</a>

<span class="nav-section-label">Operación</span>
<a href="{{ route('admin.pedidos.index') }}" class="nav-item {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16v16H4z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
    Pedidos
</a>

<span class="nav-section-label">Seguridad</span>
<a href="{{ route('admin.seguridad.palabras.index') }}" class="nav-item {{ request()->routeIs('admin.seguridad.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-5"/></svg>
    Palabras bloqueadas
</a>

<span class="nav-section-label">Mantenedores</span>
<a href="{{ route('admin.mantenedores.estados-producto.index') }}" class="nav-item {{ request()->routeIs('admin.mantenedores.estados-producto.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/></svg>
    Estados producto
</a>
<a href="{{ route('admin.mantenedores.estados-pedido.index') }}" class="nav-item {{ request()->routeIs('admin.mantenedores.estados-pedido.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16v16H4z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
    Estados pedido
</a>
<a href="{{ route('admin.mantenedores.tipos-entrega.index') }}" class="nav-item {{ request()->routeIs('admin.mantenedores.tipos-entrega.*') ? 'active' : '' }}">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h11v10H3z"/><path d="M14 11h3l4 4v2h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
    Tipos de entrega
</a>
