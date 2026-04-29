@if($categorias->isNotEmpty())
<section class="section">
    <div class="container">
        <h2 class="section-title">
            Categorías destacadas
            <a href="{{ route('productos.index') }}">Ver todas</a>
        </h2>
        <div class="categories-grid">
            @foreach($categorias as $cat)
            <a href="{{ route('productos.index', ['cat' => $cat->slug]) }}" class="category-card">
                <span class="category-icon">{{ $cat->icono }}</span>
                <span class="category-name">{{ $cat->nombre }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
