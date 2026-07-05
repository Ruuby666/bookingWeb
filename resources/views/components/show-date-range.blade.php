<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Date Range Picker</title>

@include('components.daterangepicker-assets')
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

@csrf
<input type="text" id="daterange" name="daterange" placeholder="Select a date range" readonly /><b> *</b>
<p id="total-price">Minimum {{ $property->min_nights }} nights</p>

{{-- Configuration PHP → JS --}}
<script>
    window.DATE_RANGE_CONFIG = {
        propertyId: {{ $property->id }},
        minNights: {{ $property->min_nights }},
    };
</script>
<script src="{{ asset('js/date-picker.js') }}" defer></script>
