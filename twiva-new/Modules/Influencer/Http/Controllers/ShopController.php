<?php

namespace Modules\Influencer\Http\Controllers;

use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Modules\Influencer\Http\Requests\InfluncerDomainRequest;

class ShopController extends Controller
{
    use ResponseTrait;

    public function isShopAvailable(InfluncerDomainRequest $request)
    {
        return response(['message' => $request->validated()['domain'].".twiva.co.ke is avalibale"]);
    }

}
