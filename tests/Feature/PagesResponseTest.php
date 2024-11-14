<?php

use function Pest\Laravel\get;

it('returns a successful response for home page', function () {
    get('/')
        ->assertOk();
});
