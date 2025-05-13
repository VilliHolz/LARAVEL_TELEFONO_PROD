<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Compra</title>
    <style>
        {{ $css }}
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('assets/admin/images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="company-details">
        <p><strong>{{ $company['name'] }}</strong></p>
        <p>{{ $company['address'] }}</p>
        <p>{{ $company['phone'] }}</p>
    </div>

    <div class="content">
        <p><strong>Fecha:</strong> {{ $date }}</p>
        <p><strong>Cliente:</strong> {{ $customer }}</p>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['price'], 2) }}</td>
                    <td>{{ number_format($item['quantity'] * $item['price'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <div class="total">
        <p><strong>Total: {{ number_format($total, 2) }}</strong></p>
    </div>

    <div class="footer">
        <p class="thank-you">{{ $company['footer'] }}</p>
    </div>
</body>

</html>
