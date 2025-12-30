<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // Email removed from registration per request; will be null by default
            'phone' => 'nullable|string|unique:users,phone',
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

        // If no password provided, generate an internal random password so the DB field is populated.
        $password = $request->filled('password') ? Hash::make($request->password) : Hash::make(Str::random(40));

        $user = User::create([
            'name' => $request->name,
            // Do not store email on register; keep as null unless provided later via profile update
            'email' => null,
            'password' => $password,
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

    /**
     * Return authenticated user's profile data
     */
    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'User profile',
            'status' => true,
            'data' => $request->user(),
        ], 200);
    }

    /**
     * Update authenticated user's profile data
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes','required','string','email','max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable','string', Rule::unique('users','phone')->ignore($user->id)],
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

        $updatable = [
            'name','email','phone','church','school_year','sponsor',
            'favorite_color','favorite_program','favorite_game','favorite_hymn','hobby'
        ];

        foreach ($updatable as $field) {
            if ($request->has($field)) {
                $user->$field = $request->$field;
            }
        }

        // If password is provided (optional), update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated',
            'status' => true,
            'data' => $user,
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
