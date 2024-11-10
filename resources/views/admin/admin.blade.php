<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
    @include('components.header')

    <div class="container">

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Price per Night</th>
                        <th>Capacity</th>
                        <th>Size</th>
                        <th>Bedrooms</th>
                        <th>Bathrooms</th>
                        <th>TV</th>
                        <th>Entertainment</th>
                        <th>Parking</th>
                        <th>Pool</th>
                        <th>Garden</th>
                        <th>Safe Box</th>
                        <th>Terrace</th>
                        <th>Wi-Fi</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($properties as $property)
                        <tr>
                            <td>{{ $property->title }}</td>
                            <td class="description-cell" title="{{ $property->description }}">{{ $property->description }}</td>
                            <td>{{ $property->location }}</td>
                            <td>{{ $property->price_per_night }}</td>
                            <td>{{ $property->capacity }}</td>
                            <td>{{ $property->size}} m²</td>
                            <td>{{ $property->bedrooms }}</td>
                            <td>{{ $property->bathrooms }}</td>
                            <td>{{ $property->tv ? 'Yes' : 'No' }}</td>
                            <td>{{ $property->entertainment }}</td>
                            <td>{{ $property->parking ? 'Yes' : 'No' }}</td>
                            <td>{{ $property->pool ? 'Yes' : 'No' }}</td>
                            <td>{{ $property->garden ? 'Yes' : 'No' }}</td>
                            <td>{{ $property->safeBox ? 'Yes' : 'No' }}</td>
                            <td>{{ $property->terrace ? 'Yes' : 'No' }}</td>
                            <td>{{ $property->wifi ? 'Yes' : 'No' }}</td>
                            <td>{{ $property->lat }}</td>
                            <td>{{ $property->lng }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="buttons">
           <a href="{{ route('properties.create') }}" class="btn">Add New Property</a>
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
