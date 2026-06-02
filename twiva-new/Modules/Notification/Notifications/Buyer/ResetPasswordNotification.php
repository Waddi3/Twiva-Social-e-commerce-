<?php

namespace Modules\Notification\Notifications\Buyer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Notification\Mail\ResetPasswordEmail;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new ResetPasswordEmail($this->data))
            ->to($notifiable->email)
            ->subject('Reset password request');
    }
}
