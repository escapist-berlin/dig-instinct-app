<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? array_merge($request->user()->only('name'), [
                    'discogs_connected' => !empty($request->user()->discogs_username) && !empty($request->user()->discogs_token),
                ]) : null,
                'lists' => $request->user()?->userLists()->get(),
            ],
            'urlPrev' => function() {
                $prevUrl = url()->previous();
                return ($prevUrl !== route('login') && $prevUrl && $prevUrl !== url()->current()) ? $prevUrl : null;
            },
        ];
    }
}
