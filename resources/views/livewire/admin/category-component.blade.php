@section('title', 'Categorias')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear categorias')
                <button wire:click="create()" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Nuevo categoria
                </button>
            @endcan

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif
            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($categories->count())

                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        @can('actualizar categorias')
                                            <button wire:click="edit({{ $category->id }})"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('crear eliminar')
                                            <button wire:click="confirmDelete({{ $category->id }})"
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
                    {{ $categories->links() }}
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
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content bg-gray">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $category_id ? 'Editar categoria' : 'Crear categoria' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="name">Nombre del categoria</label>
                            <input type="text" class="form-control" id="name" placeholder="Nombre del categoria"
                                wire:model="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        wire:click="closeModal()">Cancelar</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" wire:click="store()">Guardar</button>
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
                        Livewire.dispatchTo('admin.category-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
