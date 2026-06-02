<?php

namespace Modules\Shop\Http\Controllers\ECommerce;

use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Jorenvh\Share\Share;
use Kudashevs\ShareButtons\ShareButtons;
use Modules\Influencer\Entities\Product;
use Modules\Shop\Repositories\Business\ProductRepository;

class ECommerceBusinessShopProductController extends Controller
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
        return response(['products' => $view, 'auth_id' => Session::get('buyer_id'), 'card_products' => Session::get('cart')]);
    }

    public function searchProducts()
    {
        $products = $this->productRepository->searchProducts();
        $view = view('shop::components.views._products', ['products' => $products, 'card_products' => Session::get('cart') ?? []])->render();
        return response(['products' => $view, 'auth_id' => Session::get('buyer_id'), 'card_products' => Session::get('cart') ?? []]);
    }

    public function show($account, $product, $share_links)
    {
        return view(
            'shop::business.product',
            [
                'product' => $this->productRepository->getProductInformation($product),
                'products' => $this->productRepository->getSimilarProducts(),
                'productId' => $product,
                'productCount' => Session::get('cart')[$product]['quantity'] ?? 1,
                'share_links' => $share_links,
            ]
        );
    }

    public function buyNow($account, $product_id, $user_type)
    {
        $cart = Session::get('cart') ?? [];
        $validated['product_id'] = $product_id;

        $product = Product::select('id', 'size', 'color')
            ->where('id', $validated['product_id'])->first();

        $color_id = null;
        $size_id = null;
        if ($product) {
            if (count($product->colorDetails) and count($product->colorDetails) == 1) {
                $color_id = $product->colorDetails->first()?->id;
            }
            if (count($product->sizeDetails) and count($product->sizeDetails) == 1) {
                $size_id = $product->sizeDetails->first()?->id;
            }
        }


        $validated['shop_domain'] = $account;
        $validated['user_type'] = $user_type;

        if (array_key_exists($product_id, $cart)) {
            $validated['quantity'] = $cart[$validated['product_id']]['quantity'] + 1;
            $validated['color_id'] = $cart[$validated['product_id']]['color_id'] ?? null;
            $validated['size_id'] = $cart[$validated['product_id']]['size_id'] ?? null;
        } else {
            $validated['quantity'] = 1;
            $validated['color_id'] = $color_id;
            $validated['size_id'] = $size_id;
        }
        $cart[$validated['product_id']] = $validated;
        Session::put('cart', $cart);
        Session::save();

        return redirect()->route('e-commerce.buyer.checkout.account');
    }
}
