<?php

use App\Models\FollowedPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

it('can store location update for a followed person', function () {
    $person = FollowedPerson::create(['name' => 'John Doe']);

    $payload = [
        'lat' => 47.497913,
        'lon' => 19.040236,
        'acc' => 15.5,
        'time' => now()->toIso8601ZuluString('millisecond'),
        'batt' => 85,
    ];

    $response = $this->postJson(route('api.location.store', $person->id), $payload);

    $response->assertStatus(200)
        ->assertJson(['status' => 'success']);

    $this->assertDatabaseHas('location_updates', [
        'followed_person_id' => $person->id,
        'latitude' => 47.497913,
        'longitude' => 19.040236,
        'accuracy' => 15.5,
        'battery_level' => 85,
    ]);

    $this->assertDatabaseHas('followed_person', [
        'id' => $person->id,
        'last_latitude' => 47.497913,
        'last_longitude' => 19.040236,
        'last_accuracy' => 15.5,
        'last_battery_level' => 85,
    ]);
});

it('validates required fields', function () {
    $person = FollowedPerson::create(['name' => 'John Doe']);

    $response = $this->postJson(route('api.location.store', $person->id), []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['lat', 'lon']);
});

it('logs incoming data before saving', function () {
    $person = FollowedPerson::create(['name' => 'Logger Test']);

    $payload = [
        'lat' => 47.497913,
        'lon' => 19.040236,
        'acc' => 10,
        'time' => now()->toIso8601ZuluString('millisecond'),
        'batt' => 50,
    ];

    Log::shouldReceive('debug')->andReturnNull();

    Log::shouldReceive('channel')
        ->with('location')
        ->once()
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->withArgs(function (string $message, array $context) use ($person, $payload) {
            return $message === 'Location update received'
                && $context['followed_person_id'] === $person->id
                && $context['payload']['lat'] === $payload['lat'];
        });

    $this->postJson(route('api.location.store', $person->id), $payload)
        ->assertStatus(200);
});

it('converts recorded_at from UTC to app timezone before saving', function () {
    config(['app.timezone' => 'Europe/Budapest']);
    date_default_timezone_set('Europe/Budapest');

    $person = FollowedPerson::create(['name' => 'Timezone Test']);

    $utcTime = '2026-03-20T10:00:00.000Z';

    $payload = [
        'lat' => 47.497913,
        'lon' => 19.040236,
        'time' => $utcTime,
    ];

    $this->postJson(route('api.location.store', $person->id), $payload)
        ->assertStatus(200);

    $locationUpdate = $person->locationUpdates()->first();

    expect($locationUpdate->recorded_at)->toBe('2026-03-20 11:00:00');
});

it('returns 404 for non-existent person', function () {
    $response = $this->postJson('/api/location/00000000-0000-0000-0000-000000000000', [
        'lat' => 47.497913,
        'lon' => 19.040236,
    ]);

    $response->assertStatus(404);
});
