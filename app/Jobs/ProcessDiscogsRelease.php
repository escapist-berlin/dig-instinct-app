<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\DiscogsService;
use App\Models\Release;
use App\Models\Artist;
use App\Models\Label;
use App\Models\Genre;
use App\Models\Style;
use App\Models\Track;
use App\Models\UserList;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDiscogsRelease implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $wantListItem;
    protected UserList $userList;
    protected User $user;

    /**
     * Create a new job instance.
     *
     * @param array $wantListItem
     * @param UserList $userList
     * @param User $user
     */
    public function __construct(array $wantListItem, UserList $userList, User $user)
    {
        $this->wantListItem = $wantListItem;
        $this->userList = $userList;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     */
    public function handle(): void
    {
        // Rate limiting: ensure this job does not start until 1 second has passed since the last one
        sleep(1);

        $discogsService = new DiscogsService($this->user);
        $releaseData = $discogsService->getRelease($this->wantListItem['release_id']);


        // TODO: Handle the case where the API request fails or returns no data
        if (!$releaseData) {
            Log::error('Failed to fetch release data from Discogs API', [
                'release_id' => $this->wantListItem['release_id'],
                'user_id' => $this->user->id
            ]);

            return;
        }

        $releaseDataArray = [
            'discogs_id' => $releaseData['id'],
            'discogs_master_id' => $releaseData['master_id'] ?? null,
            'title' => $releaseData['title'],
            'formats' => $this->parseFormats($releaseData['formats']),
            'country' => $releaseData['country'] ?? null,
            'released' => $releaseData['released'] ?? null,
            'year' => $releaseData['year'],
            'rating_average' => $releaseData['community']['rating']['average'],
            'rating_count' => $releaseData['community']['rating']['count'],
            'have' => $releaseData['community']['have'],
            'want' => $releaseData['community']['want'],
            'num_for_sale' => $releaseData['num_for_sale'],
            'lowest_price' => $releaseData['lowest_price'],
            'uri' => $releaseData['uri'],
            'image_full_uri' => $releaseData['images'][0]['uri'] ?? null,
            'image_thumbnail_uri' => $releaseData['images'][0]['uri150'] ?? null,
        ];

        $release = Release::updateOrCreate(
            ['discogs_id' => $releaseData['id']],
            $releaseDataArray
        );

        // Check if the release was successfully stored in the database
        $storedRelease = Release::find($release->id);

        if ($storedRelease) {
            // Sync release to userList without detaching existing ones, using 'date_added' from Discogs API.
            $this->userList->releases()->syncWithoutDetaching([
                $release->id => ['date_added_to_wantlist' => $this->wantListItem['date_added']]
            ]);

            $this->storeRelatedData($release, $releaseData);
        } else {
            // Log relevant information if the release was not stored
            Log::error('Release was not stored successfully:', [
                'Title' => $releaseData['title'],
                'Discogs ID' => $releaseData['id']
            ]);
        }
    }

    private function storeRelatedData($release, $releaseData): void
    {
        foreach ($releaseData['artists'] as $artistData) {
            $artist = Artist::updateOrCreate(
                ['discogs_id' => $artistData['id']],
                ['name' => $artistData['name']]
            );
            $release->artists()->syncWithoutDetaching([$artist->id]);
        }

        foreach ($releaseData['labels'] as $labelData) {
            $label = Label::updateOrCreate(
                ['discogs_id' => $labelData['id']],
                ['name' => $labelData['name']]
            );
            $release->labels()->syncWithoutDetaching([$label->id => ['catno' => $labelData['catno']]]);
        }

        foreach ($releaseData['genres'] as $genreData) {
            $genre = Genre::updateOrCreate(
                ['name' => $genreData]
            );
            $release->genres()->syncWithoutDetaching([$genre->id]);
        }

        if (isset($releaseData['styles'])) {
            foreach ($releaseData['styles'] as $styleData) {
                $style = Style::updateOrCreate(
                    ['name' => $styleData]
                );
                $release->styles()->syncWithoutDetaching([$style->id]);
            }
        }

        foreach ($releaseData['tracklist'] as $trackData) {
            if (isset($trackData['type_']) && $trackData['type_'] === 'track') {
                $track = Track::updateOrCreate([
                    'release_id' => $release->id,
                    'title' => $trackData['title'],
                    'duration' => $trackData['duration'],
                    'position' => $trackData['position']
                ]);

                if (isset($trackData['artists'])) {
                    foreach ($trackData['artists'] as $artistData) {
                        $artist = Artist::where('discogs_id', $artistData['id'])->first();

                        if (!$artist) {
                            $artist = Artist::updateOrCreate([
                                'discogs_id' => $artistData['id'],
                                'name' => $artistData['name']
                            ]);
                        }

                        $track->artists()->syncWithoutDetaching([$artist->id]);
                    }
                }
            }
        }
    }

    private function parseFormats($formats): string
    {
        return collect($formats)->map(function ($format) {
            $parts = [];

            if (isset($format['name'])) {
                $parts[] = $format['name'];
            }

            if (isset($format['descriptions'][0])) {
                $parts[] = $format['descriptions'][0];
            }

            if (isset($format['descriptions'][1])) {
                $parts[] = $format['descriptions'][1];
            }

            return implode(', ', $parts);
        })->implode('; ');
    }
}
