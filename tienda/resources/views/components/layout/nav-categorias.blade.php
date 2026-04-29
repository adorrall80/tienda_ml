<nav class="header-nav" aria-label="Categorías principales">
    <div class="container">
        <ul class="nav-list">
            @foreach($categorias as $cat)
                <li>
                    <a href="{{ route('productos.index', ['cat' => $cat->slug]) }}"
                       @class(['active' => request('cat') === $cat->slug])>
                        {{ $cat->nombre }}
                    </a>
                </li>
            @endforeach
            <li><a href="{{ route('productos.index', ['tag' => 'oferta']) }}">Ofertas del día</a></li>
        </ul>
    </div>
</nav>