<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use Mockery\MockInterface;
use SocialiteProviders\Spotify\Provider;
use Tests\TestCase;

class SpotifyTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @throws \Throwable
     */
    public function test_redirect(): void
    {
        $provider = $this->partialMock(Provider::class);
        $provider
            ->shouldReceive('redirect')
            ->andReturn(new RedirectResponse('/'));

        Socialite::shouldReceive('driver')
            ->with('spotify')
            ->andReturn($provider)
            ->once();

        $this
            ->get(route('spotify.redirect'))
            ->assertStatus(302)
            ->assertRedirect();
    }

    public function test_callback_create(): void
    {
        $data = $this->mockSpotifyUser();

        $provider = $this->mockSpotifyProvider();
        $provider
            ->shouldReceive('user')
            ->andReturn($data);

        $this->assertDatabaseCount('users', 0);

        $this
            ->get(route('spotify.callback'))
            ->assertStatus(302)
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'spotify_id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'spotify_token' => $data->token,
            'spotify_refresh_token' => $data->refreshToken,
        ]);

        $this->assertAuthenticated();
    }

    public function test_callback_update(): void
    {
        $user = \App\Models\User::factory()->create();

        $data = $this->mockSpotifyUser(
            id: $user->spotify_id
        );

        $provider = $this->mockSpotifyProvider();
        $provider
            ->shouldReceive('user')
            ->andReturn($data);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'spotify_id' => $user->spotify_id,
            'name' => $user->name,
            'email' => $user->email,
            'spotify_token' => $user->spotify_token,
            'spotify_refresh_token' => $user->spotify_refresh_token,
        ]);

        $this
            ->get(route('spotify.callback'))
            ->assertStatus(302)
            ->assertRedirect();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'spotify_id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'spotify_token' => $data->token,
            'spotify_refresh_token' => $data->refreshToken,
        ]);

        $this->assertAuthenticated();
    }

    public function test_logout(): void
    {
        $user = \App\Models\User::factory()->create();
        $this
            ->actingAs($user)
            ->get(route('spotify.logout'))
            ->assertStatus(302)
            ->assertRedirect();

        $this->assertGuest();
    }

    protected function mockSpotifyUser(string $id = null): User | MockInterface
    {
        $user = $this->partialMock(User::class);

        $user->id = $id ?: fake()->userName();
        $user->name = fake()->name();
        $user->email = fake()->email();
        $user->token = fake()->sha256();
        $user->refreshToken = fake()->sha256();

        return $user;
    }

    protected function mockSpotifyProvider(): Provider | MockInterface
    {
        $provider = $this->partialMock(Provider::class);
        $provider
            ->shouldReceive('redirect')
            ->andReturn(new RedirectResponse('/'));

        Socialite::shouldReceive('driver')
            ->with('spotify')
            ->andReturn($provider)
            ->once();

        return $provider;
    }
}
