<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $tracks = $user->tracks()
            ->orderedByPopularity()
            ->take(10)
            ->get();

        $artists = $user->artists()
            ->orderedByPopularity()
            ->take(10)
            ->get();

        return view('personal', [
            'user' => $user,
            'tracks' => $tracks,
            'artists' => $artists,
        ]);
    }

    public function notify(Request $request): RedirectResponse
    {
        $request->validate([
            'notify' => ['sometimes', 'accepted'],
        ]);

        $value = $request->boolean('notify');

        $request->user()->update([
            'notify' => $value,
        ]);

        return redirect()->back()->with(['success' => 'Settings updated!']);
    }
}
