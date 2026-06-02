<?php


namespace Modules\AliExpress\Services\Traits;

trait AliExpressOrderServiceTrait
{

    public function freightQuery($parameters)
    {
        $api = 'aliexpress.ds.freight.query';
        $response = $this->calApi($api, $parameters);
        return $response;
    }

    public function createOrder($parameters)
    {
        $api = 'aliexpress.ds.order.create';
        $response = $this->calApi($api, $parameters);
        return $response;
    }

    public function placeOrder($parameters)
    {
        $api = 'aliexpress.trade.buy.placeorder';
        $response = $this->calApi($api, $parameters);
        return $response;
    }

    public function addressGet($parameters)
    {
        $api = 'aliexpress.ds.address.get';
        $response = $this->calApi($api, $parameters);
        return $response;
    }

    public function logisticsBuyerFreightCalculate($parameters)
    {
        $api = 'aliexpress.logistics.buyer.freight.calculate';
        $response = $this->calApi($api, $parameters);
        return $response;
    }

    public function orderTracking($parameters)
    {
        $api = 'aliexpress.ds.order.tracking.get';
        $response = $this->calApi($api, $parameters);
        return $response;
    }
}
