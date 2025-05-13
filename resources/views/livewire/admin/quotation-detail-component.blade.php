@section('title', 'Historial Cotizaciones')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear cotizaciones')
                <a class="btn btn-outline-primary btn-sm" href="{{ route('quotes.index') }}"><i class="fas fa-plus-circle"></i>
                    Nuevo
                </a>
            @endcan

            @can('reportes cotizaciones')
                <a href="{{ route('exportQuotes') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel"></i> Reporte
                </a>
            @endcan

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif

            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($quotes->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Acciones</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quotes as $quote)
                                <tr>
                                    <td>
                                        @can('crear ventas')
                                            <a href="{{ route('sales.index', Crypt::encrypt($quote->id)) }}" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-cash-register"></i>
                                            </a>
                                        @endcan

                                        @can('eliminar cotizaciones')
                                            <button wire:click="confirmDelete({{ $quote->id }})"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan

                                        <a onclick="abrirVentanaEmergente('{{ route('quotes.generate.ticket', Crypt::encrypt($quote->id)) }}')"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                    <td>{{ $quote->date }}</td>
                                    <td>{{ $quote->total }}</td>
                                    <td>{{ optional($quote->contact)->name ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="mt-2">
                    {{ $quotes->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay cotizaciones</strong>
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
                        Livewire.dispatchTo('admin.quotation-detail-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
