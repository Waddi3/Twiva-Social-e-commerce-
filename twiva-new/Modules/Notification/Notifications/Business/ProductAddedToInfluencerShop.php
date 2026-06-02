<?php

namespace Modules\Notification\Notifications\Business;

use Modules\Influencer\Entities\Product;
use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class ProductAddedToInfluencerShop extends BaseNotificationClass
{
    public array $details;
    private Product $product;


    public function __construct($product)
    {
        $this->product = $product;
        $this->onQueue('system');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        return NotificationEntity::VIA_FCM;
    }


    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'product_added_to_influencer_shop_title')?->title; // 'One of your products has been added to an influencer\'s e-shop.';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'product_added_to_influencer_shop_fcm_title')?->title; // 'Product added to influencer shop';
        $details['sms_message'] = null;
        $details['email_message'] = null;
        $details['flutter_action'] = NotificationEntity::VISIT_CATALOG_ACTION;
        $details['reason_id'] = $this->product->id;

        return $details;
    }
}
