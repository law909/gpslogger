<?php

use App\Models\FollowedPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('a followed person can be created with a uuid', function () {
    $person = FollowedPerson::factory()->create([
        'name' => 'John Doe',
    ]);

    expect($person->id)->toBeString()
        ->and(Str::isUuid($person->id))->toBeTrue()
        ->and($person->name)->toBe('John Doe');

    $this->assertDatabaseHas('followed_person', [
        'id' => $person->id,
        'name' => 'John Doe',
    ]);
});

test('id is not an incrementing integer', function () {
    $person1 = FollowedPerson::factory()->create();
    $person2 = FollowedPerson::factory()->create();

    expect($person1->id)->not->toBe(1)
        ->and($person2->id)->not->toBe(2)
        ->and($person1->id)->not->toBe($person2->id);
});
