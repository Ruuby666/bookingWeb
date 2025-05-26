<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reservations</title>
    <link href="{{ asset('css/pending.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
</head>

<body>
    @include('components.header')

    @if (session('success'))
    <x-toast :message="session('success')" type="success" />
    @endif

    @if (session('error'))
    <x-toast :message="session('error')" type="error" />
    @endif

    @php
    $sections = [
    ['title' => 'Pending Reservations', 'data' => $pending, 'showAction' => true],
    ['title' => 'Confirmed Reservations', 'data' => $reservations, 'showAction' => true],
    ];
    @endphp

    @foreach ($sections as $section)
    <div class="reservations-container">
        <h1 class="pending-title">{{ $section['title'] }}</h1>

        @if ($section['data']->isEmpty())
        <p class="no-reservations">No {{ strtolower($section['title']) }} found.</p>
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
                    @if ($section['title'] == 'Pending Reservations') <th class="pending-table-header-item"></th> @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($section['data'] as $reservation)
                <tr class="pending-row">
                    <td class="pending-id">{{ $reservation->id }}
                        <button onclick="openModal('{{ $reservation->id }}')"><b>ⓘ</b></button>
                    </td>
                    <td class="pending-property">{{ $reservation->property->title }}</td>
                    <td class="pending-guest">{{ $reservation->user->name }}</td>
                    <td class="pending-checkin">
                        {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y - H:i') }}
                    </td>
                    <td class="pending-checkout">
                        {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y - H:i') }}
                    </td>
                    <td class="pending-status">{{ $reservation->status }}</td>
                    <td class="pending-guests">{{ $reservation->guests }}</td>
                    <td class="pending-total-price">€{{ number_format($reservation->total_price, 2) }}</td>
                    @if ($reservation->status == 'pending')
                    <td class="pending-action">
                        <form action="{{ route('admin.reservations.pending.update', $reservation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="mark-completed-button">Confirmed</button>
                        </form>
                    </td>
                    @endif
                </tr>

                <div id="modal-{{ $reservation->id }}" class="modal hidden">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('{{ $reservation->id }}')">&times;</span>
                        <h2>Reserva en {{ $reservation->property->title }}</h2>
                        <ul>
                            <li><strong>Cliente: </strong> {{ $reservation->user->name }}</li>
                            <li><strong>Check-in: </strong> {{ $reservation->check_in }}</li>
                            <li><strong>Check-out: </strong> {{ $reservation->check_out }}</li>
                            <li><strong>Status: </strong> {{ $reservation->status }}</li>
                            <li><strong>Notes: </strong> {{ $reservation->notes }}</li>
                            <li><strong>Guests: </strong> {{ $reservation->guests }}</li>
                            <li><strong>Total Price: </strong> €{{ number_format($reservation->total_price, 2)}}</li>
                        </ul>
                        <div class="div-buttons">
                            @if ($reservation->status == 'pending')
                                <button class="mark-suggestion-button" data-url="{{ route('suggestion.create', $reservation) }}" onclick="redirectFromButton(this)">
                                    Suggestion
                                </button>
                                <form action="{{ route('admin.reservations.pending.update', $reservation->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="mark-completed-button">Confirmed</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endforeach

    <div id="session-data"
        data-session-lifetime="{{ config('session.lifetime') }}"
        data-redirect-url="{{ route('index') }}">
    </div>

    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>
    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).classList.add('hidden');
        }

        function redirectFromButton(button) {
            const url = button.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            }
        }
    </script>

</body>

</html>