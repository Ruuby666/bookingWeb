<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/email-booking.css') }}">

</head>

<body>
    <div class="email-container">
        <h2 class="title">📩 Nueva reserva recibida</h2>

        <hr class="divider">

        <h3 class="section-title">👤 Datos del cliente</h3>
        <p><strong>Nombre:</strong> {{ $data['name'] }}</p>
        <p><strong>Teléfono:</strong> {{ $data['number'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        @if (!empty($data['message']))
        <p><strong>Mensaje:</strong> {{ $data['message'] }}</p>
        @endif
        @if (isset($data['daterange']))
        <p><strong>Fechas seleccionadas:</strong> {{ $data['daterange'] }}</p>
        @endif

        <hr class="divider">

        <h3 class="section-title">🏠 Detalles de la propiedad</h3>
        <table class="property-table">
            <tr>
                <td><strong>Nombre:</strong></td>
                <td>{{ $data['property']->title }}</td>
            </tr>
            <tr>
                <td><strong>Ubicación:</strong></td>
                <td>{{ $data['property']->location }}</td>
            </tr>
            @if (isset($data['total_price']))
            <tr>
                <td><strong>💰 Precio total:</strong></td>
                <td class="price-total">{{ number_format($data['total_price'], 2) }} €</td>
            </tr>
            @endif
        </table>

        <hr class="divider">

        <p class="footer-note">📬 Gracias por usar nuestra plataforma. Puedes gestionar esta reserva desde el panel de administración.</p>
    </div>
</body>

</html>