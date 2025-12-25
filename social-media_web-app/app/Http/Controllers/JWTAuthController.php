<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return \response()->json([
                'status' => 200,
                'message' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'user' => $user,
            'token' => $token
        ],200);
    }
    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'errors' => 'Invalid Credentials'
            ], 401);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Login Berhasil',
            'token' => $token
        ]);
    }
}
