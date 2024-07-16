<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DiscogsReleaseController;
use App\Models\Release;
use App\Models\Artist;
use App\Models\Label;
use App\Models\Genre;
use App\Models\Style;
use App\Models\Track;
use App\Models\UserList;
use App\Traits\DiscogsAuthenticates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class DiscogsController extends Controller
{
    use DiscogsAuthenticates;

    protected $userDiscogsWantlistItemController;

    public function __construct(UserDiscogsWantlistItemController $userDiscogsWantlistItemController)
    {
        $this->userDiscogsWantlistItemController = $userDiscogsWantlistItemController;
    }

    public function updateDiscogsWantlist(): void
    {
        $user = Auth::user();
        $client = $this->createAuthenticatedClient();
        $url = "https://api.discogs.com/users/{$user->discogs_username}/wants";

        $allItems = [];
        $page = 1;
        $perPage = 500;

        do {
            $response = $client->get($url, [
                'query' => [
                    'page' => $page,
                    'per_page' => $perPage
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            foreach ($data['wants'] as $want) {
                $allItems[] = [
                    'user_id' => $user->id,
                    'release_id' => $want['id'],
                    'date_added' => $want['date_added']
                ];
            }

            $page++;
        } while ($page <= $data['pagination']['pages']);

        // Synchronize the wantlist items
        $this->userDiscogsWantlistItemController->syncUserWantlist($allItems);
    }
}
