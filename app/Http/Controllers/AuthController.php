<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\{
  Facades\Auth,
  Str
};

class AuthController extends Controller
{

  public function VerifyEmail(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email',
    ]);

    $user = User::where('email', $request->email)->get();
    return $user->isEmpty() ? abort(404, 'Resource not found') : $user;
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    $credentials = ([
      'email' => $request->email,
      'password' => $request->password,
    ]);

    if (!Auth::attempt($credentials))
      return response()->json([
        'message' => 'Unauthorized'
      ], 401);

    $user = $request->user();

    $token = $user->createToken('FlamingoAPI');

    return response()->json([
      'token_type' => 'Bearer',
      'access_token' => $token->accessToken,
    ]);
  }

  public function register(Request $request)
  {
    $request->validate([
      'name' => 'required|string',
      'email' => 'required|string|email|unique:users',
      'password' => 'required|string'
    ]);

    $user = User::create([
      'uuid' => Str::uuid(),
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password)
    ]);

    return response()->json([
      'token_type' => 'Bearer',
      'access_token' => $user->createToken('FlamingoAPI')->accessToken
    ], 201);
  }

  public function user(Request $request)
  {
    return response()->json($request->user());
  }

  public function logout(Request $request)
  {
    $request->user()->token()->revoke();

    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }
}
