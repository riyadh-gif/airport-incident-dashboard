<?php

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests away from the dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('loads the dashboard for an authenticated user and exposes chart data', function () {
    $user = User::factory()->create();

    Incident::factory()->create([
        'condition' => 'ringan',
        'occurred_at' => today()->toDateString(),
    ]);
    Incident::factory()->create([
        'condition' => 'berat',
        'occurred_at' => today()->toDateString(),
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    // Chart data is embedded as JSON for Chart.js.
    $response->assertSee('dashboard-chart-data', escape: false);
    $response->assertViewHas('chartData');
    $response->assertViewHas('chartLabels');
});

it('counts incidents for the selected date only', function () {
    $user = User::factory()->create();

    Incident::factory()->count(3)->create(['occurred_at' => '2026-06-10']);
    Incident::factory()->count(2)->create(['occurred_at' => '2026-06-11']);

    $response = $this->actingAs($user)->get('/dashboard?date=2026-06-10');

    $response->assertOk();
    $response->assertViewHas('incidentsOnDate', 3);
});
