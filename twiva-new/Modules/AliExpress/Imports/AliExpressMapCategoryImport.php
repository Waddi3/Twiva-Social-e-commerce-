<?php

namespace Modules\AliExpress\Imports;

use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\AliExpress\Entities\AliExpressCategory;

class AliExpressMapCategoryImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $categories_data = [];
        $rows->shift();
        foreach ($rows as $key => $row) {
            try {
                if ($row[5] != '-') {
                    // AliExpressCategory::query()->create(
                        $categories_data[] =
                        [
                            'category_id' => (int)$row[5],
                            'sub_category_id' => (int)$row[7],
                            'ae_category_id' => (int)$row[0],
                            'ae_sub_category_id' => (int)$row[2],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    // )
                    ;
                }
            } catch (Exception $exception) {
                logger($key, $row, $exception);
            }
        }
        // dd($categories_data);
        AliExpressCategory::query()->insert($categories_data);
        // AliExpressCategory::query()->upsert(
        //     $categories_data,
        //     ['category_id', 'sub_category_id'],
        //     [
        //         'category_id',
        //         'sub_category_id',
        //         'ae_category_id',
        //         'ae_sub_category_id',
        //         'updated_at',
        //     ]
        // );
    }
}
