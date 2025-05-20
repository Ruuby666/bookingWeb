<h2>Hola {{ $reservation->user->name }},</h2>

<p>Tenemos una sugerencia para tu reserva en <strong>{{ $reservation->property->title }}</strong>:</p>

<blockquote>
    {{ $note }}
</blockquote>

<h4>Detalles de tu reserva:</h4>
<ul>
    <li><strong>Check-in:</strong> {{ $reservation->check_in }}</li>
    <li><strong>Check-out:</strong> {{ $reservation->check_out }}</li>
</ul>

<p>Gracias por confiar en nosotros.</p>
