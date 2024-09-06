<!DOCTYPE html>
<html>

<head>
    <title>BookingOcra</title>
</head>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<body>

    <h1>Users</h1>
    <ul>
        @foreach($users as $user)
        <li>{{ $user['name'] }}</li>
        @endforeach
    </ul>

    <h1>Properties</h1>
    <ul>
        @foreach($properties as $property)
        <li>{{ $property['title'] }}</li>
        <img src="{{$property['image_url']}}" alt="Image not found">
        @endforeach
    </ul>

    <h1>Reservations</h1>
    <ul>
        @foreach($reservations as $reservation)
        <li>{{ $reservation['user_id']}} {{$reservation['property_id'] }} From: {{$reservation['check_in'] }} To: {{$reservation['check_out'] }}</li>
        @endforeach
    </ul>

    <h1>Map</h1>
    <div id="map" style=" width: 50%; height: 400px;"></div>

    <!-- Google Maps JavaScript API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('API_GOOGLE_MAPS_KEY')}}"></script>
    <script>
        function initMap() {
            let map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10, // Initial zoom level
                center: {
                    lat: 29.0669,
                    lng: -13.5900
                }, // Initial center coordinates
            });

            // Definir el array de marcadores con sus posiciones y títulos
            var markers = [
                { title: "Casa", lat: 29.009408, lng: -13.613061 },
                { title: "Fariones", lat: 28.922024, lng: -13.676059 },
                { title: "Playa Blanca", lat: 28.874166, lng: -13.825272 }
            ];

            // Iterar sobre el array de marcadores y añadirlos al mapa
            markers.forEach(function(markerInfo) {
                var pos = { lat: markerInfo.lat, lng: markerInfo.lng };
                var marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: markerInfo.title
                });
            });
        }
    </script>
    <!-- Cargar el script de Google Maps con la clave de API -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('API_GOOGLE_MAPS_KEY')}}&callback=initMap"></script>
</body>

</html>
