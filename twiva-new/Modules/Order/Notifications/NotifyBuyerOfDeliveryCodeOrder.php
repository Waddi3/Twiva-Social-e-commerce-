<?php

namespace Modules\Order\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NotifyBuyerOfDeliveryCodeOrder extends Notification
{
    use Queueable;

    public string $sms_content;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $sms_content)
    {
        $this->sms_content = $sms_content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class, 'database'];
    }

    public function toTwilio(): TwilioSmsMessage|TwilioMessage|null
    {
        return (new TwilioSmsMessage())->content($this->sms_content);
    }

    public function toArray(): array
    {
        return [$this->sms_content];
    }

}
