<?php

use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists flights for an authenticated user', function () {
    $user = User::factory()->create();
    Flight::factory()->count(2)->create();

    $this->actingAs($user)->get('/flights')->assertOk();
});

it('stores a single flight', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/flights', [
        'rows' => [
            ['flight_no' => 'GA-431', 'occurred_at' => '2026-06-16', 'location' => 'A12'],
        ],
    ]);

    $response->assertRedirect(route('flights.index'));
    expect(Flight::count())->toBe(1);
    $this->assertDatabaseHas('flights', ['flight_no' => 'GA-431', 'location' => 'A12']);
});

it('stores multiple flights in one bulk submit', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/flights', [
        'rows' => [
            ['flight_no' => 'GA-1', 'occurred_at' => '2026-06-16', 'location' => 'A1'],
            ['flight_no' => 'GA-2', 'occurred_at' => '2026-06-16', 'location' => 'B2'],
        ],
    ]);

    $response->assertRedirect(route('flights.index'));
    expect(Flight::count())->toBe(2);
});

it('rejects a flight with a missing flight number', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/flights', [
        'rows' => [
            ['flight_no' => '', 'occurred_at' => '2026-06-16', 'location' => 'A1'],
        ],
    ]);

    $response->assertSessionHasErrors('rows.0.flight_no');
    expect(Flight::count())->toBe(0);
});

it('updates a flight', function () {
    $user = User::factory()->create();
    $flight = Flight::factory()->create();

    $response = $this->actingAs($user)->put("/flights/{$flight->id}", [
        'flight_no' => 'ID-999',
        'occurred_at' => '2026-06-16',
        'location' => 'K1',
    ]);

    $response->assertRedirect(route('flights.index'));
    $this->assertDatabaseHas('flights', ['id' => $flight->id, 'flight_no' => 'ID-999']);
});
