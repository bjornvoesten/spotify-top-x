<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @implements Contracts\WithUsers<static>
 */
class Track extends Model implements Contracts\WithUsers
{
    /**
     * @use Concerns\WithUsers<static>
     */
    use Concerns\WithUsers;

    use HasFactory;

    protected $fillable = [
        'spotify_id',
        'name',
        'url',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Artist>
     */
    public function artists(): BelongsToMany
    {
        return $this
            ->belongsToMany(Artist::class)
            ->withTimestamps();
    }
}
