<?php

use App\Livewire\PopularGames;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('should see the main page shows popular games', function () {
    $response = $this->get(route('games.index'));

    Livewire::test(PopularGames::class)
        ->assertSet('popularGames', [])
        ->call('loadPopularGames');

    $response->assertSuccessful();
});
