<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Añadir estilos personalizados aquí */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Actualización de Estado</h1>
        </div>
        <div class="content">
            <p>Hola {{ $user->name }},</p>
            <p>Tu estado de cuenta ha sido actualizado a <strong>{{ $user->status }}</strong>.</p>
            <p>Gracias por tu atención.</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Con️♥️♥️  de nupzen. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>