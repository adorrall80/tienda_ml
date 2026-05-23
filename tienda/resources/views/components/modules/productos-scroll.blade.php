@if($productos->isNotEmpty())
<section class="section home-section home-section-scroll">
    <div class="container">
        <h2 class="section-title">
            <span>{{ $titulo }}</span>
            <a href="{{ $verTodosUrl }}">Ver todos</a>
        </h2>
        <div class="scroll-section">
            @foreach($productos as $producto)
                <x-modules.product-card :producto="$producto" />
            @endforeach
        </div>
    </div>
</section>
@endif
