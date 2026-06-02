<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Order\Entities\ShippingAddress;

class FixOrdersSectionCommand extends Command
{
    protected $signature = 'order-section:clean';

    protected $description = 'Command description';

    public function handle()
    {
        $notPaidOrders = Order::withTrashed()->doesntHave('orderList')->where('payment_status', 0)->get();
        $addressToDelete = $notPaidOrders->pluck('address_id')->toArray();
        ShippingAddress::whereIn('id', $addressToDelete)->forceDelete();

        $orderToDelete = Order::withTrashed()->whereHas('orderList')->where('payment_status', 0)->pluck('id')->toArray();
        Order::withTrashed()->doesntHave('orderList')->where('payment_status', 0)->forceDelete();
        OrderDetails::whereIn('order_id', $orderToDelete)->forceDelete();
        Order::withTrashed()->whereHas('orderList')->where('payment_status', 0)->delete();

        $existAddressIDS = DB::table('shipping_address')->pluck('id')->toArray();
        $guestAddress = ShippingAddress::create(
            [
                'first_name' => 'Guest',
                'last_name' => 'Guest',
                'delivery_address' => 'nairobi',
                'city_id' => '133230',
                'country_id' => '101',
                'county_id' => '4022',
                'phone_number' => '254-11111111',
                'email' => 'guest@gmail.com',
                'buyer_id' => null,
            ]
        );
        Order::whereNotIn('address_id', $existAddressIDS)->update(['address_id' => $guestAddress->id]);
        DB::table('shipping_address')->where('email', '')->update(['email' => 'guest@gmail.com']);

        $this->call('migrate',
            [
                '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_19_135103_order_section_tables.php',
                '--force' => 'true'
            ]);

    }
}
