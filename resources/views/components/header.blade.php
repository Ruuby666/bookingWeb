<link rel="stylesheet" href="{{ asset('css/header.css') }}">


<div id="header">
    <img id="nameLogo" src="/images/nameEMLWhite.png" alt="Not found">
    <div id="header-menu">
        @if(session('is_admin'))
        <a href="{{ route('admin.properties') }}" id="header-menu-word">Properties</a>
        <a href="{{ route('admin.reservations.pending') }}" id="header-menu-word">Pending Reservations</a>
        <a href="{{ route('properties.create') }}" id="header-menu-word">Add Property</a>
        @else
        <a href="{{ route('index') }}" id="header-menu-word">Home</a>
        <a href="http://127.0.0.1:8000/#properties-section" id="header-menu-word">Properties</a>
        <a href="http://127.0.0.1:8000/#map-section" id="header-menu-word">Map</a>
        @endif
    </div>
</div>
