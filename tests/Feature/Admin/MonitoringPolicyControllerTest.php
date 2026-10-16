<?php

use App\Models\Area;
use App\Models\MonitoringPolicy;
use App\Models\PolicyAcceptance;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->administrador()->withTwoFactor()->create();
});

test('publishing creates consecutive versions authored by the Administrador', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.monitoring-policy.store'), ['title' => 'Política', 'body' => '## Versión uno'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.monitoring-policy.index'));

    $this->post(route('admin.monitoring-policy.store'), ['title' => 'Política', 'body' => '## Versión dos'])
        ->assertSessionHasNoErrors();

    $current = MonitoringPolicy::current();
    expect($current->version)->toBe(2)
        ->and($current->body)->toBe('## Versión dos')
        ->and($current->published_by)->toBe($this->admin->id)
        ->and(MonitoringPolicy::count())->toBe(2);
});

test('publishing requires a title and a text', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.monitoring-policy.store'), [])
        ->assertSessionHasErrors(['title', 'body']);

    expect(MonitoringPolicy::count())->toBe(0);
});

test('lists the Empleados who have not accepted the current version', function () {
    $area = Area::factory()->create();
    $policy = MonitoringPolicy::factory()->create();
    $accepted = User::factory()->empleado($area)->create(['name' => 'Ana Aceptó']);
    User::factory()->empleado($area)->create(['name' => 'Pablo Pendiente']);
    PolicyAcceptance::factory()->create(['user_id' => $accepted->id, 'monitoring_policy_id' => $policy->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.monitoring-policy.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/monitoring-policy/index')
            ->where('empleadosCount', 2)
            ->has('pending', 1)
            ->where('pending.0.name', 'Pablo Pendiente')
            ->where('versions.0.acceptances_count', 1));
});

test('responds 404 to a Revisor', function () {
    $revisor = User::factory()->revisor()->withTwoFactor()->create();

    $this->actingAs($revisor)
        ->post(route('admin.monitoring-policy.store'), ['title' => 'Política', 'body' => 'Texto'])
        ->assertNotFound();

    expect(MonitoringPolicy::count())->toBe(0);
});
