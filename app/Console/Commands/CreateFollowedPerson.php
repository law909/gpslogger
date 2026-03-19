<?php

namespace App\Console\Commands;

use App\Models\FollowedPerson;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

#[Signature('app:create-followed-person')]
#[Description('Create a new followed person')]
class CreateFollowedPerson extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = text(
            label: 'What is the name of the followed person?',
            required: true,
        );

        $person = FollowedPerson::create(['name' => $name]);

        $this->info("Followed person created: {$person->name}");
        $this->info("ID: {$person->id}");
        $this->info('API URL: '.url("/api/location/{$person->id}"));

        return self::SUCCESS;
    }
}
