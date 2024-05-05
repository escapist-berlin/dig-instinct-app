<?php

namespace App\Http\Controllers;

use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Request $request, UserList $userList)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $releases = $userList->releases()
                              ->with(['artists', 'labels', 'genres', 'styles'])
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
    public function update(Request $request, UserList $userList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserList $userList)
    {
        //
    }
}
