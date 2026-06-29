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

        <p><strong>Nombre:</strong> {{ $reservation->guest->name }}</p>
        <p><strong>Teléfono:</strong> {{ $reservation->guest->phone ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $reservation->guest->email ?? 'N/A' }}</p>

        @if (!empty($reservation->message))
        <p><strong>Mensaje:</strong> {{ $reservation->message }}</p>
        @endif

        @if (!empty($reservation->daterange))
        <p><strong>Fechas seleccionadas:</strong> {{ $reservation->daterange }}</p>
        @endif

        <hr class="divider">

        <h3 class="section-title">🏠 Detalles de la propiedad</h3>

        <table class="property-table">
            <tr>
                <td><strong>Nombre:</strong></td>
                <td>{{ $reservation->property->title }}</td>
            </tr>

            <tr>
                <td><strong>Ubicación:</strong></td>
                <td>{{ $reservation->property->location }}</td>
            </tr>

            @if (!empty($reservation->total_price))
            <tr>
                <td><strong>💰 Precio total:</strong></td>
                <td class="price-total">{{ number_format($reservation->total_price, 2) }} €</td>
            </tr>
            @endif
        </table>

        <hr class="divider">

        <p class="footer-note">
            📬 Gracias por usar nuestra plataforma. Puedes gestionar esta reserva desde el panel de administración.
        </p>
    </div>
</body>

</html>