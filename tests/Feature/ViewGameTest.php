<?php

it('should see the game correct info', function () {
    $response = $this->get(route('games.show', 'wuthering-waves'));
    $response->assertSee('Wuthering Waves');
    $response->assertSee('Role-playing (RPG), Adventure');
    $response->assertSee('Kuro Games');
    $response->assertSee('PC, Mac, Android, iOS, PS4, PS5');
    $response->assertSee('94');
    $response->assertSee('0');

    $response->assertSuccessful();
});
