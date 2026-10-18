<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecoveryCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly string $code) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Código de recuperación de cuenta')
            ->greeting('Hola '.$notifiable->name)
            ->line('Recibes este correo porque solicitaste restablecer tu contraseña en '.config('app.name').'.')
            ->line('Tu código de verificación es: `'.$this->code.'`')
            ->line('Válido por 15 minutos.')
            ->line('Si no solicitaste este cambio, ignora este correo; tu contraseña no cambiará.')
            ->line('Si necesitas ayuda, responde a este mensaje.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return ['code' => $this->code];
    }
}
