<?php

namespace App\Enums;

enum Role: string
{
    case Administrador = 'administrador';
    case Revisor = 'revisor';
    case Empleado = 'empleado';

    /**
     * Human-readable name shown in the panel.
     */
    public function label(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Revisor => 'Revisor',
            self::Empleado => 'Empleado',
        };
    }
}
