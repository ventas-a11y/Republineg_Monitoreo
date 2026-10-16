<?php

use App\Enums\Role;
use App\Models\User;
use App\Notifications\AccountInvitation;
use Illuminate\Support\Facades\Notification;

test('creates an Administrador with a temporary password and sends the invitation', function () {
    Notification::fake();

    $this->artisan('monitoreo:crear-administrador', ['email' => 'admin@example.com', 'name' => 'Ana Admin'])
        ->expectsOutputToContain('Administrador creado: admin@example.com')
        ->assertSuccessful();

    $user = User::firstWhere('email', 'admin@example.com');
    expect($user->role)->toBe(Role::Administrador)
        ->and($user->password_change_required)->toBeTrue();

    Notification::assertSentTo($user, AccountInvitation::class);
});

test('refuses an email that already has an account', function () {
    User::factory()->create(['email' => 'admin@example.com']);

    $this->artisan('monitoreo:crear-administrador', ['email' => 'admin@example.com', 'name' => 'Ana Admin'])
        ->assertFailed();

    expect(User::count())->toBe(1);
});
