<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TwoFactorCode extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Two Factor Code')
            ->line('Your 2FA code is: ' . $notifiable->two_factor_code)
            ->line('It will expire in 10 minutes.')
            ->line('If you did not request this, please ignore.');
    }
}
