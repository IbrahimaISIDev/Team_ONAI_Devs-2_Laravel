<?php

namespace App\Services;

use App\Interfaces\AuthentificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;

class AuthentificationSanctum implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tokenResult = $user->createToken('auth_token');
            
            $token = $tokenResult instanceof NewAccessToken 
                ? $tokenResult->plainTextToken 
                : ($tokenResult->accessToken ?? $tokenResult);

            return [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
        }
        return null;
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
        }
    }
}