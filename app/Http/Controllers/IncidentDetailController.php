<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\IncidentCondition;
use App\Models\Incident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Drill-down detail endpoints for the dashboard / map (JSON).
 *
 * All access is via Eloquent with validated, parameterised input.
 */
class IncidentDetailController extends Controller
{
    /**
     * Return incidents for a given condition on a given date as JSON.
     *
     * Query params:
     *   - date (required, date)
     *   - condition (optional, one of the allowed conditions)
     *   - location (optional, gate code)
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'condition' => ['nullable', Rule::in(IncidentCondition::values())],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $date = Carbon::parse($validated['date'])->toDateString();

        $incidents = Incident::query()
            ->whereDate('occurred_at', $date)
            ->when($validated['condition'] ?? null, fn ($q, $condition) => $q->where('condition', $condition))
            ->when($validated['location'] ?? null, fn ($q, $location) => $q->where('location', $location))
            ->orderByDesc('id')
            ->get(['id', 'name', 'condition', 'hospital', 'occurred_at', 'location', 'flight_no']);

        return response()->json([
            'date' => $date,
            'condition' => $validated['condition'] ?? null,
            'location' => $validated['location'] ?? null,
            'count' => $incidents->count(),
            'incidents' => $incidents,
        ]);
    }
}
