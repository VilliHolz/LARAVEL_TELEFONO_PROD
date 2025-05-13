@section('title', 'Usuarios')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear usuarios')
                <button wire:click="create()" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus-circle"></i>
                    Nuevo
                </button>
            @endcan
            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif

            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($users->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Sucursal</th>
                                <th scope="col">Télefono</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha Registro</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->branch->name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @if ($loading)
                                            <span class="badge bg-secondary">Cargando...</span>
                                        @else
                                            <select wire:change="changeStatus({{ $user->id }}, $event.target.value)"
                                                class="form-control">
                                                <option value="Activo"
                                                    {{ $user->status === 'Activo' ? 'selected' : '' }}>Activo
                                                </option>
                                                <option value="Inactivo"
                                                    {{ $user->status === 'Inactivo' ? 'selected' : '' }}>Inactivo
                                                </option>
                                            </select>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        @can('asignar roles')
                                            <a href="{{ route('roles.assign', ['id' => encrypt($user->id)]) }}"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-user-shield"></i> Roles/Permisos
                                            </a>
                                        @endcan
                                        @can('actualizar usuarios')
                                            <button wire:click="edit({{ $user->id }})"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('eliminar usuarios')
                                            <button wire:click="confirmDelete({{ $user->id }})"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="mt-2">
                    {{ $users->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay usuarios</strong>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade @if ($isOpen) show d-block @endif" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-gray">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $user_id ? 'Editar Usuario' : 'Crear Usuario' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row">
                        <div class="form-group col-md-12">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control form-control-lg" id="name"
                                placeholder="Nombre" wire:model="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="branch">Sucursal</label>
                            <select id="branch" class="form-control form-control-lg" wire:model="branch_id">
                                <option value="">Seleccionar</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Correo</label>
                            <input type="text" class="form-control form-control-lg" id="email"
                                placeholder="Correo" wire:model="email">
                            @error('email')
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
                            <label for="password">Contraseña <span
                                    class="text-primary">{{ $opcional }}</span></label>
                            <input type="password" class="form-control form-control-lg" id="password"
                                placeholder="Contraseña" wire:model="password">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" class="form-control form-control-lg" id="password_confirmation"
                                placeholder="Confirmar Contraseña" wire:model="password_confirmation">
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        wire:click="closeModal()">Cancelar</button>
                    <button type="button" class="btn btn-outline-primary btn-sm"
                        wire:click="store()">Guardar</button>
                </div>
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
                        Livewire.dispatchTo('admin.user-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
