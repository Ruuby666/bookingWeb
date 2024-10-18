<!DOCTYPE html>
<html>

<head>
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
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" minlength="8" required>
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
