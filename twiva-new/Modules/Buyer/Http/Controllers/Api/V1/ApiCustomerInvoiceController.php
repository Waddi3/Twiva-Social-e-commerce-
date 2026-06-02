<?php

namespace Modules\Buyer\Http\Controllers\Api\V1;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Entities\Invoice;
use Modules\Buyer\Http\Requests\Api\V1\ApiCustomerInvoiceRequest;
use Modules\Buyer\Http\Resources\Api\ApiCustomerInvoiceResource;
use Modules\Buyer\Http\Resources\Api\ApiCustomerInvoiceShowResource;
use Modules\Notification\Notifications\Buyer\SendLinkInvoiceNotification;
use Modules\User\Entities\User;
use NotificationChannels\Twilio\TwilioChannel;
use Symfony\Component\HttpFoundation\Response;

class ApiCustomerInvoiceController
{
    /**
     * show list
     * create invoice
     * send sms
     */

    public function list()
    {
        $user_id = auth('api')->id();
        $invoices = Invoice::with('buyer:id,name', 'products.businessProduct:id,price,retail_price,discount', 'products.influencerProduct:id,product_price', 'user')
            ->invoice()
            ->withCount('products')
            ->where('user_id', $user_id)
            ->latest()
            ->get();
        return response()->json(['data' => ['invoices' => ApiCustomerInvoiceResource::collection($invoices)->resource]]);
    }

    public function show($id)
    {
        $user_id = auth('api')->id();
        $invoice = Invoice::with('buyer:id,name', 'products.businessProduct:id,user_id,name,title,price,retail_price,discount', 'products.influencerProduct:id,influencer_id,product_title,product_id,product_price', 'user')
            ->withCount('products')
            ->where('id', $id)
            ->invoice()
            ->where('user_id', $user_id)
            ->latest()
            ->first();
        if (!$invoice) {
            return response()->json(['data' => ['invoice' => $invoice]], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['data' => ['invoice' => new ApiCustomerInvoiceShowResource($invoice)]]);
    }

    // for select product the same api for shoppalbe content (/v1/influencer/shop/products/index) // pagination | searchable | filtered
    public function create(ApiCustomerInvoiceRequest $request)
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

        $invoice_data = [
            'slug' => Invoice::generateSlug(),
            'user_id' => $user->id,
            'buyer_id' => $validated['buyer_id'],
            'status' => Invoice::PENDING_STATUS,
            'type' => Invoice::INVOICE_TYPE,
            'due_date' => Carbon::createFromFormat(Invoice::FORMAT_DATA, $validated['due_date']),
            'message' => $validated['message'],
        ];
        $invoice = Invoice::create($invoice_data);
        if (isset($products)) {
            $attach_products = [];
            foreach ($products as $product) {
                $attach_products[] = [
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $products_validation->where('id', $product->id)->first()['quantity'],
                ];
            }
            $invoice->products()->sync($attach_products);
        }

        // TODO::H send sms for buyer/customer
        $buyer = Buyer::find($validated['buyer_id']);
        // Notification::route(TwilioChannel::class, $buyer->mobile)
        $buyer->notify(new SendLinkInvoiceNotification($invoice, $user));
        return response()->json(['data' => ['invoice' => $invoice, 'products' => $invoice->products]]);
    }
}
