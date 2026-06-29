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
                    <td class="pending-id">
                        {{ $reservation->id }}
                        <button onclick="openModal('{{ $reservation->id }}')"><b>ⓘ</b></button>
                        @if ($section['title'] == 'Confirmed Reservations' && $reservation->invoice == false) <input type="checkbox" value="{{ $reservation->id }}" class="reservation-checkbox"> @endif
                    </td>
                    <td class="pending-property">{{ $reservation->property->title }}</td>
                    <td class="pending-guest">{{ $reservation->guest->name }}</td>
                    <td class="pending-checkin">
                        {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y - H:i') }}
                    </td>
                    <td class="pending-checkout">
                        {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y - H:i') }}
                    </td>
                    <td class="pending-status">
                        <span class="status-badge status-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>
                    </td>
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

                        <div class="modal-header">
                            <h2>{{ $reservation->property->title }}</h2>

                            <span class="close"
                                onclick="closeModal('{{ $reservation->id }}')">
                                &times;
                            </span>
                        </div>

                        <div class="modal-body">

                            <ul class="reservation-info">
                                <li><strong>Client</strong><span>{{ $reservation->guest->name }}</span></li>

                                <li><strong>Email</strong><span>{{ $reservation->guest->email }}</span></li>

                                <li><strong>Phone</strong><span>{{ $reservation->guest->phone_number }}</span></li>

                                <li><strong>Check-in</strong><span>{{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y H:i') }}</span></li>

                                <li><strong>Check-out</strong><span>{{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y H:i') }}</span></li>

                                <li>
                                    <strong>Status</strong>

                                    <span class="status-badge status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </li>

                                <li><strong>Guests</strong><span>{{ $reservation->guests }}</span></li>

                                <li><strong>Total</strong><span>€{{ number_format($reservation->total_price,2) }}</span></li>

                                <li><strong>Notes</strong><span>{{ $reservation->notes ?: '-' }}</span></li>
                            </ul>

                            @if ($reservation->status == 'pending')
                            <div class="div-buttons">

                                <button
                                    class="mark-sugerencia-button"
                                    data-url="{{ route('suggestion.create', $reservation) }}"
                                    onclick="redirectFromButton(this)">
                                    Suggestion
                                </button>

                                <form action="{{ route('admin.reservations.pending.update',$reservation->id) }}"
                                    method="POST">
                                    @csrf

                                    <button class="mark-completed-button">
                                        Confirm
                                    </button>
                                </form>

                            </div>
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

    <button class="crear-factura-button" onclick="openFacturaModal()">Crear Facturas</button>

    <div id="modal-factura" class="modal hidden">
        <div class="modal-content">
            <span class="close" onclick="closeFacturaModal()">&times;</span>
            <h2>Reservas seleccionadas</h2>
            <ul id="selected-reservations-list"></ul>
            <input type="number" id="invoice-amount" placeholder="Número de la primera factura" required>
            <div class="div-buttons">
                <button class="mark-export-button" data-url="{{ route('admin.calendar.export-factura-excel') }}" onclick="redirectfacturaFromButton(this)">
                    Exportar a Exel
                </button>
            </div>
        </div>
    </div>

    <div id="session-data"
        data-session-lifetime="{{ config('session.lifetime') }}"
        data-redirect-url="{{ route('index') }}">
    </div>

    @include('components.footer')
    <script src="{{ asset('js/pending.js') }}"></script>

</body>

</html>