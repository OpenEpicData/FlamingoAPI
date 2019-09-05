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
  /**
   * login api
   *
   * @return \Illuminate\Http\Response
   */
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

  /**
   * Register api
   *
   * @return \Illuminate\Http\Response
   */
  public function register(Request $request)
  {
    $request->validate([
      'name' => 'required|string',
      'email' => 'required|string|email|unique:users',
      'password' => 'required|string|confirmed'
    ]);

    $user = User::create([
      'uuid' => Str::uuid(),
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password)
    ]);

    return response()->json([
      'message' => 'Successfully created user!',
      'token' => $user->createToken('FlamingoAPI')->accessToken
    ], 201);
  }

  /**
   * Get the authenticated User
   *
   * @return [json] user object
   */
  public function user(Request $request)
  {
    return response()->json($request->user());
  }

  /**
   * Logout user (Revoke the token)
   *
   * @return [string] message
   */
  public function logout(Request $request)
  {
    $request->user()->token()->revoke();

    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }
}
