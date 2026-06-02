<?php

namespace Modules\Buyer\Http\Controllers\Api\V1;


use Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Entities\Invoice;
use Modules\Buyer\Http\Requests\Api\V1\ApiCustomerExpressListRequest;
use Modules\Buyer\Http\Requests\Api\V1\ApiCustomerExpressRequest;
use Modules\Buyer\Http\Requests\Api\V1\ApiCustomerInvoiceRequest;
use Modules\Buyer\Http\Resources\Api\ApiCustomerExpressResource;
use Modules\Buyer\Http\Resources\Api\ApiCustomerExpressShowResource;
use Modules\Buyer\Http\Resources\Api\ApiCustomerInvoiceShowResource;
use Modules\Notification\Notifications\Buyer\SendLinkInvoiceNotification;
use Modules\User\Entities\User;
use NotificationChannels\Twilio\TwilioChannel;
use Symfony\Component\HttpFoundation\Response;

class ApiCustomerExpressController
{
    /**
     * show list
     * create invoice
     * send sms
     */

    public function list()
    {
        $user_id = auth('api')->id();
        $expresses = Invoice::with('buyer:id,name', 'products.businessProduct:id,price,retail_price,discount', 'products.influencerProduct:id,product_price', 'user')
            ->express()
            ->withCount('products')
            ->where('user_id', $user_id)
            ->latest()
            ->get();
        return response()->json(['data' => ['expresses' => ApiCustomerExpressResource::collection($expresses)->resource]]);
    }

    public function show($id)
    {
        $user_id = auth('api')->id();
        $express = Invoice::with('buyer:id,name', 'products.businessProduct:id,user_id,name,title,price,retail_price,discount', 'products.influencerProduct:id,influencer_id,product_title,product_id,product_price', 'user')
            ->withCount('products')
            ->where('id', $id)
            ->express()
            ->where('user_id', $user_id)
            ->first();
        if (!$express) {
            return response()->json(['data' => ['express' => $express]], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['data' => ['express' => new ApiCustomerExpressShowResource($express)]]);
    }

    // for select product the same api for shoppalbe content (/v1/influencer/shop/products/index) // pagination | searchable | filtered
    public function create(ApiCustomerExpressRequest $request)
    {
        $validated = $request->validated();
        $products_validation = collect($validated['products']);
        $user = User::with([
            'influencerShopProducts' => function ($with) use ($products_validation) {
                $with->whereIn('id', $products_validation->pluck('id'));
            },
            'products' => function ($with) use ($products_validation) {
                $with->whereIn('id', $products_validation->pluck('id'));
            }])
            ->where('id', auth('api')->id())
            ->first();
        if ($user->type == User::InfluencerType) {
            $products = $user->influencerShopProducts;
        } elseif ($user->type == User::BusinessType) {
            $products = $user->products;
        }

        if (!$products->count()) {
            return response()->json(['message' => 'products not found!', 'errors' => ['products' => ['products not found!']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $express_data = [
            'slug' => Invoice::generateSlug(),
            'user_id' => $user->id,
            'buyer_id' => $validated['buyer_id'],
            'status' => Invoice::PENDING_STATUS,
            'type' => Invoice::EXPRESS_TYPE,
        ];
        $express = Invoice::create($express_data);
        if (isset($products)) {
            $attach_products = [];
            foreach ($products as $product) {
                $attach_products[] = [
                    'invoice_id' => $express->id,
                    'product_id' => $product->id,
                    'quantity' => $products_validation->where('id', $product->id)->first()['quantity'],
                ];
            }
            $express->products()->sync($attach_products);
        }

        // TODO::H send sms for buyer/customer
        $buyer = Buyer::find($validated['buyer_id']);
        // Notification::route(TwilioChannel::class, $buyer->mobile)
        $buyer->notify(new SendLinkInvoiceNotification($express, $user));
        return response()->json(['data' => ['express' => $express, 'products' => $express->products]]);
    }

    public function createPreviewLink(ApiCustomerExpressRequest $request)
    {
        $validated = $request->validated();
        $attach_products = [];

        $shopDomain = auth('api')->user()->shop->domain;
        foreach ($validated['products'] as $product)
        {
            $attach_products[] = [
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'shop_domain' => $shopDomain
            ];
        }
        $attach_products = Crypt::encrypt(json_encode($attach_products));
        return \response()->json([
            'redirect_link' => route('buyer.checkout.account.preview', ['account' => $shopDomain, 'data' => $attach_products])
        ]);
    }

    public function delete($id)
    {
        $user_id = auth('api')->id();
        $express = Invoice::where('id', $id)
            ->express()
            ->where('user_id', $user_id)
            ->first();
        if (!$express) {
            return response()->json(['data' => ['express' => $express]], Response::HTTP_NOT_FOUND);
        }
        $express->delete();
        return response()->json(['data' => ['msg' => 'Express deleted']]);
    }
}
