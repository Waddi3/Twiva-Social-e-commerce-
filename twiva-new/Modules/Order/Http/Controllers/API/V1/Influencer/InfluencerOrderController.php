<?php

namespace Modules\Order\Http\Controllers\API\V1\Influencer;

use Modules\Order\Entities\Order;
use Modules\Order\Http\Requests\IndexOrderRequest;
use Modules\Order\Http\Resources\Influencer\InfluencerIndexOrdersCollection;
use Modules\Order\Http\Resources\Influencer\InfluencerShowOrderResource;
use Modules\User\Entities\User;

class InfluencerOrderController
{
    public function index(IndexOrderRequest $request)
    {
        $validated = $request->validated();
        $influencer_auth_id = auth('api')->id();
        $influencer = User::with(['shop:id,user_id,domain'])
            ->select(['id', 'name', 'email', 'type'])
            ->where('id', $influencer_auth_id)
            ->where('type', User::InfluencerType)
            ->first();
        $order_query = Order::with('orderDetails')
//            ->select('id', 'shop_id', 'business_id', 'order_amount', 'created_at', 'order_status')
            ->where('order_status', '!=', Order::CANCELED_STATUS)
            ->whereHas('shop', function ($shopQuery) use ($influencer) {
                $shopQuery->where('user_id', $influencer->id);
            })
            ->whereIn('order_status', Order::getStatusNumberFromString($validated['type'] ?? 'Placed'))
//            ->orderBy('created_at', 'desc');
            ->orderByDesc('id');

//        dd($order_query->pluck('order_status'), Order::getStatusNumberFromString($validated['type'] ?? 'Placed'));
        if (isset($validated['keyword'])) {
            $order_query
                ->whereHas('orderDetails', function ($orderDetailsQuery) use ($validated) {
                    $orderDetailsQuery
                        ->whereHas('productDetails')
                        ->with(['productDetails' => function ($productDetailsQuery) use ($validated) {
                            $productDetailsQuery->where('product_title', 'LIKE', '%' . $validated['keyword'] . '%')->orderBy('created_at', 'desc');
                        }]);
                });
        } else {
            $order_query
                ->with(['orderDetails' => function ($orderDetailsQuery) {
                    $orderDetailsQuery
//                        ->select('id', 'status', 'order_id', 'product_id', 'amount', 'quantity')
                        ->whereHas('productDetails');
                }]);
        }
        return response()->json(['data' => InfluencerIndexOrdersCollection::collection($order_query->paginate(10))->resource]);
    }

    public function show()
    {
        $influencer_auth_id = auth('api')->id();
        $influencer = User::with(['shop:id,user_id,domain'])
            ->select(['id', 'name', 'email', 'type'])
            ->where('id', $influencer_auth_id)
            ->where('type', User::InfluencerType)
            ->first();
        $order = Order::where(['id' => request('order_id')])
            ->whereHas('shop', function ($shopQuery) use ($influencer) {
                $shopQuery->where('user_id', $influencer->id);
            })
            ->with(['shippingAddress', 'orderDetails', 'orderDetails.colorDetailes', 'orderDetails.sizeDetailes', 'transactions', 'shop.user'])
            ->firstOrFail();
        return response()->json(['data' => new InfluencerShowOrderResource($order)]);
    }
}
