<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reservation Price Ranges</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/admin_reservation_price.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
</head>

<body>

    @include('components.header')

    @if(session('success'))
    <x-toast :message="session('success')" type="success" />
    @endif

    @if(session('error'))
    <x-toast :message="session('error')" type="error" />
    @endif

    <div class="container">
        <h2>Reservation Price Ranges</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price / Night</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @forelse($reservationPrices as $price)
                <tr>
                    <td>{{ $price->property->title ?? 'N/A' }}</td>
                    <td>{{ $price->start_date }}</td>
                    <td>{{ $price->end_date }}</td>
                    <td>
                        Around {{ number_format($price->price_per_night,2) }}€
                    </td>
                    <td>
                        <form
                            action="{{ route('reservation-prices.destroy',$price->id) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this range?')">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn-delete">
                                ✕
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No price ranges found.
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>


    <div class="floating-button-container">
        <button
            class="floating-button"
            onclick="openPriceModal()">
            Add Price Range
        </button>
    </div>



    <!-- Modal -->

    <div id="addPriceModal" class="modal">
        <form
            id="priceForm"
            class="modal-content price-modal"
            method="POST"
            action="{{ route('reservation-prices.create') }}">
            @csrf

            <div class="modal-header">
                <h2>Add Price Range</h2>
                <button
                    type="button"
                    class="btn-close"
                    onclick="closePriceModal()">
                    &times;
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="property_id">
                        Property
                    </label>
                    <select
                        id="property_id"
                        name="property_id"
                        required>
                        <option value="">
                            Select a property
                        </option>
                        @foreach($properties as $property)
                        <option
                            value="{{ $property->id }}"
                            {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">

                    <div class="form-group">
                        <label for="start_date">
                            Start date
                        </label>
                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">
                            End date
                        </label>
                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                            value="{{ old('end_date') }}"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="price_per_night">
                        Price per night (€)
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="1"
                        id="price_per_night"
                        name="price_per_night"
                        placeholder="150.00"
                        value="{{ old('price_per_night') }}"
                        required>
                </div>
                <div
                    id="formErrors"
                    class="error-message">
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="modal-cancel-btn"
                    onclick="closePriceModal()">
                    Cancel
                </button>

                <button
                    type="submit"
                    class="modal-save-btn">
                    Save Price Range
                </button>
            </div>
        </form>
    </div>



    @include('components.footer')

    <script src="{{ asset('js/reservation-price.js') }}"></script>

</body>

</html>