<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\AliExpress\Entities\AliExpressVariant;
use Modules\AliExpress\Http\Controllers\AliExpressController;
use Modules\AliExpress\Services\AliExpressClasses\IopClient;
use Modules\AliExpress\Services\AliExpressClasses\IopRequest;
use Modules\AliExpress\Services\AliExpressService;
use Modules\Influencer\Entities\Categories;
use Modules\Order\Entities\Order;

Route::prefix('aliexpress')->group(function () {

    Route::get('/users-authorize', [AliExpressController::class, 'usersAuthorize'])->name('ali.express.user.authorize');

    Route::get('/callback-url', [AliExpressController::class, 'callbackUrl']);

    Route::get('/generate-token', [AliExpressController::class, 'generateToken'])->name('ali.express.generate.token');

    Route::get('/fetch-variant-product', [AliExpressController::class, 'fetchVariantProduct']);

    Route::get('/freight-query', [AliExpressController::class, 'freightQuery']);

    Route::get('/address-get', [AliExpressController::class, 'addressGet']);

    Route::get('/logistics-buyer-freight-calculate', [AliExpressController::class, 'logisticsBuyerFreightCalculate']);

    Route::get('/get-categories-list', [AliExpressController::class, 'getCategoriesList']);

    Route::get('/create-order', [AliExpressController::class, 'createOrder']);

    Route::get('/place-order', [AliExpressController::class, 'placeOrder']);

    Route::get('/order-tracking', [AliExpressController::class, 'orderTracking']);

    Route::get('/products-search', [AliExpressController::class, 'search']);

    Route::get('/fetch-feed-name', [AliExpressController::class, 'fetchFeedName']);

    Route::get('/products-list-by-feed-name', [AliExpressController::class, 'productsListByFeedName']);

    Route::get('/map-categories', [AliExpressController::class, 'mapCategories']);

    Route::get('/get-product', function () {

        $method_request = request()->query('method') ?? 'POST';

        $url = "https://api-sg.aliexpress.com/sync";
        $appkey = "509712";
        $appSecret = "KaJoFgc7CsJTEgAgnevWq2EZgW6suQ8Q";


        $c = new IopClient($url, $appkey, $appSecret);
        $request = new IopRequest('aliexpress.solution.product.info.get');

        $request->addApiParam('product_id', '1307422965');


        $user = getAliExpressAccount();
        $accessToken = $user->aliExpressTokens?->access_token;

        if (is_null($accessToken)) {
            return throw new Exception('Please reconnect to ali express and get new access token.');
        }

        if (request()->query('ean_code')) {
            $request->addApiParam('ean_code', request()->query('ean_code'));
        }
        if (request()->query('aeop_a_e_product_list_query')) {
            // '{\"gmt_modified_start\":\"2012-01-01 12:13:14\",\"gmt_modified_end\":\"2012-01-01 12:13:14\",\"off_line_time\":\"7\",\"gmt_create_start\":\"2012-01-01 12:13:14\",\"subject\":\"knew odd\",\"have_national_quote\":\"n\",\"ws_display\":\"expire_offline\",\"product_status_type\":\"onSelling\",\"owner_member_id\":\"aliqatest01\",\"gmt_create_end\":\"2012-01-01 12:13:14\",\"group_id\":\"1234\",\"product_id\":\"123\",\"excepted_product_ids\":[\"[32962333569,32813963253]\",\"[32962333569,32813963253]\"],\"sku_code\":\"123ABC\",\"current_page\":\"2\",\"page_size\":\"30\"}'
            $request->addApiParam('aeop_a_e_product_list_query', request()->query('aeop_a_e_product_list_query'));
        }

        $response = json_decode($c->execute($request, $accessToken));

        dd($response);
    });

    Route::get('test-001', function () {
        $variant_query = AliExpressVariant::query()
            ->where('ali_express_product_id', 10000003858710)
            ->whereRaw("JSON_SEARCH(ae_item_sku_info_d_t_o, 'one', ?, NULL, '$.ae_sku_property_dtos.ae_sku_property_d_t_o[*].sku_property_name') IS NOT NULL", ['Color'])
            ->where(function ($where) {
                return $where->whereRaw("JSON_SEARCH(ae_item_sku_info_d_t_o, 'one', LOWER(?), NULL, '$.ae_sku_property_dtos.ae_sku_property_d_t_o[*].sku_property_value') IS NOT NULL", 'Black')
                    ->orWhereRaw("JSON_SEARCH(ae_item_sku_info_d_t_o, 'one', ?, NULL, '$.ae_sku_property_dtos.ae_sku_property_d_t_o[*].sku_property_value') IS NOT NULL", ['black']);
            });
        dd(
            $variant_query->first(),
            $variant_query->toSql()
        );
    });
});
Route::group(['prefix' => 'dev-routes'], function () {
    Route::get('twiva-categories-overview', function () {

        $categories = Categories::select(['id', 'name'])->where('id', '>', 8)
            ->with(['subcategories' => function ($subcategoriesQuery) {
                $subcategoriesQuery->select(['id', 'name', 'category_id']);
            }])->get();
        $data = "";

        foreach ($categories as $category) {
            if ($category->subcategories->isEmpty()) {
                $data .= "___" . $category->id . "," . str_replace(',', '', $category->name) . "\n";
            }
        }
        foreach ($categories as $category) {
            if ($category->subcategories->isNotEmpty()) {
                foreach ($category->subcategories as $subcategory) {
                    $data .= "___" . $category->id . "," . str_replace(',', '', $category->name) . "," . $subcategory->id . "," . str_replace(',', '', $subcategory->name) . "\n";
                }
            }
        }

        return response($data)->header('Content-Type', 'text/text');
    });
    Route::get('aliexpress-categories-overview', function () {

        $categoryIds = [
            2,
            3,
            6,
            7,
            13,
            15,
            18,
            21,
            26,
            30,
            34,
            36,
            39,
            44,
            66,
            320,
            322,
            502,
            509,
            127698009,
            1402,
            1501,
            1503,
            1511,
            1524,
            200000345,
            200000343,
            200000297
        ];
        $categoryIds = [200000142];
        $data = "";
        $aliExpressServices = new AliExpressService();
        foreach ($categoryIds as $key => $categoryId) {
            $temp = $aliExpressServices->getCategoriesList($categoryId);
        }
        return response($data)->header('Content-Type', 'text/text');
    });
});
