<?php

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests away from the map', function () {
    $this->get('/map')->assertRedirect('/login');
});

it('loads the map with per-gate incident counts for the selected date', function () {
    $user = User::factory()->create();

    Incident::factory()->count(2)->create(['location' => 'A12', 'occurred_at' => '2026-06-16']);
    Incident::factory()->create(['location' => 'B3', 'occurred_at' => '2026-06-16']);
    // Different date — should not count.
    Incident::factory()->create(['location' => 'A12', 'occurred_at' => '2026-06-15']);

    $response = $this->actingAs($user)->get('/map?date=2026-06-16');

    $response->assertOk();
    $response->assertViewHas('totalIncidents', 3);
    $response->assertViewHas('activeGates', 2);
    $response->assertViewHas('rows');
});
