<?php

namespace App\Interfaces;

interface AuthentificationServiceInterface
{
    public function authenticate(array $credentials);
    public function logout();
}