<?php

use App\Livewire\MostAnticipated;
use App\Livewire\PopularGames;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('should see the main page shows most anticipated games', function () {
    $response = $this->get(route('games.index'));

    Livewire::test(MostAnticipated::class)
        ->assertSet('mostAnticipated', [])
        ->call('loadMostAnticipated');

    $response->assertSuccessful();
});
