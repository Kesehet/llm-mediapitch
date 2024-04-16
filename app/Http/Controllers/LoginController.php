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
            $user = Socialite::driver('google')->user();

            // Here you would find or create a user in your database
            // Create a token or log in the user directly

            return redirect()->to('/home'); // or wherever you want to redirect
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors('Login failed: ' . $e->getMessage());
        }
    }
}