<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): JsonResponse
    {
        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'))) {
            // Get the authenticated user
            $user = Auth::user();

            // Generate a token for the user
            $token = $user->createToken('authToken')->plainTextToken;

            // Return the token as a JSON response
            return response()->json(['token' => $token, 'user' => $user]);
        }

        // Check if the email exists in the database
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            // Email not found
            return response()->json(['message' => 'Email not found'], 401);
        }

        // Password does not match
        return response()->json(['message' => 'Password does not match'], 401);
    }


    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(RouteServiceProvider::HOME);
    // }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}