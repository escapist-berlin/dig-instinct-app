<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserList;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Retrieve the "KollektivX Archive" list for the authenticated user
        $user = $request->user();
        $kollektivXArchiveList = UserList::where('user_id', $user->id)
                                          ->where('name', 'KollektivX Archive')
                                          ->first();

        // If the list exists, redirect to it, otherwise redirect to a default route
        if ($kollektivXArchiveList) {
            return redirect()->route('user-lists.show', ['user_list' => $kollektivXArchiveList->id]);
        } else {
            return redirect()->intended(route('dashboard', absolute: false));
        }
    }
}
