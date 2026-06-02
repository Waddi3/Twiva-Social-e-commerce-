<?php

namespace App\Console\Commands\AliExpress;

use Illuminate\Console\Command;
use Modules\AliExpress\Entities\AliExpressProduct;
use Modules\AliExpress\Services\AliExpressFetchVariantsProductService;
use Modules\Influencer\Entities\Product;

class AliExpressFetchVariantsProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ali-express:fetch-variants-products {continue=1} {--product-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch list of variants product from ali express product id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start_time = now();
        $this->info("Start at: " . now()->format('Y-m-d H:i:s'));

        $product_id = $this->option('product-id');

        $ali_express_account = getAliExpressAccount();

        $product_ids = Product::select('sku', 'ali_express_product_id')->where('user_id', $ali_express_account->id)->pluck('ali_express_product_id');
        $ali_express_product_ids_query = AliExpressProduct::query();

        if ($this->argument('continue') == '1' && !$product_id) {
            $ali_express_product_ids_query->whereNotIn('product_id', $product_ids);
        }
        if ($product_id) {
            $ali_express_product_ids_query->where('product_id', $product_id);
        }

        $totalSteps = ($ali_express_product_ids_query->clone())->count();
        // dd($totalSteps);
        $bar = $this->output->createProgressBar($totalSteps);
        $bar->start();


        ($ali_express_product_ids_query->clone())
            ->orderBy('id')
            ->chunk(
                100,
                function ($ae_products) use ($bar) {
                    foreach ($ae_products as $ae_product) {
                        // dd($ae_product);
                        $this->info(" Start work with {$ae_product->product_id}");
                        $ali_express_fetch_variants_product_service = new AliExpressFetchVariantsProductService();
                        $ali_express_fetch_variants_product_service->fetchVariantsProductFromAliExpress($ae_product);
                        $bar->advance();
                    }
                }
            );
        $bar->finish();
        $this->newLine();
        $end_time = now();
        $this->info('Progress completed! in = ' . $end_time->diffInMilliseconds($start_time) . " ms");
        $this->info("End at: " . now()->format('Y-m-d H:i:s'));
    }
}
