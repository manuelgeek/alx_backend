<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6'
        ]);

        $data = $request->all();
        //no email verification
        $data['email_verified_at'] = now();
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);
        return response()->json([
            'message' => 'Registration Successful',
            'token' => $user->createToken(env('APP_NAME'))->plainTextToken,
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'string']
        ]);

        $user = User::where('email', $request->email)->first();

        if($user) {
            if (! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            return response()->json([
                'message' => 'Login successfull',
                'token' => $user->createToken(env('APP_NAME'))->plainTextToken,
                'user' => $user
            ]);
        }
        return response()->json([
            'message' => 'No User with email',
        ], 404);
    }

    public function userDetails()
    {
        return response()->json([
            'user' => auth()->user()
        ]);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out!'
        ]);
    }
}
