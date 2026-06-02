<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfluencerBankDetails extends Model
{
    use SoftDeletes;

    protected $table = "influencer_bank_details";
    protected $primaryKey = 'influencer_id';
    public $incrementing = false;
    protected $dates = ['deleted_at','created_at','updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'created_at','updated_at','deleted_at'];

    /**
     * The attributes that should be mass assignable.
     *
     * @var array
     */
    protected $fillable = ['influencer_id','bank_id','account_number','recipient_name','ifsc_code','branch_name','city'];

    public function bank_detais() {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

}
