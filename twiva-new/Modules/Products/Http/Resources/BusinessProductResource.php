<?php

namespace Modules\Products\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Influencer\Entities\Product;
use Modules\Partner\Enums\ProductShipping;

/** @mixin Product */
class BusinessProductResource extends JsonResource {

    private Product $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function toArray($request)
    {
         $data = [
            'id'                      => $this->product->id,
            'name'                    => $this->product->name,
            'brand'                   => $this->product->brand,
            'colors'                  => $this->product->colorDetails,
            'sizes'                   => $this->product->sizeDetails,
            'description'             => $this->product->description,
            'specifications'          => $this->product->Specifications,
            'main_image_path'         => $this->product->mainImagePath,
            'images'                  => $this->product->productImages->map(function ($image)
            {
                $image->image = $image->imageUrl;
                return collect($image->toArray())->only('id', 'image', 'is_cover_pic') ?? [];
            }),
            'reviews'                 => $this->product->productReviewsAndRatings->map(function ($review)
            {
                return $review->only('id', 'product_id', 'rating', 'review', 'buyerName');
            }),
            'discount'                => $this->product->discount,
            'discount_percent'        => $this->product->discountRate,
            'category'                => $this->product->categoryDetails,
            'sub_category'            => $this->product->subCategoryDetails,
            'price'                   => (float)$this->product->price,
            'retail_price'            => $this->product->retail_price,
            'stock'                   => (float)$this->product->stock,
            'is_published'            => (bool)$this->product->status,
            'created_at'              => $this->product->created_at,
            'has_shipping_fees'       => $this->product->has_shipping_fees,
            'shipping_by_partner'     => $this->product->shipping_by == ProductShipping::Partner->value ? 1 : 0,
            'forInfluencers'          => $this->product->forInfluencers,
            'is_ali_express'          => $this->product->is_ali_express,
            'is_event_ticket_product' => $this->product->is_event_ticket_product
        ];
        if ($this->product->is_event_ticket_product)
        {
            $data['date'] = $this->product->eventTicketProduct->date;
            $data['start_time'] = $this->product->eventTicketProduct->start_time;
            $data['end_time'] = $this->product->eventTicketProduct->end_time;
            $data['location'] = $this->product->eventTicketProduct->location;
        }
        return $data;
    }
}
