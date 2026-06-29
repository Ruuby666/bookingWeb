<link rel="stylesheet" href="{{ asset('css/header.css') }}">

<div id="header">
    <img id="nameLogo" src="/images/nameEMLWhite.png" alt="Not found">
    <div id="header-menu">
        @if (Auth::check() && Auth::user()->is_super_admin)
            {{-- Super admin sees their own panel + full admin panel --}}
            <a href="{{ route('super_admin.index') }}" id="header-menu-word">Manage Admins</a>
            <a href="{{ route('admin.properties') }}" id="header-menu-word">Properties</a>
            <a href="{{ route('admin.reservations.pending') }}" id="header-menu-word">Pending</a>
            <a href="{{ route('admin.calendar') }}" id="header-menu-word">Calendar</a>
            <a href="{{ route('admin.reservation_prices') }}" id="header-menu-word">Prices</a>
        @elseif (Auth::check() && Auth::user()->is_admin)
            <a href="{{ route('admin.properties') }}" id="header-menu-word">Properties</a>
            <a href="{{ route('admin.reservations.pending') }}" id="header-menu-word">Pending Reservations</a>
            <a href="{{ route('admin.calendar') }}" id="header-menu-word">Calendar</a>
            <a href="{{ route('admin.reservation_prices') }}" id="header-menu-word">Reservation Prices</a>
        @else
            <a href="{{ route('index') }}" id="header-menu-word">Home</a>
            <a href="{{ url('/#properties-section') }}" id="header-menu-word">Properties</a>
            <a href="{{ url('/#map-title') }}" id="header-menu-word">Map</a>
        @endif
    </div>
</div>