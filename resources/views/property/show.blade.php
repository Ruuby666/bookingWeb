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
    <div id="loadingOverlay"
        style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);z-index:9999;justify-content:center;align-items:center;">
        <div
            style="border:6px solid #f3f3f3;border-top:6px solid #3498db;border-radius:50%;width:50px;height:50px;animation:spin 1s linear infinite;">
        </div>
    </div>
    <div class="container">
        <div class="content-grid">
            <div class="content-details-form">
                @include ('components.property-details', [
                    'property' => $property,
                ])
                <x-property-form :property="$property" />
            </div>
            <!-- Image Gallery -->
            <x-image-gallery :property="$property" :main-image="$mainImage" :images-without-first="$imagesWithoutFirst" />
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script>
        // --- Image Gallery Logic ---
        const property = @json($property);
        const mainImage = @json($mainImage);
        const images = [mainImage, ...@json($imagesWithoutFirst)];
    </script>

    @include('components.footer')
</body>

</html>
