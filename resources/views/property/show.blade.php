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
                        <a href="https://www.google.com/maps?q={{ $property->lat }},{{ $property->lng }}" target="_blank" data-color="orange">
                        <i class="fas fa-map-marker-alt icon"></i>
                        <span>{{ $property->location }}</span>
                        </a>
                    </div>
                    <div class="features">
                        <div class="feature">
                            <div class="bedrooms">
                                @php $bedrooms = json_decode($property->bedrooms, true); @endphp
                                @foreach ($bedrooms as $bed)
                                <span><i class="fas fa-bed icon"></i> {{ $bed }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="feature"><i class="fas fa-bath icon"></i><span>{{ $property->bathrooms }} Bathrooms</span></div>
                        <div class="feature"><i class="fas fa-ruler icon"></i><span>{{ $property->size }} m²</span></div>
                        <div class="feature"><i class="fas fa-user icon"></i><span>{{ $property->capacity }} Guests</span></div>
                        <div class="feature"><i class="fas fa-dollar-sign icon"></i><span>€{{ $property->price_per_night }} per night</span></div> <!-- TODO -->
                    </div>
                    <div class="description">
                        <h2>Property Description</h2>
                        <p>{{ nl2br(e($property->description)) }}</p>
                    </div>
                    <div class="extra-features">
                        <h5>Additional Features</h5>
                        <ul>
                            @if ($property->parking) <li><i class="fas fa-parking"></i><strong>Free Parking Spot</strong></li> @endif
                            @if ($property->pool) <li><i class="fas fa-swimming-pool"></i><strong>Pool</strong></li> @endif
                            @if ($property->garden) <li><i class="fas fa-tree"></i><strong>Garden</strong></li> @endif
                            @if ($property->safeBox) <li><i class="fas fa-lock"></i><strong>Safe Box</strong></li> @endif
                            @if ($property->terrace) <li><i class="fas fa-umbrella-beach"></i><strong>Terrace</strong></li> @endif
                            @if ($property->wifi) <li><i class="fas fa-wifi"></i><strong>Free Wi-Fi</strong></li> @endif
                            @if (!empty($property->tv)) <li><i class="fas fa-tv"></i><strong>TV:</strong> {{ $property->tv }}</li> @endif
                        </ul>
                    </div>
                </div>
                <div class="property-form">
                    <form class="contact-form" action="{{ route('send.email') }}" method="POST">
                        @csrf
                        <medium> All the fields with&nbsp;<b> * </b>&nbsp;are required. </medium>
                        <div class="date-container">
                            @include('components.show-date-range')
                            @error('daterange') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="adults">Adults <b>*</b></label>
                            <input id="adults" name="adults" type="number" placeholder="1 - {{ $property->capacity }}" value="{{ old('adults') }}" class="{{ $errors->has('adults') ? 'error' : '' }}" required>
                            <label for="children">Children <b>*</b></label>
                            <input id="children" name="children" type="number" placeholder="0 - {{ $property->capacity - 1 }}" value="{{ old('children') ?? 0 }}" class="{{ $errors->has('children') ? 'error' : '' }}" required>
                            @error('guests') <error class="error-message">{{ $message }}</error> @enderror
                        </div>
                        <div class="form-group">
                            <label for="name">Full Name <b>*</b></label>
                            <input id="name" name="name" type="text" placeholder="Enter your name" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'error' : '' }}" required>
                            @error('name') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="number">Contact Number <b>*</b></label>
                            <input id="number" type="tel" placeholder="Enter a phone number" value="{{ old('number') }}" class="{{ $errors->has('number') ? 'error' : '' }}" required>
                            <input type="hidden" name="number" id="full_phone">
                            @error('number') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email <b>*</b></label>
                            <input id="email" name="email" type="email" placeholder="Enter your email" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'error' : '' }}" required>
                            @error('email') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="verification_email">Verify Email <b>*</b></label>
                            <input id="verification_email" name="verification_email" type="email" placeholder="Verify your email" value="{{ old('verification_email') }}" class="{{ $errors->has('verification_email') ? 'error' : '' }}" required>
                            @error('verification_email') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Enter your message" maxlength="500" class="{{ $errors->has('message') ? 'error' : '' }}">{{ old('message') }}</textarea>
                            @error('message') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <input type="hidden" name="total_price" id="total_price_input" value="">
                        <button type="submit">Send Your Request</button>
                    </form>
                </div>
            </div>
            <!-- Image Gallery -->
            <div class="image-gallery">
                <div class="main-image">
                    <img src="{{ asset('images/' . $property->images_div . '/' . $mainImage) }}"
                        alt="Main Property Image" loading="lazy" onclick="openPopup('{{ $mainImage }}', 0)">
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

    <!-- Popup for large image -->
    <div id="imagePopup" class="popup" style="display:none;">
        <span class="close" onclick="closePopup()">&times;</span>
        <span class="previous" onclick="changeImage(-1)">&#10094;</span>
        <img class="popup-content" id="popupImage" src="" alt="Large Image">
        <span class="next-one" onclick="changeImage(1)">&#10095;</span>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script>
        // --- Image Gallery Logic ---
        const property = @json($property);
        const mainImage = @json($mainImage);
        const images = [mainImage, ...@json($imagesWithoutFirst)];
        let currentIndex = 0;

        function openPopup(imageUrl, index) {
            currentIndex = index;
            document.getElementById("popupImage").src = `/images/${property.images_div}/${imageUrl}`;
            document.getElementById("imagePopup").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("imagePopup").style.display = "none";
        }

        function changeImage(direction) {
            currentIndex = (currentIndex + direction + images.length) % images.length;
            document.getElementById("popupImage").src = `/images/${property.images_div}/${images[currentIndex]}`;
        }
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") closePopup();
        });

        // --- Phone Input Initialization ---
        const phoneInput = document.querySelector("#number");
        const fullPhoneInput = document.querySelector("#full_phone");
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "auto",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        });

        // --- Form Validation ---
        function showJSError(input, message) {
            const errorElement = document.createElement('span');
            errorElement.className = 'error-message js-error-message';
            errorElement.textContent = message;
            input.parentNode.appendChild(errorElement);
        }

        function clearJSErrors() {
            document.querySelectorAll('.js-error-message').forEach(el => el.remove());
            document.querySelectorAll('.js-error').forEach(el => el.classList.remove('js-error'));
        }

        function validateCapacity() {
            const adults = parseInt(document.getElementById('adults').value) || 0;
            const children = parseInt(document.getElementById('children').value) || 0;
            const maxCapacity = {
                {
                    $property - > capacity
                }
            };
            const adultsInput = document.getElementById('adults');
            const childrenInput = document.getElementById('children');
            adultsInput.classList.remove('js-error');
            childrenInput.classList.remove('js-error');
            document.querySelectorAll('#adults ~ .js-error-message, #children ~ .js-error-message').forEach(e => e.remove());
            if (adults + children > maxCapacity) {
                adultsInput.classList.add('js-error');
                childrenInput.classList.add('js-error');
                showJSError(adultsInput, `The max quests possible are ${maxCapacity}`);
            }
        }

        function validateForm() {
            let isValid = true;
            clearJSErrors();


            const requiredFields = ['adults', 'children', 'name', 'number', 'email', 'verification_email'];
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (input && !input.value.trim()) {
                    input.classList.add('js-error');
                    showJSError(input, 'This field is required');
                    isValid = false;
                }
            });
            // Phone validation
            if (!phoneInput.value.trim()) {
                phoneInput.classList.add('js-error');
                showJSError(phoneInput, 'Phone number is required');
                isValid = false;
            } else if (iti.isValidNumber()) {
                fullPhoneInput.value = iti.getNumber();
            } else {
                phoneInput.classList.add('js-error');
                showJSError(phoneInput, 'Prefix or phone number invalid');
                isValid = false;
            }
            // Email match
            const email = document.getElementById('email').value;
            const verifyEmail = document.getElementById('verification_email').value;
            if (email && verifyEmail && email !== verifyEmail) {
                const verifyInput = document.getElementById('verification_email');
                verifyInput.classList.add('js-error');
                showJSError(verifyInput, 'The emails do not match');
                isValid = false;
            }
            // Capacity
            const adults = parseInt(document.getElementById('adults').value) || 0;
            const children = parseInt(document.getElementById('children').value) || 0;
            const maxCapacity = {
                {
                    $property - > capacity
                }
            };
            if (adults + children > maxCapacity) {
                const adultsInput = document.getElementById('adults');
                const childrenInput = document.getElementById('children');
                adultsInput.classList.add('js-error');
                childrenInput.classList.add('js-error');
                showJSError(adultsInput, `There must be no more than ${maxCapacity} guests`);
                isValid = false;
            }
            return isValid;
        }

        // --- Event Listeners ---
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            document.getElementById('loadingOverlay').style.display = 'flex';
            fullPhoneInput.value = iti.getNumber();
        });
        document.getElementById('verification_email').addEventListener('blur', function() {
            const email = document.getElementById('email').value;
            const verifyEmail = this.value;
            this.classList.remove('js-error');
            const jsError = this.parentNode.querySelector('.js-error-message');
            if (jsError) jsError.remove();
            if (email && verifyEmail && email !== verifyEmail) {
                this.classList.add('js-error');
                showJSError(this, 'The emails do not match');
            }
        });
        document.querySelector('.contact-form').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });
        document.getElementById('adults').addEventListener('input', validateCapacity);
        document.getElementById('children').addEventListener('input', validateCapacity);
    </script>

    @include('components.footer')
</body>

</html>