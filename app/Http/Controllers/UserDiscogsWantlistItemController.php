<?php

namespace App\Http\Controllers;

use App\Models\UserDiscogsWantlistItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDiscogsWantlistItemController extends Controller
{
    public function syncUserWantlist(array $allItems)
    {
        $user = Auth::user();

        DB::transaction(function () use ($allItems, $user) {
            $existingItems = UserDiscogsWantlistItem::where('user_id', $user->id)->get();

            // Create a map of existing items by release_id for quick lookup
            $existingItemsMap = $existingItems->keyBy('release_id');

            // Process new items
            foreach ($allItems as $item) {
                if (isset($existingItemsMap[$item['release_id']])) {
                    // Update date_added if different
                    if ($existingItemsMap[$item['release_id']]->date_added != $item['date_added']) {
                        $existingItemsMap[$item['release_id']]->update([
                            'date_added' => $item['date_added']
                        ]);
                    }
                    // Remove from map to keep track of processed items
                    $existingItemsMap->forget($item['release_id']);
                } else {
                    // Insert new item
                    UserDiscogsWantlistItem::create($item);
                }
            }

            // Delete remaining items in the map that were not in the new wantlist
            UserDiscogsWantlistItem::whereIn('id', $existingItemsMap->pluck('id'))->delete();
        });

        return response()->json(['status' => 'success']);
    }
}
