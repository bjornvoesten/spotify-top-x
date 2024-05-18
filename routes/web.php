<?php

use App\Http\Controllers\SpotifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', static function (Request $request) {
    $user = $request->user();

    return view('welcome', [
        'user' => $user,
    ]);
});

Route::get('/spotify/redirect', [SpotifyController::class, 'redirect'])
    ->name('spotify.redirect');
Route::get('/spotify/callback', [SpotifyController::class, 'callback'])
    ->name('spotify.callback');
Route::get('/spotify/logout', [SpotifyController::class, 'logout'])
    ->name('spotify.logout');
