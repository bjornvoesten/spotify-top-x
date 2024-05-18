<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements Contracts\WithUsers<static>
 */
class Artist extends Model implements Contracts\WithUsers
{
    /**
     * @use Concerns\WithUsers<static>
     */
    use Concerns\WithUsers;

    use HasFactory;

    protected $fillable = [
        'spotify_id',
        'name',
        'uri',
    ];
}
