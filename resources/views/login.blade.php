<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>

<body>
    @include('components.header')
    <div class="content">
        <div class="main">
            <div class="login">
                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
                    <label>Login</label>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" type="email" name="email" placeholder="Email" required>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" type="password" name="password" placeholder="Password" minlength="8" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
    @if (session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @include('components.footer')
</body>

</html>
