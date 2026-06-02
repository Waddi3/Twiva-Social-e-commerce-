<?php

namespace Modules\Business\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Home\Entities\Transaction;
use Modules\User\Entities\UserSubscription;

class BusinessTransactionsHistory extends Model
{

    protected $table = 'business_transactions_history';
    protected $id = 'id';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'user_id', 'transaction_amount', 'tansaction_details', 'is_paid', 'request_type', 'request_id', 'is_active', 'created_at', 'updated_at'];

    protected $hidden = ['updated_at', 'deleted_at'];

    #request type
    const SUBSCRIPTION_PURCHASE = Transaction::SUBSCRIPTION_PURCHASE;
    const BUYER_PURCHASE = Transaction::BUYER_PURCHASE;
    const BUSINESS_PAY_FOR_CAMPAIGN = Transaction::BUSINESS_PAY_FOR_CAMPAIGN;

    public function userSubscriptionInfo()
    {
        return $this->hasMany(UserSubscription::class, 'id', 'request_id');
    }
}
