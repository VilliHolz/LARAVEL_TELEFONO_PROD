@section('title', 'Editar Orden')

<div>
    <div class="card">
        <div class="card-body shadow-sm">
            <h5 class="card-title">Editar Orden</h5>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if (session()->has('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                </div>

                <form wire:submit.prevent="saveUpdate">
                    <div class="row">
                        <!-- Modelo -->
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="model" wire:model="model"
                                placeholder="Modelo">
                            @error('model')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- IMEI -->
                        <div class="col-md-6 mb-3">
                            <label for="imei" class="form-label">IMEI</label>
                            <input type="text" class="form-control" id="imei" wire:model="imei"
                                placeholder="IMEI">
                            @error('imei')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fecha de entrada -->
                        <div class="col-md-6 mb-3">
                            <label for="entry_date" class="form-label">Fecha de Entrada</label>
                            <input type="date" class="form-control" id="entry_date" wire:model="entry_date">
                            @error('entry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fecha Prometida -->
                        <div class="col-md-6 mb-3">
                            <label for="promised_date" class="form-label">Fecha Prometida</label>
                            <input type="date" class="form-control" id="promised_date" wire:model="promised_date">
                            @error('promised_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Adelanto -->
                        <div class="col-md-4 mb-3">
                            <label for="advance" class="form-label">Adelanto</label>
                            <input type="number" step="0.01" class="form-control" id="advance"
                                wire:model="advance" placeholder="Adelanto">
                            @error('advance')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Clave -->
                        <div class="col-md-4 mb-3">
                            <label for="key" class="form-label">Clave</label>
                            <input type="text" class="form-control" id="key" wire:model="key"
                                placeholder="Clave">
                            @error('key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- PIN -->
                        <div class="col-md-4 mb-3">
                            <label for="pin" class="form-label">PIN</label>
                            <input type="text" class="form-control" id="pin" wire:model="pin"
                                placeholder="PIN">
                            @error('pin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Total -->
                        <div class="col-md-6 mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" step="0.01" class="form-control" id="total" wire:model="total"
                                placeholder="Total">
                            @error('total')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contacto -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Seleccionar Cliente</label>
                            <div class="input-group">
                                <input class="form-control" type="text" wire:model="name_client"
                                    placeholder="Nombre de Cliente" disabled>
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-lg" type="button"
                                        wire:click="openModalCliente()">Cliente</button>
                                </div>
                            </div>
                            @error('contact_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Marca -->
                        <div class="col-md-6 mb-3">
                            <label for="brand_id" class="form-label">Marca</label>
                            <select class="form-control form-control-lg" id="brand_id" wire:model="brand_id">
                                <option value="">Seleccionar Marca</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="col-md-6 mb-3">
                            <label for="observations" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observations" rows="3" wire:model="observations"
                                placeholder="Observaciones"></textarea>
                            @error('observations')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Botón de enviar -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
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
                        wire:model.live="search">

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
