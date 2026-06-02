<?php


namespace Modules\Payment\Http\Controllers\API\V3;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Modules\Home\Entities\Transaction;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenPlacedNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Events\DeliveredOrderEvent;
use Modules\User\Entities\User;
use NotificationChannels\Twilio\TwilioChannel;

// This Class is used to  pay orders for buyer
class MPESATransactionController
{

    /**
     * https://developer.safaricom.co.ke/APIs/TransactionStatus
     */
    public function queue(Request $request)
    {
        // TODO::H check this response from mpesa side
        logger("Modules/Payment/Http/Controllers/API/V3/MPESATransactionController.php:19 => queue => 26", ['$request' => $request->all()]);
    }

    public function result(Request $request)
    {
        logger("Modules/Payment/Http/Controllers/API/V3/MPESATransactionController.php:24 => result => 31", ['$request' => $request->all()]);
        $response = $request->all();
        $result = $response['Result'] ?? null;
        if (is_null($result)) {
            return false;
        }
        $result_code = $result['ResultCode'] ?? null;
        if (is_null($result_code)) {
            return false;
        }
        $originator_conversation_id = $result['OriginatorConversationID'] ?? null;
        $transaction_id = $result['TransactionID'] ?? null;
        if (!$originator_conversation_id) {
            return false;
        }
        $transaction = Transaction::where(function ($where) use ($transaction_id, $originator_conversation_id) {
            return $where->where('transaction_id', $transaction_id)
                ->orWhere('originator_conversation_id', $originator_conversation_id);
        })
            ->where('payment_gateway_type', Transaction::MPESAGateAway)
            ->where('status', Transaction::WaitingForPayment)
            ->first();

        logger('Modules/Payment/Http/Controllers/API/V3/MPESATransactionController.php:54 => ', ['transaction' => $transaction]);
        if ($transaction) {
            if ($result_code == 0) {
                // success
                $transaction_status = Transaction::PaymentSuccessfully;
                $transaction->update(['status' => $transaction_status]);

                $this->updateOrders($transaction);
            } else {
                // error
                $transaction_status = Transaction::TransactionFailed;
                $transaction->update(['status' => $transaction_status]);
            }
        }
    }

    public function updateOrders($transaction)
    {
        $ordersIds = $transaction->orders->pluck('id')->toArray();
        $orders = Order::whereIn('id', $ordersIds)->get();
        foreach ($orders as $order) {
            $order->update(['payment_status' => Order::PAID, 'order_status' => Order::PLACED_STATUS]);
            $order->orderList()->update(['status' => Order::PAID]);
            event(new DeliveredOrderEvent($order));
            if ($order->shop->user->type == User::InfluencerType) {
                foreach ($order->orderDetails as $product) {
                    $product->productFromInfluencerShop->update(['total_sale' => $product->productFromInfluencerShop->total_sale + $product->quantity]);
                    $product->productDetails->actualDetail->update(['total_sale' => $product->productDetails->actualDetail->total_sale + $product->quantity]);
                }
            } else if ($order->shop->user->type == User::BusinessType) {
                foreach ($order->orderDetails as $product) {
                    $product->businessProductDetails->update(['total_sale' => $product->businessProductDetails->total_sale + $product->quantity]);
                }
            }
            $order->business->notify(new NewOrderReceivedNotification($order));
        }
        if ($orders[0]->shop->user->type == User::InfluencerType) {
            $orders[0]->shop->influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
        }
        $notificationEmail = $orders[0]?->buyer->email ?? $orders[0]->shippingAddress->email;
        $notificationPhoneNumber = $orders[0]?->buyer->mobile ?? $orders[0]->shippingAddress->phone_number;

        // Notification::route('mail', $notificationEmail)
            // ->route(TwilioChannel::class, '+' . str_replace('-', '', $notificationPhoneNumber))
            $orders[0]->shippingAddress->notify(new OrderHasBeenPlacedNotification($ordersIds));
    }
}
