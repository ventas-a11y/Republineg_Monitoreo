<?php

use App\Actions\Consent\CheckCurrentConsent;
use App\Models\MonitoringPolicy;
use App\Models\PolicyAcceptance;
use App\Models\User;

test('consent is denied while no policy has been published', function () {
    $empleado = User::factory()->empleado()->create();

    expect(app(CheckCurrentConsent::class)->handle($empleado))->toBeFalse();
});

test('consent is denied without an acceptance of the current version', function () {
    $empleado = User::factory()->empleado()->create();
    $outdated = MonitoringPolicy::factory()->create();
    PolicyAcceptance::factory()->create(['user_id' => $empleado->id, 'monitoring_policy_id' => $outdated->id]);
    MonitoringPolicy::factory()->create();

    expect(app(CheckCurrentConsent::class)->handle($empleado))->toBeFalse();
});

test('consent is granted with an acceptance of the current version', function () {
    $empleado = User::factory()->empleado()->create();
    $current = MonitoringPolicy::factory()->create();
    PolicyAcceptance::factory()->create(['user_id' => $empleado->id, 'monitoring_policy_id' => $current->id]);

    expect(app(CheckCurrentConsent::class)->handle($empleado))->toBeTrue();
});

test('consent is denied for accounts that are not monitored', function () {
    $revisor = User::factory()->revisor()->create();
    MonitoringPolicy::factory()->create();

    expect(app(CheckCurrentConsent::class)->handle($revisor))->toBeFalse();
});

test('acceptance records cannot be modified or deleted', function () {
    $acceptance = PolicyAcceptance::factory()->create();

    expect(fn () => $acceptance->update(['ip_address' => '10.0.0.1']))->toThrow(LogicException::class)
        ->and(fn () => $acceptance->delete())->toThrow(LogicException::class);

    expect(PolicyAcceptance::sole()->ip_address)->not->toBe('10.0.0.1');
});
