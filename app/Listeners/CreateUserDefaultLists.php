<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Release;
use App\Models\UserList;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserDefaultLists
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        // Create default lists
        UserList::create([
            'name' => 'Discogs Wantlist',
            'user_id' => $user->id,
            'is_default' => 1,
        ]);

        $kollektivXArchive = UserList::create([
            'name' => 'KollektivX Archive',
            'user_id' => $user->id,
            'is_default' => 1,
        ]);

        // Add "KollektivX releases" to "KollektivX Archive" list
        $releasesWithKollektivXId = Release::whereNotNull('kollektivx_id')->get();
        foreach ($releasesWithKollektivXId as $release) {
            $kollektivXArchive->releases()->attach($release->id);
        }
    }
}
