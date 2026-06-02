<?php

namespace Modules\AliExpress\Http\Controllers;

use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\AliExpress\Entities\AliExpressCategory;
use Modules\AliExpress\Imports\AliExpressMapCategoryImport;
use Modules\AliExpress\Services\AliExpressFetchProductService;
use Modules\AliExpress\Services\AliExpressService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AliExpressController extends Controller
{

    public function __construct(public AliExpressService $ali_express_service) {}

    public function usersAuthorize()
    {
        return $this->ali_express_service->usersAuthorize();
    }

    public function callbackUrl()
    {
        $url = route('ali.express.generate.token', request()->all());
        return $this->ali_express_service->callbackUrl($url);
    }

    public function generateToken()
    {
        return $this->ali_express_service->generateToken();
    }

    public function fetchVariantProduct()
    {
        $parameters = [
            'ship_to_country' => 'KE', // required
            'product_id' => request('product_id'), // required
            'target_currency' => 'KES', // Optional
            'target_language' => 'en', // Optional
        ];
        $response = $this->ali_express_service->fetchVariantProduct($parameters);
        return $response;
    }

    public function getCategoriesAsCSV()
    {
        $parameters = [
            'categoryId' => request('category_id'),
            'language' => request('language'),
        ];
        $categoriesList = $this->ali_express_service->getCategoriesList($parameters);
        $categoriesList = $categoriesList->aliexpress_ds_category_get_response->resp_result->result;
        $categories = collect($categoriesList->categories->category);
        $parentCategories = $categories->whereNull('parent_category_id')->values();
        $childCategories = $categories->whereNotNull('parent_category_id')->values()->groupBy('parent_category_id')->values();
        $newParentCategories = [];
        foreach ($childCategories as $childCategory) {
            $parentCategory = $parentCategories->where('category_id', $childCategory[0]->parent_category_id)->first();
            if (!$parentCategory) {
                dd($childCategory);
            }
            $parentCategory->id = $parentCategory->category_id;
            $parentCategory->subcategories = $childCategory;
            $newParentCategories[] = $parentCategory;
        }
        $data = "";
        foreach ($newParentCategories as $category) {
            if ($category->subcategories->isEmpty()) {
                $data .= "___" . $category->category_id . "," . str_replace(',', '', $category->category_name) . "\n";
            }
        }
        foreach ($newParentCategories as $category) {
            if ($category->subcategories->isNotEmpty()) {
                foreach ($category->subcategories as $subcategory) {
                    if (isset($subcategory->category_name)) {
                        $data .= "___" . $category->category_id . "," . str_replace(',', '', $category->category_name)
                            . "," . $subcategory->category_id . "," . str_replace(',', '', $subcategory->category_name)
                            . "\n";
                    }
                }
            }
        }
        return response($data)->header('content-type', 'text/text');
    }

    public function getCategoriesList()
    {
        $parameters = [
            'categoryId' => request('category_id'),
            'language' => request('language'),
        ];
        return $this->ali_express_service->getCategoriesList($parameters);
    }

    public function createOrder()
    {
        $parameters =  [
            'param_place_order_request4_open_api_d_t_o' => json_encode([
                "product_items" => [
                    [
                        "sku_attr" => request('sku_attr'),
                        "product_count" => "1",
                        "product_id" => request('product_id'),
                        "order_memo" => "Please put it in a gift box.",
                    ],
                ],
                "logistics_address" => config('twiva.ali-express.logistics_address'),
            ])
        ];
        // dd($parameters);
        return $this->ali_express_service->createOrder($parameters);
    }

    public function placeOrder()
    {
        $parameters =  [
            'param_place_order_request4_open_api_d_t_o' => json_encode([
                "product_items" => [
                    [
                        "sku_attr" => request('sku_attr'),
                        "product_count" => "1",
                        "product_id" => request('product_id'),
                        "order_memo" => "Please put it in a gift box."
                    ],
                ],
                "logistics_address" => config('twiva.ali-express.logistics_address'),
            ])
        ];
        // dd($parameters);
        return $this->ali_express_service->placeOrder($parameters);
    }

    public function search()
    {

        $logger = new Logger('ali_express');
        $logger->pushHandler(new StreamHandler(storage_path('logs/ali_express.log'), Logger::DEBUG));

        $parameters = [
            'keyWord' => 'i',
            'local' => 'en',
            'countryCode' => 'KE',
            'currency' => 'KES',
            'categoryId' => null,
            'sortBy' => null,
            'pageSize' => 100,
            'pageIndex' => 6,
        ];
        return (new AliExpressFetchProductService($logger))->search($parameters);
    }

    public function freightQuery()
    {
        $parameters = [
            'queryDeliveryReq' => json_encode([
                "quantity" => request('quantity'),
                "shipToCountry" => request('shipToCountry'),
                "productId" => request('productId'),
                "provinceCode" => request('provinceCode'),
                "cityCode" => request('cityCode'),
                "language" => request('language'),
                "locale" => request('locale'),
                "selectedSkuId" => request('selectedSkuId'),
                "currency" => request('currency'),

            ]),
        ];
        // dd($parameters);
        $response = $this->ali_express_service->freightQuery($parameters);
        return $response;
    }

    public function addressGet()
    {
        $parameters = [
            // 'language' => '',
            'countryCode' => 'KE',
            // 'isMultiLanguage' => '',
        ];
        return $this->ali_express_service->addressGet($parameters);
    }

    public function logisticsBuyerFreightCalculate() // App does not have permission to access this api
    {
        $parameters = [
            'param_aeop_freight_calculate_for_buyer_d_t_o' => json_encode([
                'sku_id' => request('sku_id'),
                'city_code' => request('city_code'),
                'country_code' => request('country_code'),
                'product_id' => request('product_id'),
                'product_num' => request('product_num'),
                'province_code' => request('province_code'),
                'send_goods_country_code' => request('send_goods_country_code'),
                'price' => request('price'),
                'price_currency' => request('price_currency'),
            ]),
        ];
        return $this->ali_express_service->logisticsBuyerFreightCalculate($parameters);
    }

    public function orderTracking()
    {
        $parameters = [
            'ae_order_id' => request('ae_order_id'),
            'language' => 'en_US',
        ];
        return $this->ali_express_service->orderTracking($parameters);
    }

    public function fetchFeedName()
    {
        $parameters = [];
        return $this->ali_express_service->fetchFeedName($parameters);
    }

    public function productsListByFeedName()
    {
        $parameters = [
            'feed_name' => request('feed_name'),
            'target_currency' => request('target_currency'),
            'target_language' => request('target_language'),
            'page_size' => request('page_size'),
            'page_no' => request('page_no'),
            'country' => request('country'),
            'category_id' => request('category_id'),
            'sort' => request('sort'), // sort by：priceAsc，priceDesc，volumeAsc、volumeDesc, discountAsc, discountDesc, DSRratingAsc，DSRratingDesc,
        ];
        // dd($parameters);
        return $this->ali_express_service->productsListByFeedName($parameters);
    }

    public function mapCategories()
    {
        $import = new AliExpressMapCategoryImport();
        $file = 'excels/categories-updated.xlsx';
        $temp = Excel::import($import, $file);
        dd(AliExpressCategory::query()->get());
    }
}
