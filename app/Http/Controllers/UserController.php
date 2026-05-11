<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;

/**
 * Controller responsible for user management.
 */
class UserController extends Controller
{
    /**
     * Inject required services.
     */
    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * Update a user.
     *
     * @param UpdateUserRequest $request Updated user data
     * @param int $id User ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $this->userService->updateUser($id, $request->validated());

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }
}
