<?php

namespace Modules\Order\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Entities\Order;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderQrCodeDeliveryEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Order Confirm Delivery',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content()
    {
        $delivery_code_url = route('buyer.delivery.code.show.details', [
                'delivery_code' => $this->order->delivery_code
            ]
        );
        return new Content(
            markdown: 'order::emails.orders.delivery-code',
            with: [
                'delivery_code' => $this->order->delivery_code,
                'delivery_code_url' => $delivery_code_url,
                'qr_code' => base64_encode(
                    QrCode::size(256)
                        ->format('png')
                        ->generate($delivery_code_url)
                ),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
