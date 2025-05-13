@section('title', 'Historial Orden')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear reparaciones')
                <a class="btn btn-outline-primary btn-sm" href="{{ route('repairs.index') }}"><i
                        class="fas fa-plus-circle"></i>
                    Nuevo
                </a>
            @endcan

            @can('reportes reparaciones')
                <a href="{{ route('exportRepairs') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel"></i> Reporte
                </a>
            @endcan

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif

            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($repairs->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Acciones</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Adelanto</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($repairs as $repair)
                                <tr>
                                    <td>
                                        @if ($repair->status != 'Cancelado')
                                            @can('actualizar reparaciones')
                                                <a href="{{ route('repairs.edit', Crypt::encrypt($repair->id)) }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('eliminar reparaciones')
                                                <button wire:click="confirmDelete({{ $repair->id }})"
                                                    class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan

                                            @can('repuestos reparaciones')
                                                <a href="{{ route('repairs.list', Crypt::encrypt($repair->id)) }}"
                                                    class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-tools"></i>
                                                </a>
                                            @endcan
                                        @endif

                                        <a onclick="abrirVentanaEmergente('{{ route('repairs.generate.ticket', Crypt::encrypt($repair->id)) }}')"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                    <td>{{ $repair->entry_date }}</td>
                                    <td>{{ $repair->total }}</td>
                                    <td>{{ $repair->advance }}</td>
                                    <td>{{ $repair->brand->name }}</td>
                                    <td>{{ optional($repair->contact)->name ?? '' }}</td>
                                    <td>{{ $repair->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="mt-2">
                    {{ $repairs->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay ordenes</strong>
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
                    text: "Se anulará la orden forma permanente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, Eliminar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatchTo('admin.repair-detail-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
