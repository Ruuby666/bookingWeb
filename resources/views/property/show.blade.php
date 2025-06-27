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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />
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
            <div class="content-details-form">
                <div class="property-details">
                    <h1 class="title">{{ $property->title }}</h1>

                    <div class="location">
                        <i class="fas fa-map-marker-alt icon"></i>
                        <span>{{ $property->location }}</span>
                    </div>
                    <div class="features">
                        <div class="feature">
                            <div class="bedrooms">
                                @php
                                $bedrooms = json_decode($property->bedrooms, true);
                                @endphp
                                @foreach ($bedrooms as $key => $bed)
                                <span><i class="fas fa-bed icon"></i> {{ $bed }}</span>
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
                            <span>€{{ $property->price_per_night }} per night</span>  <!-- TODO -->
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
                </div>
                <div class="property-form">
                    <form class="contact-form" action="{{ route('send.email') }}" method="POST">
                        @csrf
                        <medium> All the fields with&nbsp;<b> * </b>&nbsp;are required. </medium>
                        <div>
                            @include('components.show-date-range')
                            @error('daterange')
                            <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="adults">Adults <b>*</b></label>
                            <input id="adults" name="adults" type="number" placeholder="1 - {{ $property->capacity }}" value="{{ old('adults') }}" class="{{ $errors->has('adults') ? 'error' : '' }}" required>

                            <label for="children">Children <b>*</b></label>
                            <input id="children" name="children" type="number" placeholder="0 - {{ $property->capacity - 1 }}" value="{{ old('children') ?? 0 }}" class="{{ $errors->has('children') ? 'error' : '' }}" required>
                            @error('guests')
                            <error class="error-message">{{ $message }}</error>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Full Name <b>*</b></label>
                            <input id="name" name="name" type="text" placeholder="Enter your name" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'error' : '' }}"  required>
                            @error('name')
                            <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="number">Contact Number <b>*</b></label>
                            <input id="number" type="tel" placeholder="Enter your phone number" value="{{ old('number') }}" class="{{ $errors->has('number') ? 'error' : '' }}" required>
                            <input type="hidden" name="number" id="full_phone">
                            @error('number')
                            <div class="error-message">{{ $message }}</div>
                            @enderror   
                        </div>

                        <div class="form-group">
                            <label for="email">Email <b>*</b></label>
                            <input id="email" name="email" type="email" placeholder="Enter your email" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'error' : '' }}" required>
                            @error('email')
                            <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="verification_email">Verify Email <b>*</b></label>
                            <input id="verification_email" name="verification_email" type="email" placeholder="Verify your email" value="{{ old('verification_email') }}" class="{{ $errors->has('verification_email') ? 'error' : '' }}" required>
                            @error('verification_email')
                            <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Enter your message" maxlength="500" class="{{ $errors->has('message') ? 'error' : '' }}">{{ old('message') }}</textarea>
                            @error('message')
                            <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <input type="hidden" name="total_price" id="total_price_input" value="">
                        <button type="submit">Send Your Request</button>
                    </form>
                </div>
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
                        onclick="openPopup('{{ $image }}', {{$index+1}})">
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Popup para mostrar la imagen en grande -->
    <div id="imagePopup" class="popup" style="display:none;">
        <span class="close" onclick="closePopup()">&times;</span>
        <span class="previous" onclick="changeImage(-1)">&#10094;</span>
        <img class="popup-content" id="popupImage" src="" alt="Large Image" class="lazy">
        <span class="next-one" onclick="changeImage(1)">&#10095;</span>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
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


        const phoneInput = document.querySelector("#number");
        const fullPhoneInput = document.querySelector("#full_phone");
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "auto",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        });

        function validateForm() {
            let isValid = true;
            const requiredFields = ['adults', 'children', 'name', 'number', 'email', 'verification_email'];
            
            // Solo limpiar errores de JavaScript, NO los del servidor
            document.querySelectorAll('.js-error-message').forEach(el => el.remove());
            document.querySelectorAll('.js-error').forEach(el => el.classList.remove('js-error'));
            
            // Validar campos requeridos
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (input && !input.value.trim()) {
                    input.classList.add('js-error');
                    showJSError(input, 'Este campo es obligatorio');
                    isValid = false;
                }
            });
            
            // Validar emails coincidentes
            const email = document.getElementById('email').value;
            const verifyEmail = document.getElementById('verification_email').value;
            if (email && verifyEmail && email !== verifyEmail) {
                const verifyInput = document.getElementById('verification_email');
                verifyInput.classList.add('js-error');
                showJSError(verifyInput, 'Los emails no coinciden');
                isValid = false;
            }
            
            // Validar capacidad total
            const adults = parseInt(document.getElementById('adults').value) || 0;
            const children = parseInt(document.getElementById('children').value) || 0;
            const maxCapacity = {{$property->capacity}};
            
            if (adults + children > maxCapacity) {
                const adultsInput = document.getElementById('adults');
                const childrenInput = document.getElementById('children');
                adultsInput.classList.add('js-error');
                childrenInput.classList.add('js-error');
                showJSError(adultsInput, `Total de huéspedes no puede exceder ${maxCapacity}`);
                isValid = false;
            }
            
            return isValid;
        }

        // Función para mostrar errores de JavaScript
        function showJSError(input, message) {
            const errorElement = document.createElement('span');
            errorElement.className = 'error-message js-error-message';
            errorElement.textContent = message;
            input.parentNode.appendChild(errorElement);
        }

        // UN SOLO event listener para el formulario
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault(); // ✅ CORREGIDO: 'e' ahora está definido
                return false;
            }
            document.getElementById('loadingOverlay').style.display = 'flex';
            fullPhoneInput.value = iti.getNumber();
        });

        // Validación en tiempo real para emails
        document.getElementById('verification_email').addEventListener('blur', function() {
            const email = document.getElementById('email').value;
            const verifyEmail = this.value;
            
            // Limpiar errores JS previos
            this.classList.remove('js-error');
            const jsError = this.parentNode.querySelector('.js-error-message');
            if (jsError) jsError.remove();
            
            if (email && verifyEmail && email !== verifyEmail) {
                this.classList.add('js-error');
                showJSError(this, 'Los emails no coinciden');
            }
        });

        // Validación en tiempo real para capacidad
        function validateCapacity() {
            const adults = parseInt(document.getElementById('adults').value) || 0;
            const children = parseInt(document.getElementById('children').value) || 0;
            const maxCapacity = {{ $property->capacity }};
            
            const adultsInput = document.getElementById('adults');
            const childrenInput = document.getElementById('children');
            
            // Limpiar errores JS previos
            adultsInput.classList.remove('js-error');
            childrenInput.classList.remove('js-error');
            const jsErrors = document.querySelectorAll('#adults ~ .js-error-message, #children ~ .js-error-message');
            jsErrors.forEach(error => error.remove());
            
            if (adults + children > maxCapacity) {
                adultsInput.classList.add('js-error');
                childrenInput.classList.add('js-error');
                showJSError(adultsInput, `Total no puede exceder ${maxCapacity} huéspedes`);
            }
        }

        document.getElementById('adults').addEventListener('input', validateCapacity);
        document.getElementById('children').addEventListener('input', validateCapacity);
    </script>


    @include('components.footer')

</body>
</html>