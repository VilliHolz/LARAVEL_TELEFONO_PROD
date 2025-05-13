<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Credito</title>
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
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAbonado = 0;
            @endphp

            @if (count($items) > 0)
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item['user'] }}</td>
                        <td>{{ $item['date'] }}</td>
                        <td>{{ number_format($item['price'], 2) }}</td>
                    </tr>
                    @php
                        $totalAbonado += $item['price'];
                    @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="3" style="text-align: center;">No se han realizado abonos aún.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="separator"></div>

    <div class="total">
        <p><strong>Total Abonado: {{ number_format($totalAbonado, 2) }}</strong></p>
        <p><strong>Total Pendiente: {{ number_format($total - $totalAbonado, 2) }}</strong></p>
        <p><strong>Total Crédito: {{ number_format($total, 2) }}</strong></p>
    </div>


    <div class="footer">
        <p class="thank-you">{{ $company['footer'] }}</p>
    </div>
</body>

</html>
