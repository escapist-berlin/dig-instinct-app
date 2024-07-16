<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class DiscogsAuthController extends Controller
{
    /**
     * Redirect the user to the Discogs authentication page.
     */
    public function redirectToDiscogs(): RedirectResponse
    {
        return Socialite::driver('discogs')->redirect();
    }

    /**
     * Obtain the user information from Discogs.
     * Save the user information and tokens to the database.
     */
    public function handleDiscogsCallback(): RedirectResponse
    {
        $discogsUser = Socialite::driver('discogs')->user();

        // Save user info and tokens to the database
        $user = Auth::user();
        $user->discogs_username = $discogsUser->nickname;
        $user->discogs_oauth_token = $discogsUser->token;
        $user->discogs_oauth_token_secret = $discogsUser->tokenSecret;
        $user->save();

        return redirect('/dashboard');
    }
}
