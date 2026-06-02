<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Entities\User;


class ProductReviews extends Model
{
    use SoftDeletes;

    protected $table = "product_reviews";
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = ['created_at','updated_at','deleted_at'];

    /**
     * The attributes that should be mass assignable.
     *
     * @var array
     */
    protected $fillable = ['buyer_id', 'product_id', 'rating', 'review'];

    public function buyer()
    {
        return $this->hasOne(User::class, 'id', 'buyer_id');
    }

    public function getBuyerNameAttribute()
    {
        return $this->buyer->name ?? 'FakeName';
    }

}
