<?php

namespace Modules\Products\Console\Commands;

use Illuminate\Console\Command;
use Modules\Influencer\Entities\Product;

class SetHasProductFeesToTrueForOldProductsCommand extends Command
{
    protected $signature = 'product:set-has-product-fees-to-true-for-old-products';

    protected $description = 'Command description';

    public function handle(): void
    {
        Product::withTrashed()->update(['has_shipping_fees' => true]);
    }
}
