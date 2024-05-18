<?php

namespace App\Actions;

use App\Models\Artist;
use App\Models\Track;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

readonly class UpdateUserPopularTracks
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
            ->get('https://api.spotify.com/v1/me/top/tracks', ['limit' => 10])
            ->collect('items')
            ->keyBy('id');

        $this->artists($items);
        $tracks = $this->tracks($items);
        $this->trackArtistPivots($items, $tracks);

        $changes = $this->user->tracks()->sync(
            $tracks->pluck('id')
        );

        return count($changes['attached'])
            + count($changes['detached'])
            + count($changes['updated']);
    }

    protected function artists(Collection $items): void
    {
        $artists = $items
            ->flatMap(fn (array $track) => $track['artists'])
            ->unique('id')
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
    }

    /**
     * @return \Illuminate\Support\Collection<\App\Models\Track>
     */
    protected function tracks(Collection $items): Collection
    {
        $tracks = $items
            ->map(fn (array $track): array => [
                'spotify_id' => $track['id'],
                'name' => $track['name'],
                'uri' => $track['uri'],
            ])
            ->all();

        Track::query()->upsert(
            values: $tracks,
            uniqueBy: 'spotify_id',
            update: ['name', 'uri'],
        );

        return Track::query()
            ->whereIn('spotify_id', Arr::pluck($tracks, 'spotify_id'))
            ->get();
    }

    /**
     * @param  \Illuminate\Support\Collection<\App\Models\Track>  $tracks
     */
    protected function trackArtistPivots(Collection $items, Collection $tracks): void
    {
        $tracks->each(fn (Track $track) => $track->artists()->sync(
            Artist::query()
                ->whereIn('spotify_id', Arr::pluck($items[$track->spotify_id]['artists'], 'id'))
                ->pluck('id')
                ->all(),
        ));
    }
}
