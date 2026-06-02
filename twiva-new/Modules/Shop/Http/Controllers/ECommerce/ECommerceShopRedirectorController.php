<?php

namespace Modules\Shop\Http\Controllers\ECommerce;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Kudashevs\ShareButtons\ShareButtons;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\SubCategories;
use Modules\Products\Services\ProductService;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Controllers\Business\ShopController as BusinessShopController;
use Modules\Shop\Http\Controllers\ECommerce\ECommerceInfluencerShopProductController;
use Modules\Shop\Http\Controllers\Influencer\ShopController as InfluencerShopController;
use Modules\Shop\Repositories\Business\ProductRepository as BusinessShopProductRepository;
use Modules\Shop\Repositories\Business\ShopRepository as BusinessShopRepository;
use Modules\Shop\Repositories\Influencer\ProductRepository as InfluencerShopProductRepository;
use Modules\Shop\Repositories\Influencer\ShopRepository as InfluencerShopRepository;
use Modules\User\Entities\User;

class ECommerceShopRedirectorController extends Controller
{
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
        $this->businessShopProductController = new ECommerceBusinessShopProductController($businessShopProductRepository);

        $this->influencerShopProductController = new ECommerceInfluencerShopProductController($influencerShopProductRepository);
        $this->influencerShopController = new InfluencerShopController($influencerShopRepository);
    }

    private function shopData($account)
    {
        return Shops::select('id', 'user_id')->with('user')->where('domain', $account)->first();
    }

    // public function index($account)
    // {
    //     Session::remove('address_id');
    //     Session::remove('city_id');
    //     if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false) {
    //         return $this->influencerShopController->index($account);
    //     } else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false) {
    //         return $this->businessShopController->index($account);
    //     }
    //     abort(404);
    // }

    public function show($product_id)
    {

        $categories = Category::with('subcategories')->latest()->get();
        $subCategories = SubCategories::select(['id', 'name'])->get();
        $selectedCategory = isset(request()->category) ? request()->category : 0;
        $selectedSubCategory = isset(request()->sub_category) ? request()->sub_category : 0;

        $user_type = request('user_type') ?? User::InfluencerType;

        if (is_null($user_type)) {
            abort(404);
        }

        $share = new ShareButtons();
        $share_links = $share->page(route('e-commerce.shop.product.show', ['account' => request('account'), 'product' => request('product'), 'user_type' => $user_type]), 'Twiva', [
            'title' => 'Twiva',
            'rel' => 'nofollow noopener noreferrer',
        ])
            ->whatsapp(['title' => 'Whatsapp'])
            ->telegram(['title' => 'Telegram'])
            ->facebook(['title' => 'Facebook'])
            ->mailto(['title' => 'Email'])
            ->twitter(['title' => 'X'])
            ->linkedin(['title' => 'Linkedin'])
            ->copylink(['title' => 'Copy link'])
            ->render();
        $buyer_id = auth('buyer')->id();


        if ($user_type == User::InfluencerType) {
            $product_query = InfluencerShopProducts::select([
                'id',
                'product_title',
                'product_title as name',
                'influencer_id',
                'influencer_id as user_id',
                'views',
                'product_description',
                'product_description as description',
                'created_at',
                'product_id',
                'shop_id',
                'product_price as retail_price',
                'product_price'
            ])
                ->with([
                    'user:id,name,type,profile_image,privilege',
                    'user.shop:id,user_id,shop_name,domain',
                    'productImages',
                    'productMainImage' => function ($query) {
                        return $query->select('shop_product_id', 'image_path', 'is_cover_pic');
                    },
                    'actualDetail'  => function ($query) {
                        return $query->select('id', 'specification', 'color', 'size', 'price', 'retail_price', 'discount', 'brand', 'is_ali_express as product_is_ali_express');
                    },
                ])
                ->whereHas('user')
                ->whereHas('actualDetail.user')
                ->whereHas('actualDetail')
                ->where('status', 1);
        } else {
            $product_query = Product::select('id', 'name', 'price', 'retail_price', 'description', 'color', 'size', 'created_at', 'user_id', 'discount', 'status', 'brand', 'views', 'is_ali_express as product_is_ali_express')
                ->where('status', 1)
                ->whereHas('user')
                ->with(['user:id,name,type,profile_image,privilege', 'user.shop:id,user_id,shop_name,domain']);
        }

        $products = (clone $product_query)->inRandomOrder()->take(4)->get();
        $product = $product_query->where('id', $product_id)->firstOrFail()->append(['color_details']);

        // $account = getAccountDomainFromProductId($product, $user_type);
        // if (is_null($account)) {
        //     abort(404);
        // }
        $session = session()->getId();
        ProductService::increaseViewProduct($product_id, $user_type, $buyer_id, $session);

        return view('front::e-commerce.pages.show-product', [
            'share_links' => $share_links,
            'user_type' => $user_type,
            'product' => $product,
            'products' => $products,
            'productId' => $product_id,
            'productCount' => session()->get('cart')[$product_id]['quantity'] ?? 1,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'selectedCategory' => $selectedCategory,
            'selectedSubCategory' => $selectedSubCategory,
        ]);
    }

    public function buyNow($product)
    {
        $user_type = request('user_type');
        $account = getAccountDomainFromProductId($product, $user_type);
        if (is_null($account)) {
            abort(404);
        }
        $user_type = $this->shopData($account)?->user?->type;
        if ($user_type == User::InfluencerType) {
            return $this->influencerShopProductController->buyNow($account, $product, $user_type);
        } else if ($user_type == User::BusinessType) {
            return $this->businessShopProductController->buyNow($account, $product, $user_type);
        }
        abort(404);
    }

    // public function orderBy($account)
    // {
    //     if ($this->shopData($account)?->user?->type == User::InfluencerType) {
    //         return $this->influencerShopProductController->orderBy($account);
    //     } else if ($this->shopData($account)?->user?->type == User::BusinessType) {
    //         return $this->businessShopProductController->orderBy($account);
    //     }
    //     abort(404);
    // }

    // public function searchProducts($account)
    // {
    //     if ($this->shopData($account)?->user?->type == User::InfluencerType) {
    //         return $this->influencerShopProductController->searchProducts($account);
    //     } else if ($this->shopData($account)?->user?->type == User::BusinessType) {
    //         return $this->businessShopProductController->searchProducts($account);
    //     }
    //     abort(404);
    // }
}
