<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>New Booking</h1>
    <p><strong>Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Contact Number:</strong> {{ $data['number'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Message:</strong> {{ $data['message'] }}</p>

    @if(isset($data['daterange']))
        <p><strong>Date Range Selected:</strong> {{ $data['daterange'] }}</p>
    @endif

    <p>Thank you for reaching out!</p>
</body>
</html>
