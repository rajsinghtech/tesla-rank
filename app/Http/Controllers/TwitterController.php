<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class TwitterController extends Controller
{
    /**
     * Redirect to Twitter
     */
    public function redirectToTwitter(): RedirectResponse
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Handle Twitter authentication callback
     */
    public function handleTwitterCallback(): RedirectResponse
    {
        try {
            $user = Socialite::driver('twitter')->user();
        } catch (Throwable $e) {
            return redirect(route('login'))->with('error', 'Twitter authentication failed.');
        }

        $existingUser = User::where('twitter_id', $user->id)->first();

        if ($existingUser) {
            Auth::login($existingUser);
        } else {
            $newUser = User::updateOrCreate([
                'email' => $user->email,
            ], [
                'name' => $user->name,
                'twitter_id' => $user->id,
                'twitter_username' => $user->nickname,
                'password' => bcrypt(request(Str::random())), // Set a random password
            ]);
            Auth::login($newUser);
        }

        return redirect()->intended('dashboard');
    }
}
