<?php

namespace Modules\AliExpress\Services;

use Exception;
use Illuminate\Support\Str;
use Modules\AliExpress\Entities\AliExpressCategory;
use Modules\AliExpress\Entities\AliExpressVariant;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\Colors;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\ProductImage;
use Modules\Influencer\Entities\SubCategories;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AliExpressFetchVariantsProductService
{

    protected $logger;
    protected $ali_express_service;

    public function __construct()
    {
        $this->logger = new Logger('ali_express');
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/ali_express_variants_' . now()->format('Y_m_d') . '.log'), Logger::DEBUG));
        $this->ali_express_service = new AliExpressService($this->logger);
    }


    public function fetchVariantsProductFromAliExpress($ae_product)
    {
        $ali_express_account = getAliExpressAccount();
        $parameters = [
            'ship_to_country' => 'KE', // required
            'product_id' => $ae_product->product_id, // required
            'target_currency' => 'KES', // Optional
            'target_language' => 'en', // Optional
        ];
        $response = $this->ali_express_service->fetchVariantProduct($parameters);
        $product = $this->createProductForAliExpressAccount($response, $ali_express_account, $ae_product);
    }

    public function createProductForAliExpressAccount($response, $ali_express_account, $ae_product)
    {
        if (isset($response->aliexpress_ds_product_get_response->result)) {
            $response_product_details = $response->aliexpress_ds_product_get_response->result->ae_item_base_info_dto;

            if ($response_product_details->product_status_type == 'onSelling') {
                $status = 1;
            } else {
                return;
            }

            try {
                $response_product_properties = $response->aliexpress_ds_product_get_response->result->ae_item_properties->ae_item_property;

                $category = $this->getSubCategoryId($response_product_details);
                $brand = collect($response_product_properties)->where('attr_name_id', 2)->first()?->attr_value;
                $brand = $brand != 'NoEnName_Null' ? $brand : null;
                // if (count($category)) {
                $product_data = [
                    'user_id' => $ali_express_account->id,
                    'category' => $category['category'],
                    'sub_category' => $category['sub_category'],
                    'name' => Str::limit($response_product_details->subject, 255, ''),
                    'sku' => $response_product_details->product_id,
                    'brand' => $brand,
                    'rating' => $response_product_details->avg_evaluation_rating,
                    'status' => $status,
                    'ali_express_sales_count' => $response_product_details->sales_count ?? "0",
                    'description' => json_decode($response_product_details->mobile_detail ?? "")->moduleList[0]->data->content ?? '-',
                    'color' => $this->fetchColorIdsVariants($response),
                    // 'size' => '',
                    // 'stock' => '',
                    // 'price' => '',
                    // 'retail_price' => '',
                    // 'main_image' => '',
                    // 'discount' => '',
                    'ali_express_product_id' => $response_product_details->product_id,
                    'is_ali_express' => true,
                ];

                $product = Product::updateOrCreate(['ali_express_product_id' => $product_data['ali_express_product_id']], $product_data);
                $prices = $this->createVariantForProduct($response, $product, $ae_product);
                // update price and discount from product

                // $this->logger->info("prices for product " . $response_product_details->product_id . " => ", ['price' => $prices]);
                if ($prices) {
                    $product->update($prices);
                    $this->attachImageProduct($response, $ali_express_account, $ae_product, $product);
                    return $product;
                } else {
                    $product->forceDelete();
                    return null;
                }
                // }
            } catch (Exception $exception) {
                if (isset($product)) {
                    $product->forceDelete();
                }
                $this->logger->info("work with {$ae_product->product_id}", ['exception-getMessage' => $exception->getMessage(), 'exception' => $exception]);
            }
        }
    }

    public function fetchColorIdsVariants($response)
    {
        $ae_variants = collect($response->aliexpress_ds_product_get_response->result->ae_item_sku_info_dtos->ae_item_sku_info_d_t_o);
        $has_stock_response_product_variants = $this->filterVariants($ae_variants);
        // dd($has_stock_response_product_variants, $response_product_variants);
        $colors = $has_stock_response_product_variants->map(function ($item) {
            if (isset($item->ae_sku_property_dtos)) {
                $properties = collect($item->ae_sku_property_dtos->ae_sku_property_d_t_o);
                $color_property = $properties->where('sku_property_id', 14) // 14 => Color  |  200001034 => Metal Color
                    ->first();
                return $color_property->sku_property_value ?? null;
            }
            return null;
        })->filter();

        // TODO::H add new colors to twiva table
        $color_ids = Colors::whereIn('name', $colors)->pluck('id')->implode(',');
        if ($colors->count() && !$color_ids) {
            $color = Colors::query()->create([
                'name' => $colors->first(),
                'hexaval' => '000000',
            ]);
            if ($color) {
                $color_ids = $color->id;
            }
        }
        return Str::length($color_ids) ? $color_ids : null;
    }

    public function filterVariants($ae_variants)
    {
        $variants_has_stock = $ae_variants->where('ipm_sku_stock', '>', 0);
        $variants = $variants_has_stock->filter(function ($item) {
            if (isset($item->ae_sku_property_dtos)) {
                $properties = collect($item->ae_sku_property_dtos->ae_sku_property_d_t_o);
                $color_property = $properties->where('sku_property_id', 14) // 14 => Color
                    ->first();
                return $color_property->sku_property_value ?? null;
            }
            return true;
        });

        return $variants->take(1);
    }
    public function createVariantForProduct($response, $product, $ae_product)
    {
        $ae_variants = collect($response->aliexpress_ds_product_get_response->result->ae_item_sku_info_dtos->ae_item_sku_info_d_t_o);
        $has_stock_response_product_variants = $this->filterVariants($ae_variants);
        $ae_variants_data = [];
        $price = [];
        $retail_price = [];
        $discount = [];
        $ipm_sku_stocks = [];
        $shipping_fee_infos = [];

        if ($has_stock_response_product_variants->count()) {
            AliExpressVariant::query()->where('ali_express_product_id', $ae_product->product_id)->delete();
        }
        foreach ($has_stock_response_product_variants as $ae_variant) {
            $price[] = $ae_variant->sku_price;
            $retail_price[] = $ae_variant->sku_price;
            $discount[] = $ae_variant->offer_sale_price;
            $ipm_sku_stocks[] = $ae_variant->ipm_sku_stock;
            $shipping_fee_info = $this->UpdatePriceAddShippingFee($ae_product, $ae_variant);
            $shipping_fee_infos[] = $shipping_fee_info;
            $ae_variants_data[] = array_merge([
                'sku_id' => $ae_variant->sku_id,
                'sku_attr' => $ae_variant->sku_attr,
                'ae_item_sku_info_d_t_o' => json_encode($ae_variant),
                'product_id' => $product->id,
                'ali_express_product_id' => $ae_product->product_id,
                'sku_price' => $ae_variant->sku_price,
                'offer_sale_price' => $ae_variant->offer_sale_price,
                'offer_bulk_sale_price' => $ae_variant->offer_bulk_sale_price,
                'created_at' => now(),
                'updated_at' => now(),
            ], $shipping_fee_info);
        }

        $shipping_fee_infos = collect($shipping_fee_infos);
        $min_shipping_fee_infos = $shipping_fee_infos->pluck('min_shipping_fee_cent')->avg();
        $max_shipping_fee_infos = $shipping_fee_infos->pluck('max_shipping_fee_cent')->avg();

        $shipping_fee_price = $min_shipping_fee_infos; // ($min_shipping_fee_infos + $max_shipping_fee_infos) / 2;

        $price = collect($price)->first();
        // $price_index = collect($price)->search($price);
        $discount = collect($discount)->first();

        $discount_amount = $price - $discount;
        $retail_price = (($price  * $this->getProfitTwivaFromAliExpressProduct()) + ($shipping_fee_price ?? 0))
            + ($discount_amount * $this->getProfitTwivaFromDiscountAliExpressProduct());


        // dd(count($ae_variants_data), $ae_variants_data);
        if (count($ae_variants_data)) {
            AliExpressVariant::query()->upsert(
                $ae_variants_data,
                ['sku_id', 'sku_attr', 'ali_express_product_id'],
                [
                    'ae_item_sku_info_d_t_o',
                    'sku_id',
                    'sku_attr',
                    'sku_price',
                    'offer_sale_price',
                    'offer_bulk_sale_price',
                    'min_shipping_fee_cent',
                    'max_shipping_fee_cent',
                    'min_delivery_day',
                    'max_delivery_day',
                    'updated_at'
                ]
            );

            return [
                'price' => $price + ($shipping_fee_price ?? 0),
                'retail_price' => $retail_price,
                'discount' => $retail_price - $discount_amount,
                'stock' => collect($ipm_sku_stocks)->sum(),
                'ali_express_price' => $price,
                'ali_express_discount' => $discount,
            ];
        }
        return false;
    }
    public function UpdatePriceAddShippingFee($ae_product, $ae_variant)
    {
        $parameters = [
            'queryDeliveryReq' => json_encode([
                "quantity" => 1,
                "shipToCountry" => 'KE',
                "productId" => $ae_product->product_id,
                // "provinceCode" => request('provinceCode'),
                // "cityCode" => request('cityCode'),
                "language" => 'en_US',
                "locale" => 'en_US',
                "selectedSkuId" => $ae_variant->sku_id,
                "currency" => 'KES',
            ]),
        ];
        $freight_query = $this->ali_express_service->freightQuery($parameters);
        // dd($freight_query);
        if ($freight_query->aliexpress_ds_freight_query_response->result->code == 200) {
            $warehouses = collect($freight_query->aliexpress_ds_freight_query_response->result->delivery_options->delivery_option_d_t_o);
            // $shipping_methods = ['CAINIAO_STANDARD', 'CAINIAO_PREMIUM'];
            // // $shipping_companies = ['AliExpress standard shipping', 'AliExpress Premium Shipping'];
            // $warehouses_selected = $warehouses->filter(function ($item) use ($shipping_methods) {
            //     //->where('company', 'AliExpress standard shipping')->orWhere('code', 'CAINIAO_STANDARD');
            //     return  in_array($item->code, $shipping_methods) || $item->free_shipping == true;
            // });
            // if ($warehouses_selected->isEmpty()) {
            $delivery_option = $this->findMinItem($warehouses);
            //     // dd($delivery_option, $warehouses);
            //     // } elseif ($warehouses_selected->count() < $warehouses->count()) {
            //     //     $delivery_option = $warehouses_selected->first();
            // } else {
            // $delivery_option = $this->findMinItem($warehouses_selected);
            // }
        }
        return [
            'min_shipping_fee_cent' => $delivery_option->shipping_fee_cent ?? null,
            'max_shipping_fee_cent' => $delivery_option->shipping_fee_cent ?? null,
            'min_delivery_day' => $delivery_option->min_delivery_days ?? null,
            'max_delivery_day' => $delivery_option->max_delivery_days ?? null,
            'delivery_option' => json_encode($delivery_option),
        ];
    }

    // public function getCategoryId()
    // {
    //     return 409; // TODO::H after mapping categories have to update
    // }

    public function getSubCategoryId($response_product_details)
    {
        $map_category = AliExpressCategory::query()
            ->where('ae_sub_category_id', $response_product_details->category_id)
            ->first();
        if (is_null($map_category)) {
            $parameters = [
                'categoryId' => $response_product_details->category_id,
                'language' => 'en_US',
            ];
            $response_category = $this->ali_express_service->getCategoriesList($parameters);
            $new_category_id = $response_category->aliexpress_ds_category_get_response->resp_result->result->categories->category[0]->category_id ?? null;
            if ($new_category_id) {
                $map_category = AliExpressCategory::query()
                    ->where('ae_sub_category_id', $new_category_id)
                    ->first();
            }
        }

        if ($map_category) {
            $category = [
                'category' => $map_category->category_id,
                'sub_category' => $map_category->sub_category_id,
            ];
        } else {
            $category = [
                'category' => $this->getCategoryOther(),
                'sub_category' => $this->getSubCategoryOther(),
            ];
        }
        return $category;
    }

    public function attachImageProduct($response, $ali_express_account, $ae_product, $product)
    {
        $images = collect($response->aliexpress_ds_product_get_response->result->ae_multimedia_info_dto->image_urls ?? []);
        $images = collect(explode(';', $images->first()))->map(function ($item, $key) use ($product) {
            return [
                'is_cover_pic' => (int)($key == 0),
                'product_id' => $product->id,
                'image' => $item,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        ProductImage::query()->where('product_id', $product->id)->delete();

        return ProductImage::query()->insert($images);
    }

    public function findMinItem($warehouses_selected)
    {
        $min_shipping_fee_cent_value = $warehouses_selected->min('shipping_fee_cent');
        $min_shipping_fee_cent_item = $warehouses_selected->filter(function ($item) use ($min_shipping_fee_cent_value) {
            if (isset($item->shipping_fee_cent)) {
                return $item->shipping_fee_cent == $min_shipping_fee_cent_value;
            }
            return true;
        });
        return $min_shipping_fee_cent_item->first();
    }

    public function getProfitTwivaFromAliExpressProduct()
    {
        $profit_twiva = config('twiva.ali-express.percentage_profit_retail_price');
        return 1 + ($profit_twiva / 100);
    }

    public function getProfitTwivaFromDiscountAliExpressProduct()
    {
        $profit_twiva = config('twiva.ali-express.percentage_profit_discount_price');
        return ($profit_twiva / 100);
    }

    public function getCategoryOther()
    {
        return cache()->remember('category-others', now()->addMinutes(60), function () {
            return Category::query()->firstOrCreate(
                ['name' => config('twiva.ali-express.default_category_name')],
            )->id;
            // where()->first();
        });
    }

    public function getSubCategoryOther()
    {
        return cache()->remember('sub-category-others', now()->addMinutes(60), function () {
            return SubCategories::query()->firstOrCreate(
                [
                    'name' => config('twiva.ali-express.default_sub_category_name'),
                    'category_id' => $this->getCategoryOther()
                ],
            )->id;
        });
    }
}
