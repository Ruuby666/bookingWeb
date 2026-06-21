<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de tu reserva</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f1de; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f1de; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#3d5a80; padding:28px 30px; text-align:center;">
                            <p style="margin:0; color:#ffffff; font-size:13px; letter-spacing:1px; text-transform:uppercase; opacity:0.85;">
                                Recordatorio de llegada
                            </p>
                            <h1 style="margin:8px 0 0; color:#ffffff; font-size:24px; font-weight:700;">
                                ¡Tu estancia empieza mañana!
                            </h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px 30px 10px;">
                            <p style="margin:0 0 18px; font-size:16px; color:#2b2b2b; line-height:1.6;">
                                Hola <strong>{{ $reservation->guest->name }}</strong>, te escribimos para recordarte
                                que tu llegada a <strong>{{ $reservation->property->title ?? 'tu alojamiento' }}</strong>
                                está prevista para <strong>mañana</strong>. Aquí tienes todo lo que necesitas para
                                llegar sin contratiempos.
                            </p>
                        </td>
                    </tr>

                    {{-- Reservation details card --}}
                    <tr>
                        <td style="padding:0 30px 24px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9f7f1; border-radius:10px; border:1px solid #ece7d9;">
                                <tr>
                                    <td style="padding:18px 20px; border-bottom:1px solid #ece7d9;">
                                        <p style="margin:0; font-size:13px; color:#3d5a80; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Propiedad</p>
                                        <p style="margin:4px 0 0; font-size:16px; color:#2b2b2b;">{{ $reservation->property->title ?? '' }}</p>
                                        <p style="margin:2px 0 0; font-size:14px; color:#6b6b6b;">{{ $reservation->property->location ?? '' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:18px 20px; border-bottom:1px solid #ece7d9;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%" style="vertical-align:top;">
                                                    <p style="margin:0; font-size:13px; color:#3d5a80; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Entrada</p>
                                                    <p style="margin:4px 0 0; font-size:16px; color:#2b2b2b;">{{ $reservation->check_in->format('d/m/Y') }}</p>
                                                    <p style="margin:2px 0 0; font-size:14px; color:#6b6b6b;">a partir de las {{ $reservation->check_in->format('H:i') }}</p>
                                                </td>
                                                <td width="50%" style="vertical-align:top;">
                                                    <p style="margin:0; font-size:13px; color:#3d5a80; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Salida</p>
                                                    <p style="margin:4px 0 0; font-size:16px; color:#2b2b2b;">{{ $reservation->check_out->format('d/m/Y') }}</p>
                                                    <p style="margin:2px 0 0; font-size:14px; color:#6b6b6b;">antes de las {{ $reservation->check_out->format('H:i') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%" style="vertical-align:top;">
                                                    <p style="margin:0; font-size:13px; color:#3d5a80; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Huéspedes</p>
                                                    <p style="margin:4px 0 0; font-size:16px; color:#2b2b2b;">{{ $reservation->guests }} {{ $reservation->guests == 1 ? 'persona' : 'personas' }}</p>
                                                </td>
                                                <td width="50%" style="vertical-align:top;">
                                                    <p style="margin:0; font-size:13px; color:#3d5a80; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Precio total</p>
                                                    <p style="margin:4px 0 0; font-size:16px; color:#e07a5f; font-weight:700;">{{ number_format($reservation->total_price, 2) }} €</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Google Maps CTA --}}
                    @if($reservation->property)
                    <tr>
                        <td style="padding:0 30px 28px; text-align:center;">
                            <a href="https://www.google.com/maps?q={{ $reservation->property->lat }},{{ $reservation->property->lng }}"
                                target="_blank"
                                style="display:inline-block; background-color:#e07a5f; color:#ffffff; text-decoration:none; font-size:15px; font-weight:700; padding:14px 32px; border-radius:8px;">
                                📍 Cómo llegar en Google Maps
                            </a>
                            <p style="margin:12px 0 0; font-size:13px; color:#9a9a9a;">
                                Te recomendamos revisar el tráfico antes de salir.
                            </p>
                        </td>
                    </tr>
                    @endif

                    {{-- Reminder note --}}
                    <tr>
                        <td style="padding:0 30px 28px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fdf3ef; border-left:4px solid #e07a5f; border-radius:6px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0; font-size:14px; color:#7a4a3a; line-height:1.6;">
                                            💶 <strong>Recuerda:</strong> el pago se realiza en efectivo en el momento del check-in.
                                            ¡Que tengas un viaje seguro y llegues sano y salvo!
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f0f0f0; padding:24px 30px; text-align:center;">
                            <img src="cid:nameEMLBlack.png" alt="Logo" style="width:160px; margin-bottom:10px;">
                            <p style="margin:0; font-size:13px; color:#7a7a7a;">
                                ¿Necesitas ayuda? Escríbenos a
                                <a href="mailto:enjoyhomelanzarote@gmail.com" style="color:#3d5a80; text-decoration:none;">enjoyhomelanzarote@gmail.com</a>
                            </p>
                            <p style="margin:8px 0 0; font-size:12px; color:#aaaaaa;">
                                © {{ date('Y') }} Enjoy Home Lanzarote. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>