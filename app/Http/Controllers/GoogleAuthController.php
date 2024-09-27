<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function authWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function authWithGoogleCallback()
    {
        $user = Socialite::driver('google')->user();

        $user = User::firstOrCreate([
            'email' => $user->getEmail()
        ], [
            'name' => $user->getName(),
            'google_id' => $user->getId()
        ]);

        Auth::login($user, true);

        return redirect('/dashboard');
    }
}
