<?php

namespace Modules\Payment\Http\Resources\Influencer\Earnings;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Modules\Order\Entities\Order */
class ShopEarningsCollection extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'rating' => $this->productDetails->ratings,
            'product_details' => $this->makeProductDetails(),
            'product_images' => $this->productImages()
        ];
    }

    private function productImages()
    {
        $influencerImages = $this->productImages->map(function ($productImage) {
            $productImage->image_path = $productImage->image_url;
            return collect($productImage)->only(['id', 'image_path', 'is_cover_pic']);
        });
        $businessImages = $this->productDetails->actualDetail()->withTrashed()->first()->productImages->map(function ($productImage) {
            $productImage->image_path = $productImage->image_url;
            return collect($productImage)->only(['id', 'image_path', 'is_cover_pic']);
        });
        $images = collect([...$influencerImages->toArray(), ...$businessImages->toArray()]);
        if ($images->count() && !in_array('1', $images->pluck('is_cover_pic')->toArray()))
        {
            $images[0]->is_cover_pic = 1;
        }
        return $images;
    }

    private function makeProductDetails()
    {
        return (object)[
            'id' => $this->productDetails->id,
            'product_title' => $this->productDetails->product_title,
            'product_price' => $this->amount * $this->quantity,
            'influencer_amount' => $this->influencerAmount,
        ];
    }
}
