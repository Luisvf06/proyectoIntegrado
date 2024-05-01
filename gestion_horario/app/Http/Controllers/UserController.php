<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Retrieve a user by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getUser($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User found.',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email' => 'email|unique:users',
            'password' => 'required|string',
        ]);
        User::create()([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User created successfully.'
        ], 201);
    }
}
