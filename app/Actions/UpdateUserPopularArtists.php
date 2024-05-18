<?php

namespace App\Actions;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

readonly class UpdateUserPopularArtists
{
    public function __construct(
        protected User $user
    ) {
        //
    }

    /**
     * @return int The number of changes
     */
    public static function run(User $user): int
    {
        return App::call(new static($user));
    }

    public function __invoke(): int
    {
        $token = $this->user->spotify_token;

        $items = Http::withToken($token)
            ->asJson()->acceptJson()
            ->throw()
            ->get('https://api.spotify.com/v1/me/top/artists', ['limit' => 10])
            ->collect('items');

        $artists = $items
            ->map(fn (array $artist): array => [
                'spotify_id' => $artist['id'],
                'name' => $artist['name'],
                'uri' => $artist['uri'],
            ])
            ->all();

        Artist::query()->upsert(
            values: $artists,
            uniqueBy: 'spotify_id',
            update: ['name', 'uri'],
        );

        $artists = Artist::query()
            ->whereIn('spotify_id', Arr::pluck($artists, 'spotify_id'))
            ->get();

        $changes = $this->user->artists()->sync(
            $artists->pluck('id')
        );

        return count($changes['attached'])
            + count($changes['detached'])
            + count($changes['updated']);
    }
}
