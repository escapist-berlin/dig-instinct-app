<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration',
        'uri',
    ];

    public function release()
    {
        return $this->belongsTo(Release::class);
    }
}
