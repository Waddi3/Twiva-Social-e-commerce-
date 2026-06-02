<?php

namespace Modules\AliExpress\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use Modules\Influencer\Entities\Product;

class AliExpressUpdateProductInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ali_express_product_id = Product::select('id', 'sku', 'is_ali_express', 'ali_express_product_id')
            ->where('id', $this->product_id)
            ->where('is_ali_express', 1)
            ->first();

        if ($ali_express_product_id) {
            Artisan::call('ali-express:fetch-variants-products', [
                '--product-id' => $ali_express_product_id->ali_express_product_id
            ]);
        }
    }
}
