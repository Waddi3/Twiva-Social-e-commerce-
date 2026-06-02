<?php


namespace Modules\AliExpress\Services\Traits;

trait AliExpressCategoriesServiceTrait
{

    public function getCategoriesList($parameters)
    {
        $api = 'aliexpress.ds.category.get';
        $response = $this->calApi($api, $parameters);
        return $response;
    }
}
