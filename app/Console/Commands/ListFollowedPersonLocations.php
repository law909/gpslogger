<?php

namespace App\Console\Commands;

use App\Models\FollowedPerson;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:list-followed-person-locations {--limit=5 : Number of locations to show per person}')]
#[Description('List the last locations for each followed person')]
class ListFollowedPersonLocations extends Command
{
    protected $aliases = ['app:list-locations'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $persons = FollowedPerson::with(['locationUpdates' => function ($query) {
            $query->latest('recorded_at');
        }])->get();

        if ($persons->isEmpty()) {
            $this->info('No followed persons found.');

            return self::SUCCESS;
        }

        foreach ($persons as $person) {
            $this->newLine();
            $this->info("📍 {$person->name} ({$person->id})");

            if ($person->locationUpdates->isEmpty()) {
                $this->warn('  No location updates found.');

                continue;
            }

            $limit = (int) $this->option('limit');

            $this->table(
                ['Latitude', 'Longitude', 'Accuracy', 'Battery', 'Recorded At', 'Created At'],
                $person->locationUpdates->take($limit)->map(fn ($update) => [
                    $update->latitude,
                    $update->longitude,
                    $update->accuracy,
                    $update->battery_level,
                    $update->recorded_at,
                    $update->created_at,
                ]),
            );
        }

        return self::SUCCESS;
    }
}
