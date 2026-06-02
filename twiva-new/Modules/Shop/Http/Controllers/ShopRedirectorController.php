<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Kudashevs\ShareButtons\ShareButtons;
use Modules\Products\Services\ProductService;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Controllers\Business\ShopController as BusinessShopController;
use Modules\Shop\Http\Controllers\Business\ShopProductController as BusinessShopProductController;
use Modules\Shop\Http\Controllers\Influencer\ShopController as InfluencerShopController;
use Modules\Shop\Http\Controllers\Influencer\ShopProductController as InfluencerShopProductController;
use Modules\Shop\Repositories\Business\ProductRepository as BusinessShopProductRepository;
use Modules\Shop\Repositories\Business\ShopRepository as BusinessShopRepository;
use Modules\Shop\Repositories\Influencer\ProductRepository as InfluencerShopProductRepository;
use Modules\Shop\Repositories\Influencer\ShopRepository as InfluencerShopRepository;
use Modules\User\Entities\User;

class ShopRedirectorController extends Controller {
    private $businessShopController;
    private $businessShopProductController;
    private $businessShopProductRepository;

    private $influencerShopController;
    private $influencerShopProductController;
    private $influencerShopProductRepository;

    public function __construct(
        BusinessShopRepository          $businessShopRepository,
        InfluencerShopRepository        $influencerShopRepository,
        BusinessShopProductRepository   $businessShopProductRepository,
        InfluencerShopProductRepository $influencerShopProductRepository,

    ) {
        $this->businessShopController = new BusinessShopController($businessShopRepository);
        $this->businessShopProductController = new BusinessShopProductController($businessShopProductRepository);

        $this->influencerShopProductController = new InfluencerShopProductController($influencerShopProductRepository);
        $this->influencerShopController = new InfluencerShopController($influencerShopRepository);
    }

    private function shopData($account)
    {
        return Shops::select('id', 'user_id')->with('user')->where('domain', $account)->first();
    }

    public function index($account)
    {
        Session::remove('address_id');
        Session::remove('city_id');
        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false)
        {
            return $this->influencerShopController->index($account);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false)
        {
            return $this->businessShopController->index($account);
        }
        abort(404);
    }

    public function products($account)
    {
        Session::remove('address_id');
        Session::remove('city_id');
        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false)
        {
            return $this->influencerShopController->products($account);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false)
        {
            return $this->businessShopController->index($account);
        }
        abort(404);
    }

    public function show($account, $product)
    {
        updateInfoProductAliExpress($product);
        $shareLinks = $this->getShareLinks();
        $buyer_id = auth('buyer')->id();
        $user_type = $this->shopData($account)?->user?->type;
        $session = session()->getId();
        ProductService::increaseViewProduct($product, $user_type, $buyer_id, $session);
        if ($user_type == User::InfluencerType)
        {
            return $this->influencerShopProductController->show($account, $product, $shareLinks);
        }
        else if ($user_type == User::BusinessType)
        {
            return $this->businessShopProductController->show($account, $product, $shareLinks);
        }
        abort(404);
    }

    public function buyNow($account, $product)
    {
        updateInfoProductAliExpress($product);
        if ($this->shopData($account)?->user?->type == User::InfluencerType)
        {
            return $this->influencerShopProductController->buyNow($account, $product);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType)
        {
            return $this->businessShopProductController->buyNow($account, $product);
        }
        abort(404);
    }

    public function orderBy($account)
    {
        if ($this->shopData($account)?->user?->type == User::InfluencerType)
        {
            return $this->influencerShopProductController->orderBy($account);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType)
        {
            return $this->businessShopProductController->orderBy($account);
        }
        abort(404);
    }

    public function searchProducts($account)
    {
        if ($this->shopData($account)?->user?->type == User::InfluencerType)
        {
            return $this->influencerShopProductController->searchProducts($account);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType)
        {
            return $this->businessShopProductController->searchProducts($account);
        }
        abort(404);
    }

    /**
     * @return string
     */
    public function getShareLinks(): string
    {
        $share = new ShareButtons();
        $share_links = $share->page(route('shop.product.show', ['account' => request('account'), 'product' => request('product')]), 'Twiva', [
            'title' => 'Twiva',
            'rel'   => 'nofollow noopener noreferrer',
        ])
            ->whatsapp(['title' => 'Whatsapp'])
            ->telegram(['title' => 'Telegram'])
            ->facebook(['title' => 'Facebook'])
            ->mailto(['title' => 'Email'])
            ->twitter(['title' => 'X'])
            ->linkedin(['title' => 'Linkedin'])
            ->copylink(['title' => 'Copy link'])
            ->render();
        return $share_links;
    }
}
