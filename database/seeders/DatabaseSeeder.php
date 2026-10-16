<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Only Áreas: accounts are created by an Administrador, and the first
     * Administrador with `php artisan monitoreo:crear-administrador`.
     */
    public function run(): void
    {
        foreach (['Contabilidad', 'Gerencia', 'RR. HH.', 'Soporte', 'Ventas'] as $name) {
            Area::firstOrCreate(['name' => $name]);
        }
    }
}
