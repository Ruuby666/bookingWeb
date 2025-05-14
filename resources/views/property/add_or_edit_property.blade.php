<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($property) ? 'Edit Property' : 'Add New Property' }}</title>
    <link rel="stylesheet" href="{{ asset('css/add_or_edit_property.css') }}">
</head>

<body>
    @include('components.header')

    <div class="content">
        <div class="form-container">
            <h2>{{ isset($property) ? 'Edit Property Details' : 'Add a New Property' }}</h2>
            <p><b>Fill in all the required fields to list your property. Include precise details like location, capacity, amenities, and a brief but rich description to help guests understand what makes your property unique.</b></p>
            </br>
            <form action="{{ isset($property) ? route('properties.update', $property->id) : route('properties.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($property))
                    @method('PUT')
                @endif

                <div class="form-item">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title"
                        value="{{ old('title', $property->title ?? '') }}" required>
                </div>

                <div class="form-item" style="grid-column: span 2;">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6" required>{{ old('description', $property->description ?? '') }}</textarea>
                </div>

                <div class="form-item">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location"
                        value="{{ old('location', $property->location ?? '') }}" required>
                </div>

                <div class="form-item">
                    <label for="price_per_night">Price per Night</label>
                    <input type="number" step="0.01" id="price_per_night" name="price_per_night"
                        value="{{ old('price_per_night', $property->price_per_night ?? '') }}" required>
                </div>

                <div class="form-item">
                    <label for="capacity">Capacity</label>
                    <input type="number" id="capacity" name="capacity"
                        value="{{ old('capacity', $property->capacity ?? '') }}" required>
                </div>

                <div class="form-item">
                    <label for="size">Size (m²)</label>
                    <input type="number" id="size" name="size" value="{{ old('size', $property->size ?? '') }}"
                        required>
                </div>

                <div class="form-item">
                    <label for="bedrooms">Bedrooms</label>
                    <input type="text" id="bedrooms" name="bedrooms"
                        value="{{ old('bedrooms', $property->bedrooms ?? '') }}" required>
                </div>

                <div class="form-item">
                    <label for="bathrooms">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms"
                        value="{{ old('bathrooms', $property->bathrooms ?? '') }}" required>
                </div>

                <div class="form-item">
                    <label for="images_div">Folder Images</label>
                    <input type="text" id="images_div" name="images_div"
                        value="{{ old('images_div', $property->images_div ?? '') }}" required>
                </div>

                <div class="form-item">
                    <label for="tv">TV (Optional)</label>
                    <input type="text" id="tv" name="tv" value="{{ old('tv', $property->tv ?? '') }}">
                </div>

                @foreach (['entertainment', 'parking', 'pool', 'garden', 'safeBox', 'terrace', 'wifi'] as $field)
                    <div class="boolean-fields">
                        <label for="{{ $field }}">{{ ucfirst($field) }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" required>
                            <option value="1" {{ old($field, $property->$field ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old($field, $property->$field ?? '') == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                @endforeach

                <div class="form-item">
                    <label for="lat">Latitude</label>
                    <input type="number" step="0.0000001" id="lat" name="lat"
                        value="{{ old('lat', $property->lat ?? '') }}" required>
                </div>

                <div class="form-item">
                    <label for="lng">Longitude</label>
                    <input type="number" step="0.0000001" id="lng" name="lng"
                        value="{{ old('lng', $property->lng ?? '') }}" required>
                </div>

                <div class="form-item button-container">
                    <button type="submit">{{ isset($property) ? 'Update Property' : 'Add Property' }}</button>
                </div>
            </form>
        </div>
    </div>

    <div id="session-data" data-session-lifetime="{{ config('session.lifetime') }}"
        data-redirect-url="{{ route('index') }}">
    </div>

    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>
</body>

</html>
