<?php

namespace Modules\Buyer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Influencer\Entities\Product;
use Modules\Notification\Mail\Buyer\EventProductDetailsMail;

class EventProductTicketDispatchedNotification extends Notification implements ShouldQueue {
    use Queueable;

    public $queue = "default";
    public Product $product;
    private string  $deliveryCode;

    public function __construct(Product $ticketProduct, string $delivery_code)
    {
        $this->product = $ticketProduct;
        $this->deliveryCode = $delivery_code;
    }
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return new EventProductDetailsMail(
            ticketProduct: $this->product,
            delivery_code: $this->deliveryCode,
            mail: $notifiable->email
        );
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
