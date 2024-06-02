<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\DiscogsServer;

class DiscogsController extends Controller
{
    protected $server;

    public function __construct()
    {
        $this->server = new DiscogsServer([
            'identifier' => env('DISCOGS_CONSUMER_KEY'),
            'secret' => env('DISCOGS_CONSUMER_SECRET'),
            'callback_uri' => route('discogs.callback'),
        ]);
    }

    public function redirectToDiscogs()
    {
        $temporaryCredentials = $this->server->getTemporaryCredentials();
        Session::put('oauth.temp.credentials', $temporaryCredentials);

        return redirect($this->server->getAuthorizationUrl($temporaryCredentials));
    }

    public function handleCallback(Request $request)
    {
        $temporaryCredentials = Session::get('oauth.temp.credentials');
        $tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $request->oauth_token, $request->oauth_verifier);

        // Fetch discogs username
        $discogsUsername = $this->server->getUserScreenName($tokenCredentials);

        // Store the discogs_username and discogs_token in the user model
        $user = auth()->user();
        $user->discogs_username = $discogsUsername;
        $user->discogs_token = $tokenCredentials->getSecret();
        $user->save();

        // Redirect to a desired route
        return redirect()->back()->with('status', 'Discogs account linked successfully!');
    }
}
