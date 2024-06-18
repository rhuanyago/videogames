<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class MostAnticipated extends Component
{
    public array $mostAnticipated = [];

    public function loadMostAnticipated()
    {
        $current         = Carbon::now()->timestamp;
        $afterFourMonths = Carbon::now()->addMonths(4)->timestamp;

        $mostAnticipatedUnformatted = Http::withHeaders([
            'Client-ID'     => config('igdb.credentials.client_id'),
            'Authorization' => 'Bearer ' . config('igdb.credentials.client_secret'),
        ])
            ->withBody(
                "fields name, cover.url, first_release_date, rating, total_rating_count, platforms.abbreviation, summary, slug;
            where platforms = (48,49,130,6)
            & (first_release_date >= {$current} & first_release_date < {$afterFourMonths});
            sort first_release_date asc;
            limit 4;"
            )
            ->post('https://api.igdb.com/v4/games')->json();

        $this->mostAnticipated = $this->formatForView($mostAnticipatedUnformatted);
    }

    public function render()
    {
        return view('livewire.most-anticipated');
    }

    private function formatForView(array $games)
    {
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb', 'cover_small', $game['cover']['url']),
                'releaseDate'   => Carbon::parse($game['first_release_date'])->format('M d, Y'),
            ]);
        })->toArray();
    }
}
