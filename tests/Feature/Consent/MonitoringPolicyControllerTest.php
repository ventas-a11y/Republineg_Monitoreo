<?php

use App\Models\Area;
use App\Models\MonitoringPolicy;
use App\Models\PolicyAcceptance;
use App\Models\User;

beforeEach(function () {
    $this->empleado = User::factory()->empleado(Area::factory()->create())->withTwoFactor()->create();
});

test('an Empleado must accept the current policy before using the panel', function () {
    MonitoringPolicy::factory()->create();

    $this->actingAs($this->empleado)
        ->get(route('dashboard'))
        ->assertRedirect(route('monitoring-policy.show'));
});

test('an Empleado reaches the panel while no policy has been published', function () {
    $this->actingAs($this->empleado)
        ->get(route('dashboard'))
        ->assertOk();
});

test('Revisores and Administradores do not have to accept the policy', function (string $role) {
    MonitoringPolicy::factory()->create();
    $user = User::factory()->{$role}()->withTwoFactor()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
})->with(['revisor', 'administrador']);

test('the policy page shows the current version as safe HTML', function () {
    MonitoringPolicy::factory()->create([
        'body' => "## Qué se registra\n\n<script>alert('x')</script>\n\n[enlace](javascript:alert(1))",
    ]);

    $this->actingAs($this->empleado)
        ->get(route('monitoring-policy.show'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('consent/monitoring-policy')
            ->where('mustAccept', true)
            ->where('acceptedAt', null)
            ->where('policy.body_html', fn (string $html): bool => str_contains($html, '<h2>Qué se registra</h2>')
                && ! str_contains($html, '<script')
                && ! str_contains($html, 'javascript:')));
});

test('accepting records the version, address and browser, then opens the panel', function () {
    $policy = MonitoringPolicy::factory()->create();

    $this->actingAs($this->empleado)
        ->withHeader('User-Agent', 'Chrome de prueba')
        ->post(route('monitoring-policy.accept'), ['accept' => '1', 'monitoring_policy_id' => $policy->id])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $acceptance = PolicyAcceptance::sole();
    expect($acceptance->user_id)->toBe($this->empleado->id)
        ->and($acceptance->monitoring_policy_id)->toBe($policy->id)
        ->and($acceptance->ip_address)->toBe('127.0.0.1')
        ->and($acceptance->user_agent)->toBe('Chrome de prueba');

    $this->get(route('dashboard'))->assertOk();
});

test('accepting requires confirming that the policy was read', function () {
    $policy = MonitoringPolicy::factory()->create();

    $this->actingAs($this->empleado)
        ->post(route('monitoring-policy.accept'), ['monitoring_policy_id' => $policy->id])
        ->assertSessionHasErrors(['accept' => 'Marca la casilla para confirmar que leíste la política.']);

    expect(PolicyAcceptance::count())->toBe(0);
});

test('an outdated version cannot be accepted after a new one is published', function () {
    $outdated = MonitoringPolicy::factory()->create();
    MonitoringPolicy::factory()->create();

    $this->actingAs($this->empleado)
        ->post(route('monitoring-policy.accept'), ['accept' => '1', 'monitoring_policy_id' => $outdated->id])
        ->assertSessionHasErrors(['monitoring_policy_id' => 'Se publicó una versión nueva de la política. Léela antes de aceptarla.']);

    expect(PolicyAcceptance::count())->toBe(0);
});

test('a new version must be accepted again', function () {
    $first = MonitoringPolicy::factory()->create();
    PolicyAcceptance::factory()->create(['user_id' => $this->empleado->id, 'monitoring_policy_id' => $first->id]);

    $this->actingAs($this->empleado)->get(route('dashboard'))->assertOk();

    MonitoringPolicy::factory()->create();

    $this->get(route('dashboard'))->assertRedirect(route('monitoring-policy.show'));
});

test('a Revisor cannot record an acceptance', function () {
    $policy = MonitoringPolicy::factory()->create();
    $revisor = User::factory()->revisor()->withTwoFactor()->create();

    $this->actingAs($revisor)
        ->post(route('monitoring-policy.accept'), ['accept' => '1', 'monitoring_policy_id' => $policy->id])
        ->assertNotFound();

    expect(PolicyAcceptance::count())->toBe(0);
});
