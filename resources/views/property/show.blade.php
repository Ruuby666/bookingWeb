<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link href="{{ asset('css/details-property.css') }}" rel="stylesheet">
</head>
<!-- <body>
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
</body> -->
<body>
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
                    <span>{{ $property->bedrooms }} Bedrooms</span>
                </div>
                <div class="feature">
                    <i class="fas fa-bath icon"></i>
                    <span>{{ $property->bathrooms }} Bathrooms</span>
                </div>
                <div class="feature">
                    <i class="fas fa-ruler icon"></i>
                    <span>{{ $property->size }} sq ft</span>
                </div>
                <div class="feature">
                    <i class="fas fa-vector-square icon"></i>
                    <span>{{ $property->land_size }} Acres</span>
                </div>
            </div>
            <div class="description">
                <h2>Property Description</h2>
                <p>{{ $property->description }}</p>
            </div>
            <form class="contact-form">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" placeholder="Enter your name">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" rows="4" placeholder="Enter your message"></textarea>
                </div>
                <button type="submit">Submit Inquiry</button>
            </form>
        </div>

        <!-- Imágenes del Apartamento -->
        <div class="image-gallery">
            <div class="main-image">
                <img src="/images/{{ $property->image_url }}" alt="Property Image">
            </div>
            <div class="thumbnail-gallery">
                <!-- Suponiendo que $property->additional_images es un array de URLs -->
            </div>
        </div>
    </div>
</div>

</body>

</html>
