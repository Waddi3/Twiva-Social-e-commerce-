<?php

namespace Modules\AliExpress\Services;

use Modules\AliExpress\Entities\AliExpressOrder;
use Modules\AliExpress\Entities\AliExpressVariant;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AliExpressOrderService
{

    protected $logger;
    protected $ali_express_service;
    protected $order;
    protected $order_items;
    protected $parameters;
    protected $response;
    protected $ali_express_order;

    public function __construct($order)
    {
        logger("construct ali express order service");
        $this->logger = new Logger('ali_express');
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/ali_express_orders_' . now()->format('Y_m_d') . '.log'), Logger::DEBUG));
        $this->ali_express_service = new AliExpressService($this->logger);
        $this->order = $order;
        $this->getOrderList();
        $this->prepareParameters();
        $this->callPlaceOrderApi();
        $this->saveResponse();
        $this->updateOrderStatus();
        // dd($this->response);
        // TODO::H save response to tracking order in ali express service
    }

    public function getOrderList()
    {

        $this->order_items = $this->order->orderList;
    }

    public function prepareParameters()
    {
        $product_items = $this->prepareProductItems();
        $this->parameters =  [
            'ds_extend_request' => json_encode([
                'payment' => [
                    'pay_currency' => 'KES',
                    'try_to_pay' => true
                ],
            ]),
            'param_place_order_request4_open_api_d_t_o' => json_encode([
                "product_items" => $product_items,
                "logistics_address" => config('twiva.ali-express.logistics_address'),
            ])
        ];
    }

    public function prepareProductItems()
    {
        /**
         * example of product_items parameters
         * [
         *      [
                        "sku_attr" => sku_attr,
                        "product_count" => "1",
                        "product_id" => product_id,
                        "order_memo" => "Please put it in a gift box."
         *      ]
         * ]
         */
        $product_items = [];
        foreach ($this->order_items as $item) {

            $product = $item->Order->shop->user->type == User::InfluencerType
                ? $item->productsInfluencer->product
                : $item->productsBusiness;

            $product_items[] = [
                "sku_attr" => $this->getSkuAttribute($item, $product),
                "product_count" => $item->quantity,
                "product_id" => $product->ali_express_product_id,
                "order_memo" => ""
            ];
        }
        return $product_items;
    }

    public function getSkuAttribute($item, $product)
    {
        if ($item->color) {
            $variant_query = AliExpressVariant::query()
                ->where('ali_express_product_id', $product->ali_express_product_id)
                ->whereRaw("JSON_SEARCH(ae_item_sku_info_d_t_o, 'one', ?, NULL, '$.ae_sku_property_dtos.ae_sku_property_d_t_o[*].sku_property_name') IS NOT NULL", ['Color'])
                ->where(function ($where) use ($item) {
                    return $where->whereRaw("JSON_SEARCH(ae_item_sku_info_d_t_o, 'one', LOWER(?), NULL, '$.ae_sku_property_dtos.ae_sku_property_d_t_o[*].sku_property_value') IS NOT NULL", [$item->colorDetailes->name])
                        ->orWhereRaw("JSON_SEARCH(ae_item_sku_info_d_t_o, 'one', ?, NULL, '$.ae_sku_property_dtos.ae_sku_property_d_t_o[*].sku_property_value') IS NOT NULL", [$item->colorDetailes->name]);
                });

            // logger('=====================================================');
            // logger('query =>', ['query-to-test' => $variant_query->toSql()]);
            // logger('=====================================================');
            $variant = $variant_query->first();
            // logger('variant info => ', ['item_color' => $item->color, 'item_colorDetailes_name' => $item->colorDetailes->name, 'product_ali_express_product_id' => $product->ali_express_product_id, 'variant' => $variant]);
            // logger('=====================================================');
            return ($variant && $variant->sku_attr != "") ? $variant->sku_attr :  null;
        }
        return null;
    }

    public function callPlaceOrderApi()
    {
        $this->response = $this->ali_express_service->createOrder($this->parameters);
    }

    public function saveResponse()
    {
        $ali_express_order_query = AliExpressOrder::query();
        $order_data = [
            'order_id' => $this->order->id,
            'response' => $this->response,
            'parameters' => $this->parameters,
        ];

        // logger('create order response => ', ['create_order_response' => $this->response]);
        if (isset($this->response->aliexpress_trade_buy_placeorder_response) && $this->response->aliexpress_trade_buy_placeorder_response->result->is_success) {
            $order_data['order_list'] = $this->response->aliexpress_trade_buy_placeorder_response->result->order_list->number;
        } elseif (isset($this->response->aliexpress_ds_order_create_response) && $this->response->aliexpress_ds_order_create_response->result->is_success) {
            $order_data['order_list'] = $this->response->aliexpress_ds_order_create_response->result->order_list->number;
        }

        $this->ali_express_order = $ali_express_order_query->create($order_data);
    }

    public function updateOrderStatus()
    {
        if (isset($this->ali_express_order)) {
            if (
                (isset($this->ali_express_order->response['aliexpress_trade_buy_placeorder_response']) && $this->ali_express_order->response['aliexpress_trade_buy_placeorder_response']['result']['is_success'])
                || (isset($this->ali_express_order->response['aliexpress_ds_order_create_response']) && $this->ali_express_order->response['aliexpress_ds_order_create_response']['result']['is_success'])
            ) {
                $this->changeOrderStatusTo(Order::PROCESSING_STATUS);
            }
        }
    }

    public function changeOrderStatusTo($status)
    {
        $this->order->update(['order_status' => $status]);
        $this->order->orderList()->update(['status' => $status]);

    }
}
