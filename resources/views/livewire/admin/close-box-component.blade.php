@section('title', 'Cajas')

<div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Cerrar Caja</h5>
                <a class="btn btn-danger btn-sm" href="{{ route('cashregisters.index') }}">Regresar</a>
            </div>
            <hr>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Fecha Apertura
                    <span class="badge bg-secondary badge-pill">{{ $box->start_date }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Monto Inicial
                    <span class="badge bg-secondary badge-pill">{{ $box->initial_amount }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Usuario
                    <span class="badge bg-secondary badge-pill">{{ $box->user->name }}</span>
                </li>
            </ul>

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif

            <table class="table table-striped mb-3" style="width: 100%">
                <thead>
                    <tr>
                        <th>Ventas Contado</th>
                        <th>Abono Ventas</th>
                        <th>Taller</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${{ number_format($totalContado, 2) }}</td>
                        <td>${{ number_format($totalAbonos, 2) }}</td>
                        <td>${{ number_format($totalTaller, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <td colspan="2">Total + Monto Inicial</td>
                    <td>{{ number_format($totalContado + $totalAbonos + $totalTaller + $box->initial_amount, 2) }}</td>
                </tfoot>
            </table>

            @can('cerrar cajas')
                @if ($box->status == 'Activo')
                    <button class="btn btn-primary" type="button" wire:click="confirmClose()">Cerrar Caja</button>
                @endif
            @endcan

        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('show-close-confirmation', () => {
                Swal.fire({
                    title: 'Esta seguro de cerrar?',
                    text: "El caja se cerrará de forma permanentemente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, Cerrar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatchTo('admin.close-box-component', 'close');
                    }
                })
            });
        });
    </script>
@endpush
