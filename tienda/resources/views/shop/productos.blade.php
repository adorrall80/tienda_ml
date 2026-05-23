<x-layouts.shop :title="$titulo . ' — TiendaMV'">

{{-- Page Header --}}
<div class="page-header">
    <div class="container">
        <p class="breadcrumb">
            <a href="{{ route('inicio') }}">Inicio</a>
            <span>›</span>
            @if($categoriaActual)
                <a href="{{ route('productos.index') }}">Productos</a>
                <span>›</span> {{ $categoriaActual->nombre }}
            @else
                Productos
            @endif
        </p>
        <h1>{{ $titulo }}</h1>
        <p class="results-count">
            {{ number_format($productos->total(), 0, ',', '.') }} resultados
        </p>
    </div>
</div>

{{-- Listing --}}
<div class="container">
    <div class="listing-layout">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="sidebar" aria-label="Filtros">
            <form method="GET" action="{{ route('productos.index') }}" id="filter-form">

                @if(request()->filled('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request()->filled('orden'))
                    <input type="hidden" name="orden" value="{{ request('orden') }}">
                @endif
                @if(request()->filled('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif

                {{-- Categorias --}}
                <div class="filter-box">
                    <h3>Categorías</h3>
                    <div class="filter-option">
                        <label>
                            <input type="radio" name="cat" value=""
                                   onchange="this.form.submit()"
                                   {{ !request('cat') ? 'checked' : '' }}>
                            Todos
                        </label>
                    </div>
                    @foreach($categorias as $cat)
                    <div class="filter-option">
                        <label>
                            <input type="radio" name="cat" value="{{ $cat->slug }}"
                                   onchange="this.form.submit()"
                                   {{ request('cat') === $cat->slug ? 'checked' : '' }}>
                            {{ $cat->nombre }}
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- Precio --}}
                <div class="filter-box">
                    <h3>Precio</h3>
                    <div class="price-inputs">
                        <input type="number" class="price-input" name="precio_min"
                               placeholder="Mín $" min="0"
                               value="{{ request('precio_min') }}">
                        <input type="number" class="price-input" name="precio_max"
                               placeholder="Máx $" min="0"
                               value="{{ request('precio_max') }}">
                    </div>
                    <button type="submit" class="apply-filter-btn">Aplicar</button>
                </div>

                {{-- Estado --}}
                <div class="filter-box">
                    <h3>Estado</h3>
                    <div class="filter-option">
                        <label>
                            <input type="radio" name="estado" value=""
                                   onchange="this.form.submit()"
                                   {{ !request('estado') ? 'checked' : '' }}>
                            Todos
                        </label>
                    </div>
                    @foreach(\App\Models\Product::ESTADOS as $valor => $label)
                    <div class="filter-option">
                        <label>
                            <input type="radio" name="estado" value="{{ $valor }}"
                                   onchange="this.form.submit()"
                                   {{ request('estado') === $valor ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- Envio --}}
                <div class="filter-box">
                    <h3>Envío</h3>
                    <div class="filter-option">
                        <label>
                            <input type="checkbox" name="envio_gratis" value="1"
                                   onchange="this.form.submit()"
                                   {{ request('envio_gratis') ? 'checked' : '' }}>
                            Solo con envío gratis
                        </label>
                    </div>
                </div>

            </form>
        </aside>

        {{-- ===== LISTING ===== --}}
        <div>

            {{-- Sort bar --}}
            <div class="sort-bar">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <button type="button" class="mobile-filter-btn" id="toggle-filters">
                        &#9881; Filtros
                    </button>
                    <div class="active-filters">
                        @if($categoriaActual)
                            <span class="filter-tag">
                                {{ $categoriaActual->nombre }}
                                <a href="{{ route('productos.index', request()->except(['cat', 'page'])) }}">×</a>
                            </span>
                        @endif
                        @if(request('envio_gratis'))
                            <span class="filter-tag">
                                Envío gratis
                                <a href="{{ route('productos.index', request()->except(['envio_gratis', 'page'])) }}">×</a>
                            </span>
                        @endif
                        @if(request('q'))
                            <span class="filter-tag">
                                "{{ request('q') }}"
                                <a href="{{ route('productos.index', request()->except(['q', 'page'])) }}">×</a>
                            </span>
                        @endif
                        @if(request('precio_min') || request('precio_max'))
                            <span class="filter-tag">
                                Precio filtrado
                                <a href="{{ route('productos.index', request()->except(['precio_min', 'precio_max', 'page'])) }}">×</a>
                            </span>
                        @endif
                        @if(request('estado') && array_key_exists(request('estado'), \App\Models\Product::ESTADOS))
                            <span class="filter-tag">
                                Estado: {{ \App\Models\Product::ESTADOS[request('estado')] }}
                                <a href="{{ route('productos.index', request()->except(['estado', 'page'])) }}">×</a>
                            </span>
                        @endif
                    </div>
                </div>

                <form method="GET" action="{{ route('productos.index') }}" class="sort-form">
                    @foreach(request()->except(['orden', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <span class="sort-label">Ordenar por:</span>
                    <select class="sort-select" name="orden" aria-label="Ordenar resultados" onchange="this.form.submit()">
                        <option value="relevante" {{ request('orden','relevante') === 'relevante' ? 'selected' : '' }}>Más relevantes</option>
                        <option value="precio_asc"  {{ request('orden') === 'precio_asc'  ? 'selected' : '' }}>Menor precio</option>
                        <option value="precio_desc" {{ request('orden') === 'precio_desc' ? 'selected' : '' }}>Mayor precio</option>
                        <option value="nuevos"      {{ request('orden') === 'nuevos'      ? 'selected' : '' }}>Más nuevos</option>
                        <option value="rating"      {{ request('orden') === 'rating'      ? 'selected' : '' }}>Mejor calificación</option>
                    </select>
                </form>

                <form method="GET" action="{{ route('productos.index') }}" class="sort-form">
                    @foreach(request()->except(['per_page', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <span class="sort-label">Mostrar:</span>
                    <select class="sort-select" name="per_page" aria-label="Productos por página" onchange="this.form.submit()">
                        <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage === 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
            </div>

            {{-- Grid --}}
            <div class="listing-grid">
                @forelse($productos as $producto)
                    <x-modules.product-card :producto="$producto" />
                @empty
                    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;">
                        <p style="font-size:18px;color:var(--text-muted);margin-bottom:12px;">
                            No encontramos productos con esos filtros.
                        </p>
                        <a href="{{ route('productos.index') }}">Ver todos los productos</a>
                    </div>
                @endforelse
            </div>

            {{-- Paginacion --}}
            @if($productos->hasPages())
            <nav class="pagination" aria-label="Paginación">
                @if($productos->onFirstPage())
                    <button class="page-btn" disabled aria-label="Página anterior">&#8249;</button>
                @else
                    <a href="{{ $productos->previousPageUrl() }}" class="page-btn" aria-label="Página anterior">&#8249;</a>
                @endif

                @php
                    $current = $productos->currentPage();
                    $last    = $productos->lastPage();
                    $from    = max(1, $current - 2);
                    $to      = min($last, $current + 2);
                @endphp

                @if($from > 1)
                    <a href="{{ $productos->url(1) }}" class="page-btn">1</a>
                    @if($from > 2)<span style="padding:0 4px;color:var(--text-muted)">…</span>@endif
                @endif

                @foreach($productos->getUrlRange($from, $to) as $page => $url)
                    @if($page == $current)
                        <button class="page-btn active" aria-current="page">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($to < $last)
                    @if($to < $last - 1)<span style="padding:0 4px;color:var(--text-muted)">…</span>@endif
                    <a href="{{ $productos->url($last) }}" class="page-btn">{{ $last }}</a>
                @endif

                @if($productos->hasMorePages())
                    <a href="{{ $productos->nextPageUrl() }}" class="page-btn" aria-label="Siguiente página">&#8250;</a>
                @else
                    <button class="page-btn" disabled aria-label="Siguiente página">&#8250;</button>
                @endif
            </nav>
            @endif

        </div>
    </div>
</div>

</x-layouts.shop>
