<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'discogs_id',
        'discogs_master_id',
        'kollektivx_id',
        'title',
        'formats',
        'country',
        'released',
        'year',
        'rating_average',
        'rating_count',
        'have',
        'want',
        'num_for_sale',
        'lowest_price',
        'uri',
        'kollektivx_uri',
        'kollektivx_is_raw',
        'kollektivx_is_restored',
        'image_full_uri',
        'image_thumbnail_uri'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'release_user');
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'release_artist');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'release_label')->withPivot('catno');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'release_genre');
    }

    public function styles()
    {
        return $this->belongsToMany(Style::class, 'release_style');
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function userLists()
    {
        return $this->belongsToMany(UserList::class, 'release_list');
    }

    public function scopeWithSearch(Builder $query, $searchQuery, $searchOption)
    {
        if (!empty($searchQuery)) {
            switch ($searchOption) {
                case 'artist':
                    $query->whereHas('artists', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', '%' . $searchQuery . '%');
                    });
                    break;

                case 'title':
                    $query->where('title', 'like', '%' . $searchQuery . '%');
                    break;

                case 'artist-title':
                    if (strpos($searchQuery, '-') !== false) {
                        list($artistPart, $titlePart) = explode('-', $searchQuery, 2);
                        $query->whereHas('artists', function ($q) use ($artistPart) {
                            $q->where('name', 'like', '%' . trim($artistPart) . '%');
                        })->where('title', 'like', '%' . trim($titlePart) . '%');
                    }
                    break;

                case 'label':
                    $query->whereHas('labels', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', '%' . $searchQuery . '%');
                    });
                    break;

                case 'country':
                    $query->where('country', 'like', '%' . $searchQuery . '%');
                    break;

                case 'genre':
                    $query->whereHas('genres', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', '%' . $searchQuery . '%');
                    });
                    break;

                case 'style':
                    $query->whereHas('styles', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', '%' . $searchQuery . '%');
                    });
                    break;
            }
        }
    }

}
