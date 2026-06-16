<?php

use App\Models\Incident;
use App\Models\User;
use App\Support\GateCoordinates;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests away from the map', function () {
    $this->get('/map')->assertRedirect('/login');
});

it('loads the map with per-gate incident aggregates for the selected date', function () {
    $user = User::factory()->create();

    Incident::factory()->count(2)->create([
        'location' => 'A12',
        'condition' => 'berat',
        'occurred_at' => '2026-06-16',
    ]);
    Incident::factory()->create([
        'location' => 'B3',
        'condition' => 'ringan',
        'occurred_at' => '2026-06-16',
    ]);
    // Different date — should not count.
    Incident::factory()->create(['location' => 'A12', 'occurred_at' => '2026-06-15']);

    $response = $this->actingAs($user)->get('/map?date=2026-06-16');

    $response->assertOk();
    $response->assertViewHas('totalIncidents', 3);
    $response->assertViewHas('activeGates', 2);
    $response->assertViewHas('mapConfig');
    $response->assertViewHas('conditionMeta');

    $gates = collect($response->viewData('gates'));

    // Busiest gate (A12, count 2) is first thanks to the count-desc ordering.
    $a12 = $gates->firstWhere('gate', 'A12');
    expect($a12)->not->toBeNull();
    expect($a12['count'])->toBe(2);
    expect($a12['breakdown']['berat'])->toBe(2);
    expect($a12['breakdown']['ringan'])->toBe(0);

    // Server-provided synthetic coordinates are deterministic.
    $coords = GateCoordinates::forGate('A12');
    expect($a12['lat'])->toBe($coords['lat']);
    expect($a12['lng'])->toBe($coords['lng']);

    $b3 = $gates->firstWhere('gate', 'B3');
    expect($b3['count'])->toBe(1);
    expect($b3['breakdown']['ringan'])->toBe(1);
});

it('renders the leaflet map container and embedded gate data', function () {
    $user = User::factory()->create();

    Incident::factory()->create([
        'location' => 'C7',
        'condition' => 'sedang',
        'occurred_at' => '2026-06-16',
    ]);

    $response = $this->actingAs($user)->get('/map?date=2026-06-16');

    $response->assertOk();
    $response->assertSee('id="incident-map"', false);
    $response->assertSee('id="incident-map-data"', false);
    // Gate code is present in the embedded JSON payload.
    $response->assertSee('C7');
});

it('ignores incidents whose location does not map to a gate', function () {
    $user = User::factory()->create();

    Incident::factory()->create([
        'location' => 'NOT-A-GATE',
        'occurred_at' => '2026-06-16',
    ]);

    $response = $this->actingAs($user)->get('/map?date=2026-06-16');

    $response->assertOk();
    $response->assertViewHas('activeGates', 0);
    $response->assertViewHas('totalIncidents', 0);
    expect(collect($response->viewData('gates')))->toHaveCount(0);
});
