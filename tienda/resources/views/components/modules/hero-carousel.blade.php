@if($slides->isNotEmpty())
<section class="hero" aria-label="Ofertas destacadas">
    <div class="carousel" role="region" aria-roledescription="carrusel">
        <div class="carousel-track">
            @foreach($slides as $slide)
            @php $n = ($loop->index % 3) + 1; @endphp
            <div class="carousel-slide slide-{{ $n }}" aria-label="Slide {{ $loop->iteration }} de {{ $loop->count }}">
                <div class="slide-content">
                    @if($slide->badge)
                        <span class="badge">{{ $slide->badge }}</span>
                    @endif
                    <h2>{!! nl2br(e($slide->titulo)) !!}</h2>
                    @if($slide->subtitulo)
                        <p>{{ $slide->subtitulo }}</p>
                    @endif
                    @if($slide->precio)
                        <div class="slide-price">{{ $slide->precio }}</div>
                    @endif
                    <a href="{{ $slide->url ?? '#' }}" class="slide-btn">{{ $slide->btn_texto }}</a>
                </div>
                <img class="slide-image"
                     src="{{ $slide->imagen }}"
                     alt="{{ $slide->titulo }}"
                     loading="{{ $loop->first ? 'eager' : 'lazy' }}">
            </div>
            @endforeach
        </div>

        <button class="carousel-btn carousel-prev" aria-label="Slide anterior">&#8249;</button>
        <button class="carousel-btn carousel-next" aria-label="Siguiente slide">&#8250;</button>

        <div class="carousel-dots" role="tablist" aria-label="Slides">
            @foreach($slides as $slide)
                <button class="dot {{ $loop->first ? 'active' : '' }}"
                        role="tab"
                        aria-label="Slide {{ $loop->iteration }}"
                        data-slide="{{ $loop->index }}"></button>
            @endforeach
        </div>
    </div>
</section>
@endif
