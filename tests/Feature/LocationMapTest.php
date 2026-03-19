<?php

use App\Models\FollowedPerson;
use App\Models\LocationUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the location map page', function () {
    $this->get('/map')->assertOk();
});

it('loads locations for a valid person', function () {
    $person = FollowedPerson::factory()->create();

    LocationUpdate::factory()->count(3)->create([
        'followed_person_id' => $person->id,
    ]);

    Livewire::test('location-map', [])
        ->set('personId', $person->id)
        ->set('limit', 10)
        ->call('loadLocations')
        ->assertSet('personName', $person->name)
        ->assertCount('locations', 3)
        ->assertSet('errorMessage', '');
});

it('shows error for invalid person id', function () {
    Livewire::test('location-map')
        ->set('personId', 'non-existent-id')
        ->call('loadLocations')
        ->assertSet('errorMessage', 'Nem található személy ezzel az azonosítóval.')
        ->assertCount('locations', 0);
});

it('shows error when person id is empty', function () {
    Livewire::test('location-map')
        ->set('personId', '')
        ->call('loadLocations')
        ->assertSet('errorMessage', 'Kérlek add meg a Person ID-t.');
});

it('respects the limit parameter', function () {
    $person = FollowedPerson::factory()->create();

    LocationUpdate::factory()->count(5)->create([
        'followed_person_id' => $person->id,
    ]);

    Livewire::test('location-map')
        ->set('personId', $person->id)
        ->set('limit', 2)
        ->call('loadLocations')
        ->assertCount('locations', 2);
});

it('filters locations by date', function () {
    $person = FollowedPerson::factory()->create();

    LocationUpdate::factory()->count(2)->create([
        'followed_person_id' => $person->id,
        'recorded_at' => '2025-06-15 10:00:00',
    ]);

    LocationUpdate::factory()->count(3)->create([
        'followed_person_id' => $person->id,
        'recorded_at' => '2025-06-16 12:00:00',
    ]);

    Livewire::test('location-map')
        ->set('personId', $person->id)
        ->set('date', '2025-06-15')
        ->call('loadLocations')
        ->assertCount('locations', 2);
});

it('returns all locations when no date is specified', function () {
    $person = FollowedPerson::factory()->create();

    LocationUpdate::factory()->count(2)->create([
        'followed_person_id' => $person->id,
        'recorded_at' => '2025-06-15 10:00:00',
    ]);

    LocationUpdate::factory()->count(3)->create([
        'followed_person_id' => $person->id,
        'recorded_at' => '2025-06-16 12:00:00',
    ]);

    Livewire::test('location-map')
        ->set('personId', $person->id)
        ->set('date', '')
        ->set('limit', 100)
        ->call('loadLocations')
        ->assertCount('locations', 5);
});
