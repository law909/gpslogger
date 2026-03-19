<?php

namespace App\Console\Commands;

use App\Models\FollowedPerson;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:list-followed-persons')]
#[Description('List all followed persons')]
class ListFollowedPersons extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $persons = FollowedPerson::all();

        if ($persons->isEmpty()) {
            $this->info('No followed persons found.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Last Latitude', 'Last Longitude', 'Last Recorded At'],
            $persons->map(fn (FollowedPerson $person) => [
                $person->id,
                $person->name,
                $person->last_latitude,
                $person->last_longitude,
                $person->last_recorded_at,
            ]),
        );

        return self::SUCCESS;
    }
}
