<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookingOcra — Vacation Rentals in Lanzarote</title>
    <meta name="description" content="Browse and book vacation rentals in Lanzarote, Canary Islands — apartments and villas with pool, wifi and free parking.">
    <link rel="canonical" href="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="BookingOcra — Vacation Rentals in Lanzarote">
    <meta property="og:description" content="Browse and book vacation rentals in Lanzarote, Canary Islands.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/logoEML.png') }}">
</head>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
    integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
<script src="https://unpkg.com/@googlemaps/markerclusterer@2.6.2/dist/index.min.js"
    integrity="sha384-EVwzhfwoZgEjEQ2ffmvGXq5cOevVMtRZTog22siWEV3jCmD0BahdPQHx8y/VIhbi" crossorigin="anonymous"></script>
{{-- jQuery, moment, and daterangepicker are loaded once by components.date-range below
     (via components.daterangepicker-assets) — nothing above this point needs them. --}}

<body>

    @include('components.header')

    @include('components.date-range', ['propertyWithImages' => $propertyWithImages])
    <h1 id="aveilable-title">Available Properties</h1>

    {{-- Loader component --}}
    <x-loader />

    <div id="carousel-container">
        <button class="prev">&#10094;</button>
        <div id="available-properties">
            @foreach ($properties as $property)
            <a href="/property/{{ $property['id'] }}">
                <div class="cardcontainer">
                    <div class="photo">
                        <img src="{{ Storage::url('images/' . $property['images_div'] . '/' . $propertyWithImages[$property['id']]) }}"
                            alt="{{ $property['title'] }}" style="height: 200px; width: 300px;">
                    </div>
                    <div class="content">
                        <p class="txt4">{{ $property['title'] }}</p>
                        <p class="txt5">{{ $property['location'] }}</p>
                        <p class="txt2">{{ $property['description'] }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <button class="next">&#10095;</button>
    </div>


    <h1 id="map-title">Map</h1>

    <div id="map"></div>

    <script>
        window.INDEX_CONFIG = {
            markers: @json($properties),
            propertyWithImages: @json($propertyWithImages),
        };
    </script>

    <script src="{{ asset('js/index.js') }}"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&loading=async&callback=initMap&v=weekly&libraries=marker,core,places,routes,geocoding,geometry,elevation,drawing,visualization"
        async defer></script>

    @include('components.footer')
</body>

</html>