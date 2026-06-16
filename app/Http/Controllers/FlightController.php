<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Models\Flight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlightController extends Controller
{
    /**
     * Searchable, paginated flight list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $flights = Flight::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('flight_no', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('flights.index', [
            'flights' => $flights,
            'search' => $search,
        ]);
    }

    /**
     * Show the bulk-add create form (1..N rows).
     */
    public function create(): View
    {
        return view('flights.create');
    }

    /**
     * Persist one or more flight rows in a single submit.
     */
    public function store(StoreFlightRequest $request): RedirectResponse
    {
        $rows = $request->flightRows();

        foreach ($rows as $row) {
            Flight::create($row);
        }

        $count = count($rows);

        return redirect()
            ->route('flights.index')
            ->with('success', "{$count} flight(s) recorded successfully.");
    }

    /**
     * Show the edit form for a single flight.
     */
    public function edit(Flight $flight): View
    {
        return view('flights.edit', [
            'flight' => $flight,
        ]);
    }

    /**
     * Update a single flight.
     */
    public function update(UpdateFlightRequest $request, Flight $flight): RedirectResponse
    {
        $flight->update($request->validated());

        return redirect()
            ->route('flights.index')
            ->with('success', 'Flight updated successfully.');
    }

    /**
     * Delete a flight.
     */
    public function destroy(Flight $flight): RedirectResponse
    {
        $flight->delete();

        return redirect()
            ->route('flights.index')
            ->with('success', 'Flight deleted successfully.');
    }
}
