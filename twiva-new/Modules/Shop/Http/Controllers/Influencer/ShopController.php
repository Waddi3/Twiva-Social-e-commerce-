<?php

namespace Modules\Shop\Http\Controllers\Influencer;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\SubCategories;
use Modules\Products\Repositories\ECommerce\ECommerceCategoryRepository;
use Modules\Shop\Repositories\Influencer\ShopRepository;

class ShopController extends Controller
{

    private ShopRepository $shopRepository;
    private ShopRepository $categoryRepository;


    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function index($account)
    {
        $shop = $this->shopRepository->ShopData();
        if (!$shop || !$shop->influencer)
        {
            abort(404);
        }
        $badges = $shop->influencer->verifiedBadges;
        $show_more_badges = $shop->influencer->verifiedBadges->splice(6);
        // dd($badges, $show_more_badges);
        return view('shop::influencer.index', [
            'tags' => $shop->influencer->interests->take(5)->pluck('name')->toArray() ?? [],
            'shop' => $shop,
            'categories' => Category::with('subcategories')->latest()->get(),
            'user' => $shop->influencer,
            'badges' => $badges,
            'show_more_badges' => $show_more_badges,
            'hasServicesProduct' => $this->shopRepository->hasServicesProducts(),
            'products' => [],
            'cards' => [],
            'card_products' => [],
        ]);
    }

    public function products($account)
    {
        // $categories = Category::with('subcategories')->latest()->get();
        $subCategories = SubCategories::select(['id', 'name'])->get();
        $selectedCategory = isset(request()->category) ? request()->category : 0;
        $selectedSubCategory = isset(request()->sub_category) ? request()->sub_category : 0;

        // added for new changes
        $categoryRepository = new ECommerceCategoryRepository();
        $categories = $categoryRepository->getSubCategories(request()->all());

        $shop = $this->shopRepository->ShopData();
        if (!$shop || !$shop->influencer)
        {
            abort(404);
        }
        $products = $shop->products()
            ->with('productDetails:id,price,retail_price,discount,brand')
            ->whereHas('actualDetail', function ($wehe_has_actual_detail)
            {
                $wehe_has_actual_detail->where('stock', '>', 0);
            });
        if (request('keyWord'))
        {
            $products->where(function ($where)
            {
                $where->where('product_title', 'LIKE', '%' . request('keyWord') . '%')
                    ->orWhereHas('product', function ($where_has_product)
                    {
                        $where_has_product->where('brand', 'LIKE', '%' . request('keyWord') . '%');
                    });
            });
        }
        if (request('category_id') || request('category'))
        {
            $products->whereHas('product', function ($productDetails)
            {
                $productDetails->where('category', request('category_id'));
            });
        }

        return view('shop::influencer.products', [
            'tags' => $shop->influencer->interests->take(5)->pluck('name')->toArray() ?? [],
            'shop' => $shop,
//            'products' => $shop->products,
            'products' => $products->get(), //->paginate(2),
            'user' => $shop->influencer,
            'cards' => [],
            'card_products' => [],
            'categories' => $categories,
            'subCategories' => $subCategories,
            'selectedCategory' => $selectedCategory,
            'selectedSubCategory' => $selectedSubCategory,

        ]);
    }


}
