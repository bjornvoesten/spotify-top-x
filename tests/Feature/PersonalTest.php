<?php

namespace Feature;

use App\Models\Artist;
use App\Models\Track;
use App\Models\User;
use Tests\TestCase;

class PersonalTest extends TestCase
{
    public function test_personal_page(): void
    {
        $user = User::factory()->create();

        $artistOne = Artist::factory()
            ->hasAttached($user)
            ->create();
        $artistTwo = Artist::factory()
            ->hasAttached($user)
            ->create();

        $trackOne = Track::factory()
            ->hasAttached($user)
            ->create();
        $trackTwo = Track::factory()
            ->hasAttached($user)
            ->create();

        $this
            ->actingAs($user)
            ->get(route('personal'))
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Tracks',
                $trackOne->name,
                $trackTwo->name,
                'Artists',
                $artistOne->name,
                $artistTwo->name,
            ]);
    }
}
