<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MapController extends Controller
{
    /** Gate rows in the synthetic terminal schematic. */
    private const ROWS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

    /** Gate columns (1..29). */
    private const COLUMN_COUNT = 29;

    /**
     * Render the airport gate map with per-gate incident counts for a date.
     */
    public function index(Request $request): View
    {
        $date = $this->resolveDate($request->query('date'));

        // Per-gate incident counts for the selected date (Eloquent groupBy).
        $counts = Incident::query()
            ->whereDate('occurred_at', $date)
            ->selectRaw('location, COUNT(*) as total')
            ->groupBy('location')
            ->pluck('total', 'location');

        $rows = [];
        foreach (self::ROWS as $rowLetter) {
            $gates = [];
            for ($col = 1; $col <= self::COLUMN_COUNT; $col++) {
                $code = $rowLetter . $col;
                $gates[] = [
                    'code' => $code,
                    'count' => (int) ($counts[$code] ?? 0),
                ];
            }
            $rows[$rowLetter] = $gates;
        }

        return view('map.index', [
            'date' => $date->toDateString(),
            'rows' => $rows,
            'columnCount' => self::COLUMN_COUNT,
            'totalIncidents' => (int) $counts->sum(),
            'activeGates' => $counts->count(),
        ]);
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
