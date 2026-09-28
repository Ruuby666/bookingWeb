@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} — {{ $title }} — BookingOcra</title>
    <link rel="stylesheet" href="{{ asset('css/error.css') }}">
</head>

<body>
    {{-- Deliberately not @include('components.header'): that partial checks
         Auth::check(), which needs the database. An error page — especially
         500/503 — must still render if the database itself is the problem. --}}
    <div id="error-header">
        <img src="/images/nameEMLWhite.png" alt="Enjoy Home Lanzarote">
    </div>

    <div class="error-page">
        <p class="error-code">{{ $code }}</p>
        <h1 class="error-title">{{ $title }}</h1>
        <p class="error-message">{{ $message }}</p>
        <a href="/" class="error-home-link">Back to homepage</a>
    </div>

    <div id="error-footer">
        <p>&copy; 2025 Enjoy Home Lanzarote. All Rights Reserved.</p>
    </div>
</body>

</html>
