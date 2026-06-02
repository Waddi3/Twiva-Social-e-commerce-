<?php

namespace App\Console\Commands\AliExpress;

use Illuminate\Console\Command;
use Modules\AliExpress\Entities\AliExpressProduct;
use Modules\AliExpress\Services\AliExpressService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AliExpressFetchProductIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ali-express:fetch-ids-products
                                    {--from=1 : start fetch from page}
                                    {--to= : end fetch in page}
                                    {--category-id= : category id}
                                    {--feed-name= : Feed name from ali express}
                                    {--sleep=1 : sleep time between each to request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch list of ids for ali express products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $from = $this->option('from');
        $to = $this->option('to');
        $category_id = $this->option('category-id');
        $feed_name = $this->option('feed-name') ?? "Twiva product selection";
        $sleep = (int)$this->option('sleep');

        $start_time = now();
        $this->info("Start at: " . now()->format('Y-m-d H:i:s'));
        $logger = new Logger('ali_express');
        $logger->pushHandler(new StreamHandler(storage_path('logs/ali_express_product_' . now()->format('Y_m_d') . '.log'), Logger::DEBUG));

        $ali_express_service = new AliExpressService($logger);

        $page_no = $from;

        do {
            $this->newLine();
            $this->info('Work with => ' . $page_no . " page");
            $parameters = [
                'feed_name' => $feed_name,
                'target_currency' => "KES",
                'target_language' => "en_US",
                'page_size' => "50",
                'page_no' => $page_no,
                // 'country' => request('country'),
                'category_id' => $category_id ?? null,
                // 'sort' => request('sort'), // sort by：priceAsc，priceDesc，volumeAsc、volumeDesc, discountAsc, discountDesc, DSRratingAsc，DSRratingDesc,
            ];
            $response_product = $ali_express_service->productsListByFeedName($parameters);
            [
                'page_no' => $page_no,
                'is_finished' => $is_finished,
                'totalSteps' => $totalSteps,
            ] = $this->checkResponse($response_product, $page_no, $from, $to);
            if (!isset($bar)) {
                $bar = $this->output->createProgressBar($totalSteps);
                $bar->start();
            }
            if (!isset($to)) {
                $to = $totalSteps;
            }
            $bar->advance();
            sleep($sleep);
        } while ($is_finished); // && $page_no <= $to

        $bar->finish();
        $this->newLine();
        $end_time = now();
        $this->info('Progress completed! in = ' . $end_time->diffInMilliseconds($start_time) . " ms");
        $this->info("End at: " . now()->format('Y-m-d H:i:s'));
    }

    public function checkResponse($response_product, $page_no, $from, $to)
    {
        $is_finished = (isset($response_product->aliexpress_ds_recommend_feed_get_response->result) && $response_product->aliexpress_ds_recommend_feed_get_response->result->is_finished)
            ? false
            : true;
        $page_no += 1;
        $total_products = $response_product->aliexpress_ds_recommend_feed_get_response->result->total_record_count ?? 0;
        $record_count = $response_product->aliexpress_ds_recommend_feed_get_response->result->current_record_count ?? 1;
        $current_record_count =  $record_count > 0 ? $record_count : 1;
        // if ($from && $to) {
        //     $totalSteps = ceil((($to - $from) * $current_record_count) / $current_record_count);
        // } elseif ($from && !$to) {
        //     // $totalSteps = ceil($total_products / $current_record_count) - $from;
        //     $totalSteps = ceil($total_products );
        // } else {
        //     $totalSteps = ceil($total_products );
        // }
        $totalSteps = ceil($total_products );
        $products = $response_product->aliexpress_ds_recommend_feed_get_response->result->products->traffic_product_d_t_o ?? [];
        $this->saveProductInDB($products);
        return [
            'page_no' => $page_no,
            'is_finished' => $is_finished,
            'totalSteps' => $totalSteps
        ];
    }

    public function saveProductInDB($products)
    {
        $ali_express_products = [];
        foreach ($products as $product) {
            $ali_express_products[] = [
                'product_id' => $product->product_id,
                'first_level_category_id' => $product->first_level_category_id,
                'second_level_category_id' => $product->second_level_category_id,
                'title' => $product->product_title,
                'product_main_image_url' => $product->product_main_image_url,
                'product_small_image_url' => json_encode($product->product_small_image_urls->productSmallImageUrl),
                'product_info' => json_encode($product),
            ];
        }
        $this->info("Import " . count($ali_express_products) . " products");
        AliExpressProduct::query()->upsert(
            $ali_express_products,
            ['product_id'],
            ['updated_at']
        );
    }

    // public function checkHasProducts($response, $page_index)
    // {
    //     if (isset($response->aliexpress_ds_text_search_response->data->products->selection_search_product)) {
    //         $this->saveProductIds($response);
    //         return $page_index + 1;
    //     }
    //     return false;
    // }

    // public function oldFetchProductBySearch()
    // {
    //     $letters = range('a', 'z');
    //     foreach ($letters as $firstLetter) {
    //         foreach ($letters as $secondLetter) {
    //             $combinations[] = $firstLetter . $secondLetter;
    //         }
    //     }


    //     $var_loop = $count == '1' ? $letters : $combinations;
    //     $totalSteps = count($var_loop);
    //     $bar = $this->output->createProgressBar($totalSteps);
    //     $bar->start();


    //     foreach ($var_loop as $letter) {
    //         $this->info(" Start work with {$letter}");
    //         $page_index = 1;
    //         do {
    //             $parameters = [
    //                 'keyWord' => $letter,
    //                 'local' => 'en',
    //                 'countryCode' => 'KE',
    //                 'currency' => 'KES',
    //                 'categoryId' => null,
    //                 'sortBy' => null,
    //                 'pageSize' => 100,
    //                 'pageIndex' => $page_index,
    //             ];
    //             $response = (new AliExpressFetchProductService($logger))->search($parameters);
    //             sleep(2);
    //             $page_index = $this->checkHasProducts($response, $page_index);
    //             $this->info(" Get the {$page_index} page");
    //         } while ($page_index);
    //         $bar->advance();
    //     }
    // }

    // public function saveProductIds($response)
    // {
    //     $products = $response->aliexpress_ds_text_search_response->data->products->selection_search_product;
    //     $ali_express_ids_products = [];
    //     foreach ($products as $product) {
    //         // dd(Str::between($product->itemUrl, 'item/', '.html'));
    //         $ali_express_ids_products[] = [
    //             'product_id' => Str::between($product->itemUrl, 'item/', '.html'),
    //             'title' => $product->title,
    //             'category_ids' => $product->cateId,
    //             'score' => $product->score,
    //             'item_url' => $product->itemUrl,
    //             'item_main_pic' => $product->itemMainPic,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ];
    //     }
    //     AliExpressIdsProduct::query()->upsert(
    //         $ali_express_ids_products,
    //         ['product_id'],
    //         ['updated_at']
    //     );
    // }
}
