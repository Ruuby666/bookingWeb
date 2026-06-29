<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Admin</title>
    <link rel="stylesheet" href="{{ asset('css/add_or_edit_property.css') }}" />
</head>

<body>
    @include('components.header')

    <div class="content">
        <div class="form-container">
            <h2>Edit Admin: {{ $admin->name }}</h2>

            <form action="{{ route('super_admin.update', $admin) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-item">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                    @error('name') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                    @error('email') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $admin->phone_number) }}">
                    @error('phone_number') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="password">New Password <small>(leave blank to keep current)</small></label>
                    <input type="password" id="password" name="password">
                    @error('password') <span style="color:red;font-size:13px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-item">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">
                </div>

                <div class="form-item button-container" style="grid-column:1/-1;">
                    <a href="{{ route('super_admin.index') }}" style="margin-right:10px;">← Back</a>
                    <button type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    @include('components.footer')
</body>
</html>