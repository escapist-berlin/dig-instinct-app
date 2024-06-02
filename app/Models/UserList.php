<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function releases()
    {
        return $this->belongsToMany(Release::class, 'release_list');
    }
}
