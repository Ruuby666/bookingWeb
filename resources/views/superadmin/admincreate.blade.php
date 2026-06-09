<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Admin</title>
    <link rel="stylesheet" href="{{ asset('css/add_or_edit_property.css') }}" />
</head>

<body>
    @include('components.header')

    <div class="content">
        <div class="form-container">
            <h2>Create New Admin</h2>

            <form action="{{ route('super_admin.store') }}" method="POST">
                @csrf

                <div class="form-item">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                    @error('phone_number') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    @error('password') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="form-item button-container" style="grid-column:1/-1;">
                    <a href="{{ route('super_admin.index') }}" style="margin-right:10px;">← Back</a>
                    <button type="submit">Create Admin</button>
                </div>
            </form>
        </div>
    </div>

    @include('components.footer')
</body>
</html>