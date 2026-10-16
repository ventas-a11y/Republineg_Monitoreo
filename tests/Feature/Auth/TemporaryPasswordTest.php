<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('a user with a temporary password is sent to change it before using the panel', function () {
    $user = User::factory()->withTemporaryPassword()->withTwoFactor()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('temporary-password.edit'));
});

test('a user with a temporary password cannot reach settings either', function () {
    $user = User::factory()->withTemporaryPassword()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertRedirect(route('temporary-password.edit'));
});

test('the change screen is rendered for a user with a temporary password', function () {
    $user = User::factory()->withTemporaryPassword()->create();

    $this->actingAs($user)
        ->get(route('temporary-password.edit'))
        ->assertOk();
});

test('replacing the temporary password clears it, verifies the email and continues to security', function () {
    $user = User::factory()->withTemporaryPassword()->create();

    $response = $this->actingAs($user)->put(route('temporary-password.update'), [
        'password' => 'nueva clave segura',
        'password_confirmation' => 'nueva clave segura',
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('security.edit'));

    $user->refresh();
    expect(Hash::check('nueva clave segura', $user->password))->toBeTrue()
        ->and($user->password_change_required)->toBeFalse()
        ->and($user->temporary_password_expires_at)->toBeNull()
        ->and($user->email_verified_at)->not->toBeNull();
});

test('the new password must differ from the temporary one', function () {
    $user = User::factory()->withTemporaryPassword()->create();

    $this->actingAs($user)->put(route('temporary-password.update'), [
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors(['password' => 'La nueva contraseña debe ser distinta de la temporal.']);

    expect($user->refresh()->password_change_required)->toBeTrue();
});

test('passwords follow length rules without composition rules', function (string $password, bool $accepted) {
    $user = User::factory()->withTemporaryPassword()->create();

    $response = $this->actingAs($user)->put(route('temporary-password.update'), [
        'password' => $password,
        'password_confirmation' => $password,
    ]);

    $accepted
        ? $response->assertSessionHasNoErrors()
        : $response->assertSessionHasErrors('password');
})->with([
    'eight lowercase letters' => ['abcdefgh', true],
    'seven characters' => ['abcdefg', false],
    'longer than 128 characters' => [str_repeat('a', 129), false],
]);

test('a user without a temporary password cannot use the change endpoint', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put(route('temporary-password.update'), [
        'password' => 'nueva clave segura',
        'password_confirmation' => 'nueva clave segura',
    ])->assertNotFound();
});

test('an expired temporary password is rejected with the generic login error', function () {
    $user = User::factory()->withTemporaryPassword()->create([
        'temporary_password_expires_at' => now()->subMinute(),
    ]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors(['email' => __('auth.failed')]);

    $this->assertGuest();
});

test('a valid temporary password logs the user in', function () {
    $user = User::factory()->withTemporaryPassword()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
});
