<?php

namespace Modules\Shop\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Controllers\API\ShopSite\V1\Business\CartController as BusinessCartController;
use Modules\Shop\Http\Controllers\API\ShopSite\V1\Influencer\CartController as InfluencerCartController;
use Modules\Shop\Http\Requests\ShopSite\BusinessSite\DeleteCardRequest;
use Modules\Shop\Http\Requests\ShopSite\InfluencerSite\CreateCardRequest as InfluencerCreateCardRequest;
use Modules\Shop\Http\Requests\ShopSite\BusinessSite\CreateCardRequest as BusinessCreateCardRequest;

use Modules\Shop\Http\Requests\ShopSite\InfluencerSite\UpdateCardRequest as InfluencerUpdateCardRequest;
use Modules\Shop\Http\Requests\ShopSite\BusinessSite\UpdateCardRequest as BusinessUpdateCardRequest;

use Modules\Shop\Http\Requests\ShopSite\InfluencerSite\DeleteCardRequest as InfluencerDeleteCardRequest;
use Modules\Shop\Http\Requests\ShopSite\BusinessSite\DeleteCardRequest as BusinessDeleteCardRequest;


use Modules\User\Entities\User;

class ShopCartRedirectorController extends Controller
{
    use ResponseTrait;

    private $InfluencerShopCartController;
    private $BusinessShopCartController;

    public function __construct()
    {
        $this->InfluencerShopCartController = new InfluencerCartController();
        $this->BusinessShopCartController = new BusinessCartController();
    }

    private function shopData($account)
    {
        return Shops::select('id', 'user_id')->with('user')->where('domain', $account)->first();
    }

    public function index($account)
    {
        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false)
        {
            return $this->InfluencerShopCartController->index($account);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false)
        {
            return $this->BusinessShopCartController->index($account);
        }
        abort(404);
    }

    public function create($account, Request $request)
    {
        Session::remove('address_id');
        Session::remove('city_id');
        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false)
        {
            $request = InfluencerCreateCardRequest::createFrom($request);
            return $this->InfluencerShopCartController->create($request);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false)
        {
            $request = BusinessCreateCardRequest::createFrom($request);
            return $this->BusinessShopCartController->create($request);
        }
        abort(404);
    }

    public function update($account, Request $request)
    {
        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false)
        {
            $request = InfluencerUpdateCardRequest::createFrom($request);
            return $this->InfluencerShopCartController->update($request);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false)
        {
            $request = BusinessUpdateCardRequest::createFrom($request);
            return $this->BusinessShopCartController->update($request);
        }
        abort(404);
    }


    public function delete($account, Request $request)
    {
        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false)
        {
            $request = InfluencerDeleteCardRequest::createFrom($request);
            return $this->InfluencerShopCartController->delete($request);
        }
        else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false)
        {
            $request = BusinessDeleteCardRequest::createFrom($request);
            return $this->BusinessShopCartController->delete($request);
        }
        abort(404);
    }

    public function updateAddress($account, Request $request) {
        // Partner
        if( $request->has('partner_id') && $request->partner_id !== null ) {
            Session::put('partner_id', $request->partner_id);
        }

        if( $request->has('city_id') && $request->city_id !== null ) {
            Session::put('city_id', $request->city_id);
            return response()->json([
                'status' => true,
                'message' => 'Shipping address updated'
            ]);
        }

        if( $request->has('address_id') && $request->address_id !== null ) {
            try {
                Session::remove('city_id');
                Session::put('address_id', $request->address_id);
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping address updated'
                ]);
            }
            catch(Exception $exception) {
                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage()
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Shipping address is required'
        ]);
    }

}
