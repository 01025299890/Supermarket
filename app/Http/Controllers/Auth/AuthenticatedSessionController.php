<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $user = Auth::user();

        // إنشاء توكن جديد للمستخدم
        $token = $user->createToken('api-token')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function destroy(Request $request): Response
    {
        // مسح كل التوكنز الخاصة بالمستخدم
        $request->user()->tokens()->delete();

        return response()->noContent();
    }
}
