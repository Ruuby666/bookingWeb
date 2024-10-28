<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link href="{{ asset('css/details-property.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
        integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
</head>

<body>
    @include('components.header')
    <a href="{{ route('index') }}"><i class="fa fa-caret-left" aria-hidden="true"></i></a>
    <div class="container">
        <div class="content-grid">
            <!-- Detalles del Apartamento -->
            <div class="property-details">
                <h1 class="title">{{ $property->title }}</h1>

                <div class="location">
                    <i class="fas fa-map-marker-alt icon"></i>
                    <span>{{ $property->location }}</span>
                </div>
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-bed icon"></i>
                        <div class="bedrooms">
                            @php
                            $bedrooms = json_decode($property->bedrooms, true);
                            @endphp
                            @foreach ($bedrooms as $key => $bed)
                            <span>Bedroom {{ $key }}: {{ $bed }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bath icon"></i>
                        <span>{{ $property->bathrooms }} Bathrooms</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-ruler icon"></i>
                        <span>{{ $property->size }}</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-user  icon"></i>
                        <span>{{ $property->capacity }} Guests</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-dollar-sign icon"></i>
                        <span>€{{ $property->price_per_night }} per night</span>
                    </div>
                </div>
                <div class="description">
                    <h2>Property Description</h2>
                    <p>{!! nl2br(e($property->description)) !!}</p> {{-- Its like that for the text spaces --}}
                </div>

                <div class="extra-features">
                    <h5>Additional Features</h5>
                    <ul>
                        @if ($property->parking)
                        <li><i class="fas fa-parking"></i><strong>Free Parking Spot</strong></li>
                        @endif
                        @if ($property->pool)
                        <li><i class="fas fa-swimming-pool"></i><strong>Pool</strong></li>
                        @endif
                        @if ($property->garden)
                        <li><i class="fas fa-tree"></i><strong>Garden</strong></li>
                        @endif
                        @if ($property->safeBox)
                        <li><i class="fas fa-lock"></i><strong>Safe Box</strong></li>
                        @endif
                        @if ($property->terrace)
                        <li><i class="fas fa-umbrella-beach"></i><strong>Terrace</strong></li>
                        @endif
                        @if ($property->wifi)
                        <li><i class="fas fa-wifi"></i><strong>Free Wi-Fi</strong></li>
                        @endif
                        @if (!empty($property->tv))
                        <li><i class="fas fa-tv"></i><strong>TV:</strong> {{ $property->tv }}</li>
                        @endif
                    </ul>
                </div>

                <form class="contact-form" action="{{ route('send.email') }}" method="POST">
                    @csrf
                    @include('components.show-date-range')
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input id="name" name="name" type="text" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label for="number">Contact Number</label>
                        <input id="number" name="number" type="number" placeholder="Enter your phone number" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Enter your message"></textarea>
                    </div>
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <button type="submit">Send Your Request</button>
                </form>

            </div>

            <!-- Imágenes del Apartamento -->
            <div class="image-gallery">
                <div class="main-image">
                    <img src="{{ asset('images/' . $property->images_div . '/' . $mainImage) }}"
                        alt="Main Property Image" loading="lazy" onclick="openPopup('{{ $property->images_div . '/' . $mainImage }}')">
                </div>
                <div class="thumbnail-gallery">
                    @foreach ($imagesWithoutFirst as $image)
                    <img class="thumbnail" src="{{ asset('images/' . $property->images_div . '/' . $image) }}"
                        alt="Property Thumbnail" loading="lazy" onclick="openPopup('{{ $property->images_div . '/' . $image }}')">
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Popup para mostrar la imagen en grande -->
    <div id="imagePopup" class="popup" style="display:none;">
        <span class="close" onclick="closePopup()">&times;</span>
        <img class="popup-content" id="popupImage" src="" alt="Large Image">
    </div>
    <script>
        function openPopup(imageUrl) {
            document.getElementById("popupImage").src = "/images/" + imageUrl;
            document.getElementById("imagePopup").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("imagePopup").style.display = "none";
        }
    </script>


    @include('components.footer')

</body>

</html>