@if ($paginator->hasPages())
<nav class="p-pagination">
    @if ($paginator->onFirstPage())
        <span class="p-page-btn disabled">← Anterior</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="p-page-btn">← Anterior</a>
    @endif

    <span class="p-page-info">Página {{ $paginator->currentPage() }}</span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="p-page-btn">Siguiente →</a>
    @else
        <span class="p-page-btn disabled">Siguiente →</span>
    @endif
</nav>
@endif
