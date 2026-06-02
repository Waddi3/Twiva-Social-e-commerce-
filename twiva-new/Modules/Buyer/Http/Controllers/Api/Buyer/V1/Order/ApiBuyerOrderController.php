<?php

namespace Modules\Buyer\Http\Controllers\Api\Buyer\V1\Order;

use Modules\Buyer\Http\Requests\Api\Buyer\V1\Order\ApiBuyerOrderCreateRequest;
use Modules\Buyer\Http\Requests\Api\Buyer\V1\Order\ApiBuyerOrderHasShippingFeesRequest;
use Modules\Buyer\Http\Resources\Api\Buyer\Order\ApiBuyerOrderResource;
use Modules\Buyer\Http\Resources\Api\Buyer\Order\ApiBuyerOrdersDetailsResource;
use Modules\Buyer\Http\Resources\Api\Buyer\Order\ApiBuyerOrderShowResource;
use Modules\Buyer\Repositories\Order\ApiBuyerOrderRepository;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\ShippingAddress;
use Modules\Order\Http\Services\ShippingService;
use Symfony\Component\HttpFoundation\Response;

class ApiBuyerOrderController {
    public function __construct(public ApiBuyerOrderRepository $api_buyer_order_repository)
    {
    }

    public function index($account)
    {
        $per_page = request('per_page') ?? 20;
        $per_page = (int)min(100, max(20, $per_page));
        $buyer = auth('buyer-api')->user();
        $orders = Order::withCount('orderList')->where('buyer_id', $buyer->id)->orderByDesc('id')->paginate($per_page);
        return ApiBuyerOrderResource::collection($orders);
    }

    public function show($account, $id)
    {

        $buyer = auth('buyer-api')->user();

        $order = Order::withCount('orderList')
            ->with('orderList')
            ->where('buyer_id', $buyer->id)
            ->where('id', $id)
            ->orderByDesc('id')
            ->first();

        if (is_null($order))
        {
            return response()->json(['error' => 'Order not found!'], Response::HTTP_BAD_REQUEST);
        }
        return new ApiBuyerOrderShowResource($order);
    }

    public function create($account, ApiBuyerOrderCreateRequest $request)
    {
        $validated = $request->validated();
        $product_ids = $this->api_buyer_order_repository->getProductIds($validated);

        if ($validated['payment_type'] == 'PayOnDelivery')
        {
            if($AliExpressProductNames = $this->api_buyer_order_repository->checkIfProductsBelongToAliExpressAndReturnNames()){
                return \response(['error' => "$AliExpressProductNames Can't Shipped With Pay On Delivery"], 422);
            }
        }

        $products = $this->api_buyer_order_repository->getProducts($validated);
        if (!$products->count())
        {
            return response()->json(['error' => 'Please select product(s).'], Response::HTTP_BAD_REQUEST);
        }
        $validate_color_size = $this->api_buyer_order_repository->validationColorSize($validated);
        if (!$validate_color_size)
        {
            return response()->json(['error' => 'Please select the size and color of the product(s).'], Response::HTTP_BAD_REQUEST);
        }
        [
            'orders'          => $orders,
            'order_ids'       => $order_ids,
            'order_amount'    => $order_amount,
            'shipping_amount' => $shipping_amount,
            'coupon_discount' => $coupon_discount
        ] = $this->api_buyer_order_repository->create($validated);


        // $is_business = $products->first() instanceof Product ? true : false;
        // $address = ShippingAddress::where('id', $validated['address_id'])->first();
        // $shipping_fees = ShippingService::shippingFee($products, $address?->city_id, $is_business);

        return response()->json(
            [
                'message'            => 'Your orders numbers: ',
                'order_ids'          => $order_ids,
                'order_amount'       => $order_amount,
                'shipping_amount'    => $shipping_amount,
                'transaction_amount' => ($order_amount + $shipping_amount) - $coupon_discount,
                // 'shipping_fees' => $shipping_fees,
                'till_number'        => config('services.MPESA.MPESA_TILL_NUMBER'),
                'orders'             => ApiBuyerOrdersDetailsResource::collection($orders),
            ],
            Response::HTTP_CREATED
        );
    }

    public function shippingFees($account, ApiBuyerOrderCreateRequest $request)
    {
        $validated = $request->validated();
        $product_ids = $this->api_buyer_order_repository->getProductIds($validated);
        $products = $this->api_buyer_order_repository->getProducts($validated);
        // $is_business = $products->first() instanceof Product ? true : false;
        $address = ShippingAddress::where('id', $validated['address_id'])->first();
        $partnerId = $validated['partner_id'];
        $shipping_fees = ShippingService::calculateShippingFee($products, $address?->city_id, $partnerId);
        if (!$products->count())
        {
            return response()->json(['error' => 'Please select product(s).'], Response::HTTP_BAD_REQUEST);
        }
        return response()->json(['shipping_fees' => $shipping_fees], Response::HTTP_CREATED);
    }

    public function hasShippingFees($account, ApiBuyerOrderHasShippingFeesRequest $request)
    {
        $validated = $request->validated();

        $products = InfluencerShopProducts::whereIn('id', $validated['products'])
            ->get();

        foreach ($products as $product)
        {

            if ($product->productDetails->business->businessShipping->shipping_by_partner)
            {
                return response()->json(['has_shipping_fees' => true]);
            }
        }
        return response()->json(['has_shipping_fees' => false]);
    }
}
