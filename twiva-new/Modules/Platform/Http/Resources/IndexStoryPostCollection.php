<?php

namespace Modules\Platform\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @see \Modules\Platform\Entities\StoryPost */
class IndexStoryPostCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => $this->productDetails->product_price ?? 0,
            'title' => $this->productDetails->product_title ?? "",
            'main_image_path' => $this->productDetails->productMainImage->imageUrl ?? ($this->images[0] ?? null),
            'platforms' => $this->platform,
            'on_data' => $this->start_date,
            'canceled_at' => $this->canceled_at,
            'is_posted' => $this->is_completed,

        ];
    }
}
