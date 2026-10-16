<?php

namespace Database\Factories;

use App\Models\MonitoringPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonitoringPolicy>
 */
class MonitoringPolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version' => fn (): int => MonitoringPolicy::nextVersion(),
            'title' => 'Política de monitoreo de equipos',
            'body' => "## Qué se registra\n\nLa app y la ventana en primer plano, los Sitios y el Tiempo activo.\n\n## Tus derechos\n\nPuedes ver tu propia actividad en el panel.",
            'published_at' => now(),
        ];
    }
}
