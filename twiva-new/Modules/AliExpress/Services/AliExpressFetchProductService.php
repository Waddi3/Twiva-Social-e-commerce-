<?php

namespace Modules\AliExpress\Services;

use Modules\AliExpress\Services\Traits\AliExpressBasicServiceTrait;
use Psr\Log\LoggerInterface;

class AliExpressFetchProductService
{

    use AliExpressBasicServiceTrait;

    protected $configs;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->configs = config('twiva.ali-express');
    }

    public function search($parameters)
    {
        $api = 'aliexpress.ds.text.search';
        $response = $this->calApi($api, $parameters);
        return $response;
    }
}
