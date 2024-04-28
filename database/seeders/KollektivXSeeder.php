<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Release;
use App\Models\Style;

class KollektivXSeeder extends Seeder
{

    public function run(): void
    {
        // Read JSON files
        $kollektivxDataFilePath = 'storage/imports/kollektivxData.json';
        $KollektivxDiscogsApiDataFilePath = 'storage/imports/KollektivxDiscogsApiData.json';

        $kollektivxJsonData = File::get($kollektivxDataFilePath);
        $KollektivxDiscogsApiJsonData = File::get($KollektivxDiscogsApiDataFilePath);

        // Decode JSON data
        $kollektivxData = json_decode($kollektivxJsonData, true);
        $KollektivxDiscogsApiData = json_decode($KollektivxDiscogsApiJsonData, true);

        foreach ($kollektivxData as $index => $data) {
            $releaseData = [
                'discogs_id' => $KollektivxDiscogsApiData[$index]['id'],
                'discogs_master_id' => $KollektivxDiscogsApiData[$index]['master_id'] ?? null,
                'kollektivx_id' => intval($data['kollektivx_id']),
                'title' => $KollektivxDiscogsApiData[$index]['title'],
                'formats' => $this->parseFormats($KollektivxDiscogsApiData[$index]['formats']),
                'country' => $KollektivxDiscogsApiData[$index]['country'] ?? null,
                'released' => $KollektivxDiscogsApiData[$index]['released'] ?? null,
                'year' => $KollektivxDiscogsApiData[$index]['year'],
                'rating_average' => $KollektivxDiscogsApiData[$index]['community']['rating']['average'],
                'rating_count' => $KollektivxDiscogsApiData[$index]['community']['rating']['count'],
                'have' => $KollektivxDiscogsApiData[$index]['community']['have'],
                'want' => $KollektivxDiscogsApiData[$index]['community']['want'],
                'num_for_sale' => $KollektivxDiscogsApiData[$index]['num_for_sale'],
                'lowest_price' => $KollektivxDiscogsApiData[$index]['lowest_price'],
                'uri' => $KollektivxDiscogsApiData[$index]['uri'],
                'kollektivx_uri' => $data['kollektivx_uri'],
                'kollektivx_is_raw' => $data['kollektivx_is_raw'],
                'kollektivx_is_restored' => $data['kollektivx_is_restored'],
                'image_full_uri' => $KollektivxDiscogsApiData[$index]['images'][0]['uri'] ?? null,
                'image_thumbnail_uri' => $KollektivxDiscogsApiData[$index]['images'][0]['uri150'] ?? null,
            ];

            $release = Release::create($releaseData);

            foreach ($KollektivxDiscogsApiData[$index]['artists'] as $artistData) {
                $artist = Artist::firstOrCreate(
                    ['discogs_id' => $artistData['id']],
                    ['name' => $artistData['name']]
                );
                // Attach artist to release without duplicating
                $release->artists()->syncWithoutDetaching([$artist->id]);
            }

            foreach ($KollektivxDiscogsApiData[$index]['labels'] as $labelData) {
                $label = Label::firstOrCreate(
                    ['discogs_id' => $labelData['id']],
                    ['name' => $labelData['name']]
                );
                // Attach label to release without duplicating
                $release->labels()->syncWithoutDetaching([$label->id]);
            }

            foreach ($KollektivxDiscogsApiData[$index]['genres'] as $genreData) {
                $genre = Genre::firstOrCreate(
                    ['name' => $genreData]
                );
                // Attach genre to release without duplicating
                $release->genres()->syncWithoutDetaching([$genre->id]);
            }

            if (isset($KollektivxDiscogsApiData[$index]['styles'])) {
                foreach ($KollektivxDiscogsApiData[$index]['styles'] as $styleData) {
                    $style = Style::firstOrCreate(
                        ['name' => $styleData]
                    );
                    // Attach genre to release without duplicating
                    $release->styles()->syncWithoutDetaching([$style->id]);
                }
            }
        }

    }

    private function parseFormats($formats)
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
