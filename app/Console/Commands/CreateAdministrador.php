<?php

namespace App\Console\Commands;

use App\Actions\Accounts\IssueTemporaryPassword;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * Bootstraps an Administrador: public registration is disabled, so the first
 * account (or a recovery account) is created from the console.
 */
#[Signature('monitoreo:crear-administrador {email : Correo de la cuenta} {name : Nombre completo}')]
#[Description('Crea una cuenta de Administrador y envía su invitación con contraseña temporal')]
class CreateAdministrador extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(IssueTemporaryPassword $issueTemporaryPassword): int
    {
        $validator = Validator::make($this->arguments(), [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'role' => Role::Administrador,
        ]);

        $temporaryPassword = $issueTemporaryPassword->handle($user);

        $this->info('Administrador creado: '.$user->email);
        $this->line('Se envió la invitación por correo. Contraseña temporal (se muestra una sola vez, vence en '.IssueTemporaryPassword::VALID_HOURS.' horas):');
        $this->line($temporaryPassword);

        return self::SUCCESS;
    }
}
