<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artists()
    {
    return $this->belongsToMany(Artist::class, 'release_artist');
    }

    public function labels()
    {
    return $this->belongsToMany(Label::class, 'release_label');
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
}
