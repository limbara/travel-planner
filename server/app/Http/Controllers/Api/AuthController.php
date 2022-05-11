<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\ErrorException;
use App\Exceptions\Api\UnauthorizedException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new UnauthorizedException("invalid email or password");
            }

            return $this->responseSuccess('Success', [
                'token' => $user->createToken("web")->plainTextToken
            ]);
        } catch (ValidationException $e) {
            throw (new ErrorException($e->getMessage()))->setErrors($e->errors());
        }
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

        return $this->responseSuccess('Success');
    }
}