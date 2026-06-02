<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;

class SendLinkInvoiceNotification extends BuyerBaseNotificationClass
{
    public
    array $details;
    private $invoice;
    private $user;


    public function __construct($invoice, $user)
    {
        $this->invoice = $invoice;
        $this->user = $user;
        $this->onQueue('system');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_MOBILE;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $details['title'] = "{$this->user->name} sends you an invoice";
        $details['fcm_title'] = null;
        $details['sms_message'] = "View your order here: {$this->invoice->buyer_link} ";
        $details['email_message'] = "View your order here: {$this->invoice->buyer_link} ";
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = null;
        return $details;
    }
}
