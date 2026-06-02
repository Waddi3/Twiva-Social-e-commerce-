<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Entities\Invoice;
use Modules\Campaign\Entities\PostPrice;
use Modules\Campaign\Entities\PostUser;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\ProductImage;
use Modules\Influencer\Entities\Shops;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Business\ProductAddedToInfluencerShop;
use Modules\Notification\Notifications\Buyer\OrderHasBeenPlacedNotification;
use Modules\Notification\Notifications\Buyer\SendLinkInvoiceNotification;
use Modules\Notification\Notifications\Buyer\TestAll as BuyerTestAll;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Notification\Notifications\User\TestAll;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Order\Mail\OrderQrCodeDeliveryEmail;
use Modules\Order\Notifications\NotifyBuyerOfDeliveryCodeOrder;
use Modules\Payment\Entities\WinningPayout;
use Modules\Payment\Http\Resources\Influencer\Earnings\CampaignEarningsCollection;
use Modules\Payment\Services\FlutterWaveService;
use Modules\Payment\Services\PayoutPaymentService;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Services\GenerateVideoService;
use Modules\Platform\Services\InstagramServices;
use Modules\Products\Services\ChatGPTService;
use Modules\User\Entities\User;
use Modules\User\Helper\EarningUser;
use Modules\User\Services\TwiloVerifyServices;
use NotificationChannels\Twilio\TwilioChannel;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use function Clue\StreamFilter\fun;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('test-fcm', function ()
{

    $user = User::where('email', 'en.g.aziz.mel@gmail.com')->first();
    $product = Product::first();
    $user->notifyNow(new ProductAddedToInfluencerShop($product));
});
Route::get('test/api/chat-gpt', function ()
{
    $check = request('check');
    if ($check == 'debug')
    {
        $chat_gpt_service = new ChatGPTService();
        $chat_gpt_service->askToChatGpt();
        dd($chat_gpt_service);
    }
    abort(404);
});

Route::get('test/reel-story-instagram', function ()
{
    ini_set("max_execution_time", 600);
    if (request()->has('send') and request('send'))
    {
        $story_id = request('story_id');
        $story = StoryPost::find($story_id);
        $user = User::find($story?->influencer_id);
        //        dd($story_id, $user, $story);
        $result_reel_to_instagram = InstagramServices::reelToInstagram($user, $story_id);
        $result_story_to_instagram = InstagramServices::storyToInstagram($user, $story_id);
        dd([
            'result_reel_to_instagram'  => $result_reel_to_instagram,
            'result_story_to_instagram' => $result_story_to_instagram,
        ]);
    }
});

if (config('app.debug') || request('key') == config('app.key'))
{

    Route::get('campaign-fix', function ()
    {
        ini_set('max_execution_time', 600);
        $wrongPostUsers = PostUser::where('status', PostUser::HiredStatus)
            ->withWhereHas('influencerPriceBase', function ($influencerPriceBaseQuery)
            {
                $influencerPriceBaseQuery->where('status', '=', PostUser::AppliedStatus);
            })->get();
        $newArray = [];
        foreach ($wrongPostUsers as $wrongPostUser)
        {
            $updatedPostPrices = PostPrice::where('infulancer_id', $wrongPostUser->user_id)
                ->where('user_type', $wrongPostUser->user_type)
                ->where('post_id', $wrongPostUser->post_id)
                ->where('status', PostUser::AppliedStatus)
                ->first();
            if ($updatedPostPrices)
            {
                $newArray[] = [
                    'error'                             => $wrongPostUser->user_id != $updatedPostPrices->infulancer_id,
                    'PostPrice ids'                     => $updatedPostPrices->id,
                    '$wrongPostUser->user_type'         => $wrongPostUser->user_id,
                    '$updatedPostPrices->infulancer_id' => $updatedPostPrices->infulancer_id,
                    '$wrongPostUser->status'            => $wrongPostUser->status,
                    '$updatedPostPrices->status'        => $updatedPostPrices->status,
                    '$updatedPostPrices->final_price'   => $updatedPostPrices->final_price
                ];
                if (request('fix') == 1)
                {
                    $updatedPostPrices->update(['status' => PostUser::HiredStatus]);
                }
            }
        }
        return [
            'updateCount'   => $newArray,
            'newWrongCount' => PostUser::where('status', PostUser::HiredStatus)
                ->whereHas('influencerPriceBase', function ($influencerPriceBaseQuery)
                {
                    $influencerPriceBaseQuery->where('status', '=', PostUser::AppliedStatus);
                })->count()
        ];
    });
    Route::get('earngins-report', function ()
    {
        $postUsers = PostUser::with(['influencer', 'post'])->where('status', PostUser::HiredStatus)
//            ->where('created_at', '>', '2024-1-1 00:00:00')
            ->where('user_id', 72)
            ->latest()
            ->get();
        $wrongRecords = [];
        $amount = 0;
        foreach ($postUsers as $postUser)
        {
            $postPrice = PostPrice::where('infulancer_id', $postUser->user_id)
                ->where('user_type', $postUser->user_type)
                ->where('post_id', $postUser->post_id)
                ->where('status', PostUser::HiredStatus)
                ->exists();
            if (!$postPrice)
            {
                $wrongRecords[] = [
                    'influencer_id'     => $postUser->influencer->id,
                    'influencer_name'   => $postUser->influencer->name,
                    'business_id'       => $postUser->post->business->id,
                    'business_name'     => $postUser->post->business->name,
                    'campaign_name'     => $postUser->post->post_name,
                    'campaign_id'       => $postUser->post->id,
                    'amount'            => $postUser->reject_amount ?? $postUser->post->price_per_post,
                    'influencer_status' => $postUser->status_string_for_business,
                    'campaign_date'     => Carbon\Carbon::parse($postUser->post->created_at)->format('Y - m - d')
                ];
                if (request('fix'))
                {
                    PostPrice::create([
                        'user_id'           => $postUser->post->business->id,
                        'infulancer_id'     => $postUser->influencer->id,
                        'user_type'         => $postUser->post->user_type,
                        'post_id'           => $postUser->post->id,
                        'status'            => PostUser::HiredStatus,
                        'final_price'       => $postUser->reject_amount ?? $postUser->post->price_per_post,
                        'complementry_item' => null,
                        'is_resend'         => 0,
                        'price'             => $postUser->post->price_per_post,
                    ]);
                }
                $amount += $postUser->reject_amount ?? $postUser->post->price_per_post;
            }
        }
        return [
            '$wrongRecords'  => $wrongRecords,
            '$amount'        => $amount,
            'totalPostUser'  => PostUser::with(['influencer', 'post'])->where('status', PostUser::HiredStatus)
                ->where('created_at', '>', '2024-1-1 00:00:00')
                ->count(),
            'totalPostPrice' => PostPrice::where('status', PostUser::HiredStatus)
                ->where('created_at', '>', '2024-1-1 00:00:00')
                ->count(),
        ];
    });
    Route::get('test-add-notifications', function ()
    {


        $client = new Client();
        $request = $client->request(
            'GET',
            config('services.MPESA.MPESA_O_AUTH_URL'),
            ['auth' => [
                config("services.MPESA.MPESA_CONSUMER_KEY"),
                config("services.MPESA.MPESA_CONSUMER_SECRET")
            ]]
        );

        dd($request->getBody()->getContents());
        $winning_payout = WinningPayout::with('user')->latest()->first();
        dd($winning_payout->user);

        $winning_payout = WinningPayout::first();
        $response = PayoutPaymentService::processPayoutPayment($winning_payout);
        dd(json_decode($response->body()));
        //        $order = Order::find(526);
        //        $delivery_code_url = route('buyer.delivery.code.show.details', [
        //                'account' => $order->shop->domain,
        //                'delivery_code' => $order->delivery_code
        //            ]
        //        );
        //        $sms_content = $delivery_code_url;
        //        Notification::route(TwilioChannel::class, '+989100440338')
        //            ->notify(new NotifyBuyerOfDeliveryCodeOrder($sms_content));
        //
        //        dd('send');
        //        return view(
        //            'order::emails.orders.delivery-code',
        //            [
        //                'delivery_code_url' => 'delivery_code_url',
        //                'qr_code' => base64_encode(
        //                    \SimpleSoftwareIO\QrCode\Facades\QrCode::size(512)
        //                        ->format('png')
        //                        ->generate('delivery_code_url')
        //                ),
        //            ],
        //        );

        $order = Order::find(533);
        $temp_email = Mail::to('hossien@zaman.tech')->send(new OrderQrCodeDeliveryEmail($order));
        dd($temp_email);

        $qr_code = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(512)
            ->format('png')
            //            ->merge(filepath: '/public/admin/images/favicon/android-chrome-512x512.png', percentage: 0.2, absolute:false)
            ->generate(request()->url());
        //        return $qr_code;
        return "<img src='data:image/png;base64," . base64_encode($qr_code) . "'>";
        $code = Order::generateDeliveryCode();
        dd($code);

        $influencer = User::find(6379);
        $influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
        $influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
        $influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());

        $business = User::find(4351);
        $orders = OrderDetails::where('business_id', $business->id)->get();
        foreach ($orders as $order)
        {
            $business->notify(new NewOrderReceivedNotification($order->Order));
        }
        return "added";
    });
    Route::get('test-session', function ()
    {
        dd(Session::get('cart'), Session::get('transaction_id'), Auth::guard('buyer')->id(), Session::all());
    });
    Route::get('test-noti-user', function ()
    {
        $user = Notification::find(1);
        dd($user);
    });
    Route::get('test-otp', function ()
    {
        return $sendVerificationCode = TwiloVerifyServices::sendVerificationCode('+989056176337');
    });
    Route::get('test-admin', function ()
    {
        $products = Product::where('user_id', 4437)->pluck('id');
        return User::withTrashed()->with('influencerDetails', 'shop', 'influencerShopProducts', 'businessTotalSales')
            ->whereIn('id', function ($query) use ($products)
            {
                $query->select('influencer_id')->from('influencer_shop_product')
                    ->whereIn('product_id', $products);
            })->count();
    });
    Route::get('test-categories', function ()
    {
        Artisan::call('categories:remove-duplication');
    });

    Route::get('test-fcm', function ()
    {
        User::find(4351)->notify(new NewOrderReceivedNotification(Order::find(403)));
        return "done";
    });
    Route::get('split-orders', function ()
    {
        $orderDetails = OrderDetails::query()->take(20)->where('id', '!=', '367')->get();
        $InfluencerProductIds = $orderDetails->pluck('product_id')->toArray();
        $businessProductsIds = InfluencerShopProducts::whereIn('id', $InfluencerProductIds)->get()->pluck('product_id')->toArray();
        Product::whereIn('id', $businessProductsIds)->update(['user_id' => 4351]);
        OrderDetails::whereIn('id', $orderDetails->pluck('id')->toArray())->update(['business_id' => 4351]);
        Artisan::call('orders:split');
    });

    Route::get('/test-orders', function ()
    {
        $order = Order::where('id', '410')->first();
        dd($order->transactions);
    });
    Route::get('/add-orders-to-aziz', function ()
    {
        foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13] as $i)
        {
            $product = Product::create([
                'user_id'       => 4351,
                'name'          => 'test-product-' . $i,
                'color'         => '1,2,3,4',
                'specification' => 'specification1,specification2,specification3',
                'category'      => 421,
                'stock'         => 5,
                'price'         => random_int(100, 9000),
                'sku'           => '1234',
                'description'   => 'specification1,specification2,specification3',
            ]);
            ProductImage::create([
                'image'        => '1626680508.png',
                'is_cover_pic' => 1,
                'product_id'   => $product->id,

            ]);
            $shopProduct = InfluencerShopProducts::create([
                'shop_id'       => '7980',
                'product_id'    => $product->id,
                'product_title' => $product->name,
                'product_price' => $product->price
            ]);
            $order = Order::create([
                'order_status'    => random_int(0, 4),
                'order_amount'    => $product->price,
                'shipping_amount' => $product->price * 0.07,
                'address_id'      => 113,
                'payment_status'  => random_int(1, 2),
                'shop_id'         => 7980,

            ]);
            OrderDetails::create(
                [
                    'product_id'    => $shopProduct->id,
                    'order_id'      => $order->id,
                    'influencer_id' => 12527,
                    'business_id'   => 4351,
                    'quantity'      => 1,
                    'amount'        => $order->id
                ]
            );
        }
        return "added to aziz";
    });
    Route::get('test-notifications', function ()
    {
        User::find(4351)->notify(new TestAll());
        Buyer::find(61)->notify(new BuyerTestAll());
        return "done";
    });
    Route::get('/test-orders', function ()
    {
        $order = Order::where('id', '410')->first();
        dd($order->transactions);
    });
    Route::get('/test-flutterwave-verify', function ()
    {

        $res = FlutterWaveService::verfiyTransaction(Transaction::where('transaction_id', 'Flutterwave-ZNW2Q-VM7yM-p')->first(), '3977205');
        dd($res);
    });
    Route::get('/test-buyer-order-mail', function ()
    {
        $ordersIds = [request('order_id')];
        $orders = Order::whereIn('id', $ordersIds)->get();
        Notification::route('mail', $orders[0]->shippingAddress->email)
            ->route(TwilioChannel::class, '+' . str_replace('-', '', $orders[0]->shippingAddress->mobile))
            ->notify(new OrderHasBeenPlacedNotification($ordersIds));
        return 'mail sent';
    });
    Route::get('/test-instagram', function ()
    {
        InstagramServices::postToInstagram(User::find(12527), 344);
    });
    Route::get('/test-update-facebook', function ()
    {

        $users = User::with(['UserPlatforms' => function ($UserPlatformsQuery)
        {
            $UserPlatformsQuery->where('platform_id', Platform::FacebookID);
        }])->whereHas('UserPlatforms', function ($UserPlatformsQuery)
        {
            $UserPlatformsQuery
                ->where('platform_id', Platform::FacebookID)
                ->where('token', '!=', null)
                ->where('additional_info', null);
        })->get();
        dd($users);
    });
    if (app()->environment() != 'production')
    {
        Route::get('test-sms-notification', function ()
        {
            $buyer = Buyer::find(114);
            $business = User::find(3441);
            $express = Invoice::find(14);
            Notification::route(TwilioChannel::class, $buyer->mobile)
                ->notifyNow(new SendLinkInvoiceNotification($express, $business));
            dd([
                'buyer'     => $buyer,
                '$business' => $business,
                '$express'  => $express
            ]);
        });
    }
}


Route::get('test/twitter', function ()
{
    $storyPostID = request('story_post_id') ?? 387;

    $story = StoryPost::find($storyPostID);
    $files = $story->absolute_images->implode(' ');
    // $buy_link = InstagramServices::prepareBuyLink($story, $influencer);
    $buy_link = "Hello it is me, How are you doing now, i miss you, do you love me!";

    $generate_video = new GenerateVideoService($files);
    dd($generate_video);
    $generate_video->generateVideoCommand();
    // $command = $generate_video->getCommand();
    $output_1 = $generate_video->runCommand();

    $generate_video->addSubtitle($buy_link);
    $generate_video->generateVideoWithSubtitleCommand();
    // $command_with_subtile = $generate_video->getCommandWithSubtile();
    $output_2 = $generate_video->runCommand(true);

    dd($generate_video, $output_1, $output_2);
    // return \Modules\Platform\Services\TwitterServices::tweet(User::find($influencerID), $storyPostID, true);
});

Route::get('test1', function(){
    $buyer =Buyer::find(72);
    $buyer->notify(new CartReminderNotification());
    Artisan::call('reminders:send-cart');
});
