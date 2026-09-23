@if($productos->isNotEmpty())
<section class="section home-section home-section-products">
    <div class="container">
        <h2 class="section-title">
            <span>{{ $titulo }}</span>
            <a href="{{ $verTodosUrl }}">Ver todas</a>
        </h2>
        <div class="products-grid">
            @foreach($productos as $producto)
                <x-modules.product-card :producto="$producto" />
            @endforeach
        </div>
    </div>
</section>
@endif
