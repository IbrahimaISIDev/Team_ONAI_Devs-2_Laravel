<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BlacklistedToken;
use App\Interfaces\AuthentificationServiceInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $result = $this->authService->authenticate($request->only('login', 'password'));

        if (!$result) {
            abort(401, 'Les identifiants sont incorrects');
        }

        return $result;
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);
    
        $user = User::where('refresh_token', $request->refresh_token)->first();
        if (!$user) {
            abort(401, 'Refresh token invalide');
        }
    
        BlacklistedToken::create(['token' => $request->refresh_token, 'type' => 'refresh']);
    
        $this->authService->revokeTokens($user);
        return $this->authService->generateTokens($user);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        
        if ($token) {
            BlacklistedToken::create([
                'token' => $token,
                'type' => 'access',
                'revoked_at' => now(),
            ]);
        }

        $this->authService->logout();
        return response(null, 204);
    }
}