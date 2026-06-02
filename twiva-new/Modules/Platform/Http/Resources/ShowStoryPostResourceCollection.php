<?php

namespace Modules\Platform\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Modules\Platform\Entities\StoryPost */
class ShowStoryPostResourceCollection extends ResourceCollection
{
    public $storyPost;

    public function __construct($storyPost)
    {
        $this->storyPost = $storyPost;
        parent::__construct($storyPost);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->storyPost->id ?? null,
            'caption' => $this->storyPost->caption ?? null,
            'product_title' => $this->storyPost->productDetails->product_title ?? null,
            'product_price' => $this->storyPost->productDetails->product_price ?? null,
            'platforms' => $this->storyPost->platform ?? null,
            'product_main_image' => $this->storyPost->productDetails->productMainImage->imageUrl ?? ($this->storyPost->images[0] ?? null),
            'created_at' => $this->storyPost->created_at ?? null,
            'start_date' => $this->storyPost->start_date ?? null,
            'repeat_type' => $this->storyPost->repeat_type ?? null,
            'repeat_days' => $this->storyPost->repeated_days ?? null,
            'end_after_type' => $this->storyPost->end_after_type ?? null,
            'end_date' => $this->storyPost->end_date ?? null,
            'number_of_time' => $this->storyPost->number_of_time ?? null,
            'images' => $this->storyPost->images,
            'is_posted' => $this->storyPost->is_completed ?? null,
            'canceled_at' => $this->storyPost->canceled_at ?? null,
            'schedule_post' => $this->storyPost->start_date_format,
            'posted_on' => $this->storyPost->posted_on,
            'updated_at' => $this->storyPost->updated_at,
            'is_completed' => $this->storyPost->is_completed,
        ];
    }
}
