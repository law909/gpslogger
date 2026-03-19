<?php

use App\Models\FollowedPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a followed person via artisan command', function () {
    $this->artisan('app:create-followed-person')
        ->expectsQuestion('What is the name of the followed person?', 'Test Person')
        ->assertExitCode(0);

    $this->assertDatabaseHas('followed_person', [
        'name' => 'Test Person',
    ]);

    expect(FollowedPerson::where('name', 'Test Person')->first())->not->toBeNull();
});
