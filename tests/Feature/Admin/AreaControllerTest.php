<?php

use App\Models\Area;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->administrador()->withTwoFactor()->create();
});

test('lists Áreas with their Empleado and Revisor counts', function () {
    $area = Area::factory()->create(['name' => 'Ventas']);
    User::factory()->empleado($area)->count(2)->create();
    User::factory()->revisor($area)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.areas.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/areas/index')
            ->where('areas.0.name', 'Ventas')
            ->where('areas.0.employees_count', 2)
            ->where('areas.0.reviewers_count', 1));
});

test('creates an Área', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.areas.store'), ['name' => 'Soporte'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.areas.index'));

    expect(Area::where('name', 'Soporte')->exists())->toBeTrue();
});

test('rejects a duplicated Área name', function () {
    Area::factory()->create(['name' => 'Soporte']);

    $this->actingAs($this->admin)
        ->post(route('admin.areas.store'), ['name' => 'Soporte'])
        ->assertSessionHasErrors('name');
});

test('renames an Área keeping its own name valid', function () {
    $area = Area::factory()->create(['name' => 'RRHH']);

    $this->actingAs($this->admin)
        ->put(route('admin.areas.update', $area), ['name' => 'RR. HH.'])
        ->assertSessionHasNoErrors();

    expect($area->refresh()->name)->toBe('RR. HH.');
});

test('responds 404 to an Empleado', function () {
    $empleado = User::factory()->withTwoFactor()->create();

    $this->actingAs($empleado)
        ->post(route('admin.areas.store'), ['name' => 'Soporte'])
        ->assertNotFound();

    expect(Area::count())->toBe(0);
});
