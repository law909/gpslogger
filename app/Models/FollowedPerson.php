<?php

namespace App\Models;

use App\Models\LocationUpdate;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FollowedPerson extends Model
{
    /** @use HasFactory<\Database\Factories\FollowedPersonFactory> */
    use HasFactory, HasUuids;

    protected $table = 'followed_person';

    protected $fillable = [
        'name',
        'last_latitude',
        'last_longitude',
        'last_accuracy',
        'last_recorded_at',
        'last_battery_level',
    ];

    /**
     * @return HasMany<LocationUpdate, $this>
     */
    public function locationUpdates(): HasMany
    {
        return $this->hasMany(LocationUpdate::class);
    }
}
