<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

test('public registration is disabled', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Intruso',
        'email' => 'intruso@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
});

test('a user without a second factor is sent to security settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('security.edit'));
});

test('a passkey counts as a second factor', function () {
    $user = User::factory()->create();
    $user->passkeys()->create([
        'name' => 'Llave de prueba',
        'credential_id' => 'credential-1',
        'credential' => [],
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('the login ends after the absolute session lifetime even with activity', function () {
    $user = User::factory()->withTwoFactor()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();

    $this->travel(config('session.absolute_lifetime') - 1)->minutes();
    $this->get(route('dashboard'))->assertOk();

    $this->travel(2)->minutes();
    $this->get(route('dashboard'))->assertRedirect(route('login'));

    $this->assertGuest();
});

test('login attempts are limited per account across different addresses', function () {
    $user = User::factory()->create();

    RateLimiter::increment(md5('login'.'account|'.$user->email), amount: 10);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.99'])
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertTooManyRequests();
});
