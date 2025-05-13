<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Orden</title>
    <style>
        {{ $css }}
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('assets/admin/images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="company-details">
        <p><strong>{{ $repair->branch->name }}</strong></p>
        <p>{{ $repair->branch->address }}</p>
        <p>{{ $repair->branch->phone }}</p>
    </div>

    <div class="content">
        <p><strong>Fecha:</strong> {{ $repair->created_at }}</p>
        <p><strong>Cliente:</strong> {{ $repair->contact->name }}</p>
        <p><strong>Marca:</strong> {{ $repair->brand->name }}</p>
        <p><strong>Modelo:</strong> {{ $repair->model }}</p>
        <p><strong>IMEI:</strong> {{ $repair->imei }}</p>
        <p><strong>Anticipo:</strong> {{ $repair->advance }}</p>
        <p><strong>Fecha prometida:</strong> {{ $repair->promised_date }}</p>
        <p><strong>Observaciones:</strong> {{ $repair->observations }}</p>
        <p><strong>Estado:</strong> {{ $repair->status }}</p>
    </div>

    <div class="separator"></div>

    <!-- Nueva sección: Detalle de repuestos -->
    <div class="repuestos">
        <p><strong>Detalle de Repuestos:</strong></p>
        @if ($repair->details->isNotEmpty())
            <table class="items">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($repair->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>${{ number_format($detail->price, 2) }}</td>
                            <td>${{ number_format($detail->quantity * $detail->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No se han agregado repuestos a esta reparación.</p>
        @endif
    </div>


    <div class="separator"></div>

    <div class="total">
        <p><strong>Total: {{ number_format($repair->total, 2) }}</strong></p>
        <p><strong>Pendiente: {{ number_format($repair->total - $repair->advance, 2) }}</strong></p>
    </div>

    <div class="footer">
        <p class="thank-you">{{ $repair->branch->message }}</p>
    </div>
</body>

</html>
