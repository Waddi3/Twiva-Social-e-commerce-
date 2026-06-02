<?php

namespace Modules\Order\Database\Seeders;

use Modules\Home\Entities\City;
use Modules\Order\Entities\ShippingRate;
use Modules\Order\Imports\ShippingRatesImport;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Modules\User\Entities\User;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $partner_id = $this->command->ask('Enter partner user id');

        $partner = User::where('type', User::PartnerType)
            ->where('id', $partner_id)
            ->first();

        if( !$partner ) {
            $this->command->error('Partner not found');
            return;
        }

        //$filePath = storage_path('app/public/SG Rate Card 2023 - New.xlsx');
        $filePath = storage_path('app/public/SG-Rate-Card-2024.xlsx');
        $shippingRatesImport = new ShippingRatesImport();
        Excel::import($shippingRatesImport, $filePath);
        if( count($shippingRatesImport->data) ) {
            foreach($shippingRatesImport->data as $rateItem) {
                $sourceCity = City::where('name', $rateItem['source_city_name'])
                    ->select('id')
                    ->first();
                $destinationCity = City::where('name', $rateItem['destination_city_name'])
                    ->select('id')
                    ->first();

                if( $sourceCity && $destinationCity ) {
                    ShippingRate::create([
                        'partner_id' => $partner->id,
                        'source_city_id' => $sourceCity->id,
                        'destination_city_id' => $destinationCity->id,
                        'rate' => $rateItem['rate']
                    ]);
                }
            }

            $this->command->info('Shipping rates have imported successfully');
        }
        else {
            $this->command->error('There is no data to import');
        }


    }
}
