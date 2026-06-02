<?php

namespace Modules\Order\Http\Controllers\API\V3\Business;

use Apxcde\OnfonSms\OnfonChannel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Modules\Buyer\Notifications\EventProductTicketDispatchedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenCanceledNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDeliveredNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDispatchedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDispatchedViaMobileNotification;
use Modules\Notification\Notifications\Buyer\OrderIsBeingProcessedNotification;
use Modules\Notification\Notifications\Buyer\OrderIsBeingProcessedViaMobileNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\TentativeDeliveryPeriod;
use Modules\Order\Events\DeliveredOrderEvent;
use Modules\Order\Http\Requests\IndexOrderRequest;
use Modules\Order\Http\Requests\NextStatusOrderRequest;
use Modules\Order\Http\Resources\IndexOrdersCollection;
use Modules\Order\Http\Resources\ShowOrderResource;
use Modules\Order\Jobs\SendQRCodeToDeliveryOrderJob;
use NotificationChannels\Twilio\TwilioChannel;
use Symfony\Component\HttpFoundation\Response;
use function request;

class OrderController extends Controller {
    public function index(IndexOrderRequest $request)
    {
        $validated = $request->validated();
        $order_query = Order::select('id', 'business_id', 'order_amount', 'created_at', 'order_status', 'shipping_amount')
            ->where('business_id', auth('api')->id())
            ->where('order_status', '!=', Order::CANCELED_STATUS)
//            ->orderBy('created_at', 'desc')
            ->orderByDesc('id')
            ->whereHas('orderDetails')
            ->with(['orderDetails' => function ($orderDetailsQuery)
            {
                $orderDetailsQuery->whereHas('productDetails');
            }]);
        if (isset($validated['keyword']))
        {
            $orders = $order_query
                ->whereHas('orderDetails', function ($orderDetailsQuery) use ($validated)
                {
                    $orderDetailsQuery->select('id', 'status', 'order_id', 'product_id', 'amount', 'quantity')
                        ->whereHas('productDetails')
                        ->with(['productDetails' => function ($productDetailsQuery) use ($validated)
                        {
                            $productDetailsQuery->where('product_title', 'LIKE', '%' . $validated['keyword'] . '%')
                                ->orderBy('created_at', 'desc');
                        }]);
                })
                ->whereIn('order_status', Arr::flatten([
                    Order::getStatusNumberFromString('Placed'),
                    Order::getStatusNumberFromString('Processing'),
                    Order::getStatusNumberFromString('Dispatched'),
                    Order::getStatusNumberFromString('Delivered')
                ]));
        }
        else
        {
            $orders = $order_query
                ->whereIn('order_status', Order::getStatusNumberFromString($validated['type'] ?? 'Placed'));
        }
        return response()->json(['data' => IndexOrdersCollection::collection($orders->paginate(10))->resource]);
    }

    public function show()
    {
        $order = Order::where(['id' => request('order_id'), 'business_id' => auth('api')->id()])
            ->with(['shippingAddress', 'orderDetails', 'orderDetails.colorDetail', 'orderDetails.sizeDetail', 'transactions', 'orderDetails.businessProductDetails',
                    'shop.user' => function ($with_shop_user)
                    {
                        return $with_shop_user->withTrashed();
                    },
            ])
            ->firstOrFail();
        return response()->json(['data' => new ShowOrderResource($order)]);
    }

    public function moveToNextStatus(NextStatusOrderRequest $request)
    {
        $validated = $request->validated();
        $order = Order::findOrFail($validated['order_id']);
        $tentative_delivery_period = TentativeDeliveryPeriod::find($validated['tentative_delivery_period_id'] ?? null);
        if ($order->order_status != Order::CANCELED_STATUS && $order->order_status != Order::DISPATCHED_STATUS)
        {
            if (in_array($order->order_status, [Order::PLACED_STATUS]))
            {
                if (!$tentative_delivery_period)
                {
                    return response()->json((object)[
                        "message" => "Please set tentative delivery period.",
                        "errors"  => (object)[
                            "tentative_delivery_period_id" => [
                                "Please set tentative delivery period."
                            ]
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            if ($order->order_status < 2)
            {
                $order->update(['order_status' => $order->order_status + 1, 'tentative_delivery_period' => $tentative_delivery_period?->days ?? $order->tentative_delivery_period]);
            }

            if ($order->fresh()->order_status == Order::PROCESSING_STATUS)
            {
                Notification::route('mail', $order->shippingAddress->email)
                    ->notify(new OrderIsBeingProcessedNotification($order));
                $order->shippingAddress->notify(new OrderIsBeingProcessedViaMobileNotification($order));
            }
            else if ($order->fresh()->order_status == Order::DISPATCHED_STATUS)
            {
                $order->setDeliveryCode();

                Notification::route('mail', $order->shippingAddress->email)
                    ->notify(new OrderHasBeenDispatchedNotification($order));

                $order->shippingAddress->notify(new OrderHasBeenDispatchedViaMobileNotification($order));
                SendQRCodeToDeliveryOrderJob::dispatch($order);
                foreach ($order->orderList as $orderList)
                {
                    $order->setDeliveryCode();
                    if ($orderList->businessProductDetails->is_event_ticket_product)
                    {
                        $order->shippingAddress->notify(new EventProductTicketDispatchedNotification(
                            ticketProduct: $orderList->businessProductDetails,
                            delivery_code: $order->fresh()->delivery_code,
                        ));
                    }
                }
                SendQRCodeToDeliveryOrderJob::dispatch($order)->onQueue('order-actions');
            }
            return response()->json(['data' => "New Order Status is " . $order->fresh()->statusString]);
        }
        return response()->json('This Action is not allowed', 403);

    }

    public function cancel()
    {
        $order = Order::with('shippingAddress')->findOrFail(request('order_id'));
        if (in_array($order->order_status, [Order::PROCESSING_STATUS, Order::DISPATCHED_STATUS, Order::PLACED_STATUS]))
        {
            $order->update(['order_status' => Order::CANCELED_STATUS]);
            $order->shippingAddress->notify(new OrderHasBeenCanceledNotification($order));
            return response()->json(['data' => 'Order has been canceled']);
        }
        return response()->json(['data' => 'This Action is not allowed'], 403);

    }
}
