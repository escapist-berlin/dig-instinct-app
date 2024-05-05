<?php

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class ReleaseController extends Controller
{
    public function index(Request $request) // TODO: sortBy
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $query = Release::with(['artists', 'labels', 'genres', 'styles']);
        $releases = $query->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Dashboard', [
            'releases' => $releases
        ]);
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'discogs_id' => 'required|integer',
            'discogs_master_id' => 'nullable|integer',
            'kollektivx_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'formats' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'released' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'rating_average' => 'nullable|integer',
            'rating_count' => 'nullable|integer',
            'have' => 'nullable|integer',
            'want' => 'nullable|integer',
            'num_for_sale' => 'nullable|integer',
            'lowest_price' => 'nullable|numeric',
            'uri' => 'nullable|string|max:255',
            'kollektivx_uri' => 'nullable|string|max:255',
            'kollektivx_is_raw' => 'nullable|boolean',
            'kollektivx_is_restored' => 'nullable|boolean',
            'image_full_uri' => 'nullable|string|max:255',
            'image_thumbnail_uri' => 'nullable|string|max:255',
        ]);

        $release = Release::create($validatedData);

        return response()->json(['message' => 'Release created successfully', 'release' => $release], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Release $release)
    {
        $release->load('artists', 'labels', 'genres', 'styles', 'tracks.artists', 'userLists'); // OPTIMIZE???

        return Inertia::render('Releases/Show', ['release' => $release]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Release $release)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Release $release)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Release $release)
    {
        //
    }

    public function updateList(Request $request, $releaseId)
    {
        $request->validate([
            'list_id' => 'required|integer|exists:user_lists,id',
        ]);

        $release = Release::findOrFail($releaseId);
        $release->userLists()->sync([$request->list_id]);

        return redirect()->route('releases.show', $release)->with('success', 'List updated successfully.');
    }
}
