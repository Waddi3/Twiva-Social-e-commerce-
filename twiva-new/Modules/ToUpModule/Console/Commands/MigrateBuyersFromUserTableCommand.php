<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Buyer\Entities\Buyer;
use Modules\Influencer\Entities\BuyerDetails;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\ShippingAddress;
use Modules\User\Entities\User;

class MigrateBuyersFromUserTableCommand extends Command
{
    protected $signature = 'fix:buyers';

    protected $description = 'Command description';

    public function handle()
    {
        $buyerIdsFromOrders = Order::withTrashed()->get()->pluck('buyer_id')->unique()->values()->toArray();
        $buyerIdsFromShippingAddress = ShippingAddress::all()->pluck('buyer_id')->unique()->values()->toArray();
        $oldBuyers = User::whereIn('id', $buyerIdsFromOrders)
            ->orWhereIn('id', $buyerIdsFromShippingAddress)->withTrashed()->get();

        $progressBar = $this->output->createProgressBar($oldBuyers->count());
        $progressBar->start();
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_11_213609_make_buyer_id_nullable_in_all_tables.php'
        ]);

        foreach ($oldBuyers as $oldBuyer)
        {
            $isGuest = Str::contains($oldBuyer->email, "guestaccount");
            $newBuyerId = null;
            if (!$isGuest)
            {
                $oldBuyerDetail = BuyerDetails::where('buyer_id', $oldBuyer->id)->first();
                $data['name'] = $oldBuyerDetail ? $oldBuyerDetail->name : ($oldBuyer->name ?? $oldBuyer->email);
                $data['email'] = $oldBuyer->email;
                $data['password'] = $oldBuyer->password ?? bcrypt($oldBuyer->email);
                $data['phone_code'] = $oldBuyer->phone_code;
                $data['phone_number'] = $oldBuyer->phone_number;
                $newBuyer = Buyer::create($data);
                $newBuyerId = $newBuyer->id;
                if ($oldBuyer->type != User::BusinessType && $oldBuyer->type != User::InfluencerType)
                {
                    $this->error("forceDelete  {$oldBuyer->id} {$oldBuyer->type}");

                    $oldBuyer->forceDelete();
                }
            }
            else
            {
                $this->error("forceDelete guest {$oldBuyer->id} {$oldBuyer->type}");
                $oldBuyer->forceDelete();
            }

            ShippingAddress::where('buyer_id', $oldBuyer->id)->update(['buyer_id' => $newBuyerId]);
            Order::withTrashed()->where('buyer_id', $oldBuyer->id)->update(['buyer_id' => $newBuyerId]);
        }
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_11_213609_change_buyer_id_foreign_in_all_tables.php'
        ]);
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_16_225953_edit_shipping_address_table.php'
        ]);


    }
}
