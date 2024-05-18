<?php

namespace Tests\Feature;

use App\Actions\UpdateUserPopularArtists;
use App\Actions\UpdateUserPopularTracks;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery\MockInterface;
use SocialiteProviders\Spotify\Provider;
use Tests\TestCase;

class SpotifyTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function test_redirect(): void
    {
        $provider = $this->partialMock(Provider::class);
        $provider
            ->shouldReceive('redirect')
            ->andReturn(new RedirectResponse(route('personal')));

        Socialite::shouldReceive('driver')
            ->with('spotify')
            ->andReturn($provider)
            ->once();

        $this
            ->get(route('spotify.redirect'))
            ->assertStatus(302)
            ->assertRedirect(route('personal'));
    }

    public function test_callback_create(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/me/top/tracks*' => Http::response(
                json_decode(
                    file_get_contents(__DIR__.'/../Fixtures/resources/spotify-tracks.json'),
                    true, 512, JSON_THROW_ON_ERROR
                )
            ),
            'https://api.spotify.com/v1/me/top/artists*' => Http::response(
                json_decode(
                    file_get_contents(__DIR__.'/../Fixtures/resources/spotify-artists.json'),
                    true, 512, JSON_THROW_ON_ERROR
                )
            ),
        ]);

        $data = $this->mockSpotifyUser();

        $provider = $this->mockSpotifyProvider();
        $provider
            ->shouldReceive('user')
            ->andReturn($data);

        $this->assertDatabaseCount('users', 0);

        $this
            ->get(route('spotify.callback'))
            ->assertStatus(302)
            ->assertRedirect(route('personal'));

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
        Http::fake([
            'https://api.spotify.com/v1/me/top/tracks*' => Http::response(
                json_decode(
                    file_get_contents(__DIR__.'/../Fixtures/resources/spotify-tracks.json'),
                    true, 512, JSON_THROW_ON_ERROR
                )
            ),
            'https://api.spotify.com/v1/me/top/artists*' => Http::response(
                json_decode(
                    file_get_contents(__DIR__.'/../Fixtures/resources/spotify-artists.json'),
                    true, 512, JSON_THROW_ON_ERROR
                )
            ),
        ]);

        $user = User::factory()->create();

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
            ->assertRedirect(route('personal'));

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
        $user = User::factory()->create();
        $this
            ->actingAs($user)
            ->get(route('spotify.logout'))
            ->assertStatus(302)
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    protected function mockSpotifyUser(?string $id = null): SocialiteUser|MockInterface
    {
        $user = $this->partialMock(SocialiteUser::class);

        $user->id = $id ?: fake()->userName();
        $user->name = fake()->name();
        $user->email = fake()->email();
        $user->token = fake()->sha256();
        $user->refreshToken = fake()->sha256();

        return $user;
    }

    protected function mockSpotifyProvider(): Provider|MockInterface
    {
        $provider = $this->partialMock(Provider::class);
        $provider
            ->shouldReceive('redirect')
            ->andReturn(new RedirectResponse('personal'));

        Socialite::shouldReceive('driver')
            ->with('spotify')
            ->andReturn($provider)
            ->once();

        return $provider;
    }

    public function test_update_user_popular_artists(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseEmpty('artists');
        $this->assertDatabaseEmpty('artist_user');

        Http::fake([
            'https://api.spotify.com/v1/me/top/artists*' => Http::response(
                json_decode(
                    file_get_contents(__DIR__.'/../Fixtures/resources/spotify-artists.json'),
                    true, 512, JSON_THROW_ON_ERROR
                )
            ),
        ]);

        UpdateUserPopularArtists::run(user: $user);

        $this->assertDatabaseCount('artists', 10);
        $this->assertDatabaseHas('artists', [
            'spotify_id' => '1g9nyCbUH0kbNgXAsw7tUB',
            'name' => 'Bankzitters',
            'uri' => 'spotify:artist:1g9nyCbUH0kbNgXAsw7tUB',
        ]);
        $this->assertDatabaseCount('artist_user', 10);
        $this->assertNotNull(
            $user->artists()
                ->where('spotify_id', '1g9nyCbUH0kbNgXAsw7tUB')
                ->first()
        );
    }

    public function test_update_user_popular_tracks(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/me/top/tracks*' => Http::response(
                json_decode(
                    file_get_contents(__DIR__.'/../Fixtures/resources/spotify-tracks.json'),
                    true, 512, JSON_THROW_ON_ERROR
                )
            ),
        ]);

        $user = User::factory()->create();

        $this->assertDatabaseEmpty('tracks');
        $this->assertDatabaseEmpty('track_user');

        UpdateUserPopularTracks::run(user: $user);

        $this->assertDatabaseCount('tracks', 10);
        $this->assertDatabaseHas('tracks', [
            'spotify_id' => '4eqKoFDvkBK96nYgUTXUWp',
            'name' => 'Cupido',
            'uri' => 'spotify:track:4eqKoFDvkBK96nYgUTXUWp',
        ]);
        $this->assertDatabaseCount('track_user', 10);
        $this->assertNotNull(
            $user->tracks()
                ->where('spotify_id', '4eqKoFDvkBK96nYgUTXUWp')
                ->first()
        );
    }
}
