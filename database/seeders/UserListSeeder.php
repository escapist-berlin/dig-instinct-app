<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Release;
use App\Models\UserList;

class UserListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Ensure to clear old data
        UserList::where('user_id', 1)->delete();

        $listNames = [
            'Discovery Queue',
            'KollektivX Archive',
            'Rekordbox Ready',
            'In My Library',
            'No-Go Grooves',
        ];

        $userLists = [];
        foreach ($listNames as $listName) {
          $userLists[$listName] = UserList::create([
                'user_id' => 1,
                'name' => $listName,
            ]);
        }

        // Add "KollektivX releases" to "KollektivX Archive" user list
        $releasesWithKollektivXId = Release::whereNotNull('kollektivx_id')->get();
        foreach ($releasesWithKollektivXId as $release) {
            $userLists['KollektivX Archive']->releases()->attach($release->id);
        }
    }
}
