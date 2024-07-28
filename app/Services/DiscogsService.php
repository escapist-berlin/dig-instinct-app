<?php

namespace App\Services;

use App\Models\User;
use App\Traits\DiscogsAuthenticates;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class DiscogsService
{
    use DiscogsAuthenticates;

    protected User $user;
    protected \GuzzleHttp\Client $client;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->client = $this->createAuthenticatedClient($user);
    }

    /**
     * Get release information from Discogs API.
     *
     * @param int $release_id
     * @return array|null
     * @throws GuzzleException
     */
    public function getRelease(int $release_id): ?array
    {
        $url = "https://api.discogs.com/releases/{$release_id}";

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            report($e);
            return null;
        }
    }

    /**
     * Get the user's wantlist from Discogs API.
     *
     * @return array
     * @throws GuzzleException
     */
    public function getWantlist(): array
    {
        $url = "https://api.discogs.com/users/{$this->user->discogs_username}/wants";

        $wantlistData = [];
        $page = 1;
        $perPage = 500;

        do {
            try {
                $response = $this->client->get($url, [
                    'query' => [
                        'page' => $page,
                        'per_page' => $perPage
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                foreach ($data['wants'] as $wantlistItem) {
                    $wantlistData[] = [
                        'release_id' => $wantlistItem['id'],
                        'date_added' => $wantlistItem['date_added']
                    ];
                }

                $page++;
            } catch (RequestException $e) {
                report($e);
                break;
            }
        } while ($page <= $data['pagination']['pages']);

        return $wantlistData;
    }
}



