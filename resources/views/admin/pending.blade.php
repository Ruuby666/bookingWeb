<!-- resources/views/admin/pending.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reservations</title>
    <link href="{{ asset('css/pending.css') }}" rel="stylesheet">
</head>

<body>
    @include('components.header')

    <div class="pending-container">
        <h1 class="pending-title">Pending Reservations</h1>

        @if ($pending->isEmpty())
            <p class="no-reservations">No pending reservations found.</p>
        @else
            <table class="pending-reservations-table">
                <thead>
                    <tr class="pending-table-header">
                        <th>Reservation ID</th>
                        <th class="pending-table-header-item">Property</th>
                        <th class="pending-table-header-item">Guest Name</th>
                        <th class="pending-table-header-item">Check-in</th>
                        <th class="pending-table-header-item">Check-out</th>
                        <th class="pending-table-header-item">Status</th>
                        <th class="pending-table-header-item">Guests</th>
                        <th class="pending-table-header-item">Total Price</th>
                        <th class="pending-table-header-item"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pending as $reservation)
                        <tr class="pending-row">
                            <td class="pending-id">{{ $reservation->id }}</td>
                            <td class="pending-property">{{ $reservation->property->title }}</td>
                            <td class="pending-guest">{{ $reservation->user->name }}</td>
                            <td class="pending-checkin">
                                {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }}</td>
                            <td class="pending-checkout">
                                {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</td>
                            <td class="pending-status">{{ $reservation->status }}</td>
                            <td class="pending-guests">{{ $reservation->guests }}</td>
                            <td class="pending-total-price">€{{ number_format($reservation->total_price, 2) }}</td>
                            <td class="pending-action">
                                <form action="{{ route('admin.reservations.pending.update', $reservation->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="mark-completed-button">Mark as Completed</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="reservations-container">
        <h1 class="pending-title">Confirmed Reservations</h1>

        @if ($reservations->isEmpty())
            <p class="no-reservations">No confirmed reservations found.</p>
        @else
            <table class="pending-reservations-table">
                <thead>
                    <tr class="pending-table-header">
                        <th>Reservation ID</th>
                        <th class="pending-table-header-item">Property</th>
                        <th class="pending-table-header-item">Guest Name</th>
                        <th class="pending-table-header-item">Check-in</th>
                        <th class="pending-table-header-item">Check-out</th>
                        <th class="pending-table-header-item">Status</th>
                        <th class="pending-table-header-item">Guests</th>
                        <th class="pending-table-header-item">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservations as $reservation)
                        <tr class="pending-row">
                            <td class="pending-id">{{ $reservation->id }}</td>
                            <td class="pending-property">{{ $reservation->property->title }}</td>
                            <td class="pending-guest">{{ $reservation->user->name }}</td>
                            <td class="pending-checkin">
                                {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }}</td>
                            <td class="pending-checkout">
                                {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</td>
                            <td class="pending-status">{{ $reservation->status }}</td>
                            <td class="pending-guests">{{ $reservation->guests }}</td>
                            <td class="pending-total-price">€{{ number_format($reservation->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div id="session-data" data-session-lifetime="{{ config('session.lifetime') }}"
        data-redirect-url="{{ route('index') }}">
    </div>

    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>
</body>

</html>
