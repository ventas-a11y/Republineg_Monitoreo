<?php

namespace App\Notifications;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Invitation email with the temporary password of a new (or reset) account.
 * Not queued: the plaintext password must never sit in the queue.
 */
class AccountInvitation extends Notification
{
    public function __construct(
        private readonly string $temporaryPassword,
        private readonly CarbonInterface $expiresAt,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu cuenta en '.config('app.name'))
            ->greeting('Hola, '.$notifiable->name)
            ->line('Se creó tu cuenta en '.config('app.name').' con el rol '.$notifiable->role->label().'.')
            ->line('Tu contraseña temporal es: **'.$this->temporaryPassword.'**')
            ->line('Vence el '.$this->expiresAt->timezone('America/Guayaquil')->format('d/m/Y \a \l\a\s H:i').'.')
            ->action('Iniciar sesión', route('login'))
            ->line('Al entrar deberás cambiarla por una contraseña propia y configurar la verificación en dos pasos o una passkey.')
            ->line('Si no esperabas este correo, avisa a tu Administrador.');
    }
}
