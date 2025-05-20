<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link href="{{ asset('css/details-property.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
        integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
</head>

<body>
    @include('components.header')
    <a href="{{ route('index') }}"><i class="fa fa-caret-left" aria-hidden="true"></i></a>
    @if (session('success'))
    <x-toast :message="session('success')" type="success" />
    @endif

    @if (session('error'))
    <x-toast :message="session('error')" type="error" />
    @endif

    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);z-index:9999;justify-content:center;align-items:center;">
        <div style="border:6px solid #f3f3f3;border-top:6px solid #3498db;border-radius:50%;width:50px;height:50px;animation:spin 1s linear infinite;"></div>
    </div>
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
                        <span>{{ $property->size }} m²</span>
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
                    <p>{{ nl2br(e($property->description)) }}</p>
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
                        <label for="guests">Guests</label>
                        <input id="guests" name="guests" type="number" placeholder="Guests" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input id="name" name="name" type="text" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label for="number">Contact Number</label>
                        <input id="number" name="number" type="number" placeholder="Enter your phone number"
                            required>
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

            <!-- Galería de imágenes -->
            <div class="image-gallery">
                <div class="main-image">
                    <img src="{{ asset('images/' . $property->images_div . '/' . $mainImage) }}"
                        alt="Main Property Image" loading="lazy" onclick="openPopup('{{ $mainImage }}',  0 )">
                </div>
                <div class="thumbnail-gallery">
                    @foreach ($imagesWithoutFirst as $index => $image)
                    <img class="thumbnail" src="{{ asset('images/' . $property->images_div . '/' . $image) }}"
                        alt="Property Thumbnail" loading="lazy"
                        onclick="openPopup('{{ $image }}', {{ $index + 1 }})">
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Popup para mostrar la imagen en grande -->
    <div id="imagePopup" class="popup" style="display:none;">
        <span class="close" onclick="closePopup()">&times;</span>
        <img class="popup-content" id="popupImage" src="" alt="Large Image" class="lazy">
        <span class="previos" onclick="changeImage(-1)">&#10094;</span>
        <span class="next-one" onclick="changeImage(1)">&#10095;</span>
    </div>
    <script>
        let currentIndex = 0;
        const property = @json($property);
        const mainImage = @json($mainImage);
        let images = [mainImage];

        function openPopup(imageUrl, index) {
            currentIndex = index;
            document.getElementById("popupImage").src = "/images/" + property.images_div + "/" + imageUrl;
            document.getElementById("imagePopup").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("imagePopup").style.display = "none";
        }

        function changeImage(direction) {
            currentIndex += direction;
            if (currentIndex < 0) currentIndex = images.length - 1;
            if (currentIndex >= images.length) currentIndex = 0;
            document.getElementById("popupImage").src = "/images/" + property.images_div + "/" + images[currentIndex];
        }

        document.addEventListener("DOMContentLoaded", function() {
            const imageArray = @json($imagesWithoutFirst);
            images = images.concat(imageArray);
        });

        // Agregar manejo del teclado para cerrar con Esc
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                closePopup();
            }
        });

        document.querySelector('.contact-form').onsubmit = function(e) {
            e.preventDefault();
            loadingOverlay.style.display = 'flex';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', this.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', this._token.value);
            xhr.onload = function() {
                loadingOverlay.style.display = 'none';
                if (xhr.status == 200) {
                    toastSuccess.classList.add('show');
                    setTimeout(() => toastSuccess.classList.remove('show'), 3000);
                    e.target.reset();
                } else {
                    alert('Error al enviar el mensaje');
                }
            };
            xhr.onerror = function() {
                loadingOverlay.style.display = 'none';
                alert('Error al enviar el mensaje');
            };
            xhr.send(new FormData(e.target));
        };
    </script>


    @include('components.footer')

</body>

</html>