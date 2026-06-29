<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reservation Confirmation</title>
    <link href="{{ asset('css/details-property.css') }}" rel="stylesheet">
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; margin: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;">
        <tr style="background-color: #007BFF; color: white;">
            <td style="padding: 20px; text-align: center;">
                <h1 style="margin: 0;">Reservation Confirmation</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <h2 style="margin-top: 0;">Hello {{ $reservation->guest->name }},</h2>
                <p>We are pleased to confirm your reservation at <strong>{{ $reservation->property->title }}</strong>.</p>

                <table style="width: 100%; margin-top: 20px;">
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

                <p style="margin-top: 20px;">
                    The payment will be made in cash at the time of check-in. Please confirm your arrival and departure times.
                </p>

                <p style="color: #d9534f;"><strong>Important:</strong> You will receive an email the day before with the apartment information.</p>

                <p style="margin-top: 30px;">Thank you for choosing us. We look forward to welcoming you!</p>

                <p>Best regards,<br><strong>Reservations Team</strong></p>
            </td>
        </tr>
        <tr style="background-color: #f0f0f0;">
            <td style="text-align: center; padding: 20px;">
                <img src="cid:nameEMLBlack.png" alt="Logo" style="width: 250px; margin-bottom: 10px;">
                <p style="font-size: 12px; color: #888;">© 2025 Your Reservation Company. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>

</html>