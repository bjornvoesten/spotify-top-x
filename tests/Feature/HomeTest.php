<?php

namespace Feature;

use App\Models\Artist;
use App\Models\Track;
use App\Models\User;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_home_page(): void
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $artistOne = Artist::factory()
            ->hasAttached($userOne)
            ->hasAttached($userTwo)
            ->create();
        $artistTwo = Artist::factory()
            ->hasAttached($userOne)
            ->create();

        $trackOne = Track::factory()
            ->hasAttached($userOne)
            ->hasAttached($userTwo)
            ->create();
        $trackTwo = Track::factory()
            ->hasAttached($userOne)
            ->create();

        $this
            ->get(route('home'))
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Tracks',
                "{$trackOne->name} (2)",
                "{$trackTwo->name} (1)",
                'Artists',
                "{$artistOne->name} (2)",
                "{$artistTwo->name} (1)",
            ]);
    }
}
