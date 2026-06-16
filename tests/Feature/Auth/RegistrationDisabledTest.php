<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('does not register a public registration route', function () {
    expect(Route::has('register'))->toBeFalse();
});

it('returns 404 for the public registration GET route', function () {
    $this->get('/register')->assertNotFound();
});

it('returns 404 for the public registration POST route', function () {
    $this->post('/register', [
        'name' => 'Intruder',
        'nip' => '99999',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
});

it('does not expose the password-reset-by-email route', function () {
    expect(Route::has('password.request'))->toBeFalse();
    $this->get('/forgot-password')->assertNotFound();
});

it('does not expose the email-verification route', function () {
    expect(Route::has('verification.notice'))->toBeFalse();
});
