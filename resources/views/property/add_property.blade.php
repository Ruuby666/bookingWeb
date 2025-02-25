<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Property</title>
    <link rel="stylesheet" href="{{ asset('css/add_property.css') }}">
</head>

<body>
    @include('components.header')
    <div class="content">
        <div class="form-container">
            <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-item">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-item">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2" required></textarea>
                </div>

                <div class="form-item">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="form-item">
                    <label for="price_per_night">Price per Night</label>
                    <input type="number" step="0.01" id="price_per_night" name="price_per_night" required>
                </div>

                <div class="form-item">
                    <label for="capacity">Capacity</label>
                    <input type="number" id="capacity" name="capacity" required>
                </div>

                <div class="form-item">
                    <label for="size">Size</label>
                    <input type="number" id="size" name="size" required>
                </div>

                <div class="form-item">
                    <label for="bedrooms">Bedrooms</label>
                    <input type="text" id="bedrooms" name="bedrooms" required>
                </div>

                <div class="form-item">
                    <label for="bathrooms">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" required>
                </div>

                <div class="form-item">
                    <label for="images_div">Folder_Images</label>
                    <input type="text" id="images_div" name="images_div" required>
                </div>

                <!-- Optional and Boolean Fields -->
                <div class="form-item">
                    <label for="tv">TV</label>
                    <input type="text" id="tv" name="tv">
                </div>

                <div class="boolean-fields">
                    <label for="entertainment">Entertainment</label>
                    <select id="entertainment" name="entertainment" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="boolean-fields">
                    <label for="parking">Parking</label>
                    <select id="parking" name="parking" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="boolean-fields">
                    <label for="pool">Pool</label>
                    <select id="pool" name="pool" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="boolean-fields">
                    <label for="garden">Garden</label>
                    <select id="garden" name="garden" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="boolean-fields">
                    <label for="safeBox">Safe Box</label>
                    <select id="safeBox" name="safeBox" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="boolean-fields">
                    <label for="terrace">Terrace</label>
                    <select id="terrace" name="terrace" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="boolean-fields">
                    <label for="wifi">WiFi</label>
                    <select id="wifi" name="wifi" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-item">
                    <label for="lat">Latitude</label>
                    <input type="number" step="0.0000001" id="lat" name="lat" required>
                </div>

                <div class="form-item">
                    <label for="lng">Longitude</label>
                    <input type="number" step="0.0000001" id="lng" name="lng" required>
                </div>

                <div class="form-item button-container">
                    <button type="submit">Add Property</button>
                </div>
            </form>
        </div>
    </div>

    <div id="session-data"
        data-session-lifetime="{{ config('session.lifetime') }}"
        data-redirect-url="{{ route('index') }}">
    </div>

    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>

</body>

</html>
