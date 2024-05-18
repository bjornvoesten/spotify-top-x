<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Spotify\Provider;

class SpotifyController extends Controller
{
    protected function driver(): Provider
    {
        /** @var \SocialiteProviders\Spotify\Provider $provider */
        $provider = Socialite::driver('spotify');
        $provider->scopes(['user-read-email', 'user-top-read']);

        return $provider;
    }

    public function redirect(): RedirectResponse
    {
        return $this->driver()->redirect();
    }

    public function callback(): RedirectResponse
    {
        /** @var \SocialiteProviders\Manager\OAuth2\User $data */
        $data = $this->driver()->user();

        /** @var \App\Models\User $user */
        $user = User::query()->updateOrCreate([
            'spotify_id' => $data->id,
        ], [
            'name' => $data->name,
            'email' => $data->email,
            'spotify_token' => $data->token,
            'spotify_refresh_token' => $data->refreshToken,
        ]);

        Auth::login($user);

        return redirect()->route('personal');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
