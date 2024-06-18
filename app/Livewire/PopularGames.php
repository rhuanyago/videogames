<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class PopularGames extends Component
{
    public array $popularGames = [];

    public function loadPopularGames()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $after  = Carbon::now()->addMonths(2)->timestamp;

        $popularGamesUnformatted = Cache::remember('popular-games', 7, function () use ($before, $after) {
            return Http::withHeaders([
                'Client-ID'     => config('igdb.credentials.client_id'),
                'Authorization' => 'Bearer ' . config('igdb.credentials.client_secret'),
            ])
                ->withBody(
                    "fields name, cover.url, first_release_date, rating, total_rating_count, platforms.abbreviation, slug;
                where platforms = (48,49,130,6)
                & cover.url != null & (first_release_date >= {$before} & first_release_date < {$after});
                sort rating desc;
                limit 12;"
                )
                ->post('https://api.igdb.com/v4/games')->json();
        });

        $this->popularGames = $this->formatForView($popularGamesUnformatted);
    }

    public function render()
    {
        return view('livewire.popular-games');
    }

    private function formatForView(array $games)
    {
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'coverImageUrl' => isset($game['cover']) ? Str::replace('thumb', 'cover_big', $game['cover']['url']) : null,
                'rating'        => isset($game['rating']) ? round($game['rating']) . '%' : null,
                'platforms'     => isset($game['platforms']) ? collect($game['platforms'])->pluck('abbreviation')->implode(', ') : null,
            ]);
        })->toArray();
    }
}
