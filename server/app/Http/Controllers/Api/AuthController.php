<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\UnauthorizedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!Auth::attempt([
            'email' => $request->post('email'),
            'password' => $request->post('password')
        ])) {
            throw new UnauthorizedException("invalid email or password");
        }

        $user = Auth::user();

        return $this->responseSuccess('Success', [
            'token' => $user->createToken("web")->plainTextToken
        ]);
    }

    public function user()
    {
        $user = Auth::user();

        return $this->responseSuccess('Success', [
            'user' => $user
        ]);
    }

    public function logout()
    {
        $user = Auth::user();

        $user->tokens()->where('name', 'web')->delete();

        Auth::guard('web')->logout();

        return $this->responseSuccess('Success');
    }
}
