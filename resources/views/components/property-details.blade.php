<div class="property-details">
    <h1 class="title">{{ $property->title }}</h1>
    <div class="location">
        <a href="https://www.google.com/maps?q={{ $property->lat }},{{ $property->lng }}" target="_blank"
            data-color="orange">
            <i class="fas fa-map-marker-alt icon"></i>
            <span>{{ $property->location }}</span>
        </a>
    </div>
    <div class="features">
        <div class="feature">
            <div class="bedrooms">
                @php $bedrooms = json_decode($property->bedrooms, true); @endphp
                @foreach ($bedrooms as $bed)
                    <span><i class="fas fa-bed icon"></i> {{ $bed }}</span>
                @endforeach
            </div>
        </div>
        <div class="feature"><i class="fas fa-bath icon"></i><span>{{ $property->bathrooms }} Bathrooms</span></div>
        <div class="feature"><i class="fas fa-ruler icon"></i><span>{{ $property->size }} m²</span></div>
        <div class="feature"><i class="fas fa-user icon"></i><span>{{ $property->capacity }} Guests</span></div>
        <div class="feature"><i class="fas fa-dollar-sign icon"></i><span>€{{ $property->price_per_night }} per
                night</span></div> <!-- TODO -->
    </div>
    <div class="description">
        <h2>Property Description</h2>
        <p style="white-space: pre-line;">{{ $property->description }}</p>
    </div>
    <div class="extra-features">
        <h5>Additional Features</h5>
        <ul>
            @if ($property->parking)
                <li><i class="fas fa-parking"></i><strong>Free Parking Spot</strong></li>
            @endif
            @if ($property->pool)
                <li><i class="fas fa-swimming-pool"></i><strong>Pool</strong></li>
            @endif
            @if ($property->garden)
                <li><i class="fas fa-tree"></i><strong>Garden</strong></li>
            @endif
            @if ($property->safeBox)
                <li><i class="fas fa-lock"></i><strong>Safe Box</strong></li>
            @endif
            @if ($property->terrace)
                <li><i class="fas fa-umbrella-beach"></i><strong>Terrace</strong></li>
            @endif
            @if ($property->wifi)
                <li><i class="fas fa-wifi"></i><strong>Free Wi-Fi</strong></li>
            @endif
            @if (!empty($property->tv))
                <li><i class="fas fa-tv"></i><strong>TV:</strong> {{ $property->tv }}</li>
            @endif
        </ul>
    </div>
</div>
