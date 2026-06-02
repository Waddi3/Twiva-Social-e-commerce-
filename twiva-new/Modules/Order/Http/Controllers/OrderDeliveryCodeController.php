<?php

namespace Modules\Order\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Buyer\Entities\Buyer;
use Modules\Home\Entities\Transaction;
use Modules\Notification\Notifications\Buyer\ResendOrderDeliveryCodeNotification;
use Modules\Order\Entities\DeliveryIssue;
use Modules\Order\Entities\Order;
use Modules\Order\Events\DeliveredOrderEvent;
use Modules\Order\Http\Requests\ReportIssueRequest;
use Modules\Order\Http\Requests\ResendDeliveryCodeRequest;
use Modules\Order\Repositories\OrderDeliveryRepository;

class OrderDeliveryCodeController extends Controller {
    private OrderDeliveryRepository $orderDeliveryRepository;

    public function __construct(OrderDeliveryRepository $orderDeliveryRepository)
    {
        $this->orderDeliveryRepository = $orderDeliveryRepository;
    }

    public function insert(Request $request)
    {
        if (!$request->has('delivery_code') or is_null($request->get('delivery_code')))
        {
            return view('order::delivery-code.insert');
        }
        return redirect()->route('buyer.delivery.code.show.details', ['delivery_code' => $request->delivery_code]);
    }

    public function show($delivery_code)
    {
        $order = Order::with('orderDetails')
            ->where('delivery_code', $delivery_code)
            ->whereIn('order_status', [Order::DISPATCHED_STATUS]) //  Order::DELIVERED_STATUS
            ->first();
        if (!$order)
        {
            return view('order::delivery-code.insert')
                ->with('delivery_message', 'Sorry, this code is not valid. Please try again or re-send the code to the customer.')
                ->with('delivery_code', $delivery_code);
        }
        $account = $order->shop->domain;
        //        request()->merge(['account' => $account]);
        return view('order::delivery-code.index', ['order' => $order, 'account' => $account]);
    }

    public function confirm($delivery_code)
    {
        $resend = request('resend');
        if ($resend)
        {
            $phoneNumber = explode('-', request('mobile'));
            $buyer = Buyer::where('phone_number', $phoneNumber[1])
                ->where('phone_code', str_replace('+', '', $phoneNumber[0]))
                ->first();

            if (!$buyer)
            {
                return view('order::delivery-code.insert', [
                    'delivery_code' => $delivery_code,
                ])->with('error', 'User not found!');
            }

            $order = Order::where('buyer_id', $buyer->id)
                ->where('order_status', Order::DISPATCHED_STATUS)
                ->orderBy('id', 'desc')
                ->first();

            if (!$order)
            {
                return view('order::delivery-code.insert', [
                    'delivery_code' => $delivery_code,
                ])->with('error', 'No order found!');
            }

            $buyer->notify(new ResendOrderDeliveryCodeNotification($order));

            return view('order::delivery-code.insert', [
                'delivery_code' => $delivery_code,
            ])->with('message', 'Code sent!');
        }

        $order = Order::with('orderDetails')
            ->where('delivery_code', $delivery_code)
            ->where('order_status', Order::DISPATCHED_STATUS)
            ->first();

        if (!$order)
        {
            return view('order::delivery-code.insert')
                ->with('delivery_message', 'Sorry, this code is not valid. Please try again or re-send the code to the customer.')
                ->with('delivery_code', $delivery_code);
        }
        /*$order->update(['order_status' => Order::DELIVERED_STATUS, 'delivery_date' => now()]);

            event(new DeliveredOrderEvent($order));
        Notification::route('mail', $order->shippingAddress->email)
            ->route(TwilioChannel::class, $order->shippingAddress->mobile)
            ->notify(new OrderHasBeenDeliveredNotification($order));*/
        $account = $order?->shop?->domain;
        return view('order::delivery-code.index', ['order' => $order, 'account' => $account]);
    }

    public function reportIssue(ReportIssueRequest $request)
    {

        $status = false;
        try
        {
            $deliveryIssue = DeliveryIssue::create([
                'order_id' => $request->order_id,
                'issue'    => $request->issue
            ]);

            if ($deliveryIssue)
            {
                $status = true;
            }

            return view('order::delivery-code.report-issue', ['status' => $status]);

            /*return response()->json([
                'status' => false,
                'message' => 'Can not report the issue'
            ]);*/
        }
        catch (Exception $exception)
        {
            return view('order::delivery-code.report-issue', ['status' => $status]);
        }
    }

    public function payment(Request $request)
    {
        $order = Order::find($request->order_id);
        if (!$order)
        {
            return abort('404');
        }
        // Pre paid
        if (!$request->has('payment_method'))
        {
            // $order->payment_status = Order::PAID;
            if ($order->payment_status == Order::PAID)
            {
                $order->order_status = Order::DELIVERED_STATUS;
                $order->delivery_date = now();
                $order->save();
                $account = $order->shop->domain;
                return view('order::delivery-code.index', ['order' => $order, 'account' => $account]); // return verify success payment
            }
        }
        else
        {
            // Pay on delivery = 2
            if ($request->payment_method == 'paycash')
            {
                $order->payment_status = Order::PAY_ON_DELIVERY;
                $order->order_status = Order::DELIVERED_STATUS;
                $order->delivery_date = now();
                $order->save();
                $order->orderList()->update(['status' => Order::PAID]);
                event(new DeliveredOrderEvent($order));
                $account = $order->shop->domain;
                //        request()->merge(['account' => $account]);
                return view('order::delivery-code.index', ['order' => $order, 'account' => $account]);
            } // Mpesa
            else
            {
                $result = $this->payUsingMpesa($order, $request->all());
                if ($result)
                {
                    return view('order::delivery-code.confirm-payment', ['order' => $order]);
                }
            }
        }

        return redirect()->route('buyer.delivery.code.show.details', ['delivery_code' => $order->delivery_code]);
    }

    public function resend(ResendDeliveryCodeRequest $request)
    {
        Log::error('Resend Method');
        $phoneNumber = explode('-', $request->mobile);

        $buyer = Buyer::where('phone_number', $phoneNumber[1])
            ->where('phone_code', $phoneNumber[0])
            ->first();
        if (!$buyer)
        {
            return redirect()->route('buyer.delivery.code.show.details', [
                'delivery_code' => $request->delivery_code,
            ])->with('error', 'User not found!');
        }

        $order = Order::where('buyer_id', $buyer->id)
            ->where('order_status', Order::DISPATCHED_STATUS)
            ->orderBy('id', 'desc')
            ->first();

        if (!$order)
        {
            return redirect()->route('buyer.delivery.code.show.details', [
                'delivery_code' => $request->delivery_code
            ])->with('error', 'Order not found!');
        }

        // Send Delivery code
        // Notification::route(TwilioChannel::class, $order->shippingAddress->mobile)
        $order->shippingAddress->notify(new ResendOrderDeliveryCodeNotification($order));

        return redirect()->route('buyer.delivery.code.show.details', [
            'delivery_code' => $request->delivery_code
        ])->with('message', 'Code sent!');
    }

    public function payUsingMpesa($order, $data)
    {
        Session::remove('transaction_id');
        //Session::remove('payment_type');

        list($transaction, $response) = $this->orderDeliveryRepository->storeTransaction($order, $data);
        if (isset(json_decode($response)->errorMessage))
        {
            return false;
        }
        Session::put('transaction_id', $transaction->transaction_id ?? null);
        return true;
    }

    public function confirmPayment(Request $request)
    {
        if ($request->order_id)
        {
            $order = Order::find($request->order_id);
            if ($order)
            {
                $order->payment_status = Order::PAID;
                $order->order_status = Order::DELIVERED_STATUS;
                $order->save();
                $order->orderList()->update(['status' => Order::PAID]);
                event(new DeliveredOrderEvent($order));
                $account = $order->shop->domain;
                //        request()->merge(['account' => $account]);
                return view('order::delivery-code.index', ['order' => $order, 'account' => $account]);
                // return redirect()->route('buyer.delivery.code.show.details', ['delivery_code' => $order->delivery_code]);
            }
        }
    }

    public function checkOrderStatus()
    {
        $transaction = Transaction::where('transaction_id', Session::get('transaction_id'))->first();
        if (!Session::has('transaction_id') || !$transaction || $transaction->status == Transaction::TransactionFailed)
        {
            return response()->json(
                [
                    'transaction_status' => Transaction::TransactionFailed,
                ]
            );
        }
        if ($transaction->status == Transaction::PaymentSuccessfully)
        {
            if (auth('buyer')->check()) {
                auth('buyer')->user()->update([
                    'cart' => null,
                    'cart_last_updated_date' => null,
                ]);
            }
            Session::remove('transaction_id');
            Session::remove('orders_ids');
            Session::remove('productsData');
            Session::remove('cart');
            Session::remove('flutterwave_payment_url');
            Session::remove('address_id');
            return response()->json(
                [
                    'transaction_status' => Transaction::PaymentSuccessfully,
                ]
            );
        }
        return response()->json(
            [
                'transaction_status' => Transaction::WaitingForPayment,
            ]
        );
    }
}
