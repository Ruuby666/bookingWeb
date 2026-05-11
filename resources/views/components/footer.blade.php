<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
<div id="footer">
    <div id="footer-content">
        <img id="logo" src="/images/logoEML.png" alt="Not found">
        <p>enjoyhomelanzarote@gmail.com</p>
    </div>

    @auth
        @if (Auth::check() && Auth::user()->is_admin)
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Log out</button>
            </form>
        @else

        @endif
    @endauth
    <p>&copy; 2025 Enjoy Home Lanzarote. All Rights Reserved.</p>
</div>
</div>
