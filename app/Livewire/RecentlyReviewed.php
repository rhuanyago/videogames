<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class RecentlyReviewed extends Component
{
    public array $recentlyReviewed = [];

    public function loadRecentlyReviewed()
    {
        $before  = Carbon::now()->subMonths(2)->timestamp;
        $current = Carbon::now()->timestamp;

        $recentlyReviewedUnformatted = Http::withHeaders([
            'Client-ID'     => config('igdb.credentials.client_id'),
            'Authorization' => 'Bearer ' . config('igdb.credentials.client_secret'),
        ])
            ->withBody(
                "fields name, cover.url, first_release_date, rating, total_rating_count, platforms.abbreviation, summary, slug;
            where platforms = (48,49,130,6) & cover.url != null
            & (first_release_date >= {$before} & first_release_date < {$current} & total_rating_count > 5);
            sort rating desc;
            limit 3;"
            )
            ->post('https://api.igdb.com/v4/games')->json();

        $this->recentlyReviewed = $this->formatForView($recentlyReviewedUnformatted);
    }

    public function render()
    {
        return view('livewire.recently-reviewed');
    }

    private function formatForView(array $games)
    {
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
                'rating'        => isset($game['rating']) ? round($game['rating']) . '%' : null,
                'platforms'     => isset($game['platforms']) ? collect($game['platforms'])->pluck('abbreviation')->implode(', ') : null,
            ]);
        })->toArray();
    }
}
