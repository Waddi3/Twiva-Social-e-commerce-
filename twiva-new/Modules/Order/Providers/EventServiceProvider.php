<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\Events\CreatedOrderEvent;
use Modules\Order\Events\DeliveredOrderEvent;
use Modules\Order\Events\OrderPaidSuccessfully;
use Modules\Order\Listeners\CheckPointOfInvitorListen;
use Modules\Order\Listeners\CreatedOrderListener;
use Modules\Order\Listeners\EmailInfluencer;
use Modules\Order\Listeners\EmailSeller;
use Modules\Order\Listeners\NotifiySeller;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPaidSuccessfully::class => [
            EmailSeller::class,
            EmailInfluencer::class,
            NotifiySeller::class
        ],
        DeliveredOrderEvent::class => [
            CheckPointOfInvitorListen::class,
        ],
        CreatedOrderEvent::class => [
            CreatedOrderListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
