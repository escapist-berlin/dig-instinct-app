<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'discogs_id',
        'name',
    ];

    public function releases()
    {
        return $this->belongsToMany(Release::class, 'release_artist');
    }

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'artist_track');
    }
}
