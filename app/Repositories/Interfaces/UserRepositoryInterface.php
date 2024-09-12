<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function getAllUsers(Request $request);
    public function createUser(array $data);
    public function getUserById(int $id);
    public function updateUser(User $user, array $data);
    public function deleteUser(User $user);
}