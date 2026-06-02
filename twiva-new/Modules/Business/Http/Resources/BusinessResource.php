<?php

namespace Modules\Business\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    private $business;

    public function __construct($business)
    {
        $this->business = $business;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->business->id,
            'business_name' => $this->business->name,
            'type' => $this->business->type,
            'name' => $this->business->name,
            'contact_name' => $this->business->businessDetails->contact_name,
            'logo' => $this->business->profile_image,
            'referral_code' => $this->business->referral_code,
            'phone_number' => $this->business->phone_number,
            'phone_code' => $this->business->phone_code,
            'email' => $this->business->email,
            'description' => $this->business->description,
            'country' => $this->business->businessDetails->countryDetails?->only('id', 'name') ?? [],
            'city' => $this->business->businessDetails->cityDetails?->only('id', 'name') ?? [],
            'state' => $this->business->businessDetails->stateDetails?->only('id', 'name') ?? [],
            'area' => $this->business->businessDetails->area,
            'shipping_by_partner' => !is_null($this->business->businessShipping) ? $this->business?->businessShipping?->shipping_by_partner : null,
            'has_shipping_fees' => !is_null($this->business->businessShipping) ? $this->business?->businessShipping?->has_shipping_fees : null,
            'pay_on_delivery' => !is_null($this->business->businessShipping) ? $this->business?->businessShipping?->pay_on_delivery : null,
            'pass_withdraw' => $this->business->pass_withdraw,
//            'exportShopProductsLink' => route('business.product.exportCSV', ['influencer_id' => base64_encode(base64_encode($this->influencer->id))])

        ];
    }
}
