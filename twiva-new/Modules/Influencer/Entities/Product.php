<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Business\Entities\Company;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Platform\Entities\StoryPost;
use Modules\Products\Entities\ProductVideo;
use Modules\Products\EventTicketProduct;
use Modules\User\Entities\User;


class Product extends Model {
    use SoftDeletes;

    const MAIN_IMAGE_PATH = 'images/products';
    const PER_PAGE_DEFAULT = 12;
    const HOT_DEAL_PER_PAGE_DEFAULT = 4;
    const TRENDING_PER_PAGE_DEFAULT = 4;

    protected $table = 'products';
    // protected $id = 'id';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'comp_id',
        'category',
        'sub_category_old',
        'sub_category',
        'name',
        'title',
        'sku',
        'brand',
        'rating',
        'total_sale',
        'created_by',
        'status',
        'description',
        'specification',
        'color',
        'size',
        'stock',
        'price',
        'retail_price',
        'main_image',
        'discount',
        'created_at',
        'updated_at',
        'deleted_at',
        'influid',
        'invite',
        'has_shipping_fees',
        'shipping_by',
        'views',
        'is_trending',
        'ali_express_product_id',
        'ali_express_price',
        'ali_express_discount',
        'ali_express_sales_count',
        'is_ali_express',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = [
        'color_details',
        'size_details',
        'category_details',
        'subcategory_details',
        'main_image_path',
        'user_type'
    ];

    protected $casts = [
        'retail_price' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // ->append(['profile_image'])
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')->where('image', '!=', null);
    }

    public function productReviewsAndRatings()
    {
        return $this->hasMany(ProductReviews::class, 'product_id', 'id');
    }

    public function getColorDetailsAttribute()
    {
        if ($this->color)
        {
            $color = explode(',', $this->color);
            return Colors::whereIn('id', $color)->select('id', 'name', 'hexaval')->get();
        }
        else
        {
            return collect([]);
        }
    }

    public function getSizeDetailsAttribute()
    {
        if ($this->size)
        {
            if (getType(explode(',', $this->size)[0] == 'integer'))
            {
                $size = explode(',', $this->size);
                return Sizes::whereIn('id', $size)->select('id', 'name')->get();
            }
            else
            {
                return collect(['this part is working']);
            }
        }
        else
        {
            return collect([]);
        }
    }

    public function getMainImagePathAttribute()
    {
        $image = $this->productImages()->where('is_cover_pic', 1)->value('image');
        if ($image && Str::contains($image, 'http'))
        {
            return $image;
        }
        return $image ? asset('storage/images/products/' . $image) : null;
    }

    public function getSpecificationsAttribute()
    {
        $specifications = explode(',', $this->specification);
        return collect($specifications)->filter(function ($specification)
        {
            return strlen($specification);
        });
    }

    public function getCategoryDetailsAttribute()
    {
        if (isset($this->attributes['category']))
        {
            return Categories::where('id', $this->attributes['category'])->first() ?? null;
        }
        return null;
    }

    public function getCategoryAttribute()
    {
        return $this->getCategoryDetailsAttribute();
    }

    public function getSubcategoryDetailsAttribute()
    {

        if (isset($this->attributes['sub_category']))
        {
            return SubCategories::where('id', $this->attributes['sub_category'])->first();
        }
        else
        {
            return null;
        }
    }

    public function getSubCategoryAttribute()
    {
        return $this->getSubcategoryDetailsAttribute();
    }


    public function getRatingAttribute()
    {
        return $this->productReviewsAndRatings->average('rating') ?? 5;
    }

    public function getDiscountRateAttribute()
    {
        return $this->discount ?
            (float)number_format((100 - (($this->discount * 100) / $this->retail_price)), 0, '.')
            : null;
    }

    public function eShopConnected()
    {
        return $this->hasMany(InfluencerShopProducts::class, 'product_id')->distinct('shop_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'user_id', 'user_id');
    }

    public function productTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->name,
        );
    }

    public function productPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => is_null($this->discount) ? $this->retail_price : $this->discount,
        );
    }

    public function influencerEarning()
    {
        $discount_amount = is_null($this->discount) ? 0 : ($this->retail_price - $this->discount);
        $earning = $this->retail_price - $this->price + $discount_amount;
        return $earning * Order::Influencer60Percent;
    }

    public function businessEarning()
    {
        $discount_amount = is_null($this->discount) ? 0 : ($this->retail_price - $this->discount);
        return $this->price - $discount_amount;
    }

    public function forInfluencers()
    {
        return $this->belongsToMany(User::class, 'shop_influencers', 'shop_id', 'user_id', 'id', 'id')->withTimestamps();
    }

    public static function listProductsOf($user, $per_page, $query, $order_by, $order_as)
    {
        if ($user->type == User::InfluencerType)
        {
            // dd('if influencer product');
            $products = InfluencerShopProducts::where('influencer_id', $user->id)
                ->select(['id', 'product_title as name', 'product_price', 'status', 'product_id'])
                ->whereHas('actualDetail', function ($whereHas) use ($query)
                {
                    $whereHas->where('stock', '>', 0);
                })
                ->where(function ($where) use ($query)
                {
                    $query = "%$query%";
                    $where->where('id', 'like', $query)
                        ->orWhere('product_title', 'like', $query)
                        ->orWhereHas('actualDetail', function ($whereHas) use ($query)
                        {
                            $whereHas->where('brand', 'like', $query);
                        })->orWhereHas('actualDetail.category', function ($whereHas) use ($query)
                        {
                            $whereHas->where('name', 'like', $query);
                        })->orWhereHas('actualDetail.subCategory', function ($whereHas) use ($query)
                        {
                            $whereHas->where('name', 'like', $query);
                        });
                });

            if ($order_by == 'price')
            {
                $products->orderBy('product_price', $order_as);
            }
            elseif ($order_by == 'created_at')
            {
                $products->orderBy($order_by, $order_as);
            }
            else
            {
                $products->inRandomOrder();
            }
        }
        else
        {
            // dd('else business product');
            $products = Product::where('user_id', $user->id)
                ->select(['id', 'user_id', 'name', 'title', 'category', 'sub_category', 'brand', 'status', 'retail_price', 'discount', /*'has_shipping_fees',*/ 'color', 'size', 'stock', 'is_ali_express as product_is_ali_express'])
                ->where('stock', '>', 0)
                ->where(function ($where) use ($query)
                {
                    $query = "%$query%";
                    $where->where('id', 'like', $query)
                        ->orWhere('name', 'like', $query)
                        ->orWhere('brand', 'like', $query)
                        ->orWhereHas('category', function ($whereHas) use ($query)
                        {
                            $whereHas->where('name', 'like', $query);
                        })
                        ->orWhereHas('subCategory', function ($whereHas) use ($query)
                        {
                            $whereHas->where('name', 'like', $query);
                        });
                });
            if ($order_by)
            {
                $products->orderBy($order_by, $order_as);
            }
            else
            {
                $products->inRandomOrder();
            }
        }
        // dd($products->count());
        // $products = $products->paginate($per_page);
        $products = $products->get();
        return $products;
    }

    public static function getImages($product)
    {
        $url_images = [];
        if ($product->resource instanceof Product)
        {
            $images = $product->productImages;
            $url_images = $images->map(function ($image)
            {
                return $image->imageUrl;
            });
        }
        else
        {
            $influencerProductImages = $product->influencerProductImages->map(function ($image)
            {
                return $image->imageUrl;
            })->toArray();
            $BusinessProductImages = $product->BusinessProductImages->map(function ($image)
            {
                return $image->imageUrl;
            })->toArray();
            $url_images = array_merge(
                $influencerProductImages,
                $BusinessProductImages
            );
            $url_images = collect($url_images)->unique();
        }
        return $url_images;
    }

    public static function getPrice($product)
    {
        if ($product->resource instanceof Product)
        {
            return $product->retail_price;
        }
        else
        {
            $discount = $product->actualDetail?->discount ? ($product->actualDetail->retail_price - $product->actualDetail?->discount) : 0;
            return $product->product_price + $discount;
        }
    }

    public static function getDiscountPrice($product)
    {
        if ($product->resource instanceof Product)
        {
            return $product->discount;
        }
        else
        {
            return $product->product_price;
        }
    }

    public static function getIsAliExpress($product)
    {
        if ($product->resource instanceof Product)
        {
            return $product->is_ali_express;
        }
        else
        {
            return $product->actualDetail->is_ali_express;
        }
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category', 'id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategories::class, 'sub_category', 'id');
    }

    public static function product($user, $id, $with_similar = true)
    {
        if ($user->type == User::InfluencerType)
        {
            // dd('if influencer product');
            $product = InfluencerShopProducts::where('influencer_id', $user->id)
                ->select(['id', 'product_title as name', 'product_price', 'product_description as description', 'status', 'product_id'])
                ->with(
                    [
                        'influencerProductImages' => function ($influencerProductImagesQuery)
                        {
                            $influencerProductImagesQuery->select('id', 'shop_product_id', 'image_path');
                        },
                        'BusinessProductImages'   => function ($BusinessProductImagesQuery)
                        {
                            $BusinessProductImagesQuery->select('id', 'image', 'product_id');
                        },
                        'productImages',
                        'productMainImage'
                    ]
                )
                ->whereHas('user')
                ->whereHas('actualDetail')
                ->whereHas('actualDetail.user')
                ->active()
                ->whereHas('actualDetail', function ($whereHas)
                {
                    $whereHas->active()->where('stock', '>', 0);
                });
        }
        else
        {
            // dd('else business product');
            $product = Product::where('user_id', $user->id)
                ->select(['id', 'user_id', 'name', 'title', 'category', 'sub_category', 'brand', 'status', 'retail_price', 'discount', 'description', 'specification', 'color', 'size', /*'has_shipping_fees',*/ 'stock', 'is_ali_express as product_is_ali_express'])
                ->where('stock', '>', 0)
                ->with('productImages');
        }
        $similar_products = collect();
        if ($with_similar)
        {
            $similar_products_query = $product->clone();
            $similar_products = $similar_products_query->where('id', '<>', $id)->inRandomOrder()->take(4)->get();
        }
        // dd($products->count());
        $product = $product->where('id', $id)->first();
        return [$product, $similar_products];
    }

    public static function products($user, $product_ids)
    {
        if ($user->type == User::InfluencerType)
        {
            // dd('if influencer product');
            $products = InfluencerShopProducts::with('actualDetail')
                ->where('influencer_id', $user->id)
                ->whereIn('id', $product_ids)
                ->select(['id', 'product_title as name', 'product_price', 'product_description as description', 'status', 'product_id']);
        }
        else
        {
            // dd('else business product');
            $products = Product::where('user_id', $user->id)
                ->whereIn('id', $product_ids)
                ->select(['id', 'user_id', 'name', 'title', 'category', 'sub_category', 'brand', 'status', 'retail_price', 'discount', 'description', 'specification', 'color', 'size'/*, 'has_shipping_fees'*/])
                ->where('stock', '>', 0);
        }
        // dd($products->count());
        $products = $products->get();
        return $products;
    }


    public static function getStock($product)
    {
        if ($product->resource instanceof Product)
        {
            return $product->stock;
        }
        else
        {
            return $product->actualDetail->stock;
        }
    }

    public function getUserTypeAttribute()
    {
        return User::BusinessType;
    }

    public function scopeWithDiscountPercentage($query, $ordered = true, $order_direction = 'desc', $as = 'discount_percentage') // asc, desc
    {
        $query->addSelect(
            DB::raw("
            CASE
                WHEN discount IS NOT NULL THEN
                    ROUND(100 - ((discount * 100) / retail_price), 0)
                 ELSE NULL
            END AS $as
        ")
        );
        if ($ordered)
        {
            $query->orderBy($as, $order_direction);
        }
        return $query;
    }

    public function scopeBasicSelect($query)
    {
        return $query->select([
            'id',
            'name',
            'sku',
            'user_id',
            'brand',
            'retail_price',
            'discount',
            'size',
            'color',
            'category',
            'sub_category',
            'category as category_id',
            'sub_category as sub_category_id',
            'is_ali_express as product_is_ali_express'
        ]);
    }

    public function scopeWithCountOfRelatedInfluencerProducts($query, $ordered = true, $order_direction = 'DESC', $as = 'frequency')
    {
        $query->addSelect(DB::raw("
            (select count(influencer_shop_product.id) from influencer_shop_product where products.id = influencer_shop_product.product_id) AS $as
        "));
        if ($ordered)
        {
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
            ->where('users.type', User::BusinessType)
            ->groupBy('order_details.product_id');

        $query->leftJoinSub($sub_query, 'orders_count', function ($join)
        {
            $join->on($this->table . '.id', '=', 'orders_count.product_id');
        })
            ->addSelect(DB::raw("IFNULL(orders_count.$as, 0) AS $as"))// ->having($as, '>', 0)
        ;
        if ($ordered)
        {
            $query->orderBy($as, $order_direction);
        }
        return $query;
    }

    public function scopeActive($query, $is_active = 1)
    {
        return $query->where('products.status', $is_active);
    }

    public function video(): MorphOne
    {
        return $this->morphOne(ProductVideo::class, 'productable');
    }

    public static function resizeImage($image_path, $new_path)
    {
        Image::make(Storage::disk('public')
            ->get($image_path))->encode('jpg')
            ->resize('1080', '1350')
            ->save(Storage::disk('public')->path($new_path));
    }

    public static function resizeImageForVideo($images, $separator = ' ')
    {
        $imagePaths = '';
        foreach ($images as $image)
        {
            $image_path = self::MAIN_IMAGE_PATH . '/' . $image;
            if (Storage::disk('public')->exists($image_path))
            {
                $image_name = explode('.', $image)[0];
                $new_image_name = $image_name . '.jpg';
                $new_image_name = StoryPost::IMAGE_PATH . '/' . $new_image_name;
                $new_image_name = str_replace('//', '/', $new_image_name);
                // storage/influencer/stories/images/images/products/1651136131.jpg
                self::resizeImage($image_path, "$new_image_name");
                $imagePaths .= $new_image_name . $separator;
            }
            else
            {
                if (Str::contains($image, 'http'))
                {
                    $image_name = last(explode('/', $image));
                    $image_name = explode('.', $image_name)[0];
                    $new_image_name = $image_name . '.jpg';
                    $new_image_name = StoryPost::IMAGE_PATH . '/' . $new_image_name;
                    $new_image_name = str_replace('//', '/', $new_image_name);
                    $response = Http::get($image);
                    // dd(strpos($response->header('Content-Type'), 'image'));
                    if ($response->successful())
                    {
                        $imageContent = $response->body();
                        Storage::disk('public')->put($new_image_name, $imageContent);
                        self::resizeImage($new_image_name, "$new_image_name");
                        $imagePaths .= $new_image_name . $separator;
                    }
                }
            }
        }
        return $imagePaths;
    }

    public static function removeNA($details)
    {
        $filtered = $details->filter(function ($value, $key)
        {
            return $value->name !== 'N/A';
        });
        return $filtered->pluck('name')->count()
            ? $filtered->pluck('name')->implode(', ')
            : null;
    }

    public function discount(): Attribute
    {
        return Attribute::make(
            get: function ()
            {
                return $this->attributes['discount'] ? round($this->attributes['discount']) : $this->attributes['discount'];
            }
        );
    }

    public function retailPrice(): Attribute
    {
        return Attribute::make(
            get: function ()
            {
                return round($this->attributes['retail_price']);
            }
        );
    }

    public static function getImageUrl($product)
    {
        // dd($product);
        $image = $product->main_image ?? $product->product_main_image;
        if ($image && Str::contains($image, 'http'))
        {
            return $image;
        }
        return asset('storage/images/products/' . $image);
    }

    public static function shipmentTag($product)
    {
        if ($product instanceof InfluencerShopProducts)
        {
            $product = $product->actualDetail;
        }
        if ($product->product_is_ali_express)
        {
            return Product::shipmentTagStyle('Overseas Shipment', '#613f95', '#ded8e8');
        }
        return Product::shipmentTagStyle('Local Dispatch', '#36b460', '#d8efde');
    }

    public static function shipmentTagColor($product)
    {
        if ($product instanceof InfluencerShopProducts)
        {
            $product = $product->actualDetail;
        }
        if ($product->product_is_ali_express)
        {
            return '#613f95';
        }
        return '#36b460';
    }

    public static function shipmentTagBackgroundColor($product)
    {
        if ($product instanceof InfluencerShopProducts)
        {
            $product = $product->actualDetail;
        }
        if ($product->product_is_ali_express)
        {
            return '#ded8e8';
        }
        return '#d8efde';
    }

    public static function shipmentTagText($product)
    {
        if ($product instanceof InfluencerShopProducts)
        {
            $product = $product->actualDetail;
        }
        if ($product->product_is_ali_express)
        {
            return 'Overseas Shipment';
        }
        return 'Local Dispatch';
    }

    public static function shipmentTagStyle($text, $color, $background_color)
    {
        return "<span style='color: $color; background-color: $background_color; padding: 2px 4px; border-radius: 5px;'>$text</span>";
    }

    public static function renderCardForECommerce($product)
    {
        return view('front::components.e-commerce.layout.elements.product-card', [
            'id'                         => $product->influencer_shop_product_id,
            'userType'                   => $product->user_type ?? "Influencer",
            'image'                      => Product::getImageUrl($product),
            'discount'                   => $product->discount_rate,
            'userImage'                  => $product->profile_image ? asset('storage/' . User::IMAGE_PATH . '/' . $product->profile_image) : User::defaultAvatar(),
            'userName'                   => $product->user_name,
            'productName'                => $product->influencer_shop_product_product_title,
            'productPrice'               => $product->discount_rate ? $product->product_discount : $product->product_retail_price,
            'retailPrice'                => $product->product_retail_price,
            'shipmentTagColor'           => Product::shipmentTagColor($product),
            'shipmentTagBackgroundColor' => Product::shipmentTagBackgroundColor($product),
            'shipmentTagText'            => Product::shipmentTagText($product),
        ])->render();
    }

    public static function renderCardForShop($product)
    {
        return view('shop::components.layout.elements.product-card', [
            'id'                         => $product->id,
            'userType'                   => $product->user_type,
            'image'                      => $product->main_image_path,
            'discount'                   => $product->discount_rate,
            'userImage'                  => $product?->user?->profile_image,
            'userName'                   => $product?->user?->name,
            'productName'                => $product->name,
            'productPrice'               => $product->product_price,
            'retailPrice'                => $product->retail_price,
            'shipmentTagColor'           => Product::shipmentTagColor($product),
            'shipmentTagBackgroundColor' => Product::shipmentTagBackgroundColor($product),
            'shipmentTagText'            => Product::shipmentTagText($product),
        ])->render();
    }

    public function eventTicketProduct(): HasOne
    {
        return $this->hasOne(EventTicketProduct::class, 'business_product_id');
    }

    public function getIsEventTicketProductAttribute(): bool
    {
        return (boolean)$this->eventTicketProduct?->exists() ?? false;
    }
}
