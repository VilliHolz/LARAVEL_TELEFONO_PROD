@section('title', 'Repuestos')

<div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class=" d-flex justify-content-between align-items-center">
                <h5 class="card-title">Orden</h5>
                <a onclick="abrirVentanaEmergente('{{ route('repairs.generate.ticket', Crypt::encrypt($repair->id)) }}')"
                    class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-print"></i>
                </a>
            </div>
            <hr>

            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Fecha
                    <span class="badge bg-secondary badge-pill">{{ $repair->entry_date }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Modelo
                    <span class="badge bg-secondary badge-pill">{{ $repair->model }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Marca
                    <span class="badge bg-secondary badge-pill">{{ $repair->brand->name }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Cliente
                    <span class="badge bg-secondary badge-pill">{{ $repair->contact->name }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Costo Total
                    <span class="badge bg-secondary badge-pill">{{ number_format($repair->total, 2) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Adelanto
                    <span class="badge bg-secondary badge-pill">{{ number_format($repair->advance, 2) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Pendiente
                    <span
                        class="badge bg-secondary badge-pill">{{ number_format($repair->total - $repair->advance, 2) }}</span>
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center 
                    {{ $repair->status == 'Pendiente' ? 'bg-warning text-dark' : '' }}
                    {{ $repair->status == 'Entregado' ? 'bg-success text-white' : '' }}
                    {{ $repair->status == 'Cancelado' ? 'bg-danger text-white' : '' }}">
                    Estado
                    <div class="form-group">
                        <select class="form-control form-control-lg"
                            wire:change="changeStatus({{ $repair->id }}, $event.target.value)">
                            <option value="Pendiente" {{ $repair->status == 'Pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="Entregado" {{ $repair->status == 'Entregado' ? 'selected' : '' }}>Entregado
                            </option>
                            <option value="Cancelado" {{ $repair->status == 'Cancelado' ? 'selected' : '' }}>Cancelado
                            </option>
                        </select>
                    </div>
                </li>

            </ul>

            <h6 class="text-center">Agregar Repuestos</h6>
            <hr>

            @if (session()->has('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-md-7">
                    <strong>Productos</strong>
                    <input type="text" class="form-control mb-3" placeholder="Buscar..." wire:model.live="search">
                    <div class="table-responsive">
                        @if ($products->isEmpty())
                            <div class="alert alert-warning" role="alert">
                                No se encontraron productos.
                            </div>
                        @else
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>${{ number_format($product->sale_price, 2) }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm"
                                                    wire:click="addToCart({{ $product->id }})">
                                                    <i class="fas fa-add"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div>
                                {{ $products->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-5">
                    <h4>Repuestos</h4>
                    @if (!empty($cart))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart as $item)
                                        <tr>
                                            <td>
                                                <h6><b>{{ $item['quantity'] }}</b> x {{ $item['name'] }}</h6>
                                                <button class="btn btn-sm btn-success"
                                                    wire:click="incrementQuantity({{ $item['id'] }})">+</button>
                                                <button class="btn btn-sm btn-warning"
                                                    wire:click="decrementQuantity({{ $item['id'] }})">-</button>
                                                <button class="btn btn-sm btn-danger"
                                                    wire:click="removeFromCart({{ $item['id'] }})">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <input type="number" id="price-{{ $item['id'] }}"
                                                    wire:blur="updatePrice({{ $item['id'] }}, $event.target.value)"
                                                    value="{{ $item['price'] }}"
                                                    class="form-control form-control-sm w-100">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <h5 class="mt-3">Total: ${{ $total }}</h5>
                        @can('repuestos reparaciones')
                            @if ($repair->status == 'Pendiente')
                                <button class="btn btn-success mt-2" wire:click="save()">
                                    Guardar
                                </button>
                            @endif
                        @endcan
                    @else
                        <p>El carrito está vacío.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
