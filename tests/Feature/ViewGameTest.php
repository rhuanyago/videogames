<?php

use Illuminate\Support\Facades\Http;

it('should see the game correct info', function () {
    $response = $this->get(route('games.show', 'wuthering-waves'));
    $response->assertSuccessful();
});
