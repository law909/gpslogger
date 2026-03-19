<?php

use App\Models\FollowedPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays a message when no followed persons exist', function () {
    $this->artisan('app:list-followed-persons')
        ->expectsOutput('No followed persons found.')
        ->assertExitCode(0);
});

it('lists all followed persons in a table', function () {
    $person = FollowedPerson::factory()->create(['name' => 'Test Person']);

    $this->artisan('app:list-followed-persons')
        ->expectsTable(
            ['ID', 'Name', 'Last Latitude', 'Last Longitude', 'Last Recorded At'],
            [
                [
                    $person->id,
                    'Test Person',
                    $person->last_latitude,
                    $person->last_longitude,
                    $person->last_recorded_at,
                ],
            ],
        )
        ->assertExitCode(0);
});
