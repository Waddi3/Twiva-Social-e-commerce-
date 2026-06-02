<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Influencer\Entities\Categories;
use Modules\Influencer\Entities\Colors;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\ProductImage;
use Modules\Influencer\Entities\ProductReviews;
use Modules\Influencer\Entities\Sizes;
use Modules\Influencer\Entities\SubCategories;
use Modules\Order\Entities\OrderDetails;
use Modules\User\Entities\User;

class InfluencerShopProducts extends Model
{
    use SoftDeletes;

    protected $table = 'influencer_shop_product';
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $fillable = ['influencer_id', 'product_id', 'total_sale', 'shop_id', 'product_cover_image', 'product_title', 'product_price', 'product_description'];

    protected $appends = [
        'color_details', 'size_details', 'category_details', 'subcategory_details'
    ];

    public function actualDetail(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function productDetails(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function productMainImage(): HasOne
    {
        $influencerShopProductImages = $this->hasOne(InfluencerShopProductImages::class, 'shop_product_id', 'id')
            ->whereNotNull('image_path')
            ->where('is_cover_pic', 1);
        if ($influencerShopProductImages->exists()) {
            return $influencerShopProductImages;
        } else {
            return $this->businessProductMainImage();
        }
    }

    public function businessProductMainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->whereNotNull('image')
            ->where('is_cover_pic', 1);
    }

    public function influencerProductImages(): HasMany
    {
        return $this->hasMany(InfluencerShopProductImages::class, 'shop_product_id', 'id')->whereNotNull('image_path');
    }

    public function BusinessProductImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')->whereNotNull('image');
    }

    public function productReviewsAndRatings(): HasMany
    {
        return $this->hasMany(ProductReviews::class, 'product_id', 'product_id');
    }

    public function getRatingsAttribute()
    {
        return $this->productReviewsAndRatings->average('rating') ?? 5;
    }

    public function getColorDetailsAttribute()
    {
        if ($this->productDetails->color) {
            $color = explode(',', $this->productDetails->color);
            return Colors::whereIn('id', $color)->select('id', 'name', 'hexaval')->get();
        } else {
            return collect([]);
        }
    }

    public function getSizeDetailsAttribute()
    {
        if ($this->productDetails->size) {
            $size = explode(',', $this->productDetails->size);
            return Sizes::whereIn('id', $size)->select('id', 'name')->get();
        } else {
            return collect([]);
        }
    }

    public function getCategoryDetailsAttribute()
    {
        return Categories::where('id', $this->category)->first();
    }

    public function getSubcategoryDetailsAttribute()
    {
        if ($this->sub_category) {
            $sub_cat = explode(',', $this->sub_category);
            return SubCategories::whereIn('id', $sub_cat)->get();
        } else {
            return [];
        }
    }

    public function GetImagePathAttribute()
    {
        return config('app.dev_storage_url') . $this->productMainImage->image;
    }

    public function getMainImagePathAttribute()
    {
        return $this->productMainImage?->image_url;
    }

    public function getBuyingLinkAttribute(): string
    {
        $userId = base64_encode(base64_encode($this->user_id));
        $productId = base64_encode(base64_encode($this->id));
        return 'https://buyer.twiva.co.ke/buyer-product-detail.php?in=' . $userId . '&pid=' . $productId;
    }

    public function influencerShop(): BelongsTo
    {
        return $this->belongsTo(Shops::class, 'shop_id');
    }

    public function productSales(): HasMany
    {
        return $this->hasMany(OrderDetails::class, 'product_id')->where('status', 3);
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
