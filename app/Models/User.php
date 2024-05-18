<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'spotify_id',
        'spotify_token',
        'spotify_refresh_token',
        'notify',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'notify' => 'boolean',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Track>
     */
    public function tracks(): BelongsToMany
    {
        return $this
            ->belongsToMany(Track::class)
            ->withTimestamps();
    }

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
