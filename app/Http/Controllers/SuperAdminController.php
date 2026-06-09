<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuperAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controller responsible for super admin operations.
 * Manages admin users: list, create, toggle status, delete.
 */
class SuperAdminController extends Controller
{
    /**
     * Display all admin users.
     */
    public function index(): View
    {
        $admins = User::where('is_admin', true)
            ->where('is_super_admin', false)
            ->orderBy('name')
            ->get();
        return view('superadmin.superadmin', compact('admins'));
    }

    /**
     * Show the create admin form.
     */
    public function create(): View
    {
        return view('superadmin.admincreate');
    }

    /**
     * Store a new admin user.
     */
    public function store(StoreSuperAdminRequest $request): RedirectResponse
    {
        User::create([
            'name'         => $request->validated('name'),
            'email'        => $request->validated('email'),
            'phone_number' => $request->validated('phone_number'),
            'password'     => $request->validated('password'),
            'is_admin'     => true,
            'is_super_admin' => false,
        ]);

        return redirect()
            ->route('super_admin.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Show the edit admin form.
     */
    public function edit(User $admin): View
    {
        abort_if($admin->is_super_admin, 403);
        return view('superadmin.adminedit', compact('admin'));
    }

    /**
     * Update an admin user.
     */
    public function update(UpdateAdminRequest $request, User $admin): RedirectResponse
    {
        abort_if($admin->is_super_admin, 403);

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $admin->update($data);

        return redirect()
            ->route('super_admin.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Toggle the is_admin status of a user.
     */
    public function toggleAdmin(User $admin): RedirectResponse
    {
        abort_if($admin->is_super_admin, 403);

        $admin->update(['is_admin' => ! $admin->is_admin]);

        $status = $admin->is_admin ? 'enabled' : 'disabled';

        return redirect()
            ->route('super_admin.index')
            ->with('success', "Admin access {$status} for {$admin->name}.");
    }

    /**
     * Delete an admin user.
     */
    public function destroy(User $admin): RedirectResponse
    {
        abort_if($admin->is_super_admin, 403);

        $admin->delete();

        return redirect()
            ->route('super_admin.index')
            ->with('success', 'Admin deleted successfully.');
    }
}