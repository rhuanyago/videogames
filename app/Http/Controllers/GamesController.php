<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $before          = Carbon::now()->subMonths(2)->timestamp;
        $after           = Carbon::now()->addMonths(2)->timestamp;
        $current         = Carbon::now()->timestamp;
        $afterFourMonths = Carbon::now()->addMonths(4)->timestamp;

        // $client = new \GuzzleHttp\Client (['base_uri' => 'https://api.igdb.com/v4/']);

        // $response = $client->request('POST', 'multiquery', [
        //     'headers' => [
        //         'Client-ID' => config('igdb.credentials.client_id'),
        //         'Authorization' => 'Bearer ' . config('igdb.credentials.client_secret'),
        //     ],
        //     'body' => '
        //         query games "Playstation" {
        //             fields name, rating, total_rating_count, platforms.name, first_release_date;
        //             where platforms = {6,48,130,49};
        //             sort total_rating_count desc;
        //             limit 2;
        //         };
        //     '
        // ]);

        // $body = $response->getBody();

        // dump(json_decode($body));

        return view('index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $game = Http::withHeaders([
            'Client-ID'     => config('igdb.credentials.client_id'),
            'Authorization' => 'Bearer ' . config('igdb.credentials.client_secret'),
        ])
            ->withBody(
                "fields name, cover.url, first_release_date, rating, total_rating_count, platforms.abbreviation, slug, involved_companies.company.name,
            genres.name, aggregated_rating, summary, websites.*, videos.*, screenshots.*, similar_games.cover.url, similar_games.name, similar_games.rating,
            similar_games.platforms.abbreviation, similar_games.slug;
                where slug = \"{$slug}\";
            "
            )
            ->post('https://api.igdb.com/v4/games')->json();

        abort_if(!$game, 404);

        return view('show', [
            'game' => $this->formatGameForView($game[0]),
        ]);
    }

    private function formatGameForView(array $game)
    {
        $temp = collect($game)->merge([
            'coverImageUrl'     => Str::replaceFirst('thumb', 'cover_big', data_get($game, 'cover.url')),
            'genres'            => collect(data_get($game, 'genres'))->pluck('name')->implode(', '),
            'involvedCompanies' => $game['involved_companies'][0]['company']['name'],
            'platforms'         => collect(data_get($game, 'platforms'))->pluck('abbreviation')->implode(', '),
            'memberRating'      => array_key_exists('rating', $game)
                                ? round($game['rating']) . '%'
                                : '0%',
            'criticRating' => array_key_exists('aggregated_rating', $game)
                                ? round($game['aggregated_rating']) . '%'
                                : '0%',
            'trailer'     => 'https://youtube.com/watch/' . $game['videos'][0]['video_id'],
            'screenshots' => collect($game['screenshots'])->map(function ($screenshot) {
                return [
                    'big'  => Str::replaceFirst('thumb', 'screenshot_big', $screenshot['url']),
                    'huge' => Str::replaceFirst('thumb', 'screenshot_huge', $screenshot['url']),
                ];
            })->take(9),
            'similarGames' => collect($game['similar_games'])->map(function ($game) {
                return collect($game)->merge([
                    'coverImageUrl' => array_key_exists('cover', $game)
                                        ? Str::replaceFirst('thumb', 'cover_big', $game['cover']['url'])
                                        : 'https://via.placeholder.com/264x352',
                    'rating' => array_key_exists('rating', $game)
                                    ? round($game['rating']) . '%'
                                    : '0%',
                    'platforms' => array_key_exists('platforms', $game)
                                    ? collect($game['platforms'])->pluck('abbreviation')->implode(', ')
                                    : 'N/A',
                ]);
            })->take(6),
            'social' => [
                'website'  => collect($game['websites'])->first(),
                'facebook' => collect($game['websites'])->filter(function ($website) {
                    return Str::contains($website['url'], 'facebook');
                })->first(),
                'twitter' => collect($game['websites'])->filter(function ($website) {
                    return Str::contains($website['url'], 'twitter');
                })->first(),
                'instagram' => collect($game['websites'])->filter(function ($website) {
                    return Str::contains($website['url'], 'instagram');
                })->first(),
            ],
        ]);

        return $temp;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
