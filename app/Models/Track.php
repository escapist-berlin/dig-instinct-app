<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration',
        'position',
    ];

    public function release()
    {
        return $this->belongsTo(Release::class);
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'artist_track');
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_track');
    }
}
