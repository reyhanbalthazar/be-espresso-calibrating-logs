<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CoffeeShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'coffee_shop_id' => 'nullable|exists:coffee_shops,id', // For joining existing coffee shop
            'create_coffee_shop' => 'nullable|boolean', // To create new coffee shop
            'coffee_shop_name' => 'nullable|string|max:150|required_if:create_coffee_shop,1', // Required if creating new coffee shop
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle coffee shop assignment
        $coffeeShopId = null;

        if ($request->create_coffee_shop) {
            // Create new coffee shop
            $coffeeShop = CoffeeShop::create([
                'name' => $request->coffee_shop_name,
                'address' => $request->coffee_shop_address ?? null,
                'phone' => $request->coffee_shop_phone ?? null,
                'email' => $request->coffee_shop_email ?? null,
            ]);
            $coffeeShopId = $coffeeShop->id;
        } elseif ($request->coffee_shop_id) {
            // Join existing coffee shop
            $coffeeShopId = $request->coffee_shop_id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'coffee_shop_id' => $coffeeShopId,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Registration successful'
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all existing tokens (optional)
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke all tokens for this user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
