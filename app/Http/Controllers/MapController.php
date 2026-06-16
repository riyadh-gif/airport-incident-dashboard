<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\IncidentCondition;
use App\Models\Incident;
use App\Support\GateCoordinates;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Render the interactive Leaflet incident map.
     *
     * For the selected date (?date, default today) we build per-gate
     * aggregates (gate code, synthetic lat/lng, total count and a
     * per-condition breakdown) and hand them to the view as data that is
     * embedded as JSON for the Leaflet module to consume.
     */
    public function index(Request $request): View
    {
        $date = $this->resolveDate($request->query('date'));

        // Per-gate, per-condition incident counts for the selected date.
        // Eloquent only — grouped, parameterised, no raw SQL string building.
        $grouped = Incident::query()
            ->whereDate('occurred_at', $date)
            ->get(['location', 'condition'])
            ->groupBy('location');

        $gates = [];
        $totalIncidents = 0;

        foreach ($grouped as $location => $incidents) {
            $coords = GateCoordinates::forGate((string) $location);

            // Skip unknown/legacy location strings that don't map to a gate.
            if ($coords === null) {
                continue;
            }

            $breakdown = $this->conditionBreakdown($incidents->pluck('condition'));
            $count = $incidents->count();
            $totalIncidents += $count;

            $gates[] = [
                'gate' => (string) $location,
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'count' => $count,
                'breakdown' => $breakdown,
            ];
        }

        // Stable ordering (busiest first) for a predictable legend/markers.
        usort($gates, static fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        return view('map.index', [
            'date' => $date->toDateString(),
            'gates' => $gates,
            'totalIncidents' => $totalIncidents,
            'activeGates' => count($gates),
            'mapConfig' => [
                'center' => [GateCoordinates::CENTER_LAT, GateCoordinates::CENTER_LNG],
                'zoom' => GateCoordinates::DEFAULT_ZOOM,
            ],
            'conditionMeta' => $this->conditionMeta(),
        ]);
    }

    /**
     * Build an ordered per-condition breakdown for a set of condition values.
     *
     * Always returns every known condition (count 0 when absent) keyed by
     * the enum value, so the front-end legend/popups are consistent.
     *
     * @param  \Illuminate\Support\Collection<int, string|null>  $conditions
     * @return array<string, int>
     */
    private function conditionBreakdown($conditions): array
    {
        $counts = $conditions
            ->map(static fn ($value): ?IncidentCondition => IncidentCondition::tryFromValue($value))
            ->filter()
            ->countBy(static fn (IncidentCondition $c): string => $c->value);

        $breakdown = [];
        foreach (IncidentCondition::cases() as $case) {
            $breakdown[$case->value] = (int) ($counts[$case->value] ?? 0);
        }

        return $breakdown;
    }

    /**
     * Condition labels + colors for the front-end legend and popups.
     *
     * @return array<int, array{value: string, label: string, color: string}>
     */
    private function conditionMeta(): array
    {
        $colors = IncidentCondition::chartColors();

        return array_map(static fn (IncidentCondition $c): array => [
            'value' => $c->value,
            'label' => $c->label(),
            'color' => $colors[$c->value],
        ], IncidentCondition::cases());
    }

    private function resolveDate(?string $value): Carbon
    {
        if ($value === null || $value === '') {
            return Carbon::today();
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return Carbon::today();
        }
    }
}
