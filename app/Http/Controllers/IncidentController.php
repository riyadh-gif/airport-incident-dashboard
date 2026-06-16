<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\IncidentCondition;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Incident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentController extends Controller
{
    /**
     * Searchable, paginated incident list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $incidents = Incident::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('flight_no', 'like', "%{$search}%")
                        ->orWhere('hospital', 'like', "%{$search}%")
                        ->orWhere('condition', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('incidents.index', [
            'incidents' => $incidents,
            'search' => $search,
        ]);
    }

    /**
     * Show the bulk-add create form (1..N rows).
     */
    public function create(): View
    {
        return view('incidents.create', [
            'conditions' => IncidentCondition::cases(),
        ]);
    }

    /**
     * Persist one or more incident rows in a single submit.
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $rows = $request->incidentRows();

        foreach ($rows as $row) {
            Incident::create($row);
        }

        $count = count($rows);

        return redirect()
            ->route('incidents.index')
            ->with('success', "{$count} incident(s) recorded successfully.");
    }

    /**
     * Show the edit form for a single incident.
     */
    public function edit(Incident $incident): View
    {
        return view('incidents.edit', [
            'incident' => $incident,
            'conditions' => IncidentCondition::cases(),
        ]);
    }

    /**
     * Update a single incident.
     */
    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        $incident->update($request->validated());

        return redirect()
            ->route('incidents.index')
            ->with('success', 'Incident updated successfully.');
    }

    /**
     * Delete an incident.
     */
    public function destroy(Incident $incident): RedirectResponse
    {
        $incident->delete();

        return redirect()
            ->route('incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }
}
