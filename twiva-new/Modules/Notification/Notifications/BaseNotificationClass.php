<?php

namespace Modules\Notification\Notifications;

use Apxcde\OnfonSms\OnfonMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Mail\BaseMail;
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

abstract class BaseNotificationClass extends Notification implements ShouldQueue
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
        // Logger('toOnfon notification');
        $this->details['sms_message'] = $this->details['sms_message'] . " Click on the link below to open twiva:  https://app.twiva.co.ke/";
        return (new OnfonMessage())
            ->content($this->details['sms_message']);
    }

    public function toMail($notifiable): BaseMail
    {
        $this->details['name'] = $notifiable->name;
        return (new BaseMail($this->details))
            ->to($notifiable->email)
            ->subject($this->details['title']);
    }

    public function toArray(): array
    {
        return $this->details;
    }

    public static function checkSystem($via)
    {
        if (System::first()->shouldSendSMSNotificationsToInfluencersAndBusinesses ?? false)
        {
            if (($key = array_search(NotificationEntity::VIA_MOBILE, $via)) !== false)
            {
                unset($via[$key]);
            }
        }

        return $via;
    }

    public function toFcm(): FcmMessage
    {
        $data = $this->details;
        unset($data['sms_message'], $data['email_message']);
        if ($data['reason_id'])
        {
            $data['reason_id'] = (string)$data['reason_id'];
        }
        else
        {
            unset($data['reason_id']);
        }

        return FcmMessage::create()
            ->setData($data)
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($data['fcm_title'])
                ->setBody($data['title']))
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')));
    }
}
