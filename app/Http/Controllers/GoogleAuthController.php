<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
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

        Module::updateOrCreate([
            'name' => $user->email,
        ], ['user_id' => $user->id])->saveAsRoot();

        Auth::login($user, true);

        return redirect('/dashboard');
    }
}
