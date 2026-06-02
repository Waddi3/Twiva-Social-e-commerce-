<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Products\Entities\ProductVideo;
use Modules\Shop\Entities\ShoppingCart;
use Modules\User\Entities\User;

class InfluencerShopProducts extends Model
{
    use SoftDeletes;

    protected $table = 'influencer_shop_product';
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $fillable = ['influencer_id', 'product_id', 'total_sale', 'shop_id', 'product_cover_image', 'product_title', 'product_price', 'product_description'];

    protected $appends = [
        'color_details',
        'size_details',
        'category_details',
        'subcategory_details',
        'user_type'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function actualDetail()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function productDetails()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function productMainImage()
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

    public function businessProductMainImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->whereNotNull('image')
            ->where('is_cover_pic', 1);
    }

    public function influencerProductImages()
    {
        return $this->hasMany(InfluencerShopProductImages::class, 'shop_product_id', 'id')->whereNotNull('image_path');
    }

    public function productImages()
    {
        return $this->hasMany(InfluencerShopProductImages::class, 'shop_product_id', 'id')->whereNotNull('image_path');
    }

    public function BusinessProductImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')->whereNotNull('image');
    }

    public function productReviewsAndRatings()
    {
        return $this->hasMany(ProductReviews::class, 'product_id', 'product_id');
    }

    public function getRatingsAttribute()
    {
        return $this->productReviewsAndRatings->average('rating') ?? 5;
    }

    public function getColorDetailsAttribute()
    {
        if ($this->actualDetail?->color) {
            $color = explode(',', $this->actualDetail?->color);
            return Colors::whereIn('id', $color)->select('id', 'name', 'hexaval')->get();
        } else {
            return collect([]);
        }
    }

    public function getSizeDetailsAttribute()
    {
        if ($this->actualDetail?->size) {
            $size = explode(',', $this->actualDetail?->size);
            return Sizes::whereIn('id', $size)->select('id', 'name')->get();
        } else {
            return collect([]);
        }
    }

    public function getCategoryDetailsAttribute()
    {
        return Categories::where('id', $this->actualDetail?->category?->id)->first();
    }

    public function getCategoryAttribute()
    {
        return $this->getCategoryDetailsAttribute();
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

    public function getSubCategoryAttribute()
    {
        if (isset($this->actualDetail->attributes['sub_category'])) {
            return SubCategories::where('id', $this->actualDetail->attributes['sub_category'])->first();
        } else {
            return null;
        }
    }

    public function cart()
    {
        return $this->hasOne(ShoppingCart::class, 'product_id', 'id');
    }

    public function GetImagePathAttribute()
    {
        return config('app.dev_storage_url') . $this->productMainImage->image;
    }

    public function getMainImagePathAttribute()
    {
        return $this->productMainImage?->image_url;
    }

    public function getBuyingLinkAttribute()
    {
        $userId = base64_encode(base64_encode($this->user_id));
        $productId = base64_encode(base64_encode($this->id));
        return 'https://buyer.twiva.co.ke/buyer-product-detail.php?in=' . $userId . '&pid=' . $productId;
    }

    public function influencerShop()
    {
        return $this->belongsTo(Shops::class, 'shop_id');
    }

    public function productSales()
    {
        return $this->hasMany(OrderDetails::class, 'product_id')->where('status', 3);
    }

    public function influencer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'influencer_id', 'id');
    }

    public function influencerEarning()
    {
        $discount_amount = is_null($this->product->discount_price) ? 0 : ($this->product->retail_price - $this->product->discount_price);
        $earning = $this->product_price - $this->product->price + $discount_amount;
        return $earning * Order::Influencer60Percent;
    }

    public function businessEarning()
    {
        $discount_amount = is_null($this->product->discount) ? 0 : ($this->product->retail_price - $this->product->discount);
        return $this->product->price - $discount_amount;
    }

    public function getDiscountRateAttribute()
    {
        return $this->actualDetail?->discount ?
            abs((float)number_format((100 - (($this->product_price * 100) / $this->actualDetail->retail_price)), 0, '.'))
            : null;
    }

    public function getDiscountAttribute()
    {
        return $this->actualDetail?->discount;
    }

    public function getRetailPriceAttribute()
    {
        return $this->actualDetail?->retail_price;
    }

    public function getBrandAttribute()
    {
        return $this->actualDetail?->brand;
    }


    public function getSkuAttribute()
    {
        return $this->actualDetail?->sku;
    }


    public function getSpecificationAttribute()
    {
        return $this->actualDetail?->specification;
    }

    public function getHasShippingFeesAttribute()
    {
        return $this->actualDetail?->has_shipping_fees;
    }

    public function getUserTypeAttribute()
    {
        return User::InfluencerType;
    }


    public function scopeWithDiscountPercentage($query, $orderBy = 'desc', $as = 'discount_percentage') // asc, desc
    {
        return $query->addSelect(
            DB::raw("
            CASE
                WHEN products.discount IS NOT NULL THEN
                    ROUND(100 - ((products.discount * 100) / products.retail_price), 0)
                ELSE NULL
            END AS $as
        ")
        )
            ->orderBy($as, $orderBy);
    }

    public function scopeBasicSelect($query)
    {
        return $query->select([
            'influencer_shop_product.id',
            'influencer_shop_product.product_title as name',
            'influencer_shop_product.influencer_id',
            'influencer_shop_product.influencer_id as user_id',
            'influencer_shop_product.product_price',
            'influencer_shop_product.product_id'
        ]);
    }

    public function scopeWithCountOfRelatedInfluencerProducts($query, $ordered = true, $order_direction = 'DESC', $as = 'frequency')
    {
        $sub_query = self::select('product_id', DB::raw("COUNT(product_id) AS $as"))
            ->groupBy('product_id');
        $query->leftJoinSub($sub_query, 'frequencies', function ($join) {
            $join->on('influencer_shop_product.product_id', '=', 'frequencies.product_id');
        });
        $query->addSelect('frequency');
        if ($ordered) {
            $query->orderBy($as, $order_direction);
        }
        return $query;
    }

    public function scopeWithCountOfOrders($query, $ordered = true, $order_direction = 'DESC', $as = 'orders_count')
    {

        $sub_query = OrderDetails::leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('shops', 'shops.id', '=', 'orders.shop_id')
            ->leftJoin('users', 'users.id', '=', 'shops.user_id')
            ->select('order_details.product_id', DB::raw("COUNT(order_details.product_id) AS $as"))
            ->where('orders.order_status', 3)
            ->whereBetween('orders.updated_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->where('users.type', User::InfluencerType)
            ->groupBy('order_details.product_id');

        $query->leftJoinSub($sub_query, 'orders_count', function ($join) {
            $join->on($this->table . '.id', '=', 'orders_count.product_id');
        })
            ->addSelect(DB::raw("IFNULL(orders_count.$as, 0) AS $as"))
            // ->having($as, '>', 0)
        ;
        if ($ordered) {
            $query->orderBy($as, $order_direction);
        }
        return $query;
    }

    public function scopeActive($query, $is_active = 1)
    {
        return $query->where('influencer_shop_product.status', $is_active);
    }

    public function getProductPriceAttribute()
    {
        return is_null($this->product?->discount) ? $this->product?->retail_price : $this->product?->discount;
    }

    public function video(): MorphOne
    {
        return $this->morphOne(ProductVideo::class, 'productable');
    }
}
