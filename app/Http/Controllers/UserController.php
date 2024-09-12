<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserServiceInterface;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        return $this->userService->getAllUsers($request);
    }

    public function store(Request $request)
    {
        return $this->userService->createUser($request->all());
    }

    public function show(int $id)
    {
        return $this->userService->getUserById($id);
    }

    public function update(Request $request, User $user)
    {
        return $this->userService->updateUser($user, $request->all());
    }

    public function destroy(User $user)
    {
        return $this->userService->deleteUser($user);
    }
}