<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Track;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $tracks = Track::query()
            ->orderedByPopularity()
            ->take(10)
            ->get();

        $artists = Artist::query()
            ->orderedByPopularity()
            ->take(10)
            ->get();

        return view('home', [
            'user' => $user,
            'tracks' => $tracks,
            'artists' => $artists,
        ]);
    }
}
