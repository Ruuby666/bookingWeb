<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property['title'] }}</title>
    <link href="{{ asset('css/propertyDetails.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>{{ $property['title'] }}</h1>
        <img src="/images/{{ $property['image_url'] }}" alt="{{ $property['title'] }}" width="400">
        <p><strong>Description:</strong> {{ $property['description'] }}</p>
        <p><strong>Location:</strong> {{ $property['location'] }}</p>
        <p><strong>Price per night:</strong> ${{ $property['price_per_night'] }}</p>
        <p><strong>Capacity:</strong> {{ $property['capacity'] }} people</p>
    </div>

    <a href="{{ route('index') }}">Back to all properties</a>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
