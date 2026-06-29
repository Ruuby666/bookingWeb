<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
        }

        .email-container {
            max-width: 700px;
            margin: 30px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .title {
            font-size: 22px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 22px 0;
        }

        .section-title {
            font-size: 16px;
            color: #111827;
            margin-bottom: 10px;
            border-left: 4px solid #3b82f6;
            padding-left: 10px;
        }

        p {
            font-size: 14px;
            line-height: 1.6;
            margin: 6px 0;
        }

        strong {
            color: #111827;
        }

        .property-table {
            width: 100%;
            border-collapse: collapse;
        }

        .property-table td {
            padding: 10px 6px;
            font-size: 14px;
            border-bottom: 1px solid #f1f1f1;
        }

        .price {
            font-weight: bold;
            color: #16a34a;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #f3f4f6;
            color: #374151;
        }

        .footer-note {
            margin-top: 25px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }

        .highlight-box {
            background: #f9fafb;
            padding: 10px;
            border-radius: 8px;
        }

    </style>
</head>

<body>

<div class="email-container">

    <h2 class="title">📩 Nueva reserva recibida</h2>

    <p class="subtitle">
        Se ha creado una nueva reserva en el sistema. Revisa los detalles a continuación.
    </p>

    <hr class="divider">

    {{-- CLIENTE --}}
    <h3 class="section-title">👤 Cliente</h3>

    <p><strong>Nombre:</strong> {{ $reservation->guest->name }}</p>
    <p><strong>Email:</strong> {{ $reservation->guest->email }}</p>
    <p><strong>Teléfono:</strong> {{ $reservation->guest->phone_number ?? 'N/A' }}</p>

    @if(!empty($reservation->notes))
        <div class="highlight-box">
            <strong>Notas:</strong>
            <p>{{ $reservation->notes }}</p>
        </div>
    @endif

    <hr class="divider">

    {{-- RESERVA --}}
    <h3 class="section-title">📅 Reserva</h3>

    <p><strong>ID:</strong> #{{ $reservation->id }}</p>

    <p>
        <strong>Fechas:</strong>
        {{ $reservation->check_in->format('d/m/Y') }}
        →
        {{ $reservation->check_out->format('d/m/Y') }}
    </p>

    <p>
        <strong>Huéspedes:</strong> {{ $reservation->guests }}
    </p>

    <p>
        <strong>Estado:</strong>
        <span class="status">{{ ucfirst($reservation->status) }}</span>
    </p>

    <hr class="divider">

    {{-- PROPIEDAD --}}
    <h3 class="section-title">🏠 Propiedad</h3>

    <table class="property-table">
        <tr>
            <td><strong>Nombre:</strong></td>
            <td>{{ $reservation->property->title }}</td>
        </tr>

        <tr>
            <td><strong>Ubicación:</strong></td>
            <td>{{ $reservation->property->location }}</td>
        </tr>

        <tr>
            <td><strong>Precio por noche:</strong></td>
            <td>{{ number_format($reservation->property->price_per_night, 2) }} €</td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ECONOMÍA --}}
    <h3 class="section-title">💰 Pago</h3>

    <p class="price">
        Total: {{ number_format($reservation->total_price, 2) }} €
    </p>

    <hr class="divider">

    <p class="footer-note">
        📬 Reserva generada automáticamente desde la plataforma.<br>
        Puedes gestionarla desde el panel de administración.
    </p>

</div>

</body>
</html>