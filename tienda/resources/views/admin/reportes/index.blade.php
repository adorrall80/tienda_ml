<x-layouts.panel title="Reportes de Productos">
    <x-slot name="nav">@include('admin.partials.nav')</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="dashboard-grid">
        <div class="p-card" style="grid-column: span 12;">
            <div class="p-card-header">
                <h2>Reportes Recibidos</h2>
            </div>
            <div class="p-card-body p-0">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Motivo</th>
                            <th>Comentarios</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($report->product)
                                    <a href="{{ route('admin.productos.index') }}?buscar={{ $report->product->slug }}" target="_blank">
                                        {{ Str::limit($report->product->nombre, 30) }}
                                    </a>
                                @else
                                    <span class="text-muted">Producto eliminado</span>
                                @endif
                            </td>
                            <td>{{ $report->reason ? $report->reason->nombre : 'Otro' }}</td>
                            <td>{{ $report->comentarios ?? '-' }}</td>
                            <td>{{ $report->user ? $report->user->name : 'Desconocido' }}</td>
                            <td>
                                @if($report->estado === 'pendiente')
                                    <span class="badge badge-warning">Pendiente</span>
                                @else
                                    <span class="badge badge-success">Revisado</span>
                                @endif
                            </td>
                            <td>
                                @if($report->estado === 'pendiente')
                                    <form action="{{ route('admin.reportes.block', $report) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" style="padding: 4px 10px; font-size:12px;" onclick="return confirm('¿Seguro que quieres bloquear este producto?');">Bloquear Producto</button>
                                    </form>
                                    <form action="{{ route('admin.reportes.dismiss', $report) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size:12px;">Descartar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 20px;">No hay reportes.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div style="margin-top: 20px;">
        {{ $reports->links() }}
    </div>
</x-layouts.panel>
