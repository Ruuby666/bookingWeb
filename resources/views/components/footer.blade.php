<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
<div id="footer">
    <div id="footer-content">
        <img id="logo" src="/images/logoEML.png" alt="Not found">
        <p>enjoyhomelanzarote@gmail.com</p>
    </div>
    
    @if(session('is_admin'))
    <a id="a-footer" href="{{ route('admin.logout') }}">Log out</a>
    @else
    <p>&copy; 2025 Enjoy Home Lanzarote. All Rights Reserved.</p>
    @endif
</div>
</div>