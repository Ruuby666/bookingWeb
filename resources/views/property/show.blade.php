<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link href="{{ asset('css/details-property-copy.css') }}" rel="stylesheet">
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
                    <img src="/images/{{ $property->image_url }}" alt="Property Image">
                </div>
                <div class="thumbnail-gallery">
                    {{-- @foreach($property->additional_images as $image)
                        <img src="/images/{{ $property->image_url }}" alt="Property Image">
                    @endforeach --}}
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')

</body>

</html>
