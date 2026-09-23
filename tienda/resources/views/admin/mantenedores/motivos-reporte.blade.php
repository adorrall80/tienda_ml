<x-layouts.panel title="Motivos de Reporte">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="dashboard-grid">
        <div class="p-card">
            <div class="p-card-header">
                <h2>Lista de motivos</h2>
            </div>
            <div class="p-card-body p-0">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reasons as $reason)
                        <tr>
                            <td>{{ $reason->id }}</td>
                            <td>
                                <form action="{{ route('admin.mantenedores.motivos-reporte.update', $reason) }}" method="POST" style="display:flex;gap:10px;">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="nombre" class="form-control" value="{{ $reason->nombre }}" required>
                            </td>
                            <td>
                                    <select name="activo" class="form-control">
                                        <option value="1" {{ $reason->activo ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ ! $reason->activo ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                            </td>
                            <td>
                                    <button type="submit" class="btn btn-primary" style="padding: 4px 10px; font-size:12px;">Guardar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding: 20px;">No hay motivos configurados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-card">
            <div class="p-card-header">
                <h2>Nuevo motivo</h2>
            </div>
            <div class="p-card-body">
                <form action="{{ route('admin.mantenedores.motivos-reporte.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Fraude" required>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Estado</label>
                        <select name="activo" class="form-control">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-actions" style="margin-top: 15px;">
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.panel>
