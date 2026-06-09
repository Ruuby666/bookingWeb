<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Super Admin — Manage Admins</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
    <style>
        .badge-active   { background: #d4edda; color: #155724; border-radius: 4px; padding: 2px 8px; font-size: 13px; }
        .badge-inactive { background: #f8d7da; color: #721c24; border-radius: 4px; padding: 2px 8px; font-size: 13px; }
        .super-badge    { background: #cce5ff; color: #004085; border-radius: 4px; padding: 2px 8px; font-size: 13px; }
        .btn-toggle     { background: #ffc107; color: #212529; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn-toggle:hover { background: #e0a800; }
        .btn-edit       { background: #17a2b8; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-edit:hover { background: #138496; color: white; }
    </style>
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
        <h1 class="page-title">🛡️ Super Admin — Manage Admins</h1>

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
                    @forelse ($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->phone_number ?? '—' }}</td>
                            <td>
                                @if ($admin->is_admin)
                                    <span class="badge-active">Active</span>
                                @else
                                    <span class="badge-inactive">Disabled</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('super_admin.edit', $admin) }}" class="btn-edit">✏️ Edit</a>

                                    <form action="{{ route('super_admin.toggle', $admin) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-toggle">
                                            {{ $admin->is_admin ? '🔴 Disable' : '🟢 Enable' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('super_admin.destroy', $admin) }}" method="POST"
                                        onsubmit="return confirm('Delete {{ $admin->name }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">🗑️ Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No admin users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('components.footer')
</body>
</html>