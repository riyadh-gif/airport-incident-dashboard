<?php

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns drill-down incident details as JSON filtered by date and condition', function () {
    $user = User::factory()->create();

    Incident::factory()->count(2)->create(['condition' => 'berat', 'occurred_at' => '2026-06-16']);
    Incident::factory()->create(['condition' => 'ringan', 'occurred_at' => '2026-06-16']);

    $response = $this->actingAs($user)->getJson('/details/incidents?date=2026-06-16&condition=berat');

    $response->assertOk();
    $response->assertJsonPath('count', 2);
    $response->assertJsonPath('condition', 'berat');
});

it('validates the date parameter on the detail endpoint', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->getJson('/details/incidents')->assertStatus(422);
});
