<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('forbids operators from accessing user management', function () {
    $operator = User::factory()->create(['role' => 'operator']);

    $this->actingAs($operator)->get('/users')->assertForbidden();
    $this->actingAs($operator)->get('/users/create')->assertForbidden();
});

it('allows admins to view the user list without leaking password hashes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $other = User::factory()->create(['password' => Hash::make('super-secret-hash-value')]);

    $response = $this->actingAs($admin)->get('/users');

    $response->assertOk();
    $response->assertDontSee($other->password, escape: false);
});

it('lets an admin create a user with a hashed password', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/users', [
        'name' => 'New Operator',
        'nip' => '30001',
        'role' => 'operator',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('users.index'));

    $user = User::where('nip', '30001')->first();
    expect($user)->not->toBeNull();
    expect($user->password)->not->toBe('password123');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('rejects a duplicate NIP', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['nip' => '40001']);

    $response = $this->actingAs($admin)->post('/users', [
        'name' => 'Dup',
        'nip' => '40001',
        'role' => 'operator',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('nip');
});

it('lets an admin delete another user but not themselves', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $victim = User::factory()->create();

    $this->actingAs($admin)->delete("/users/{$victim->id}")->assertRedirect(route('users.index'));
    expect(User::find($victim->id))->toBeNull();

    $this->actingAs($admin)->delete("/users/{$admin->id}");
    expect(User::find($admin->id))->not->toBeNull();
});
