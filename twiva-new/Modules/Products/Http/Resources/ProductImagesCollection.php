<?php

namespace Modules\Products\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Entities\User;

/** @see \Modules\Influencer\Entities\InfluencerShopProducts */
class ProductImagesCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth('api')->user();

        $images = $user->type == User::InfluencerType ? 'BusinessProductImages' : 'productImages';

        if ($user->type == User::BusinessType) {
            $this->influencerProductImages = collect();
        }
        return [
            'influencer_images' => $this->influencerProductImages->map(function ($image) {
                $image->imageUrl = $image->imageUrl;
                return collect($image)->only('imageUrl');
            }),
            'business_images' => $this->$images->map(function ($image) {
                $image->imageUrl = $image->imageUrl;
                return collect($image)->only('imageUrl');
            }),
        ];

    }
}
