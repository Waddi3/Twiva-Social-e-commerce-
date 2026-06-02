<?php

namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Modules\Order\Entities\Order;
use Modules\Order\Mail\OrderQrCodeDeliveryEmail;
use Modules\Order\Notifications\NotifyBuyerOfDeliveryCodeOrder;
use Modules\System\Entities\System;
use NotificationChannels\Twilio\TwilioChannel;

class SendQRCodeToDeliveryOrderJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;
    public $shipping_address;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->shipping_address = $this->order->shippingAddress;
        $this->onQueue('order-actions');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendEmail();
//        $this->sendSms();
    }

    private function sendEmail()
    {
        if ($this->shipping_address->email)
        {
            Mail::to($this->shipping_address->email)->send(new OrderQrCodeDeliveryEmail($this->order));
        }
    }

    private function sendSms()
    {
        // This SMS function is not in use. | The SMS function is not being used.
        if ($this->shipping_address->mobile)
        {
            if (System::first()->shouldSendSMSNotificationsToBuyers ?? false)
            {
                $delivery_code_url = route('buyer.delivery.code.show.details', [
                        'delivery_code' => $this->order->delivery_code
                    ]
                );
                $sms_content = $delivery_code_url;
                // Notification::route(TwilioChannel::class, $this->shipping_address->mobile)
                $this->shipping_address->notify(new NotifyBuyerOfDeliveryCodeOrder($sms_content));
            }
        }
    }
}
