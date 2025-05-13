@section('title', 'Sucursales')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear sucursales')
                <button wire:click="create()" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Nueva sucursal
                </button>
            @endcan

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif
            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($branches->count())

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Acciones</th>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Correo</th>
                                <th>Representante</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($branches as $index => $branch)
                                <tr>
                                    <td class="text-center">
                                        @can('actualizar sucursales')
                                            <button wire:click="edit({{ $branch->id }})"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('eliminar sucursales')
                                            <button wire:click="confirmDelete({{ $branch->id }})"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $branch->name }}</td>
                                    <td>{{ $branch->phone }}</td>
                                    <td>{{ $branch->address }}</td>
                                    <td>{{ $branch->email }}</td>
                                    <td>{{ $branch->representative }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $branch->status == 'Activo' ? 'bg-success' : ($branch->status == 'Inactivo' ? 'bg-danger' : '') }}">
                                            {{ $branch->status }}
                                        </span>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="mt-2">
                    {{ $branches->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay datos</strong>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade @if ($isOpen) show d-block @endif" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-gray">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $branch_id ? 'Editar sucursal' : 'Crear sucursal' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close">
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control form-control-lg" id="name"
                                    placeholder="Nombre" wire:model="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone">Teléfono</label>
                                <input type="text" class="form-control form-control-lg" id="phone"
                                    placeholder="Teléfono" wire:model="phone">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address">Dirección</label>
                                <input type="text" class="form-control form-control-lg" id="address"
                                    placeholder="Dirección" wire:model="address">
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Correo</label>
                                <input type="email" class="form-control form-control-lg" id="email"
                                    placeholder="Correo electrónico" wire:model="email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="message">Mensaje</label>
                                <textarea class="form-control form-control-lg" id="message" placeholder="Mensaje" wire:model="message"></textarea>
                                @error('message')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="representative">Representante</label>
                                <input type="text" class="form-control form-control-lg" id="representative"
                                    placeholder="Representante" wire:model="representative">
                                @error('representative')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Estado</label>
                                <select class="form-control form-control-lg" id="status" wire:model="status">
                                    <option value="">Seleccione</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                @error('status')
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
                    title: '¿Está seguro de eliminar?',
                    text: "La sucursal se eliminará de forma permanente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, Eliminar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatchTo('admin.branch-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
