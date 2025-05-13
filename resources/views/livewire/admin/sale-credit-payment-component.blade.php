@section('title', 'Historial Pagos')

<div>
    <div class="row shadow-sm">
        <div class="col-lg-12">
            <a class="btn btn-outline-primary btn-sm" href="{{ route('creditsales.index') }}"><i class="fas fa-list"></i>
                Créditos
            </a>

            @if (session()->has('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif

            <input type="text" class="form-control mt-3" placeholder="Buscar..." wire:model.live="search">

            @if ($payments->count())

                <div class="table-responsive">
                    <table class="table table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Acciones</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Crédito #</th>
                                <th scope="col">Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        <a wire:click="confirmDelete({{ $payment->id }})" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </td>
                                    <td>{{ $payment->date }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->credit_id }}</td>
                                    <td>{{ $payment->user->name ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-2">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="alert alert-warning mt-3" role="alert">
                    <strong>No hay pagos</strong>
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
                    title: 'Esta seguro de eliminar?',
                    text: "El pago será eliminado permanentemente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, Eliminar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatchTo('admin.sale-credit-payment-component', 'delete', {
                            valor: id
                        });
                    }
                })
            });
        });
    </script>
@endpush