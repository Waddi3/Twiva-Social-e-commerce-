<?php

namespace Modules\Campaign\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

/**
 * @method static create(array $input)
 */
class Post extends Model
{
    const BROADCAST = 'BROADCAST';
    const POST_TYPE = [
        null => null,
        self::BROADCAST => 'BROADCAST',
    ];

    protected $fillable = [
        'post_name',
        'price_per_post',
        'description',
        'what_not_to_include',
        'user_id',
        'post_type',
        'what_to_include',
        'status',
        'hashtag',
        'other_requirement',
        'expiration_date',
        'approve_status',
        'user_type'
    ];


    public function Images()
    {
        return $this->hasMany(PostImage::class);
    }

    public function getMainImageAttribute()
    {
        return count($this->Images) ? $this->Images[0]->mediaPath : null;
    }

    public function postDetail()
    {
        return $this->hasMany(PostDetail::class);
    }

    public function user()
    {
        return $this->business();
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function price()
    {
        return $this->hasMany(PostPrice::class)->where('user_id', auth('api')->id());
    }

    public function prices()
    {
        return $this->hasMany(PostPrice::class);
    }

    public function updatePrice()
    {
        return $this->hasMany("App\model\PostPrice", "post_id");
    }

    public function postUserComplete()
    {
        return $this->hasMany(PostUser::class, "post_id")
            ->with(['postDetail', 'user'])
            ->select("post_users.*", "post_prices.price", "post_prices.complementry_item")
            ->whereIn('post_users.status', [1, 3, 4, 5])
            ->join("post_prices", "post_users.post_id", "=", "post_prices.post_id");
    }

    public function postUserCompleted()
    {
        $user_id = auth()->id();
        if (!$user_id) {
            $user_id = auth()->id();
        }
        return $this->hasMany(PostUser::class, "post_id")
            ->with(['postDetail', 'user'])
            ->select(DB::raw(" distinct post_users.id "), "post_users.*", "post_prices.price", "post_prices.infulancer_id as influence", "post_prices.complementry_item", DB::raw("(CASE WHEN (select id FROM `infulancer_ratings` where type = 'post' and user_id = " . $user_id . " and post_id = post_users.post_id and infulencer_id = post_users.user_id) > 0 THEN 1 ELSE 0 END) as is_rated"))->groupBy("post_users.id")
            ->join("post_prices", "post_users.post_id", "=", "post_prices.post_id");
    }

    public function influencersInPost()
    {
        return $this->hasMany(PostUser::class, 'post_id')
            ->where(function ($where) {
                return $where->whereHas('influencer')->orWhereHas('whatsAppGroup');
            });
    }

    public function getAppliedCountAttribute()
    {
        return $this->influencersInPost->where('status', PostUser::AppliedStatus)->count();
    }

    public function getDeclinedCountAttribute()
    {
        return $this->influencersInPost->where('status', PostUser::RejectedStatus)->count();
    }

    public function getHiredCountAttribute()
    {
        return $this->influencersInPost->where('status', PostUser::HiredStatus)->count();
    }

    public function getPendingCountAttribute()
    {
        return $this->influencersInPost->where('status', PostUser::PENDING_STATUS)->count();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'request_id')->where('request_type', Transaction::BUSINESS_PAY_FOR_CAMPAIGN);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'post_products')->withTimestamps();
    }
}
