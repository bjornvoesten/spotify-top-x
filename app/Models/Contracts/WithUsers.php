<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @mixin TModel
 */
interface WithUsers
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\User>
     */
    public function users(): BelongsToMany;

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $query
     */
    public function scopeOrderedByPopularity(Builder $query): void;
}
