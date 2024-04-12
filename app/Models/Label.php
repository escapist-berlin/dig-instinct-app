<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'discogs_id',
        'name',
    ];

    public function releases()
    {
        return $this->belongsToMany(Release::class, 'release_label');
    }
}
