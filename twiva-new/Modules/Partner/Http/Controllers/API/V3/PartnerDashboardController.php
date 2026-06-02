<?php

namespace Modules\Partner\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Modules\Influencer\Entities\Product;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDispatchedNotification;
use Modules\Notification\Notifications\Buyer\OrderIsBeingProcessedNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\TentativeDeliveryPeriod;
use Modules\Order\Http\Requests\NextStatusOrderRequest;
use Modules\Order\Jobs\SendQRCodeToDeliveryOrderJob;
use Modules\Partner\Enums\ProductShipping;
use Modules\Partner\Http\Requests\PartnerOrdersRequest;
use Modules\Partner\Http\Requests\PartnerUpdateOrderStatusRequest;
use Modules\Partner\Http\Resources\PartnerOrdersResource;
use Modules\Partner\Http\Resources\PartnerShowOrderResource;
use Modules\User\Entities\User;
use Illuminate\Support\Collection;
use NotificationChannels\Twilio\TwilioChannel;
use PDF;
use Symfony\Component\HttpFoundation\Response;

class PartnerDashboardController extends Controller
{
    public function orders(PartnerOrdersRequest $request)
    {
        $validated = $request->validated();
        $orders = Order::select('orders.id', 'business_id', 'buyer_id', 'order_amount', 'orders.created_at', 'order_status', 'shipping_amount')
            ->where('partner_id', auth()->id())
            ->where('order_status', '!=', Order::CANCELED_STATUS)
            ->orderByDesc('orders.id')
            ->with(['buyer', 'business'])
            ->where(function ($where) {
                $where->whereHas('orderDetails.productsHasShipping', function ($where_has) {
                    $where_has->where('has_shipping_fees', 1);
                })->orWhereHas('orderDetails.productsInfluencerHasShipping.product', function ($where_has) {
                    $where_has->where('has_shipping_fees', 1);
                });
            });
        if (isset($validated['keyword'])) {
            $orders = $orders
                ->whereHas('orderDetails.productDetails', function ($productDetailsQuery) use ($validated) {
                    $productDetailsQuery->where('product_title', 'LIKE', '%' . $validated['keyword'] . '%')
                        ->orderBy('created_at', 'desc');
                })
                ->orWhereHas('buyer', function ($buyer) use ($validated) {
                    $buyer->where('name', 'LIKE', '%' . $validated['keyword'] . '%')
                        ->orderBy('created_at', 'desc');
                })
                ->orWhereHas('business', function ($business) use ($validated) {
                    $business->where('name', 'LIKE', '%' . $validated['keyword'] . '%')
                        ->orderBy('created_at', 'desc');
                });
        }
        $orders = $orders
            ->whereIn('order_status', Order::getStatusNumberFromString($validated['type'] ?? 'Placed'));

        // Total delivered
        $deliveredOrders = Order::select('id', 'business_id', 'buyer_id', 'order_amount', 'created_at', 'order_status', 'shipping_amount')
            ->where('order_status', Order::DELIVERED_STATUS)
            ->orderByDesc('id')
            ->with(['buyer', 'business'])->whereHas('orderDetails.businessProductDetails', function ($query) {
                $query->where('shipping_by', ProductShipping::Partner->value);
            })->count();

        // Total placed
        $placedOrders = Order::select('id', 'business_id', 'buyer_id', 'order_amount', 'created_at', 'order_status', 'shipping_amount')
            ->where('order_status', Order::PLACED_STATUS)
            ->orderByDesc('id')
            ->with(['buyer', 'business'])->whereHas('orderDetails.businessProductDetails', function ($query) {
                $query->where('shipping_by', ProductShipping::Partner->value);
            })->count();

        // Total dispatched
        $dispatchedOrders = Order::select('id', 'business_id', 'buyer_id', 'order_amount', 'created_at', 'order_status', 'shipping_amount')
            ->where('order_status', Order::DISPATCHED_STATUS)
            ->orderByDesc('id')
            ->with(['buyer', 'business'])->whereHas('orderDetails.businessProductDetails', function ($query) {
                $query->where('shipping_by', ProductShipping::Partner->value);
            })->count();

        return response()->json([
            'data' => PartnerOrdersResource::collection($orders->paginate(10))->resource,
            'total_delivered' => $deliveredOrders,
            'total_placed' => $placedOrders,
            'total_dispatched' => $dispatchedOrders
        ]);
    }

    public function show()
    {
        $order = Order::where(['id' => \request('order_id')])
            ->with([
                'shippingAddress:id,buyer_id,first_name,last_name,delivery_address,phone_number,latitude,longitude,city_id,country_id,county_id',
                'orderDetails',
                'orderDetails.colorDetail',
                'orderDetails.sizeDetail',
                'transactions',
                'orderDetails.productsBusiness',
                'orderDetails.productsInfluencer',
                'shop.user'
            ])
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // try {
        if ($order->shop->user->type == User::InfluencerType) {
            $products = $order->orderDetails->map(function ($orderDetails) {
                return collect([
                    'id' => $orderDetails->productsInfluencer->id,
                    'name' => $orderDetails->productsInfluencer->product_title,
                    'sku' => $orderDetails->productsInfluencer->product->sku,
                    'brand' => $orderDetails->productsInfluencer->product->brand,
                    'description' => $orderDetails->productsInfluencer->product->description,
                    'main_image_path' => $orderDetails->productsInfluencer->product->main_image_path,
                    'price' => $orderDetails->product_price,
                    'quantity' =>  $orderDetails->quantity,
                    'amount' =>  $orderDetails->amount,
                    'color' => $orderDetails->colorDetail?->name ?? '',
                    'size' => $orderDetails->sizeDetail?->name ?? '',
                ]);
            });
        } else {
            $products = $order->orderDetails->map(function ($orderDetails) {
                return collect([
                    'id' => $orderDetails->productsBusiness->id,
                    'name' => $orderDetails->productsBusiness->name,
                    'sku' => $orderDetails->productsBusiness->sku,
                    'brand' => $orderDetails->productsBusiness->brand,
                    'description' => $orderDetails->productsBusiness->description,
                    'main_image_path' => $orderDetails->productsBusiness->main_image_path,
                    'price' => $orderDetails->product_price,
                    'quantity' =>  $orderDetails->quantity,
                    'amount' =>  $orderDetails->amount,
                    'color' => $orderDetails->colorDetail->name,
                    'size' => $orderDetails->sizeDetail->name,
                ]);
            });
        }
        $order->businesses = new Collection();


        $business = $order->business;
        if ($business) {
            if (!$products->isEmpty()) {
                $business->products = $products;
            }
            $order->businesses->push($business);
        }

        return response()->json(['data' => new PartnerShowOrderResource($order)]);
        // } catch (\Exception $exception) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'An error occurred while loading data',
        //         'error' => $exception->getMessage()
        //     ], 404);
        // }
    }

    public function updateStatus(PartnerUpdateOrderStatusRequest $request)
    {
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        try {
            // 4 = shipped
            if ($request->status === 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Delivered status is not allowed'
                ], 403);
            }
            $order->order_status = $request->status;
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'The order status updated successfully'
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'A problem occurred while updating order status',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function moveToNextStatus(NextStatusOrderRequest $request)
    {
        $validated = $request->validated();
        $order = Order::findOrFail($validated['order_id']);
        $tentative_delivery_period = TentativeDeliveryPeriod::find($validated['tentative_delivery_period_id'] ?? null);
        if ($order->order_status != Order::CANCELED_STATUS && $order->order_status != Order::DISPATCHED_STATUS) {
            if (in_array($order->order_status, [Order::PLACED_STATUS])) {
                if (!$tentative_delivery_period) {
                    return response()->json((object)[
                        "message" => "Please set tentative delivery period.",
                        "errors" => (object)[
                            "tentative_delivery_period_id" => [
                                "Please set tentative delivery period."
                            ]
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            if ($order->order_status < 2) {
                $order->update(['order_status' => $order->order_status + 1, 'tentative_delivery_period' => $tentative_delivery_period?->days ?? $order->tentative_delivery_period]);
            }

            if ($order->fresh()->order_status == Order::PROCESSING_STATUS) {
                // Notification::route('mail', $order->shippingAddress->email)
                    // ->route(TwilioChannel::class, $order->shippingAddress->mobile)
                $order->shippingAddress->notify(new OrderIsBeingProcessedNotification($order));
            } else if ($order->fresh()->order_status == Order::DISPATCHED_STATUS) {
                $order->setDeliveryCode();
                // Notification::route('mail', $order->shippingAddress->email)
                    // ->route(TwilioChannel::class, $order->shippingAddress->mobile)
                $order->shippingAddress->notify(new OrderHasBeenDispatchedNotification($order));
                SendQRCodeToDeliveryOrderJob::dispatch($order);
            }
            return response()->json(['data' => "New Order Status is " . $order->fresh()->statusString]);
        }
        return response()->json('This Action is not allowed', 403);
    }

    public function export(Request $request)
    {

        $order = Order::where(['id' => \request('id')])
        ->with([
            'shippingAddress:id,buyer_id,first_name,last_name,delivery_address,phone_number,latitude,longitude,city_id,country_id,county_id',
            'orderDetails',
            'orderDetails.colorDetail',
            'orderDetails.sizeDetail',
            'transactions',
            'orderDetails.productsBusiness',
            'orderDetails.productsInfluencer',
            'shop.user'
        ])
        ->first();

    if (!$order) {
        return response()->json([
            'status' => false,
            'message' => 'Order not found'
        ], 404);
    }

        // try {
            if ($order->shop->user->type == User::InfluencerType) {
                $products = $order->orderDetails->map(function ($orderDetails) {
                    return collect([
                        'id' => $orderDetails->productsInfluencer->id,
                        'name' => $orderDetails->productsInfluencer->product_title,
                        'sku' => $orderDetails->productsInfluencer->product->sku,
                        'brand' => $orderDetails->productsInfluencer->product->brand,
                        'description' => $orderDetails->productsInfluencer->product->description,
                        'main_image_path' => $orderDetails->productsInfluencer->product->main_image_path,
                        'price' => $orderDetails->product_price,
                        'quantity' =>  $orderDetails->quantity,
                        'amount' =>  $orderDetails->amount,
                        'color' => $orderDetails->colorDetail?->name ?? '',
                        'size' => $orderDetails->sizeDetail?->name ?? '',
                    ]);
                });
            } else {
                $products = $order->orderDetails->map(function ($orderDetails) {
                    return collect([
                        'id' => $orderDetails->productsBusiness->id,
                        'name' => $orderDetails->productsBusiness->name,
                        'sku' => $orderDetails->productsBusiness->sku,
                        'brand' => $orderDetails->productsBusiness->brand,
                        'description' => $orderDetails->productsBusiness->description,
                        'main_image_path' => $orderDetails->productsBusiness->main_image_path,
                        'price' => $orderDetails->product_price,
                        'quantity' =>  $orderDetails->quantity,
                        'amount' =>  $orderDetails->amount,
                        'color' => $orderDetails->colorDetail->name,
                        'size' => $orderDetails->sizeDetail->name,
                    ]);
                });
            }
            $order->businesses = new Collection();


            $business = $order->business;
            if ($business) {
                if (!$products->isEmpty()) {
                    $business->products = $products;
                }
                $order->businesses->push($business);
            }
            $data = ['order' => $order];
            $pdf = PDF::loadView('partner::pdf.order', $data);
            $filePath = '/invoices/order' . $order->id . '.pdf';
            $fileFullPath = public_path() . $filePath;

            if (file_exists($fileFullPath)) {
                unlink($fileFullPath);
            }

            $pdf->save($fileFullPath);
            if (file_exists($fileFullPath)) {
                return response()->json([
                    'status' => true,
                    'file_path' => url($filePath)
                ]);
            }

            return $pdf->download('Twiva_Order' . $order->id . '.pdf');
        // } catch (\Exception $exception) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'An error occurred while loading data',
        //         'error' => $exception->getMessage()
        //     ], 404);
        // }
    }
}
