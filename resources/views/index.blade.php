<!DOCTYPE html>
<html>

<head>
    <title>BookingOcra</title>
</head>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<body>

    <h1>Users</h1>
    <ul>
        @foreach ($users as $user)
            <li>{{ $user['name'] }}</li>
        @endforeach
    </ul>
    <h1>Properties</h1>
    <ul>
        @foreach ($properties as $property)
            <li>{{ $property['title'] }}</li>
            <img src="{{ $property['image_url'] }}" alt="Image not found">
        @endforeach
    </ul>

    <h1>Reservations</h1>
    <ul>
        @foreach ($reservations as $reservation)
            <li>{{ $reservation['user_id'] }} {{ $reservation['property_id'] }} From: {{ $reservation['check_in'] }} To:
                {{ $reservation['check_out'] }}</li>
        @endforeach
    </ul>

    <h1>Map</h1>
    <div id="map" style=" width: 50%; height: 400px;"></div>

    <!-- Google Maps JavaScript API -->
    <script>
        let markers = [{
                title: "Beautiful Beach House",
                description: "A stunning beach house with an amazing view.",
                location: "Santa Monica Beach",
                price_per_night: "$150",
                capacity: 6,
                image_url: "/images/house.png", // Adjust the path to your image
                position: {
                    lat: 29.009408,
                    lng: -13.613061
                }
            },
            {
                title: "Mountain Cabin",
                description: "A cozy cabin in the mountains.",
                location: "Mountain Range",
                price_per_night: "$100",
                capacity: 4,
                image_url: "/images/house2.png", // Adjust the path to your image
                position: {
                    lat: 28.922024,
                    lng: -13.676059
                }
            },
            {
                title: "City Apartment",
                description: "An apartment in the city center.",
                location: "City Center",
                price_per_night: "$200",
                capacity: 2,
                image_url: "/images/house3.png", // Adjust the path to your image
                position: {
                    lat: 28.874166,
                    lng: -13.825272
                }
            }
        ];

        async function initMap() {
            // Initialize the map
            let map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: {
                    lat: 29.0669,
                    lng: -13.5900
                },
                mapId: "af934e8f21fb7b29",
            });

            // Check if advanced markers are available
            map.addListener('mapcapabilities_changed', () => {
                const mapCapabilities = map.getMapCapabilities();
                if (!mapCapabilities.isAdvancedMarkersAvailable) {
                    console.log('Advanced markers are not available');
                }
            });

            // Iterar sobre el array de marcadores y añadirlos al mapa
            markers.forEach((markerInfo) => {
                let content = null;
                if (markerInfo.title == 'Mountain Cabin') {

                    const icon = document.createElement("div");
                    icon.innerHTML = '<i class="fa fa-home fa-lg fa-spin"></i>';
                    const faPin = new google.maps.marker.PinElement({
                        glyph: icon,
                        glyphColor: "#000000",
                        background: "#FFD514",
                        borderColor: "#ff8300",
                    });
                    content = faPin.element;

                } else if (markerInfo.title == 'City Apartment') {

                    let beachFlagImg = document.createElement("img");
                    beachFlagImg.src = "/images/check.png";
                    beachFlagImg.height = 20;
                    beachFlagImg.width = 20;
                    content = beachFlagImg;

                } else {
                    let pin = new google.maps.marker.PinElement({
                        glyphColor: "white",
                    });
                    content = pin.element;
                }

                var marker = new google.maps.marker.AdvancedMarkerElement({
                    position: markerInfo.position,
                    map: map,
                    title: markerInfo.title,
                    content: content, // Use content only if pinElement is not used
                });
                /*  //Puedo hacer que se guarde aqui la información
                marker.addListener('click', ({domEvent, latLng}) => {
                    const {target} = domEvent;
                    console.log(target);

                }); */

                // Crear la InfoWindow
                const infoWindow = new google.maps.InfoWindow({
                    content: buildContent(markerInfo),
                });

                // Mostrar el pop-up al hacer clic en el marcador
                marker.addListener("click", () => {
                    // Customize this part as needed for your application
                    if (currentInfoWindow) {
                        currentInfoWindow.close();
                    }
                    infoWindow.open(map, marker);
                    currentInfoWindow = infoWindow;
                });
            });
        }
        let currentInfoWindow = null;

        // Function to build content for InfoWindow
        function buildContent(property) {
            const content = document.createElement("div");

            content.classList.add("property");
            content.innerHTML = `
                <div class="property-image">
                    <img src="${property.image_url}" alt="${property.title}" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <div class="property-details">
                    <h3>${property.title}</h3>
                    <p>${property.description}</p>
                    <p><strong>Location:</strong> ${property.location}</p>
                    <p><strong>Price per Night:</strong> ${property.price_per_night}</p>
                    <p><strong>Capacity:</strong> ${property.capacity} people</p>
                </div>
                `;
            return content;
        }
    </script>
    <!-- Cargar el script de Google Maps con la clave de API -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('API_GOOGLE_MAPS_KEY') }}&loading=async&callback=initMap&v=weekly&libraries=marker,core,places,routes,geocoding,geometry,elevation,drawing,visualization"
        async defer></script>
</body>

</html>
