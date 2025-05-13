@section('title', 'Historial Crédito Ventas')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            @can('crear ventas')
                <a class="btn btn-outline-primary btn-sm" href="{{ route('sales.index') }}"><i class="fas fa-plus-circle"></i>
                    Nueva venta
                </a>
            @endcan

            @can('pagos ventas a credito')
                <a class="btn btn-outline-info btn-sm" href="{{ route('sales.credit.payments.index') }}"><i
                        class="fas fa-eye"></i>
                    Ver Abonos
                </a>
            @endcan

            @if (session()->has('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif

            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($creditsales->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Acciones</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Abonos</th>
                                <th scope="col">Pendiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($creditsales as $credit)
                                <tr>
                                    <td>
                                        @can('pagos ventas a credito')
                                            <a onclick="abrirVentanaEmergente('{{ route('sale.credit.generate.ticket', Crypt::encrypt($credit->id)) }}')"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-print"></i>
                                            </a>

                                            <button wire:click="openModal({{ $credit->id }})"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-money-bill"></i> Abonar
                                            </button>
                                        @endcan

                                    </td>
                                    <td>{{ $credit->date }}</td>
                                    <td>{{ number_format($credit->amount, 2) }}</td>
                                    <td>{{ optional($credit->sale->contact)->name ?? '' }}</td>
                                    <td>
                                        @php
                                            $totalPayments = $credit->payments->sum('amount');
                                        @endphp
                                        {{ number_format($totalPayments, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $pending = $credit->amount - $totalPayments;
                                        @endphp
                                        {{ number_format($pending, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-2">
                    {{ $creditsales->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay créditos</strong>
                </div>
            @endif

        </div>
    </div>

    <div class="modal fade @if ($isOpen) show d-block @endif" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="abonoModalLabel">Agregar Abono</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="addAbono">
                        <div class="mb-3">
                            <label for="abonoAmount" class="form-label">Monto del Abono</label>
                            <input type="number" class="form-control" id="abonoAmount" wire:model="abonoAmount"
                                placeholder="Ingrese el monto del abono" required>
                            @error('abonoAmount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <input type="hidden" wire:model="creditSaleId">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="closeModal()">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm">Guardar Abono</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('ticket-generated', respuesta => {
                abrirVentanaEmergente(respuesta[0].url);
            });
        });
    </script>
@endpush
