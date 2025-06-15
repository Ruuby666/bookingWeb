<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reservation Confirmation</title>
    <link rel="stylesheet" href="{{ asset('css/email-confirmation.css') }}">

</head>

<body >
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <h1 >Reservation Confirmation</h1>
            </td>
        </tr>
        <tr>
            <td>
                <h2>Hello {{ $reservation->user->name }},</h2>
                <p>We are pleased to confirm your reservation at <strong>{{ $reservation->property->title }}</strong>.</p>

                <table>
                    <tr>
                        <td><strong>Check-in:</strong></td>
                        <td>{{ $reservation->check_in }}</td>
                    </tr>
                    <tr>
                        <td><strong>Check-out:</strong></td>
                        <td>{{ $reservation->check_out }}</td>
                    </tr>
                    <tr>
                        <td><strong>Number of guests:</strong></td>
                        <td>{{ $reservation->guests }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total price:</strong></td>
                        <td>€{{ number_format($reservation->total_price, 2) }}</td>
                    </tr>
                </table>

                <p>
                    The payment will be made in cash at the time of check-in. Please confirm your arrival and departure times.
                </p>

                <p><strong>Important:</strong> You will receive an email the day before with the apartment information.</p>

                <p>Thank you for choosing us. We look forward to welcoming you!</p>

                <p>Best regards,<br><strong>Reservations Team</strong></p>
            </td>
        </tr>
        <tr>
            <td>
                <img src="cid:nameEMLBlack.png" alt="Logo">
                <p>© 2025 Your Reservation Company. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>

</html>