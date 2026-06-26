<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Super Admin — Manage Admins</title>
    <link rel="stylesheet" href="{{ asset('css/superadmin.css') }}" />
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
</head>

<body>
    @include('components.header')

    @if (session('success'))
        <x-toast :message="session('success')" type="success" />
    @endif
    @if (session('error'))
        <x-toast :message="session('error')" type="error" />
    @endif

    <div class="container">
        <h1 class="page-title">Super Admin — Manage Users</h1>

        <div class="buttons">
            <a href="{{ route('super_admin.create') }}" class="btn">➕ Create New Admin</a>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone_number ?? '—' }}</td>
                            <td>
                                @if ($user->is_admin)
                                    <span class="badge-active">Active</span>
                                @else
                                    <span class="badge-inactive">Disabled</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('super_admin.edit', $user) }}" class="btn-edit">Edit</a>

                                    <form action="{{ route('super_admin.toggle', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-toggle">
                                            {{ $user->is_admin ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('super_admin.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('components.footer')
</body>
</html>