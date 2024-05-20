<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SpotifyListUpdated;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function test_list_updated_notification_sent_on_login(): void
    {
        $userOne = User::factory()->notify()->create();
        $userTwo = User::factory()->notify()->create();
        $userThree = User::factory()->create();

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

        Notification::fake();

        $event = new Login(
            guard: 'web',
            user: $userOne,
            remember: true,
        );

        // Assert notification is sent

        Event::dispatch($event);
        Notification::assertSentToTimes($userOne, SpotifyListUpdated::class, 1);
        Notification::assertSentToTimes($userTwo, SpotifyListUpdated::class, 1);
        Notification::assertNotSentTo($userThree, SpotifyListUpdated::class);

        // Assert notification will not be sent without changes

        Event::dispatch($event);
        Notification::assertSentToTimes($userOne, SpotifyListUpdated::class, 1);
        Notification::assertSentToTimes($userTwo, SpotifyListUpdated::class, 1);
        Notification::assertNotSentTo($userThree, SpotifyListUpdated::class);
    }
}
