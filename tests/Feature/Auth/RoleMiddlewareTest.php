<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows an admin to access a role:admin route', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/users');

    $response->assertOk();
});

it('forbids an operator from accessing a role:admin route', function () {
    $operator = User::factory()->create(['role' => 'operator']);

    $response = $this->actingAs($operator)->get('/users');

    $response->assertForbidden();
});

it('redirects a guest away from a role:admin route', function () {
    // The `auth` middleware runs before `role`, so guests are redirected to login.
    $response = $this->get('/users');

    $response->assertRedirect('/login');
});
