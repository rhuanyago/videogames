<?php

use App\Livewire\ComingSoon;
use App\Livewire\MostAnticipated;
use App\Livewire\PopularGames;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('should see the main page shows coming soon games', function () {
    $response = $this->get(route('games.index'));

    Livewire::test(ComingSoon::class)
        ->assertSet('comingSoon', [])
        ->call('loadComingSoon');

    $response->assertSuccessful();
});
