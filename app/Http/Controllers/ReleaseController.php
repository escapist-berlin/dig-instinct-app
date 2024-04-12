<?php

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class ReleaseController extends Controller
{
    public function index()
    {
        // $releaseId = "1159235";

        // $response = Http::get("https://api.discogs.com/releases/{$releaseId}");

        // if ($response->successful()) {
        //     $data = $response->json();

        //     return Inertia::render('Releases/Index', [
        //         'releaseData' => $data
        //     ]);
        // } else {
        //     return response()->json(['error' => 'Failed to fetch data from Discogs API'], $response->status());
        // }
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Release $release)
    {
        //
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
}
