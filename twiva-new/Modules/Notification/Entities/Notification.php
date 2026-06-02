<?php

namespace Modules\Notification\Entities;

use Apxcde\OnfonSms\OnfonChannel;
use Illuminate\Database\Eloquent\Model;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenCanceledNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDeliveredNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDispatchedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenPlacedNotification;
use Modules\Notification\Notifications\Buyer\OrderIsBeingProcessedNotification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Twilio\TwilioChannel;

class Notification extends Model
{
    protected $casts = ['data' => 'array'];

    const VIA_EMAIL = ['mail', 'database'];
    const VIA_FCM = [FcmChannel::class, 'database'];
    const VIA_MOBILE = [OnfonChannel::class, 'database'];
    const VIA_FCM_MOBILE = [FcmChannel::class, OnfonChannel::class, 'database'];
    const VIA_EMAIL_MOBILE = ['mail', OnfonChannel::class, 'database'];
    const VIA_EMAIL_FCM = ['mail', FcmChannel::class, 'database'];
    const VIA_ALL = [FcmChannel::class, OnfonChannel::class, 'database', 'mail'];

    const NOTHING_ACTION = 'NOTHING_ACTION';
    // influencer
    const VISIT_PAYMENT_HISTORY_ACTION = 'VISIT_PAYMENT_HISTORY_ACTION';
    const VISIT_REFERRAL_POINTS_ACTION = 'VISIT_REFERRAL_POINTS_ACTION';
    const VISIT_MY_EARNINGS_ACTION = 'VISIT_MY_EARNINGS_ACTION';
    const VISIT_POST_STORY_ACTION = 'VISIT_POST_STORY_ACTION';
    const VISIT_INVITATION_ACTION = 'VISIT_INVITATION_ACTION';
    const VISIT_COMMUNITY_ACTION = 'VISIT_COMMUNITY_ACTION';
    // business
    const VISIT_CAMPAIGN_ACTION = 'VISIT_CAMPAIGN_ACTION';
    const VISIT_ORDER_ACTION = 'VISIT_ORDER_ACTION';
    const VISIT_CATALOG_ACTION = 'VISIT_CATALOG_ACTION';
    // buyer
    protected $guarded = [];


    public function user()
    {
        return $this->morphTo(__FUNCTION__,'notifiable_type','notifiable_id');
    }


//    public function buyer()
//    {
//       return $this->morphTo(Buyer::class,'notifiable_type');
//
//    }

    public function getIsOrderAttribute(): bool
    {
        return ($this->type == NewOrderReceivedNotification::class)
            || ($this->type == OrderHasBeenCanceledNotification::class)
            || ($this->type == OrderHasBeenDeliveredNotification::class)
            || ($this->type == OrderHasBeenDispatchedNotification::class)
            || ($this->type == OrderHasBeenPlacedNotification::class)
            || ($this->type == OrderIsBeingProcessedNotification::class);
    }

    public function getNotificationTypeAttribute()
    {
        return preg_replace('/(?<=[a-z])[A-Z]|[A-Z](?=[a-z])/', ' $0', class_basename($this->type));
    }
}
