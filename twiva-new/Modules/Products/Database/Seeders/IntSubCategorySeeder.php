<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Influencer\Entities\Product;

class IntSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $products = Product::select('id', 'sub_category_old')->withTrashed()->get();
        foreach($products as $product) {
            $subCategoryOld = (int)$product->sub_category_old;
            if( $subCategoryOld && !is_null($subCategoryOld) ) {
                $product->sub_category = $subCategoryOld;
                $product->save();
            }
        }

        $this->command->info('Done');
    }
}
