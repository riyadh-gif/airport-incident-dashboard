<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('shows a NIP field on the login screen', function () {
    $response = $this->get('/login');

    $response->assertOk();
    $response->assertSee('name="nip"', escape: false);
    $response->assertDontSee('type="email"', escape: false);
});

it('lets an operator log in with correct NIP and password', function () {
    $operator = User::factory()->create([
        'nip' => '20999',
        'role' => 'operator',
        'password' => Hash::make('secret-password'),
    ]);

    $response = $this->post('/login', [
        'nip' => '20999',
        'password' => 'secret-password',
    ]);

    $this->assertAuthenticatedAs($operator);
    $response->assertRedirect(route('dashboard', absolute: false));
});

it('rejects login with a wrong password', function () {
    User::factory()->create([
        'nip' => '20998',
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->post('/login', [
        'nip' => '20998',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('nip');
});

it('rejects login for an unknown NIP', function () {
    $response = $this->post('/login', [
        'nip' => '00000',
        'password' => 'whatever',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('nip');
});
