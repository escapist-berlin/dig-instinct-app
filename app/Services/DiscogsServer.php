<?php

namespace App\Services;

use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\User;
use Psr\Http\Message\ResponseInterface;

class DiscogsServer extends Server
{
    public function urlTemporaryCredentials(): string
    {
        return 'https://api.discogs.com/oauth/request_token';
    }

    public function urlAuthorization(): string
    {
        return 'https://www.discogs.com/oauth/authorize';
    }

    public function urlTokenCredentials(): string
    {
        return 'https://api.discogs.com/oauth/access_token';
    }

    public function urlUserDetails(): string
    {
        return 'https://api.discogs.com/oauth/identity';
    }

    public function userDetails($data, TokenCredentials $tokenCredentials): User
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $user = new User();
        $user->uid = $data['id'];
        $user->nickname = $data['username'];

        return $user;
    }

    public function userUid($data, TokenCredentials $tokenCredentials): string
    {
        $data = json_decode($data, true);

        return $data['id'];
    }

    public function userEmail($data, TokenCredentials $tokenCredentials): ?string
    {
        // Discogs API does not provide email information
        return null;
    }

    public function userScreenName($data, TokenCredentials $tokenCredentials): ?string
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return $data['username'];
    }
}
