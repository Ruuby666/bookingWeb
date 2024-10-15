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
                            <span>{{ json_decode($property->bedrooms, true)['1'] ?? 'N/A' }}</span>
                            <span>{{ json_decode($property->bedrooms, true)['2'] ?? 'N/A' }}</span>
                            <span>{{ json_decode($property->bedrooms, true)['3'] ?? 'N/A' }}</span>
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
                    <p>{{ $property->description }}</p>
                </div>

                <div class="extra-features">
                    <h5>Additional Features</h5>
                    <ul>
                        <li><i class="fas fa-parking"></i><strong>{{ $property->parking ? 'Free Parking Spot' : null }}</strong></li>
                        <li><i class="fas fa-parking"></i><strong>{{ $property->pool ? 'Pool' : null }}</strong></li>
                        <li><i class="fas fa-parking"></i><strong>{{ $property->garden ? 'Garden' : null }}</strong></li>
                        <li><i class="fas fa-parking"></i><strong>{{ $property->safeBox ? 'Safe Box' : null }}</strong></li>
                        <li><i class="fas fa-parking"></i><strong>{{ $property->terrace ? 'Terrace' : null }}</strong></li>
                        <li><i class="fas fa-parking"></i><strong>{{ $property->wifi ? 'Free wifi' : null }}</strong></li>
                        <li><i class="fas fa-parking"></i><strong>TV:</strong> {{ $property->tv }}</li>
                    </ul>
                </div>

                @include('components.show-date-range')

                <form class="contact-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input id="name" type="text" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="number">Contact Number</label>
                        <input id="number" type="number" placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" rows="2" placeholder="Enter your message"></textarea>
                    </div>
                    <button type="submit">Submit Inquiry</button>
                </form>
            </div>

            <!-- Imágenes del Apartamento -->
            <div class="image-gallery">
                <div class="main-image">
                    <img src="{{ asset('images/' . $property->images_div . '/' . $mainImage) }}" alt="Main Property Image" loading="lazy">
                </div>
                <div class="thumbnail-gallery">
                    @foreach($imagesWithoutFirst as $image)
                    <img class="thumbnail" src="{{ asset('images/' . $property->images_div . '/' . $image) }}" alt="Property Thumbnail" loading="lazy">
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')

</body>

</html>