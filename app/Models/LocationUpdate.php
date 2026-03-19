<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationUpdate extends Model
{
    /** @use HasFactory<\Database\Factories\LocationUpdateFactory> */
    use HasFactory;

    protected $fillable = [
        'followed_person_id',
        'latitude',
        'longitude',
        'accuracy',
        'recorded_at',
        'battery_level',
    ];

    /**
     * @return BelongsTo<FollowedPerson, $this>
     */
    public function followedPerson(): BelongsTo
    {
        return $this->belongsTo(FollowedPerson::class);
    }
}
