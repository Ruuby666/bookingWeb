<div class="property-form">
    <form class="contact-form" action="{{ $sendEmailRoute }}" method="POST">
        @csrf
        <medium> All the fields with&nbsp;<b> * </b>&nbsp;are required. </medium>
        <div class="date-container">
            @include('components.show-date-range', ['property' => $property])
            @error('daterange')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="adults">Adults <b>*</b></label>
            <input id="adults" name="adults" type="number" min="1" placeholder="1 - {{ $property->capacity }}"
                value="{{ old('adults') }}" class="{{ $errors->has('adults') ? 'error' : '' }}" required>
            <label for="children">Children <b>*</b></label>
            <input id="children" name="children" type="number" placeholder="0 - {{ $property->capacity - 1 }}"
                value="{{ old('children') }}" class="{{ $errors->has('children') ? 'error' : '' }}" required>
            @error('guests')
                <error class="error-message">{{ $message }}</error>
            @enderror
        </div>
        <div class="form-group">
            <label for="name">Full Name <b>*</b></label>
            <input id="name" name="name" type="text" placeholder="Enter your name"
                value="{{ old('name') }}" class="{{ $errors->has('name') ? 'error' : '' }}" required>
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="number">Contact Number <b>*</b></label>
            <input id="number" name="number" type="tel" placeholder="Enter a phone number" value="{{ old('number') }}"
                class="{{ $errors->has('number') ? 'error' : '' }}" required>
            @error('number')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">Email <b>*</b></label>
            <input id="email" name="email" type="email" placeholder="Enter your email"
                value="{{ old('email') }}" class="{{ $errors->has('email') ? 'error' : '' }}" required>
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="verification_email">Verify Email <b>*</b></label>
            <input id="verification_email" name="verification_email" type="email" placeholder="Verify your email"
                value="{{ old('verification_email') }}"
                class="{{ $errors->has('verification_email') ? 'error' : '' }}" required>
            @error('verification_email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" placeholder="Enter your message" maxlength="500"
                class="{{ $errors->has('message') ? 'error' : '' }}">{{ old('message') }}</textarea>
            @error('message')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="total_price" id="total_price_input" value="">
        <button type="submit">Send Your Request</button>
    </form>
</div>

<script>
    window.FORM_CONFIG = {
        maxCapacity: {{ $maxCapacity }}
    };
</script>
<script src="{{ asset('js/property-form.js') }}" defer></script>
