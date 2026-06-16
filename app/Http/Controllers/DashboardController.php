<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\IncidentCondition;
use App\Models\Flight;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with metric cards, a condition-breakdown chart,
     * and a date filter (query param `date`, default today).
     */
    public function index(Request $request): View
    {
        $date = $this->resolveDate($request->query('date'));

        // Condition breakdown for the selected date (Eloquent groupBy).
        $breakdown = Incident::query()
            ->whereDate('occurred_at', $date)
            ->selectRaw('condition, COUNT(*) as total')
            ->groupBy('condition')
            ->pluck('total', 'condition');

        $chart = $this->buildChartData($breakdown);

        $incidentsOnDate = (int) $breakdown->sum();
        $latestFlightNo = Flight::query()->latest('occurred_at')->value('flight_no');

        // "This period" = the calendar month containing the selected date.
        $periodTotal = Incident::query()
            ->whereBetween('occurred_at', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ])
            ->count();

        return view('dashboard', [
            'date' => $date->toDateString(),
            'incidentsOnDate' => $incidentsOnDate,
            'latestFlightNo' => $latestFlightNo,
            'periodTotal' => $periodTotal,
            'chartLabels' => $chart['labels'],
            'chartData' => $chart['data'],
            'chartColors' => $chart['colors'],
        ]);
    }

    /**
     * Resolve a date string into a Carbon date, defaulting to today.
     */
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

    /**
     * Build labels/data/colors arrays for the Chart.js dataset, covering
     * every known condition (zero-filled) so the chart is stable.
     *
     * @param  \Illuminate\Support\Collection<string, int>  $breakdown
     * @return array{labels: list<string>, data: list<int>, colors: list<string>}
     */
    private function buildChartData($breakdown): array
    {
        $colors = IncidentCondition::chartColors();

        $labels = [];
        $data = [];
        $hex = [];

        foreach (IncidentCondition::cases() as $condition) {
            $labels[] = $condition->label();
            $data[] = (int) ($breakdown[$condition->value] ?? 0);
            $hex[] = $colors[$condition->value];
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $hex];
    }
}
