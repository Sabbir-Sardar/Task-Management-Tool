<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    //Register Method
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to Register',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Login Method
    public function login(LoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $user = Auth::user();

            return response()->json([
                'message' => 'Login Successful',
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Logout Method
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'LogOut Successful'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to Logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
