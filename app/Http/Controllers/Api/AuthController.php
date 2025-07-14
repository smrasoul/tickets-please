<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginUserRequest $request)
    {
        $request->validated();

        if(!Auth::attempt($request->only('email', 'password'))){
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
           'Authenticated',
            [
                'token' => $user->createToken(
                    'API Token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth()   // can be set globally in sanctum.php
                                        //as minutes
                                        // overrides this attr everywhere
                )->plainTextToken,
            ]
        );

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}
