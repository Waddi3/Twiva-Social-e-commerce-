<?php

namespace Modules\Shop\Http\Controllers\Business;

use Illuminate\Routing\Controller;
use Modules\Home\Entities\Category;
use Modules\Shop\Repositories\Business\ShopRepository;

class ShopController extends Controller {
    private ShopRepository $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function index($account)
    {
        $shop = $this->shopRepository->getShop();
        if (!$shop || !$shop->user)
        {
            abort(404);
        }
        return view('shop::influencer.index', [
            'shop'               => $shop,
            'user'               => $shop->user,
            'hasServicesProduct' => $this->shopRepository->hasServicesProducts(),
            'cards'              => [],
            'card_products'      => [],
            'products'           => [],
            'tags'               => [],
        ]);

    }


}
