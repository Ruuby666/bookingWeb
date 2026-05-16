<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Enjoy Home Lanzarote</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f6f0; }
        .privacy-container {
            max-width: 860px;
            margin: 60px auto;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
        }
        h1 { color: #1E4D8C; margin-bottom: 10px; }
        h2 { color: #1E4D8C; margin-top: 40px; font-size: 18px; }
        p, li { color: #4A4A4A; line-height: 1.8; font-size: 15px; }
        ul { padding-left: 20px; }
        .last-updated { color: #888; font-size: 13px; margin-bottom: 40px; }
    </style>
</head>
<body>

@include('components.header')

<div class="privacy-container">
    <h1>Privacy Policy</h1>
    <p class="last-updated">Last updated: {{ date('F d, Y') }}</p>

    <p>
        Enjoy Home Lanzarote ("we", "our", or "us") is committed to protecting your personal data.
        This policy explains what information we collect, how we use it, and your rights under the
        General Data Protection Regulation (GDPR) and Spanish data protection law (LOPDGDD).
    </p>

    <h2>1. Who is responsible for your data?</h2>
    <p>
        <strong>Enjoy Home Lanzarote</strong><br>
        Email: enjoyhomelanzarote@gmail.com<br>
        Lanzarote, Canary Islands, Spain
    </p>

    <h2>2. What data do we collect?</h2>
    <ul>
        <li><strong>Name and surname</strong> — provided when making a booking request.</li>
        <li><strong>Email address</strong> — used to send booking confirmations and communications.</li>
        <li><strong>Phone number</strong> — provided when making a booking request.</li>
        <li><strong>Booking details</strong> — dates, number of guests, and total price.</li>
        <li><strong>Messages</strong> — any notes or requests you include in your booking form.</li>
    </ul>

    <h2>3. Why do we collect your data?</h2>
    <ul>
        <li>To process and manage your booking request.</li>
        <li>To communicate with you regarding your reservation.</li>
        <li>To send you pre-arrival information about your stay.</li>
        <li>To generate invoices when required.</li>
    </ul>
    <p>
        The legal basis for processing your data is the execution of a contract (Article 6.1.b GDPR),
        specifically the booking agreement between you and Enjoy Home Lanzarote.
    </p>

    <h2>4. How long do we keep your data?</h2>
    <p>
        We retain your personal data for as long as necessary to fulfill the purposes described above,
        and in any case for a minimum of 5 years to comply with Spanish tax and accounting obligations.
    </p>

    <h2>5. Do we share your data with third parties?</h2>
    <p>
        We do not sell or share your personal data with third parties for marketing purposes.
        Your data may be shared with:
    </p>
    <ul>
        <li>Our email provider, for sending booking-related communications.</li>
        <li>Public authorities, if required by law.</li>
    </ul>

    <h2>6. Your rights</h2>
    <p>Under the GDPR you have the right to:</p>
    <ul>
        <li><strong>Access</strong> — request a copy of the personal data we hold about you.</li>
        <li><strong>Rectification</strong> — request correction of inaccurate data.</li>
        <li><strong>Erasure</strong> — request deletion of your data ("right to be forgotten").</li>
        <li><strong>Restriction</strong> — request that we limit how we use your data.</li>
        <li><strong>Portability</strong> — receive your data in a structured, machine-readable format.</li>
        <li><strong>Objection</strong> — object to processing based on legitimate interests.</li>
    </ul>
    <p>
        To exercise any of these rights, contact us at
        <a href="mailto:enjoyhomelanzarote@gmail.com">enjoyhomelanzarote@gmail.com</a>.
        We will respond within 30 days. You also have the right to lodge a complaint with the
        Spanish Data Protection Authority (AEPD) at <a href="https://www.aepd.es" target="_blank">www.aepd.es</a>.
    </p>

    <h2>7. Cookies</h2>
    <p>
        This website uses only technical session cookies necessary for the operation of the booking
        system. No tracking or advertising cookies are used.
    </p>

    <h2>8. Changes to this policy</h2>
    <p>
        We may update this policy from time to time. The date at the top of this page will always
        reflect the latest version.
    </p>
</div>

@include('components.footer')

</body>
</html>