<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'church' => 'nullable|string|max:255',
            'school_year' => 'nullable|string|max:255',
            'sponsor' => 'nullable|string|max:255',
            'favorite_color' => 'nullable|string|max:255',
            'favorite_program' => 'nullable|string|max:255',
            'favorite_game' => 'nullable|string|max:255',
            'favorite_hymn' => 'nullable|string|max:255',
            'hobby' => 'nullable|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'status' => false,
                'data' => $validator->errors(),
            ], 422);
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'church' => $request->church,
            'school_year' => $request->school_year,
            'sponsor' => $request->sponsor,
            'favorite_color' => $request->favorite_color,
            'favorite_program' => $request->favorite_program,
            'favorite_game' => $request->favorite_game,
            'favorite_hymn' => $request->favorite_hymn,
            'hobby' => $request->hobby,
        ]);
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'User registered successfully',
            'status' => true,
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }    

    public function login(Request $request)
    {
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details',
                'status' => false,
                'data' => null,
            ], 401);
        }
    
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful',
            'status' => true,
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }
    

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    
        return response()->json([
            'message' => 'Successfully logged out',
            'status' => true,
            'data' => null,
        ], 200);
    }
}
