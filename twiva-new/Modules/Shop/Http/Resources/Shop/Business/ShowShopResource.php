<?php

namespace Modules\Shop\Http\Resources\Shop\Business;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShowShopResource extends ResourceCollection
{
    private $shop;

    public function __construct($shop)
    {
        parent::__construct($shop);
        $this->shop = $shop;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->shop->id,
            'eshop_name' => $this->shop->shop_name,
            'cover_image_path' => $this->shop->coverImage,
            'logo_path' => $this->shop->logoImage,
            'description' => $this->shop->shop_description,
            'domain' => $this->shop->domain,
            'facebook_domain_verification_code' => $this->shop->facebook_domain_verification_code,
        ];
    }
}
