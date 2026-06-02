<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Passport\HasApiTokens;
use Modules\Business\Entities\BusinessMpesa;
use Modules\Business\Entities\BusinessShipping;
use Modules\Business\Entities\Company;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Entities\Invoice;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\ContentType;
use Modules\Influencer\Entities\InfluencerDetails;
use Modules\Order\Entities\Order;
use Modules\Shop\Entities\Shops;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\StoryPost;
use Illuminate\Support\Str;
use Modules\AliExpress\Entities\AliExpressToken;
use Modules\Chat\Entities\ChatGroup;
use Modules\Chat\Entities\Timeline;
use Modules\Platform\Entities\WhatsAppGroup;

class User extends Authenticatable {
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'email_verification_token',
        'email_verified_at',
        'reset_password_token'
    ];
    const InfluencerType = 'Influencer';
    const BusinessType = 'Business';
    const BuyerType = 'Buyer';
    const PartnerType = 'Partner';
    const MALE = 'MALE';
    const FEMALE = 'FEMALE';
    const NOT_SPECIFIED = 'NOT_SPECIFIED';
    const IMAGE_PATH = 'images/products';

    const POINT_TO_KSH = 2500;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_number'      => 'string',
        'fcm_token'         => 'array'
    ];

    const ageRanges = ['17-20', '20-35', '35-45', '+45'];

    // User

    protected $appends = [
        'redeem_price',

        'profile_image'
    ];

    public function routeNotificationForOnfon($notification)
    {
        return $this->mobile;
    }

    public function routeNotificationForFcm()
    {
        if ($this->fcm_token ?? [])
        {
            return $this->fcm_token[count($this->fcm_token) - 1];
        }
        return [];
    }

    public function getMobileAttribute()
    {
        return '+' . $this->phone_code . $this->phone_number;
    }

    public function routeNotificationForTwilio()
    {
        return $this->mobile;
    }

    public function getProfileImageAttribute(): string|null
    {
        if (isset($this->attributes['profile_image']) && $this->attributes['profile_image'] != "" && !is_null($this->attributes['profile_image']))
        {
            return asset('storage/' . User::IMAGE_PATH . '/' . $this->attributes['profile_image']) ?? '';
        }
        return self::defaultAvatar();
    }

    // influencer
    public function contentTypes(): BelongsToMany
    {
        return $this->belongsToMany(ContentType::class, 'content_type_influencers', 'influencer_id');
    }

    public function getInfluencerTotalSalesAttribute()
    {
        $start = (new Carbon('first day of this month'))->startOfDay();
        $end = (new Carbon('last day of this month'))->endOfDay();
        $amount = 0;
        $earning_this_month = 0;
        $count = 0;
        $this->shop?->orders()->where('orders.order_status', Order::DELIVERED_STATUS)
            //            ->whereBetween('orders.created_at', [$start, $end])
            //            ->whereBetween('orders.delivery_date', [$start, $end]) // check if it is necessary
            ->chunk(100, function ($orders) use (&$amount, &$earning_this_month, &$count)
            {
                $orders->each(function ($order) use (&$amount, &$earning_this_month, &$count)
                {
                    $count += 1;
                    $amount += $order->order_amount + $order->shipping_amount;
                    if (now()->format('Y-m') == Carbon::parse($order->created_at)->format('Y-m'))
                    {
                        $earning_this_month += $order->InfluencerAmount;
                    }
                });
            });
        return ['count' => $count, 'amount' => $amount, 'earning' => $earning_this_month];
    }

    public function businessShipping()
    {
        return $this->hasOne(BusinessShipping::class, 'business_id')->orderBy('id')->latest();
    }

    public function shop()
    {
        return $this->hasOne(Shops::class, 'user_id');
    }

    public function InfluencerDetails(): HasOne
    {
        return $this->hasOne(InfluencerDetails::class, "influencer_id", 'id');
    }

    public function getInterestsAttribute(): Collection
    {
        return Category::whereIn('id', explode(',', $this->InfluencerDetails->interests))->select('id', 'name')->get();
    }


    public function UserPlatforms(): HasMany
    {
        return $this->hasMany(UserPlatform::class, 'user_id');
    }


    //business
    public function businessProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->businessProducts();
    }

    public function businessDetails(): HasOne
    {
        return $this->hasOne(Company::class, 'user_id', 'id');
    }

    public function getContactNameAttribute()
    {
        return $this->businessDetails?->contact_name ?? null;
    }

    public function OTP()
    {
        return $this->hasMany(Otp::class);
    }


    public function businessTotalSales()
    {
        $start = (new Carbon('first day of this month'))->startOfDay();
        $end = (new Carbon('last day of this month'))->endOfDay();
        return $this->hasMany(Order::class, 'business_id')
            ->where('order_status', Order::DELIVERED_STATUS);
        //            ->whereBetween('orders.delivery_date', [$start, $end])
        //            ->whereBetween('orders.created_at', [$start, $end]);
    }

    public static function calculateBusinessEarning($user)
    {
        $amount = 0;
        $earning_this_month = 0;
        $count = 0;
        $user->businessTotalSales()->chunk(100, function ($orders) use (&$amount, &$earning_this_month, &$count)
        {
            $orders->each(function ($order) use (&$amount, &$earning_this_month, &$count)
            {
                $count += 1;
                $amount += $order->order_amount + $order->shipping_amount;
                if (now()->format('Y-m') == Carbon::parse($order->created_at)->format('Y-m'))
                {
                    $earning_this_month += $order->BusinessAmount;
                }
            });
        });
        return ['count' => $count, 'amount' => $amount, 'earning' => $earning_this_month];
    }

    public function influencerShopProducts()
    {
        return $this->hasMany(InfluencerShopProducts::class, 'influencer_id');
    }

    public function getInfluencerCountAttribute()
    {
        $products = Product::select('id')->where('user_id', $this->id)->pluck('id');
        return InfluencerShopProducts::select('influencer_id')->distinct()->whereIn('product_id', $products)->groupBy('influencer_id')->get()->count();
        return User::withTrashed()->with('influencerDetails', 'shop', 'influencerShopProducts', 'businessTotalSales')
            ->whereIn('id', function ($query) use ($products)
            {
                $query->select('influencer_id')->from('influencer_shop_product')
                    ->whereIn('product_id', $products);
            })->count();
    }

    public function influencerStories()
    {
        return $this->hasMany(StoryPost::class, 'influencer_id');
    }

    public function redeem()
    {
        return $this->hasMany(RedeemUser::class);
    }

    public function redeemPrice(): Attribute
    {
        return Attribute::get(function ()
        {
            $points = $this->hasMany(RedeemUser::class)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('points');
            return number_format($points * self::POINT_TO_KSH);
        });
    }

    public function mpesa()
    {
        return $this->hasOne(BusinessMpesa::class, 'busi_ID', 'id');
    }

    public function buyers()
    {
        return $this->hasMany(Buyer::class, 'created_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id', 'id');
    }

    public static function fromShop($account, $with_column = ['id', 'type'])
    {
        return User::select($with_column)
            ->whereHas('shop', function ($where_has) use ($account)
            {
                $where_has->where('domain', $account);
            })
            ->first();
    }

    public function scopeFindWithApp($query, $app_id, $app_secret)
    {
        return $query->where('app_id', $app_id)->where('app_secret', $app_secret);
    }

    /**
     * app_type ['id', 'secret']
     */
    public function generateApp($app_type = 'id', $len = 32)
    {
        $generated = $this->app_id ?? Str::random($len);
        $app = User::where("app_$app_type", $generated)->exists();
        if ($app)
        {
            $this->generateApp($app_type);
        }
        return $generated;
    }

    public function setAppId($value)
    {
        if (is_null($value))
        {
            $this->app_id = $this->generateApp('id', 16);
        }
    }

    public function setAppSecret($value)
    {
        if (is_null($value))
        {
            $this->app_secret = $this->generateApp('secret', 32);
        }
    }

    public function updatePrivilege($privilege)
    {
        $this->increment('privilege', $privilege);
        return $this;
    }

    public static function defaultAvatar()
    {
        return asset('admin/images/Profile.svg');
    }

    public function brandTheme()
    {
        return $this->hasOne(BrandTheme::class);
    }


    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_users')->withPivot(['status', 'comments'])->withTimestamps();
    }

    public function verifiedBadges()
    {
        return $this->hasMany(BadgeUser::class)->where('status', BadgeUser::STATUSES['approved'])->with('badge');
    }

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_users')->withTimestamps();
    }

    public function whatsappGroups()
    {
        return $this->hasMany(WhatsAppGroup::class);
    }

    public function unreadTimelines()
    {
        return Timeline::where('user_id', '<>', auth('api')->id())->whereNull('chat_group_id')->whereDoesntHave('views')->count();
    }

    public function aliExpressTokens()
    {
        return $this->hasOne(AliExpressToken::class)->latest('id');
    }
}
