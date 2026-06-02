<?php

namespace Modules\Notification\Mail\Buyer;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\Order;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventProductDetailsMail extends Mailable {
    use Queueable, SerializesModels;

    public Product $product;
    private array $details = [];
    private string  $delivery_code;

    public function __construct(Product $ticketProduct, string $delivery_code,string $mail)
    {
        $this->product = $ticketProduct;
        $this->delivery_code = $delivery_code;
        $this->to($mail);

    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Event Product Payment',
        );
    }

    public function content(): Content
    {
        $deliveryCodeUrl = route('buyer.delivery.code.show.details', [
                'delivery_code' => $this->delivery_code
            ]
        );
        return new Content(
            view: 'order::emails.event-product-payment',
            with: [
                'delivery_code' => $this->delivery_code,
                'delivery_code_url' => route('buyer.delivery.code.show.details', [
                        'delivery_code' => $this->delivery_code
                    ]
                ),
                'qr_code' => base64_encode(
                    QrCode::size(256)
                        ->format('png')
                        ->generate($deliveryCodeUrl)
                ),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
