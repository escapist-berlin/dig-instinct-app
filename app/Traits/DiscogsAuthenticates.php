<?php

namespace App\Traits;

use App\Models\User;
use GuzzleHttp\Client;

trait DiscogsAuthenticates
{
    /**
     * Create an authenticated HTTP client for Discogs API requests.
     * @param User $user
     * @return Client
     */
    protected function createAuthenticatedClient(User $user): Client
    {
        $token = $user->discogs_oauth_token;
        $tokenSecret = $user->discogs_oauth_token_secret;

        $oauth_consumer_key = env('DISCOGS_CLIENT_ID');
        $oauth_signature_method = 'PLAINTEXT';
        $oauth_signature = env('DISCOGS_CLIENT_SECRET') . "&" . $tokenSecret;

        $authorization = "OAuth oauth_consumer_key=\"$oauth_consumer_key\", oauth_token=\"$token\", oauth_signature_method=\"$oauth_signature_method\", oauth_signature=\"$oauth_signature\"";

        return new Client([
            'headers' => [
                'Authorization' => $authorization,
                'User-Agent' => 'YourAppName/1.0',
            ]
        ]);
    }
}
