<?php

use App\Livewire\PopularGames;
use App\Livewire\RecentlyReviewed;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('should see the main page shows recently games', function () {
    $response = $this->get(route('games.index'));

    Livewire::test(RecentlyReviewed::class)
        ->assertSet('recentlyReviewed', [])
        ->call('loadRecentlyReviewed');

    $response->assertSuccessful();
});
