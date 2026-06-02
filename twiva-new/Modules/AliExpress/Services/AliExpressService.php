<?php

namespace Modules\AliExpress\Services;

use Modules\AliExpress\Services\Traits\AliExpressAuthorizeServiceTrait;
use Modules\AliExpress\Services\Traits\AliExpressBasicServiceTrait;
use Modules\AliExpress\Services\Traits\AliExpressCategoriesServiceTrait;
use Modules\AliExpress\Services\Traits\AliExpressOrderServiceTrait;
use Modules\AliExpress\Services\Traits\AliExpressProductsServiceTrait;
use Psr\Log\LoggerInterface;

class AliExpressService
{
    use AliExpressAuthorizeServiceTrait;
    use AliExpressBasicServiceTrait;
    use AliExpressCategoriesServiceTrait;
    use AliExpressProductsServiceTrait;
    use AliExpressOrderServiceTrait;

    protected $configs;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->configs = config('twiva.ali-express');
    }
}
