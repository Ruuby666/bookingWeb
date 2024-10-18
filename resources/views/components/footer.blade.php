<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
<div id="footer">
    <img id="logo" src="/images/logoEML.png" alt="Not found">
    <p>contact@company.com</p>
    <p>+123 456 7890</p>
    <p>&copy; 2024 Company Name. All Rights Reserved.</p>
    @if(session('is_admin'))
        <a id="a-footer" href="{{ route('admin.logout') }}">Log out</a> 
    @else
        <a id="a-footer" href="{{ route('login') }}">Log in</a>
    @endif
</div>
</div>
