<?php

namespace Modules\Notification\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Buyer\Entities\Buyer;
use Modules\Notification\Entities\Notification;
use Modules\Order\Entities\ShippingAddress;
use Modules\User\Entities\User;


class NotificationFixNotifiableTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notifications_morph_map = [
            User::class => 'users',
            ShippingAddress::class => 'shipping_address',
            Buyer::class => 'buyers',
            'users' => 'users',
            'buyers' => 'buyers',
            'shipping_address' => 'shipping_address',
        ];
        Model::unguard();

        // php artisan module:seed Notification --class=NotificationFixNotifiableTypeTableSeeder
        Notification::chunk(100, function ($notifications) use ($notifications_morph_map) {
            foreach ($notifications as $key => $notification) {
                $notification->update([
                    'notifiable_type' => $notifications_morph_map[$notification->notifiable_type],
                ]);
            }
            echo ("sub done \n");
        });
        echo ("done\n");
    }
}
