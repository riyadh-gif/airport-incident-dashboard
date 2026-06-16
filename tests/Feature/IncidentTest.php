<?php

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists incidents for an authenticated user', function () {
    $user = User::factory()->create();
    Incident::factory()->count(3)->create();

    $this->actingAs($user)->get('/incidents')->assertOk();
});

it('stores a single incident', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/incidents', [
        'rows' => [
            [
                'name' => 'Budi Santoso',
                'condition' => 'ringan',
                'hospital' => 'RS Darmo',
                'occurred_at' => '2026-06-16',
                'location' => 'A12',
                'flight_no' => 'GA-431',
            ],
        ],
    ]);

    $response->assertRedirect(route('incidents.index'));
    expect(Incident::count())->toBe(1);
    $this->assertDatabaseHas('incidents', ['name' => 'Budi Santoso', 'location' => 'A12']);
});

it('stores multiple incidents in one bulk submit', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/incidents', [
        'rows' => [
            ['name' => 'A', 'condition' => 'ringan', 'hospital' => 'H1', 'occurred_at' => '2026-06-16', 'location' => 'A1', 'flight_no' => 'GA-1'],
            ['name' => 'B', 'condition' => 'berat', 'hospital' => 'H2', 'occurred_at' => '2026-06-16', 'location' => 'B2', 'flight_no' => 'GA-2'],
            ['name' => 'C', 'condition' => 'sedang', 'hospital' => 'H3', 'occurred_at' => '2026-06-16', 'location' => 'C3', 'flight_no' => 'GA-3'],
        ],
    ]);

    $response->assertRedirect(route('incidents.index'));
    expect(Incident::count())->toBe(3);
});

it('rejects an invalid condition', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/incidents', [
        'rows' => [
            ['name' => 'A', 'condition' => 'invalid-condition', 'hospital' => 'H1', 'occurred_at' => '2026-06-16', 'location' => 'A1', 'flight_no' => 'GA-1'],
        ],
    ]);

    $response->assertSessionHasErrors('rows.0.condition');
    expect(Incident::count())->toBe(0);
});

it('rejects an empty gate/location', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/incidents', [
        'rows' => [
            ['name' => 'A', 'condition' => 'ringan', 'hospital' => 'H1', 'occurred_at' => '2026-06-16', 'location' => '', 'flight_no' => 'GA-1'],
        ],
    ]);

    $response->assertSessionHasErrors('rows.0.location');
    expect(Incident::count())->toBe(0);
});

it('updates an incident', function () {
    $user = User::factory()->create();
    $incident = Incident::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($user)->put("/incidents/{$incident->id}", [
        'name' => 'New Name',
        'condition' => 'berat',
        'hospital' => 'RS Baru',
        'occurred_at' => '2026-06-16',
        'location' => 'K9',
        'flight_no' => 'JT-200',
    ]);

    $response->assertRedirect(route('incidents.index'));
    $this->assertDatabaseHas('incidents', ['id' => $incident->id, 'name' => 'New Name', 'location' => 'K9']);
});

it('deletes an incident', function () {
    $user = User::factory()->create();
    $incident = Incident::factory()->create();

    $this->actingAs($user)->delete("/incidents/{$incident->id}")->assertRedirect(route('incidents.index'));
    expect(Incident::count())->toBe(0);
});
