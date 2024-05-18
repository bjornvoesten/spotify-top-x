<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\SpotifyController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)
    ->name('home');
Route::get('/personal', [PersonalController::class, 'show'])
    ->middleware('auth:web')
    ->name('personal');
Route::patch('/personal', [PersonalController::class, 'notify'])
    ->middleware('auth:web')
    ->name('personal.notify');

Route::get('/spotify/redirect', [SpotifyController::class, 'redirect'])
    ->name('spotify.redirect');
Route::get('/spotify/callback', [SpotifyController::class, 'callback'])
    ->name('spotify.callback');
Route::get('/spotify/logout', [SpotifyController::class, 'logout'])
    ->name('spotify.logout');
