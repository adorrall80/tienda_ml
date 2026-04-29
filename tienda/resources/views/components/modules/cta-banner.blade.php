@props([
    'titulo'   => 'Publicá gratis en TiendaMV',
    'texto'    => 'Llega a millones de compradores en todo Chile. Sin comisiones en tu primera venta.',
    'btnTexto' => 'Empezar a vender',
    'btnUrl'   => '#',
])

<section class="section">
    <div class="container">
        <div class="cta-banner">
            <div class="cta-content">
                <h2>{{ $titulo }}</h2>
                <p>{{ $texto }}</p>
            </div>
            <a href="{{ $btnUrl }}" class="cta-btn">{{ $btnTexto }}</a>
        </div>
    </div>
</section>
