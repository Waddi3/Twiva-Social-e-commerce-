<?php
namespace Modules\AliExpress\Repositories;

use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class AliExpressRepository {
    public function checkIfProductsBelongToAliExpressAndReturnNames(string $userType, array $productIds): string
    {
        if ($userType == User::InfluencerType)
        {
            return InfluencerShopProducts::with('actualDetail')
                ->select(['id', 'product_id', 'product_title'])
                ->whereIn('id', $productIds)
                ->whereHas('actualDetail', function ($actualDetailQuery)
                {
                    $actualDetailQuery->where('is_ali_express', true);
                })
                ->pluck('product_title')
                ->implode(', ');
        }
        return Product::select(['id', 'name', 'user'])
            ->whereIn('id', $productIds)
            ->where('is_ali_express', true)
            ->pluck('name')
            ->implode(', ');
    }
}