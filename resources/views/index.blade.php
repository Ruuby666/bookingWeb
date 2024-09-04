<!DOCTYPE html>
<html>
<head>
    <title>BookingOcra</title>
</head>
<body>

<h1>Users</h1>
<ul>
    @foreach($users as $user)
        <li>{{ $user->name }}</li>
    @endforeach
</ul>

<h1>Properties</h1>
<ul>
    @foreach($properties as $property)
        <li>{{ $property->title }}</li>
        <img src="{{$property->image_url}}" alt="Image not found">
    @endforeach
    </ul>

<h1>Reservations</h1>
<ul>
    @foreach($reservations as $reservation)
        <li>{{ $reservation->user_id}}   {{$reservation->property_id }}   From: {{$reservation->check_in }}  To: {{$reservation->check_out }}</li>
    @endforeach
</ul>

</body>
</html>
