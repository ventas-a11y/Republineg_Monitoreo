<?php

use App\Enums\Role;
use App\Models\Area;
use App\Models\User;
use App\Notifications\AccountInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $this->admin = User::factory()->administrador()->withTwoFactor()->create();
    $this->area = Area::factory()->create();
});

describe('index', function () {
    test('lists accounts for an Administrador', function () {
        User::factory()->empleado($this->area)->create(['name' => 'Carla Pérez']);

        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('admin/users/index')
                ->has('users', 2)
                ->where('users', fn ($users): bool => collect($users)
                    ->firstWhere('name', 'Carla Pérez')['areas'] === [$this->area->name]));
    });

    test('responds 404 to a Revisor', function () {
        $revisor = User::factory()->revisor($this->area)->withTwoFactor()->create();

        $this->actingAs($revisor)
            ->get(route('admin.users.index'))
            ->assertNotFound();
    });
});

describe('store', function () {
    test('creates an Empleado and emails a temporary password that expires in 72 hours', function () {
        $this->freezeSecond();

        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Carla Pérez',
            'email' => 'carla@example.com',
            'role' => Role::Empleado->value,
            'area_id' => $this->area->id,
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.users.index'));

        $user = User::firstWhere('email', 'carla@example.com');
        expect($user->role)->toBe(Role::Empleado)
            ->and($user->area_id)->toBe($this->area->id)
            ->and($user->password_change_required)->toBeTrue()
            ->and($user->temporary_password_expires_at->equalTo(now()->addHours(72)))->toBeTrue()
            ->and($user->email_verified_at)->toBeNull();

        Notification::assertSentTo($user, AccountInvitation::class, function (AccountInvitation $notification) use ($user): bool {
            $body = implode("\n", $notification->toMail($user)->introLines);
            preg_match('/\*\*(\w{16})\*\*/', $body, $matches);

            return isset($matches[1]) && Hash::check($matches[1], $user->password);
        });
    });

    test('creates a Revisor with the reviewed Áreas and no own Área', function () {
        $otherArea = Area::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'María Gómez',
            'email' => 'maria@example.com',
            'role' => Role::Revisor->value,
            'area_id' => $this->area->id,
            'reviewed_area_ids' => [$this->area->id, $otherArea->id],
        ])->assertSessionHasNoErrors();

        $user = User::firstWhere('email', 'maria@example.com');
        expect($user->area_id)->toBeNull()
            ->and($user->reviewedAreas->pluck('id')->sort()->values()->all())
            ->toBe(collect([$this->area->id, $otherArea->id])->sort()->values()->all());
    });

    test('requires an Área for an Empleado', function () {
        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Carla Pérez',
            'email' => 'carla@example.com',
            'role' => Role::Empleado->value,
        ])->assertSessionHasErrors(['area_id' => 'Un Empleado debe pertenecer a un Área.']);
    });

    test('requires at least one reviewed Área for a Revisor', function () {
        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'María Gómez',
            'email' => 'maria@example.com',
            'role' => Role::Revisor->value,
            'reviewed_area_ids' => [],
        ])->assertSessionHasErrors(['reviewed_area_ids' => 'Un Revisor debe tener al menos un Área asignada.']);
    });

    test('rejects an email that already has an account', function () {
        User::factory()->create(['email' => 'carla@example.com']);

        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Carla Pérez',
            'email' => 'carla@example.com',
            'role' => Role::Administrador->value,
        ])->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    });
});

describe('update', function () {
    test('switching an Empleado to Revisor drops the own Área and assigns reviewed Áreas', function () {
        $user = User::factory()->empleado($this->area)->create();

        $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => Role::Revisor->value,
            'reviewed_area_ids' => [$this->area->id],
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.users.index'));

        $user->refresh();
        expect($user->role)->toBe(Role::Revisor)
            ->and($user->area_id)->toBeNull()
            ->and($user->reviewedAreas->pluck('id')->all())->toBe([$this->area->id]);
    });

    test('a changed email must be verified again', function () {
        $user = User::factory()->empleado($this->area)->create();

        $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => 'nuevo@example.com',
            'role' => Role::Empleado->value,
            'area_id' => $this->area->id,
        ])->assertSessionHasNoErrors();

        expect($user->refresh()->email_verified_at)->toBeNull();
    });

    test('an Administrador cannot remove their own Administrador role', function () {
        $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => Role::Empleado->value,
            'area_id' => $this->area->id,
        ])->assertSessionHasErrors(['role' => 'No puedes quitarte el rol de Administrador.']);

        expect($this->admin->refresh()->role)->toBe(Role::Administrador);
    });

    test('responds 404 for an identifier that is not a ULID', function () {
        $this->actingAs($this->admin)
            ->get('/admin/usuarios/123/editar')
            ->assertNotFound();
    });
});

describe('resendInvitation', function () {
    test('replaces the password with a new temporary one and emails it', function () {
        $user = User::factory()->empleado($this->area)->create();

        $this->actingAs($this->admin)
            ->post(route('admin.users.invitation', $user))
            ->assertRedirect();

        $user->refresh();
        expect($user->password_change_required)->toBeTrue()
            ->and(Hash::check('password', $user->password))->toBeFalse();

        Notification::assertSentTo($user, AccountInvitation::class);
    });
});
