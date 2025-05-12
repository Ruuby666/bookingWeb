@props(['message', 'type' => 'success'])

@if ($message)
    <div class="toast {{ $type }}">
        <span class="icon">
            {{ $type === 'success' ? '✔️' : '❌' }}
        </span>
        <span>{{ $message }}</span>
    </div>

    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 25px;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            animation: fadeOut 5s forwards;
        }

        .toast.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .toast.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1
