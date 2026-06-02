<?php

namespace Modules\Shop\Http\Controllers\Business;

use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Jorenvh\Share\Share;
use Kudashevs\ShareButtons\ShareButtons;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\Product;
use Modules\Shop\Repositories\Business\ProductRepository;

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
        $categories = Category::with('subcategories')->latest()->get();
        $productInformation = $this->productRepository->getProductInformation($product);
        return view('shop::business.product',
            [
                'product' => $productInformation,
                'products' => $this->productRepository->getSimilarProducts(),
                'productId' => $product,
                'productCount' => Session::get('cart')[$product]['quantity'] ?? 1,
                'share_links' => $share_links,
                'categories' => $categories
            ]);
    }

    public function buyNow($account, $product_id)
    {
        $cart = Session::get('cart');
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
