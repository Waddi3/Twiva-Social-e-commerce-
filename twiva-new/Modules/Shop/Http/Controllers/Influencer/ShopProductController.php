<?php

namespace Modules\Shop\Http\Controllers\Influencer;

use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Shop\Entities\ShoppingCart;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Repositories\Influencer\BuyerRepository;
use Modules\Shop\Repositories\Influencer\ProductRepository;

class ShopProductController extends Controller
{
    use ResponseTrait;

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function orderBy()
    {
        $products = $this->productRepository->getProductInformationSortedBy();
        $view = view('shop::components.views._products', ['products' => $products, 'card_products' => Session::get('cart')])->render();
        return response(['products' => $view, 'auth_id' => session()->get('buyer_id'), 'card_products' => Session::get('cart')]);
    }

    public function searchProducts()
    {
        $products = $this->productRepository->searchProducts();
        $view = view('shop::components.views._products', ['products' => $products, 'card_products' => Session::get('cart') ?? []])->render();
        return response(['products' => $view, 'auth_id' => session()->get('buyer_id'), 'card_products' => Session::get('cart') ?? []]);
    }

    public function show($account, $product, $share_links)
    {
        $categories = Category::with('subcategories')->latest()->get();
        return view('shop::influencer.product',
            [
                'product' => $this->productRepository->getProductInformation($product),
                'products' => $this->productRepository->getSimilarProducts(),
                'productId' => $product,
                'productCount' => session()->get('cart')[$product]['quantity'] ?? 1,
                'share_links' => $share_links,
                'categories' => $categories
            ]);
    }


    public function buyNow($account, $product_id)
    {
        $cart = Session::get('cart');
        $validated['product_id'] = $product_id;

        $influencer_product = InfluencerShopProducts::with('product:id,size,color')->select('id', 'product_title', 'product_price', 'product_id')
            ->where('id', $validated['product_id'])->first();
        $color_id = null;
        $size_id = null;
        if ($influencer_product) {
            if ($influencer_product?->product?->color_details and count($influencer_product?->product?->color_details) and count($influencer_product?->product?->color_details) == 1) {
                $color_id = $influencer_product?->product?->color_details->first()?->id;
            }
            if ($influencer_product?->product?->size_details and count($influencer_product?->product?->size_details) and count($influencer_product?->product?->size_details) == 1) {
                $size_id = $influencer_product?->product?->size_details->first()?->id;
            }
        }

        if (!isset($validated['quantity'])) {
            $validated['quantity'] = 1;
            $validated['color_id'] = $color_id;
            $validated['size_id'] = $size_id;
            $validated['shop_domain'] = $account;
        }
        $cart[$validated['product_id']] = $validated;
        Session::put('cart', $cart);
        Session::save();

        return redirect()->route('buyer.checkout.account', ['account' => $account]);
    }
}
