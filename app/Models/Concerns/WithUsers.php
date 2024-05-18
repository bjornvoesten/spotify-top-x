<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @mixin TModel
 */
trait WithUsers
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\User>
     */
    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->withTimestamps();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    public function scopeOrderedByPopularity(Builder $query, string $direction = 'desc'): void
    {
        $query
            ->withCount('users as popularity')
            ->orderBy('popularity', $direction);
    }
}
