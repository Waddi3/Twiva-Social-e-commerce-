<?php

namespace Modules\Order\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Order\Entities\ShippingRate;

class ShippingRatesImport implements ToCollection
{
    public array $data;
    public function collection(Collection $rows)
    {
        for($i = 1; $i <= 37; $i++) {
            for($j = 2; $j <= 38; $j++) {
                $this->data[] = [
                    'source_city_name' => trim($rows[$i][0]),
                    'destination_city_name' => trim($rows[$j-1][0]),
                    'rate' => (double)trim($rows[$i][$j])
                ];
            }
        }
    }
}
