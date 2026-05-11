<!-- Image Gallery -->
<div class="image-gallery">
    <div class="main-image">
        <img
            src="{{ asset('images/' . $property->images_div . '/' . $mainImage) }}"
            alt="Main Property Image"
            loading="lazy"
            onclick="galleryOpenPopup('{{ $mainImage }}', 0)"
        >
    </div>
    <div class="thumbnail-gallery">
        @foreach ($imagesWithoutFirst as $index => $image)
            <img
                class="thumbnail"
                src="{{ asset('images/' . $property->images_div . '/' . $image) }}"
                alt="Property Thumbnail"
                loading="lazy"
                onclick="galleryOpenPopup('{{ $image }}', {{ $index + 1 }})"
            >
        @endforeach
    </div>
</div>

<!-- Popup / Lightbox -->
<div id="galleryPopup" class="popup" style="display:none;" onclick="galleryCloseOnBackdrop(event)">
    <span class="close" onclick="galleryClosePopup()">&times;</span>
    <span class="previous" onclick="galleryChangeImage(-1)">&#10094;</span>
    <img class="popup-content" id="galleryPopupImage" src="" alt="Large Image">
    <span class="next-one" onclick="galleryChangeImage(1)">&#10095;</span>
</div>

<script>
(function () {
    // Datos de las imágenes inyectados desde PHP
    const _imagesDiv  = @json($property->images_div);
    const _mainImage  = @json($mainImage);
    const _rest       = @json($imagesWithoutFirst);
    const _allImages  = [_mainImage, ..._rest];   // [0] = main, [1..n] = thumbnails

    let _currentIndex = 0;

    function _src(filename) {
        return `/images/${_imagesDiv}/${filename}`;
    }

    window.galleryOpenPopup = function (imageFilename, index) {
        _currentIndex = index;
        document.getElementById('galleryPopupImage').src = _src(imageFilename);
        document.getElementById('galleryPopup').style.display = 'flex';
    };

    window.galleryClosePopup = function () {
        document.getElementById('galleryPopup').style.display = 'none';
    };

    window.galleryChangeImage = function (direction) {
        _currentIndex = (_currentIndex + direction + _allImages.length) % _allImages.length;
        document.getElementById('galleryPopupImage').src = _src(_allImages[_currentIndex]);
    };

    // Cerrar al hacer clic fuera de la imagen (en el fondo oscuro)
    window.galleryCloseOnBackdrop = function (event) {
        if (event.target.id === 'galleryPopup') {
            galleryClosePopup();
        }
    };

    // Cerrar con tecla Escape y navegar con flechas del teclado
    document.addEventListener('keydown', function (e) {
        const popup = document.getElementById('galleryPopup');
        if (popup.style.display === 'none') return;

        if (e.key === 'Escape')      galleryClosePopup();
        if (e.key === 'ArrowLeft')   galleryChangeImage(-1);
        if (e.key === 'ArrowRight')  galleryChangeImage(1);
    });
})();
</script>
