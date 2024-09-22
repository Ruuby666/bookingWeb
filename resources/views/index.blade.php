<!DOCTYPE html>
<html>

<head>
    <title>BookingOcra</title>
</head>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
    integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<body>

    @include('components.header')

    <h1 id="properties-section">Properties</h1>

    @include('components.date-range')

    <div class="container" id="available-properties">
        @foreach ($properties as $property)
        <div class="cardcontainer">

                <div class="photo">
                    <img src="/images/{{$property['image_url']}}" alt="Image not found"style="height: 200px; width: 300px;">
                </div>
                <div class="content">
                    <p class="txt4">{{ $property['title'] }}</p>
                    <p class="txt5">{{ $property['location'] }}</p>
                    <p class="txt2">{{ $property['description'] }}</p>
                </div>
                <div class="footer">
                    <p><a class="waves-effect waves-light btn" href="/property/{{ $property['id'] }}">Read More</a><a
                            id="heart"><span class="like"><i class="fab fa-gratipay"></i>Like</span></a></p>
                </div>

        </div>
        @endforeach
    </div>

    <h1 id="map-section">Map</h1>
    <div id="map"></div>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Google Maps JavaScript API -->
    <script>
        let markers = @json($properties);

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
                let position = {
                    lat: parseFloat(markerInfo.lat),
                    lng: parseFloat(markerInfo.lng)
                };
                if (markerInfo.id == 2) {

                    const icon = document.createElement("div");
                    icon.innerHTML = '<i class="fa fa-home fa-lg fa-spin"></i>';
                    const faPin = new google.maps.marker.PinElement({
                        glyph: icon,
                        glyphColor: "#000000",
                        background: "#FFD514",
                        borderColor: "#ff8300",
                    });
                    content = faPin.element;

                } else if (markerInfo.id == 1) {

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
                    position: position,
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
                    <img src="/images/${property.image_url}" alt="${property.title}" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <div class="property-details">
                    <h3>${property.title}</h3>
                    <p>${property.description}</p>
                    <p><strong>Location:</strong> ${property.location}</p>
                    <p><strong>Price per Night:</strong> ${property.price_per_night}</p>
                    <p><strong>Capacity:</strong> ${property.capacity} people</p>
                    <a href="/property/${property.id}">
                        View Property Details
                    </a>

                </div>
                `;
            return content;
        }
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('API_GOOGLE_MAPS_KEY') }}&loading=async&callback=initMap&v=weekly&libraries=marker,core,places,routes,geocoding,geometry,elevation,drawing,visualization"
        async defer></script>
</body>

</html>
