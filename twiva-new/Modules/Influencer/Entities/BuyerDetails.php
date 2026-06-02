<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerDetails extends Model
{
    use SoftDeletes;

    protected $table = "buyer_details";
    protected $primaryKey = 'buyer_id';
    public $incrementing = false;
    protected $dates = ['deleted_at','created_at','updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at','deleted_at'];

    /**
     * The attributes that should be mass assignable.
     *
     * @var array
     */
    protected $fillable = ['buyer_id','name','dob','gender','description','profile_image'];

    /**
     * Get rest of user info
     *
     * @var array
     */
    public function user() {
        return $this->hasOne('App\User', 'id', 'buyer_id');
    }
}
