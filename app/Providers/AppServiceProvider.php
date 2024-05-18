<?php

namespace App\Providers;

use App\Actions\UpdateUserPopularArtists;
use App\Actions\UpdateUserPopularTracks;
use App\Notifications\SpotifyListUpdated;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Spotify\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(static function (SocialiteWasCalled $event) {
            $event->extendSocialite('spotify', Provider::class);
        });

        Event::listen(static function (Login $event) {
            /** @var \App\Models\User $user */
            $user = $event->user;

            $trackChangeCount = UpdateUserPopularTracks::run(user: $user);
            $artistChangeCount = UpdateUserPopularArtists::run(user: $user);

            if (! ($trackChangeCount + $artistChangeCount)) {
                return;
            }

            if (! $user->notify) {
                return;
            }

            $user->notify(new SpotifyListUpdated());
        });
    }
}
