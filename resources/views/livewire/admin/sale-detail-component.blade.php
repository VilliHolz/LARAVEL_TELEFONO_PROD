@section('title', 'Historial Ventas')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear ventas')
                <a class="btn btn-outline-primary btn-sm" href="{{ route('sales.index') }}"><i class="fas fa-plus-circle"></i>
                    Nuevo
                </a>
            @endcan

            @can('reportes ventas')
                <a href="{{ route('exportSales') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel"></i> Reporte
                </a>
            @endcan

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif

            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($sales->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Acciones</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Metodo</th>
                                <th scope="col">Forma Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>
                                        @can('anular ventas')
                                            <button wire:click="confirmDelete({{ $sale->id }})"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan

                                        <a onclick="abrirVentanaEmergente('{{ route('sale.generate.ticket', Crypt::encrypt($sale->id)) }}')"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                    <td>{{ optional($sale->contact)->name ?? '' }}</td>
                                    <td>
                                        @if ($sale->method == 'CONTADO')
                                            <span class="badge badge-success">CONTADO</span>
                                        @elseif($sale->method == 'CREDITO')
                                            <span class="badge badge-primary">CREDITO</span>
                                        @endif
                                    </td>

                                    <td>{{ optional($sale->paymentMethod)->name ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="mt-2">
                    {{ $sales->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay ventas</strong>
                </div>
            @endif

        </div>
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('show-delete-confirmation', id => {
                Swal.fire({
                    title: 'Esta seguro de anular?',
                    text: "Se anulará la venta forma permanente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, Eliminar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatchTo('admin.sale-detail-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
