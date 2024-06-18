<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class ComingSoon extends Component
{
    public array $comingSoon = [];

    public function loadComingSoon()
    {
        $current = Carbon::now()->timestamp;

        $comingSoonUnformatted = Http::withHeaders([
            'Client-ID'     => config('igdb.credentials.client_id'),
            'Authorization' => 'Bearer ' . config('igdb.credentials.client_secret'),
        ])
            ->withBody(
                "fields name, cover.url, first_release_date, rating, total_rating_count, platforms.abbreviation, summary, slug;
            where platforms = (48,49,130,6) & (first_release_date > {$current});
            sort first_release_date asc;
            limit 4;"
            )
            ->post('https://api.igdb.com/v4/games')->json();

        $this->comingSoon = $this->formatForView($comingSoonUnformatted);
    }

    public function render()
    {
        return view('livewire.coming-soon');
    }

    private function formatForView(array $games)
    {
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb', 'cover_small', data_get($game, 'cover.url')),
                'releaseDate'   => Carbon::parse(data_get($game, 'first_release_date'))?->format('M d, Y'),
            ]);
        })->toArray();
    }
}
