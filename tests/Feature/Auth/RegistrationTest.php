<?php

use App\Models\UserList;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $kollektivXArchiveList = UserList::where('user_id', auth()->id())
        ->where('name', 'KollektivX Archive')
        ->first();

    $response->assertRedirect(route('user-lists.show', ['user_list' => $kollektivXArchiveList->id], absolute: false));
});
