<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find or create user
            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail()
            ], [
                'name' => $googleUser->getName(),
                'password' => Hash::make(uniqid()),  // You might want to handle password differently
            ]);

            // Login the user
            Auth::login($user, true);

            // Generate a token for API access
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'message' => 'Logged in with Google successfully!'
            ]);

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors('Unable to login with Google: ' . $e->getMessage());
        }
    }
}