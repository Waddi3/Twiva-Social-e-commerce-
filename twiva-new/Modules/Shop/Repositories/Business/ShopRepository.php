<?php


namespace Modules\Shop\Repositories\Business;


use Illuminate\Support\Facades\Notification;
use LaravelIdea\Helper\Modules\Influencer\Entities\_IH_Product_C;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Shop\Entities\Shops;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenPlaced;
use Modules\Notification\Notifications\Buyer\OrderHasBeenPlacedNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Order\Entities\Order;
use NotificationChannels\Twilio\TwilioChannel;

class ShopRepository {
    private ?string $account;
    private ?Shops $shop;

    public function __construct()
    {
        $this->account = request('account');
        $this->shop = $this->getShop();
    }

    public function getShop()
    {
        return Shops::where(['is_active' => 1, 'deleted_at' => null, 'domain' => $this->account])->first();
    }

    public function shopProducts()
    {
        return $this->getProductsQuery()->get();
    }

    public function hasServicesProducts(): bool
    {
        return Product::where('user_id', $this->shop->user_id)
            ->whereHas('eventTicketProduct')
            ->exists();
    }

    public function getProductsQuery()
    {
        return Product::select('id', 'user_id', 'name', 'price', 'retail_price', 'discount', 'stock', 'brand', 'is_ali_express as product_is_ali_express')
            ->where(['user_id' => $this->shop->user_id, 'status' => 1])
            ->where('stock', '>', 0);
    }

    public function getSimilarProducts()
    {
        return $this->getProductsQuery()->inRandomOrder()->take(4)->get();
    }

    // API
    public static function updateBusinessShop($data)
    {
        $shop = Shops::where('user_id', auth('api')->id())->first();
        if (isset($data['shop_cover_image_path']))
        {
            $data['shop_cover_image_path'] = $data['shop_cover_image_path']->store(Shops::COVER_IMAGE_PATH, 'public');
            $data['shop_cover_image_path'] = str_replace(Shops::COVER_IMAGE_PATH . '/', '', $data['shop_cover_image_path']);
        }
        if (isset($data['shop_logo_path']))
        {
            $data['shop_logo_path'] = $data['shop_logo_path']->store(Shops::LOGO_PATH, 'public');
            $data['shop_logo_path'] = str_replace(Shops::LOGO_PATH . '/', '', $data['shop_logo_path']);
        }
        $shop->update($data);
        return $shop;
    }

}
