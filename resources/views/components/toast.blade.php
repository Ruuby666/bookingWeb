@props(['message', 'type' => 'success'])

@if ($message)
    <div class="toast {{ $type }}">
        <span class="icon">
            {{ $type === 'success' ? '✔️' : '❌' }}
        </span>
        <span>{{ $message }}</span>
    </div>
@endif
