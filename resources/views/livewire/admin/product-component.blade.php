@section('title', 'Productos')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear productos')
                <button wire:click="create()" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus-circle"></i>
                    Nuevo
                </button>
            @endcan

            @can('reportes productos')
                <a href="{{ route('exportProducts') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel"></i> Reporte
                </a>
            @endcan

            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif
            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($products->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Acciones</th>
                                <th scope="col">Imagen</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Barcode</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Categoría</th>
                                <th scope="col">Precio de Compra</th>
                                <th scope="col">Precio de Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        @can('actualizar productos')
                                            <button wire:click="edit({{ $product->id }})"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('eliminar productos')
                                            <button wire:click="confirmDelete({{ $product->id }})"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                    <td>
                                        @if ($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="Imagen del producto"
                                                width="100">
                                        @else
                                            <span>No hay imagen</span>
                                        @endif
                                    </td>

                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->barcode }}</td>
                                    <td>{{ $product->brand->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->purchase_price }}</td>
                                    <td>{{ $product->sale_price }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="mt-2">
                    {{ $products->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay productos</strong>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade @if ($isOpen) show d-block @endif" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-gray">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $product_id ? 'Editar Producto' : 'Crear Producto' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="store()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control form-control-lg" id="name"
                                    placeholder="Nombre" wire:model="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="barcode">Código de Barras</label>
                                <input type="text" class="form-control form-control-lg" id="barcode"
                                    placeholder="Código de Barras" wire:model="barcode">
                                @error('barcode')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="imei">IMEI</label>
                                <input type="text" class="form-control form-control-lg" id="imei"
                                    placeholder="IMEI" wire:model="imei">
                                @error('imei')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="purchase_price">Precio de Compra</label>
                                <input type="number" class="form-control form-control-lg" id="purchase_price"
                                    placeholder="Precio de Compra" wire:model="purchase_price">
                                @error('purchase_price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="sale_price">Precio de Venta</label>
                                <input type="number" class="form-control form-control-lg" id="sale_price"
                                    placeholder="Precio de Venta" wire:model="sale_price">
                                @error('sale_price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="min_stock">Stock Mínimo</label>
                                <input type="number" class="form-control form-control-lg" id="min_stock"
                                    placeholder="Stock Mínimo" wire:model="min_stock">
                                @error('min_stock')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="stock">Stock</label>
                                <input type="number" class="form-control form-control-lg" id="stock"
                                    placeholder="Stock" wire:model="stock">
                                @error('stock')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="brand">Marca</label>
                                <select id="brand" class="form-control form-control-lg" wire:model="brand_id">
                                    <option value="">Seleccionar</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category">Categoría</label>
                                <select id="category" class="form-control form-control-lg" wire:model="category_id">
                                    <option value="">Seleccionar</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label for="image">Imagen (Opcional)</label>
                                <input type="file" class="form-control form-control-lg" id="image"
                                    wire:model="image">
                                @error('image')
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
                        Livewire.dispatchTo('admin.product-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush
