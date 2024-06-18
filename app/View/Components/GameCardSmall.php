<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GameCardSmall extends Component
{
    public $game;

    public function __construct($game)
    {
        $this->game = $game;
    }

    public function render(): View|Closure|string
    {
        return view('components.game-card-small');
    }
}
