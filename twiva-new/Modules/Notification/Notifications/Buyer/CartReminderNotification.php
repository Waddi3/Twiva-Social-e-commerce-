<?php
namespace Modules\Notification\Notifications\Buyer;

use Apxcde\OnfonSms\OnfonChannel;
use Apxcde\OnfonSms\OnfonMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Buyer\Entities\Buyer;

class CartReminderNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', OnfonChannel::class];
    }

    public function toMail($buyer)
    {
        return (new MailMessage)
            ->subject('Complete Your Purchase!')
            ->view('notification::email.buyer.cartReminder', ['buyer' => $buyer]);
    }

    public function toOnfon($buyer)
    {
        $details['sms_message'] = 'You have items left in your cart! Complete your purchase now and don’t miss out!';
        return (new OnfonMessage())
            ->content($details['sms_message']);
    }
}

