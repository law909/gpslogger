<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationUpdateRequest;
use App\Models\FollowedPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationUpdateController extends Controller
{
    public function store(Request $request, FollowedPerson $followedPerson): JsonResponse
    {
        // $validated = $request->validated();
        $validated = $request->all();

        Log::debug('Location update request received');

        Log::channel('location')->info('Location update received', [
            'followed_person_id' => $followedPerson->id,
            'followed_person_name' => $followedPerson->name,
            'payload' => $request->all(),
        ]);

        $recordedAt = isset($validated['time'])
            ? Carbon::createFromTimestampMs($validated['time'])
            : now();

        DB::transaction(function () use ($followedPerson, $validated, $recordedAt) {
            $followedPerson->locationUpdates()->create([
                'latitude' => $validated['lat'],
                'longitude' => $validated['lon'],
                'accuracy' => $validated['acc'] ?? null,
                'recorded_at' => $recordedAt,
                'battery_level' => $validated['batt'] ?? null,
            ]);

            $followedPerson->update([
                'last_latitude' => $validated['lat'],
                'last_longitude' => $validated['lon'],
                'last_accuracy' => $validated['acc'] ?? null,
                'last_recorded_at' => $recordedAt,
                'last_battery_level' => $validated['batt'] ?? null,
            ]);
        });

        return response()->json(['status' => 'success']);
    }
}
