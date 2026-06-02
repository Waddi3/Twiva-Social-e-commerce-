<?php

namespace Modules\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Entities\Order;
use Modules\Order\Events\OrderPaidSuccessfully;

class EmailInfluencer implements ShouldQueue
{
    public $tries = 3;

    public function __construct()
    {
        //
    }

    public function handle(OrderPaidSuccessfully $event)
    {
        $order = Order::select('id', 'influencer_id', 'created_at')->where('id', $event->order)
            ->with([
                    'influencerInfo' => function ($q) {
                        return $q->select('id', 'name', 'email');
                    }]
            )->first();

        Mail::send('emails.Influencer.newOrder',
            [
                'id' => $order->id,
                'date' => $order->created_at->format('d-M-Y'),
                'influencerName' => ($order->shop->influencerInfo->name ?? $order->shop->influencerInfo->email),
            ],
            function ($m) use ($order) {
                $m->from(config('mail.from.address'), config('app.name'));
                $m->to($order->shop->influencerInfo->email)->subject('You have new Sale');
            });
    }
}
