<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Información de tu reserva</title>
</head>
<body>
    <h2>¡Hola {{ $reservation->name }}!</h2>
    <p>Te recordamos que tu reserva está confirmada para mañana.</p>
    <ul>
        <li><strong>Propiedad:</strong> {{ $reservation->property->title ?? '' }}</li>
        <li><strong>Fecha de entrada:</strong> {{ $reservation->start_date }}</li>
        <li><strong>Fecha de salida:</strong> {{ $reservation->end_date }}</li>
        <li><strong>Adultos:</strong> {{ $reservation->adults }}</li>
        <li><strong>Niños:</strong> {{ $reservation->children }}</li>
    </ul>
    <p>¡Gracias por reservar con nosotros!</p>
</body>
</html>