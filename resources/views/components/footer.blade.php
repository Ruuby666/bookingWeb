<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
<div id="footer">
    <img id="logo" src="/images/logoEML.png" alt="Not found">
    <p>enjoyhomelanzarote@gmail.com</p>
    <p>+34 XXXXXXXX</p>
    <p>&copy; 2025 Enjoy Home Lanzarote. All Rights Reserved.</p>
    @if(session('is_admin'))
        <a id="a-footer" href="{{ route('admin.logout') }}">Log out</a>
    @else
        
    @endif
</div>
</div>
