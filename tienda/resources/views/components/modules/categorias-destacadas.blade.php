@if($categorias->isNotEmpty())
<section class="section home-section home-section-categories">
    <div class="container">
        <h2 class="section-title">
            <span>Categorías destacadas</span>
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
