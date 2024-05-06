<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
      $user = User::where('user_name', $request->user_name)->first();

      if (! $user || ! Hash::check($request->password, $user->password)) {
         return response()->json([
           'message' => 'The provided credentials are incorrect.'
         ], Response::HTTP_UNPROCESSABLE_ENTITY);

      }

      return response()->json([
             'data' => [
             'attributes'=> [
                 'id' => $user->id,
                 'name' => $user->name,
                 'email' => $user->email
              ],
             'token' => $user->createToken($request->device_name)->plainTextToken
           ],
          ], Response::HTTP_OK);
    }

}

