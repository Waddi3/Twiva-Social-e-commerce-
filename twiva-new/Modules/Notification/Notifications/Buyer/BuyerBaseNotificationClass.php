<?php

namespace Modules\Notification\Notifications\Buyer;

use Apxcde\OnfonSms\OnfonMessage;
use Faker\Provider\Base;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Mail\BaseMail;
use Modules\Notification\Mail\BuyerOrderDetailsMail;
use Modules\System\Entities\System;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;
use TypeError;

abstract class BuyerBaseNotificationClass extends Notification implements ShouldQueue
{
    use Queueable;

    public array $via;
    public array $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    abstract function makeDetails();

    abstract function via($notifiable);


    public function toTwilio(): TwilioSmsMessage|TwilioMessage|null
    {
        $this->details['sms_message'] = $this->details['sms_message'] . " Click on the link below to open twiva:  https://app.twiva.co.ke/";
        return (new TwilioSmsMessage())->content($this->details['sms_message']);
    }

    public function toOnfon($notifiable)
    {
        $this->details['sms_message'] = $this->details['sms_message'] . " Click on the link below to open twiva:  https://app.twiva.co.ke/";
        return (new OnfonMessage())
            ->content($this->details['sms_message']);
    }

    public function toMail($notifiable): BuyerOrderDetailsMail|BaseMail|null
    {
        if (!$this->details['email']) {
            return null;
        }
        if (($this->details['email_type'] ?? false) == 'order_details') {
            return (new BuyerOrderDetailsMail($this->details['orders'] ?? []))
                ->to($this->details['email'])
                ->subject($this->details['title']);
        }

        return (new BaseMail($this->details))
            ->to($this->details['email'])
            ->subject($this->details['title']);

    }

    public function toArray(): array
    {
        return $this->details;
    }

    public function toFcm(): FcmMessage
    {
        return FcmMessage::create()
            ->setData($this->details)
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->details['fcm_title'])
                ->setBody($this->details['title']))
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')));
    }

    public static function checkSystem($via)
    {
        if (System::first()->shouldSendSMSNotificationsToBuyers ?? false) {
            if (($key = array_search(NotificationEntity::VIA_MOBILE, $via)) !== false) {
                unset($via[$key]);
            }
        }
        return $via;
    }
}
