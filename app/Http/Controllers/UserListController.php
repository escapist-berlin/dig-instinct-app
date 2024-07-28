<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserListRequest;
use App\Jobs\ProcessDiscogsRelease;
use App\Models\Release;
use App\Models\UserList;
use App\Services\DiscogsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UserListController extends Controller
{
    protected DiscogsService $discogsService;

    public function __construct()
    {
        $this->discogsService = new DiscogsService(Auth::user());
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userLists = Auth::user()->userLists()->withCount('releases')->get();

        return Inertia::render('UserLists/Index', ['lists' => $userLists]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserListRequest $request): RedirectResponse
    {
        $request->user()->userLists()->create($request->validated());

        return redirect(route('user-lists.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, UserList $userList)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $query = $request->input('query', '');
        $searchOption = $request->input('search_option', '');

        $releases = $userList->releases()
                              ->with(['artists', 'labels', 'genres', 'styles'])
                              ->tap(fn ($q) => $q->withSearch($query, $searchOption))
                              ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('UserLists/Show', [
            'list' => $userList,
            'releases' => $releases
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserList $userList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserListRequest $request, UserList $userList): RedirectResponse
    {
        Gate::authorize('update', $userList);

        $userList->update($request->validated());

        return redirect(route('user-lists.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserList $userList)
    {
        $userList->delete();

        return redirect(route('user-lists.index'));
    }

    public function syncDiscogsWantlist(): void
    {
        $user = Auth::user();
        $wantlistData = $this->discogsService->getWantlist();

        $discogsWantlistUserList = UserList::where('user_id', $user->id)
            ->where('name', 'Discogs Wantlist')
            ->first();

        $releases = $discogsWantlistUserList->releases()
            ->withPivot('date_added_to_wantlist')
            ->get();

        $existingReleaseIds = $releases->pluck('discogs_id')->toArray();
        $wantlistReleaseIds = array_column($wantlistData, 'release_id');

        // Find releases in Discogs Wantlist that are not in the local $discogsWantlistUserList
        $newReleases = array_filter($wantlistData, function($item) use ($existingReleaseIds) {
            return !in_array($item['release_id'], $existingReleaseIds);
        });

        // Find releases in local $discogsWantlistUserList that are not in the Discogs Wantlist and remove them
        $releasesToRemoveDiscogsIds = array_diff($existingReleaseIds, $wantlistReleaseIds);

        // Map discogs_id to release_id for releases to remove
        $releasesToRemove = Release::whereIn('discogs_id', $releasesToRemoveDiscogsIds)->pluck('id')->toArray();

        foreach ($newReleases as $newRelease) {
            $release = Release::where('discogs_id', $newRelease['release_id'])->first();

            if (!$release) {
                // Dispatch job to process and add the new release
                ProcessDiscogsRelease::dispatch($newRelease, $discogsWantlistUserList, $user);
            } else {
                // Add release to $discogsWantlistUserList
                $discogsWantlistUserList->releases()->syncWithoutDetaching([$release->id]);
            }
        }

        // Remove releases from $discogsWantlistUserList if they are missing in $wantlistData
        if (!empty($releasesToRemove)) {
            $discogsWantlistUserList->releases()->detach($releasesToRemove);
        }
    }
}
