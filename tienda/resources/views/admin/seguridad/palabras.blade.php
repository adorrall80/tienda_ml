<x-layouts.panel title="Palabras bloqueadas">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Agregar palabra o patron bloqueado</h3>
        </div>
        <div class="p-card-body">
            <form method="POST" action="{{ route('admin.seguridad.palabras.store') }}" class="search-form" style="align-items:flex-start">
                @csrf
                <div class="form-group" style="min-width:260px">
                    <label class="form-label" for="term">Palabra o patron</label>
                    <input id="term" name="term" class="form-input @error('term') form-input-error @enderror" value="{{ old('term') }}" placeholder="select">
                    @error('term')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:22px">Agregar</button>
            </form>
        </div>
    </div>

    <div class="p-card">
        <div class="p-card-header">
            <h3 class="p-card-title">Mantenedor</h3>
        </div>
        <table class="p-table">
            <thead>
                <tr><th>Palabra</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($terms as $term)
                    <tr>
                        <td>
                            <form id="term-form-{{ $term->id }}" method="POST" action="{{ route('admin.seguridad.palabras.update', $term) }}" class="search-form">
                                @csrf
                                @method('PUT')
                                <input name="term" class="form-input-sm" value="{{ $term->term }}">
                                <label class="form-check">
                                    <input type="checkbox" name="active" value="1" @checked($term->active)>
                                    Activa
                                </label>
                            </form>
                        </td>
                        <td>
                            <span class="badge {{ $term->active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $term->active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button type="submit" form="term-form-{{ $term->id }}" class="btn btn-sm btn-outline">Guardar</button>
                                <form method="POST" action="{{ route('admin.seguridad.palabras.destroy', $term) }}" onsubmit="return confirm('¿Eliminar esta palabra bloqueada?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-link-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-row">No hay palabras bloqueadas.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($terms->hasPages())
            <div class="pagination-wrap">{{ $terms->links() }}</div>
        @endif
    </div>
</x-layouts.panel>
