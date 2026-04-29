@if($productos->isNotEmpty())
<section class="section">
    <div class="container">
        <h2 class="section-title">
            {{ $titulo }}
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
