<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function update(UpdateUserRequest $request, $id)
    {
        $this->userService->updateUser($id, $request->validated());

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}
