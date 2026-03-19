<?php

use App\Models\FollowedPerson;
use App\Models\LocationUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays a message when no followed persons exist', function () {
    $this->artisan('app:list-followed-person-locations')
        ->expectsOutput('No followed persons found.')
        ->assertExitCode(0);
});

it('displays a warning when a person has no location updates', function () {
    FollowedPerson::factory()->create(['name' => 'Empty Person']);

    $this->artisan('app:list-followed-person-locations')
        ->expectsOutputToContain('No location updates found.')
        ->assertExitCode(0);
});

it('lists the last 5 locations for a followed person', function () {
    $person = FollowedPerson::factory()->create(['name' => 'Test Person']);

    // Create 7 updates, expect only the latest 5
    foreach (range(1, 7) as $i) {
        LocationUpdate::create([
            'followed_person_id' => $person->id,
            'latitude' => 47.0 + $i * 0.01,
            'longitude' => 19.0 + $i * 0.01,
            'accuracy' => 10,
            'battery_level' => 80,
            'recorded_at' => now()->subMinutes(7 - $i),
        ]);
    }

    $this->artisan('app:list-followed-person-locations')
        ->expectsOutputToContain('Test Person')
        ->doesntExpectOutputToContain('47.01')
        ->doesntExpectOutputToContain('47.02')
        ->expectsOutputToContain('47.03')
        ->assertExitCode(0);
});
