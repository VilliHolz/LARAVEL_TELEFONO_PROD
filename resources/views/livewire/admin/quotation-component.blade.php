@section('title', 'Nueva cotización')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @if (session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>

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
            <h4>Carrito</h4>
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
                                            value="{{ $item['price'] }}" class="form-control form-control-sm w-100">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <h5 class="mt-3">Total: ${{ $total }}</h5>
                <button class="btn btn-success mt-2" wire:click="openModalProcesar()">Guardar
                    Cotización
                </button>
            @else
                <p>El carrito está vacío.</p>
            @endif
        </div>

    </div>

    <div class="modal fade @if ($isOpenProcesar) show d-block @endif" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Completar Cotización
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModalProcesar()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-2">
                            <div class="input-group">
                                <input class="form-control" type="text" wire:model="name_client"
                                    placeholder="Nombre de Cliente" disabled>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button"
                                        wire:click="openModalCliente()">Cliente</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-info text-center" role="alert">
                                <h5 class="mt-3">Total: ${{ $total }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" wire:click="closeModalProcesar()">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" wire:click="saveQuote()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade @if ($isOpenCliente) show d-block @endif" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Clientes
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModalCliente()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <input type="text" class="form-control mt-3" placeholder="Buscar..."
                        wire:model.live="searchCustomer">

                    @if ($contacts->count())
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Dirrecion</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contacts as $contact)
                                        <tr>
                                            <td>{{ $contact->name }}</td>
                                            <td>{{ $contact->phone }}</td>
                                            <td>{{ $contact->address }}</td>
                                            <td>
                                                <button wire:click="setClient({{ $contact->id }})"
                                                    class="btn btn-outline-primary btn-sm">
                                                    Seleccionar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2">
                            {{ $contacts->links() }}
                        </div>
                    @else
                        <div class="alert alert-warning mt-3" role="alert">
                            <strong>No hay datos</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('livewire:init', function() {
        Livewire.on('ticket-generated', respuesta => {
            abrirVentanaEmergente(respuesta[0].url);
        });
    });
</script>
