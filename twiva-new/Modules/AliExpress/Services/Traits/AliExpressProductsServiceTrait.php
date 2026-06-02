<?php


namespace Modules\AliExpress\Services\Traits;

trait AliExpressProductsServiceTrait
{

    public function fetchVariantProduct($parameters)
    {

        $api = 'aliexpress.ds.product.get';
        $response = $this->calApi($api, $parameters);
        return $response;
    }

    public function fetchFeedName($parameters)
    {

        $api = 'aliexpress.ds.feedname.get';
        $response = $this->calApi($api, $parameters);
        return $response;
    }

    public function productsListByFeedName($parameters)
    {

        $api = 'aliexpress.ds.recommend.feed.get';
        $response = $this->calApi($api, $parameters);
        return $response;
    }
}
