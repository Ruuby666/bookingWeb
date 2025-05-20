<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Reserva</title>
</head>
<body>
    <h2>Hola {{ $reservation->user->name }},</h2>

    <p>Tu reserva en <strong>{{ $reservation->property->title }}</strong> ha sido confirmada.</p>

    <ul>
        <li><strong>Check-in:</strong> {{ $reservation->check_in }}</li>
        <li><strong>Check-out:</strong> {{ $reservation->check_out }}</li>
        <li><strong>Número de huéspedes:</strong> {{ $reservation->guests }}</li>
        <li><strong>Precio total:</strong> €{{ number_format($reservation->total_price, 2) }}</li>
    </ul>

    <p>Debera ingresar la mitad del importe total en el siguiente IBAN <b>ES984795487937549375</b> : {{ $reservation->total_price/2 }}</p>

    <p>Si no se ingresa en un plazo de 14 días quedará cancelado.</p>

    <p>Gracias por confiar en nosotros. ¡Te esperamos!</p>

    <p>Saludos,<br>Equipo de Reservas</p>
</body>
</html>
