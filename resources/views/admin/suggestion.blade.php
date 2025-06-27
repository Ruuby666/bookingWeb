<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reservations</title>
    <link href="{{ asset('css/sugestions.css') }}" rel="stylesheet">
</head>

<body>
    @include('components.header')

    <div class="main-container">
        <div class="content">
            <h2>Reserva en {{ $reservation->property->title }}</h2>
            <ul>
                <li><strong>Cliente: </strong> {{ $reservation->user->name }}</li>
                <li><strong>Email: </strong> {{ $reservation->user->email }}</li>
                <li><strong>Check-in: </strong> {{ $reservation->check_in }}</li>
                <li><strong>Check-out: </strong> {{ $reservation->check_out }}</li>
                <li><strong>Status: </strong> {{ $reservation->status }}</li>
                <li><strong>Notes: </strong> {{ $reservation->notes }}</li>
                <li><strong>Guests: </strong> {{ $reservation->guests }}</li>
                <li><strong>Total Price: </strong> €{{ number_format($reservation->total_price, 2)}}</li>
            </ul>
        </div>

        <div class="form-container">
            <h3>Enviar sugerencia</h3>
            <form method="POST" action="{{ route('reservations.sendSuggestion', $reservation->id) }}">
                @csrf
                <label for="note">Nota:</label>
                <textarea id="note" name="note" rows="5" required></textarea>
                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>


    @include('components.footer')
</body>

</html>