@section('title', 'Cajas')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear cajas')
                <button wire:click="create()" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus-circle"></i>
                    Abrir Caja
                </button>
            @endcan

            @can('reportes cajas')
                <a href="{{ route('exportBoxs') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel"></i> Reporte
                </a>
            @endcan

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif
            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($cashregisters->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Acciones</th>
                                <th scope="col">Fecha Apertura</th>
                                <th scope="col">Monto Inicial</th>
                                <th scope="col">Fecha Cierre</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Monto Final</th>
                                <th scope="col">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cashregisters as $cashregister)
                                <tr>
                                    <td>
                                        @can('crear cajas')
                                            <a href="{{ route('cashregister.close', Crypt::encrypt($cashregister->id)) }}"
                                                class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-lock"></i>
                                            </a>
                                        @endcan

                                        @if ($cashregister->status == 'Activo')
                                            @can('actualizar cajas')
                                                <button wire:click="edit({{ $cashregister->id }})"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endcan

                                            @can('eliminar cajas')
                                                <button wire:click="confirmDelete({{ $cashregister->id }})"
                                                    class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        @endif
                                    </td>
                                    <td>{{ $cashregister->start_date }}</td>
                                    <td>{{ $cashregister->initial_amount }}</td>
                                    <td>{{ $cashregister->end_date }}</td>
                                    <td>{{ $cashregister->user->name }}</td>
                                    <td>{{ $cashregister->final_amount }}</td>
                                    <td>{{ $cashregister->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="mt-2">
                    {{ $cashregisters->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay cajas</strong>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade @if ($isOpen) show d-block @endif" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content bg-gray">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $cash_register_id ? 'Editar Caja' : 'Crear Caja' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="store()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="start_date">Fecha Apertura</label>
                                <input type="date" class="form-control form-control-lg" id="start_date"
                                    placeholder="Nombre" wire:model="start_date">
                                @error('start_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label for="initial_amount">Monto Inicial</label>
                                <input type="number" step="0.01" min="0.01" class="form-control form-control-lg"
                                    id="initial_amount" placeholder="Código de Barras" wire:model="initial_amount">
                                @error('initial_amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            wire:click="closeModal()">Cancelar</button>
                        <button type="submit" class="btn btn-outline-primary btn-sm">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {

            Livewire.on('show-delete-confirmation', id => {
                Swal.fire({
                    title: 'Esta seguro de eliminar?',
                    text: "El carpeta se eliminará de forma permanente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, Eliminar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatchTo('admin.cash-register-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
