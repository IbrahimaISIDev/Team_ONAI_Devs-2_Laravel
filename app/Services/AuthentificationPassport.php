<?php

namespace App\Services;

use App\Interfaces\AuthentificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->accessToken;
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
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        $tokenRepository->revokeAccessToken(Auth::user()->token()->id);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId(Auth::user()->token()->id);
    }
}
