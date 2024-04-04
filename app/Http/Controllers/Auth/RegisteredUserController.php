<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Channel; // Make sure to import the Channel model
use App\Models\WaterParam; // Make sure to import the WaterParam model

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return response(['can access'], 200);
        // return view('auth.register');
        // return json_encode("can access");

    }

    public function destroy(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Delete the user's channels along with associated water parameters
        $user->channels->each(function ($channel) {
            $channel->waterparams()->delete(); // Delete associated water parameters
            $channel->actions()->delete();
            $channel->delete(); // Delete the channel
        });

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity status code
        }

        // Attempt to create the user
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            // Return a JSON response indicating successful registration
            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'complete' => true,
            ]);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs during user creation
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(), // You can customize the error message here
            ], 500); // 500 Internal Server Error status code
        }
    }
}