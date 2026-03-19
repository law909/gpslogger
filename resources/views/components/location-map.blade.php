<?php

use App\Models\FollowedPerson;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url]
    public string $personId = '';

    #[Url]
    public int $limit = 10;

    /** @var array<int, array{lat: float, lng: float, recorded_at: string, accuracy: float|null}> */
    public array $locations = [];

    public string $personName = '';

    public string $errorMessage = '';

    public function loadLocations(): void
    {
        $this->errorMessage = '';
        $this->locations = [];
        $this->personName = '';

        if (blank($this->personId)) {
            $this->errorMessage = 'Kérlek add meg a Person ID-t.';

            return;
        }

        $person = FollowedPerson::find($this->personId);

        if (! $person) {
            $this->errorMessage = 'Nem található személy ezzel az azonosítóval.';

            return;
        }

        $this->personName = $person->name;

        $updates = $person->locationUpdates()
            ->latest('recorded_at')
            ->limit($this->limit)
            ->get();

        if ($updates->isEmpty()) {
            $this->errorMessage = 'Nincsenek helyadatok ehhez a személyhez.';

            return;
        }

        $this->locations = $updates->map(fn ($u) => [
            'lat' => (float) $u->latitude,
            'lng' => (float) $u->longitude,
            'recorded_at' => $u->recorded_at,
            'accuracy' => $u->accuracy ? (float) $u->accuracy : null,
        ])->all();
    }
};
?>

<div class="px-4 py-6">
    <h1 class="mb-6 text-2xl font-semibold text-zinc-900 dark:text-white">Tartózkodási helyek térképen</h1>

    <form wire:submit="loadLocations" class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end">
        <div class="w-full sm:w-32">
            <flux:input wire:model="personId" label="Person ID" placeholder="Személy azonosító..." />
        </div>
        <div>
            <flux:input wire:model="limit" label="Limit" type="number" min="1" max="1000" />
        </div>
        <div>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                Keresés
            </flux:button>
        </div>
    </form>

    @if ($errorMessage)
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ $errorMessage }}
        </div>
    @endif

    @if ($personName)
        <p class="mb-2 text-sm text-zinc-600 dark:text-zinc-400">
            <strong>{{ $personName }}</strong> — utolsó {{ count($locations) }} helyadat
        </p>
    @endif

    <div
        id="map"
        class="h-[400px] w-full rounded-lg border border-zinc-200 sm:h-[500px] lg:h-[600px] dark:border-zinc-700"
        wire:ignore
    ></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @script
    <script>
        let map = null;
        let markersGroup = null;

        function initMap() {
            if (map) return;
            map = L.map('map').setView([47.4979, 19.0402], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(map);
            markersGroup = L.layerGroup().addTo(map);
        }

        function updateMarkers(locations) {
            if (!map) initMap();
            markersGroup.clearLayers();

            if (!locations || locations.length === 0) return;

            const latlngs = [];

            locations.forEach((loc, index) => {
                const latlng = [loc.lat, loc.lng];
                latlngs.push(latlng);

                const isLatest = index === 0;
                const marker = L.circleMarker(latlng, {
                    radius: isLatest ? 10 : 6,
                    fillColor: isLatest ? '#ef4444' : '#3b82f6',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8,
                });

                let popup = `<strong>${isLatest ? '📍 Legutóbbi' : '#' + (index + 1)}</strong><br>`;
                popup += `${loc.lat.toFixed(6)}, ${loc.lng.toFixed(6)}<br>`;
                if (loc.accuracy) popup += `Pontosság: ${loc.accuracy}m<br>`;
                if (loc.battery) popup += `Töltöttség: ${loc.battery}%<br>`;
                popup += `<small>${loc.recorded_at}</small>`;

                marker.bindPopup(popup);
                markersGroup.addLayer(marker);
            });

            if (latlngs.length > 1) {
                const polyline = L.polyline(latlngs, { color: '#6366f1', weight: 2, opacity: 0.6, dashArray: '5,10' });
                markersGroup.addLayer(polyline);
                map.fitBounds(polyline.getBounds().pad(0.1));
            } else {
                map.setView(latlngs[0], 16);
            }
        }

        initMap();

        $wire.$watch('locations', (locations) => {
            updateMarkers(locations);
        });

        // Load initial data if present
        if ($wire.locations && $wire.locations.length > 0) {
            updateMarkers($wire.locations);
        }
    </script>
    @endscript
</div>
