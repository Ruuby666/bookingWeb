<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Range Picker</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('css/date-range.css') }}">
</head>
<style>
    @keyframes price-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .price-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ccc;
        border-top-color: #2a4261;
        border-radius: 50%;
        animation: price-spin 0.7s linear infinite;
        vertical-align: middle;
    }
</style>

<body>
    @csrf
    <input type="text" id="daterange" name="daterange" placeholder="Select a date range" readonly /><b> *</b>
    <p id="total-price">Minimum {{ $property->min_nights }} nights</p>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    {{-- Configuration PHP → JS --}}
    <script>
        window.DATE_RANGE_CONFIG = {
            propertyId: {{ $property->id }},
            minNights: {{ $property->min_nights }},
        };
    </script>
    <script src="{{ asset('js/date-picker.js') }}" defer></script>

</body>

</html>
