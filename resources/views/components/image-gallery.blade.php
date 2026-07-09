<!-- Image Gallery -->
<div class="image-gallery">
    <div class="main-image">
        <img
            src="{{ Storage::url('images/' . $property->images_div . '/' . $mainImage) }}"
            alt="Main Property Image"
            loading="lazy"
            role="button"
            tabindex="0"
            aria-label="Open image gallery"
            onclick="galleryOpenPopup('{{ $mainImage }}', 0)"
            onkeydown="galleryHandleActivationKey(event, () => galleryOpenPopup('{{ $mainImage }}', 0))"
        >
    </div>
    <div class="thumbnail-gallery">
        @foreach ($imagesWithoutFirst as $index => $image)
            <img
                class="thumbnail"
                src="{{ Storage::url('images/' . $property->images_div . '/' . $image) }}"
                alt="Property Thumbnail"
                loading="lazy"
                role="button"
                tabindex="0"
                aria-label="Open image {{ $index + 2 }} in gallery"
                onclick="galleryOpenPopup('{{ $image }}', {{ $index + 1 }})"
                onkeydown="galleryHandleActivationKey(event, () => galleryOpenPopup('{{ $image }}', {{ $index + 1 }}))"
            >
        @endforeach
    </div>
</div>

<!-- Popup / Lightbox -->
<div
    id="galleryPopup"
    class="popup"
    style="display:none;"
    role="dialog"
    aria-modal="true"
    aria-label="Property image viewer"
    onclick="galleryCloseOnBackdrop(event)"
>
    <span
        class="close"
        role="button"
        tabindex="0"
        aria-label="Close image viewer"
        onclick="galleryClosePopup()"
        onkeydown="galleryHandleActivationKey(event, galleryClosePopup)"
    >&times;</span>
    <span
        class="previous"
        role="button"
        tabindex="0"
        aria-label="Previous image"
        onclick="galleryChangeImage(-1)"
        onkeydown="galleryHandleActivationKey(event, () => galleryChangeImage(-1))"
    >&#10094;</span>
    <img class="popup-content" id="galleryPopupImage" src="" alt="Large Image">
    <span
        class="next-one"
        role="button"
        tabindex="0"
        aria-label="Next image"
        onclick="galleryChangeImage(1)"
        onkeydown="galleryHandleActivationKey(event, () => galleryChangeImage(1))"
    >&#10095;</span>
</div>

<script>
(function () {
    // Image data injected from PHP
    const _imagesDiv  = @json($property->images_div);
    const _mainImage  = @json($mainImage);
    const _rest       = @json($imagesWithoutFirst);
    const _allImages  = [_mainImage, ..._rest];   // [0] = main, [1..n] = thumbnails

    let _currentIndex = 0;
    let _triggerElement = null;

    function _src(filename) {
        return `/storage/images/${_imagesDiv}/${filename}`;
    }

    // Shared keydown handler so gallery images and lightbox controls
    // (all rendered as non-native <img>/<span> "buttons") are operable
    // the same way native buttons are: Enter or Space activates them.
    window.galleryHandleActivationKey = function (event, callback) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            callback();
        }
    };

    window.galleryOpenPopup = function (imageFilename, index) {
        _currentIndex = index;
        _triggerElement = document.activeElement;
        document.getElementById('galleryPopupImage').src = _src(imageFilename);
        const popup = document.getElementById('galleryPopup');
        popup.style.display = 'flex';
        popup.querySelector('.close').focus();
    };

    window.galleryClosePopup = function () {
        document.getElementById('galleryPopup').style.display = 'none';
        _triggerElement?.focus();
    };

    window.galleryChangeImage = function (direction) {
        _currentIndex = (_currentIndex + direction + _allImages.length) % _allImages.length;
        document.getElementById('galleryPopupImage').src = _src(_allImages[_currentIndex]);
    };

    // Close when clicking outside the image (on the dark backdrop)
    window.galleryCloseOnBackdrop = function (event) {
        if (event.target.id === 'galleryPopup') {
            galleryClosePopup();
        }
    };

    // Close with the Escape key and navigate with arrow keys
    document.addEventListener('keydown', function (e) {
        const popup = document.getElementById('galleryPopup');
        if (popup.style.display === 'none') return;

        if (e.key === 'Escape')      galleryClosePopup();
        if (e.key === 'ArrowLeft')   galleryChangeImage(-1);
        if (e.key === 'ArrowRight')  galleryChangeImage(1);
    });
})();
</script>
